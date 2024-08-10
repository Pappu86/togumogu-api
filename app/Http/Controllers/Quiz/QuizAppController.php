<?php

namespace App\Http\Controllers\Quiz;

use App\Http\Controllers\Controller;
use App\Models\Quiz\Quiz;
use App\Models\Quiz\QuizResult;
use App\Models\Quiz\Question;
use App\Models\Quiz\QuestionOption;
use App\Models\Quiz\QuestionAnswer;
use App\Models\User\Customer;
use App\Http\Resources\Quiz\QuizAppResource;
use App\Http\Resources\Quiz\QuizSingleResource;
use App\Http\Resources\Quiz\QuizAnswerReviewResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Traits\CommonHelpers;

class QuizAppController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param $locale
     * @return AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function getAll(Request $request, $locale)
    {
        App::setLocale($locale);

        $query = $request->query('query');
        $sortBy = $request->query('sortBy');
        $direction = $request->query('direction');
        $per_page = $request->query('per_page', 10);
        $category = $request->query('category');

        $quizs = Quiz::with(['translations'])->where('status', 'active')->latest();

        if ($query) {
            $quizs = $quizs->whereTranslationLike('title', '%' . $query . '%');
        }
        if ($sortBy) {
            $quizs = $quizs->orderBy($sortBy, $direction);
        }
        if ($per_page === '-1') {
            $results = $quizs->get();
            $quizs = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $quizs = $quizs->paginate($per_page);
        }
        return QuizAppResource::collection($quizs);
    }

    /**
     * @param $locale
     * @param $slug
     * @return QuizSingleResource|JsonResponse
     */
    public function getSingle($locale, $slug): QuizSingleResource|JsonResponse
    {
        // load relations
        $quiz = Quiz::with(['translations'])->whereTranslation('slug', $slug)->firstOrFail();

        if ($quiz) {
            return new QuizSingleResource($quiz);
        } else {
            return response()->json([
                'data' => collect()
            ]);
        }
    }

    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function getReview(Request $request)
    {
        $answererId = $request->get('answerer_id');
        $quizId = $request->get('quiz_id');

        try {

            $quizResult = DB::table('question_answers')
                ->where('answerer_id', '=', $answererId)
                ->where('quiz_id', '=', $quizId)
                ->get();

            // return success message
            return QuizAnswerReviewResource::collection($quizResult);
        } catch (Throwable $exception) {
            // log exception
            report($exception);
            // return failed message
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function checkAnswerer(Request $request): JsonResponse
    {
        $customer = Auth::user();
        $customerId = $customer?->id;

        if(!$customerId ) {
            return response()->json([ 'message' => Lang::get('auth.not_allow') ], 401);
        }
        $quizId = $request->get('quiz_id');
        $quiz=Quiz::where('id', '=', $quizId)->first();
        $isRetryAllow=$quiz?->retry_allow;
        
        if(isset($customerId)  && !$isRetryAllow){
            $quizResult = QuizResult::where('customer_id', '=', $customerId)->where('quiz_id', '=', $quizId)->first();
            if(isset($quizResult?->id)) {
                return response()->json([
                    'message' => 'You have already participated in this quiz. Please wait for the winner announcement',
                    'isValid' => false
                ], 422);
            }
        }
     
        return response()->json([
            'message' => 'Welcome to Togumogu quiz!',
            'isValid' => true
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function createAnswererAccount(Request $request): JsonResponse
    {
        
        $email = $request->get('email');
        $mobile = $request->get('mobile');
        $customerId = $request->get('customer_id');

        if (isset($mobile)) {
            $customer = Customer::query()->where('mobile', '=', $mobile)->first();
            $customerId = $customer?->id;
        } else {
            $customer = Customer::query()->where('email', '=', $email)->first();
            $customerId = $customer?->id;
        }

        // begin database transaction
        DB::beginTransaction();
        try {

            $quizResult = QuizResult::create([
                'customer_id' => $customer?->id,
                'quiz_id' => $request->get('quiz_id'),
                'name' => $request->get('name')?:$customer?->name?:'',
                'email' => $request->get('email')?:$customer?->email?:'',
                'mobile' => $request->get('mobile')?:$customer?->mobile?:'',
                'referral_url' => '',
                'quiz_score' => $request->get('quiz_score')?:0,
                'answerer_score' => 0,
                'view_time' => $request->get('view_time'),
                'submit_time' => $request->get('submit_time'),
                'taken_time' => $request->get('taken_time'),
            ]);

            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.create'),
                'data' => [
                    "id" => $quizResult?->id,
                    "customer_id" => $quizResult?->customer_id,
                    "name" => $quizResult?->name,
                    "email" => $quizResult?->email,
                    "mobile" => $quizResult?->mobile,
                    "referral_url" => $quizResult?->referral_url,
                    "quiz_score" => $quizResult?->quiz_score,
                    "answerer_score" => $quizResult?->answerer_score,
                    "view_time" => $quizResult?->view_time,
                    "submit_time" => $quizResult?->submit_time,
                    "taken_time" => $quizResult?->taken_time,
                ]
            ], 201);
        } catch (Throwable $exception) {
            // log exception
            report($exception);
            // rollback database
            DB::rollBack();
            // return failed message
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function submitQuestionAnswer(Request $request): JsonResponse
    {
        $auth_id = Auth::id();
        $answers = $request->get('answers');
        $answerer_id = $request->get('answerer_id');
        $quiz_id = $request->get('quiz_id');
        $customer_id = $request->get('customer_id', $auth_id);
        $quiz_score = $request->get('total_points');

        if (!isset($answerer_id)) {
            return response()->json([ 'message' => 'Some things went wrong. Please, try again!'], 422);
        }

        if (isset($answerer_id)) {
            $existAnswer = QuestionAnswer::where('answerer_id', '=', $answerer_id)
                ->where('quiz_id', '=', $quiz_id)
                ->first();
            
            if(isset($existAnswer?->id)) {
                return response()->json([
                    'message' => 'You have already participated in this quiz. Please wait for the winner announcement',
                    'isValid' => false
                ], 422);
            }
        }

        // begin database transaction
        DB::beginTransaction();
        try {
           $selectedOptionsId = collect($answers)->pluck('id');
            
           $questionAnswers = QuestionOption::whereIn('id', $selectedOptionsId)
            ->get()
            ->map(function ($answer) use (
                $customer_id, $answerer_id, $quiz_id) {

                $answererScore = 0;
                $question = Question::where('id', $answer->question_id)->first();
                if($answer?->is_answer) {
                    $answererScore += $question?->score?:1;
                }

                return [
                    'answerer_score' => $answererScore,
                    'customer_id' => $customer_id,
                    'answerer_id' => $answerer_id,
                    'quiz_id' => $quiz_id,
                    'question_id' => $answer?->question_id,
                    'question_option_id' => $answer?->id,
                    'is_right_answer' => $answer?->is_answer,
                    'answer_option' => json_encode([
                        'id' => $answer->id,
                        'text' => $answer->text,
                        'image' => $answer->image,
                        'video' => $answer->image,
                        'link' => $answer->link,
                        'hint' => $answer->hint,
                        'description' => $answer->description,
                        'is_answer' => $answer->is_answer,
                    ]),
                    'question' => json_encode([
                        'id' => $question->id,
                        'title' => $question?->title?:'',
                        'image' => $question?->image?:'',
                        'video' => $question?->video?:'',
                        'audio' => $question?->audio?:'',
                        'hint' => $question->hint,
                        'score' => $question?->score?:0,
                        'type' => $question?->type?:'',
                    ]),
                    'question_options' => json_encode($question->options),
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                ];

            })->all();

            //Insert all answers in question answer table
            DB::table('question_answers')->insert($questionAnswers);

            //Update quiz result table
            $customerScore = array_sum(array_column($questionAnswers, 'answerer_score'));
            $updateableQuizResult = DB::table('quiz_results')->where('id', $answerer_id)->where('submission_status', 'pending');
            $quizResultFirst = $updateableQuizResult->first();
            
            $updateableQuizResult->update([
                'answerer_score' => $customerScore?:0,
                'quiz_score' => $quiz_score?:0,
                'submission_status' => 'success'
            ]);

            $quizResult = DB::table('quiz_results')->where('id', $answerer_id)->first();

            // commit database
            DB::commit();
            
            //Start Adding reward point
            $rewardPoints = 0;
            if($quizResultFirst?->submission_status === 'pending') {    
                $commonHelpers = new CommonHelpers;
                $rewardPoints = $commonHelpers->addQuizSubmissionRewardPoints($quizResult);
            }
            //End Adding reward point 

            // return success message
            return response()->json([
                'message' => Lang::get('crud.sent'),
                'rewardPoints' =>  $rewardPoints,
                'data' => [
                    "id" => $quizResult->id,
                    "name" => $quizResult->name,
                    "email" => $quizResult->email,
                    "mobile" => $quizResult->mobile,
                    "referral_url" => $quizResult->referral_url,
                    "quiz_score" => $quizResult->quiz_score,
                    "answerer_score" => $quizResult->answerer_score,
                    "view_time" => $quizResult->view_time,
                    "submit_time" => $quizResult->submit_time,
                    "taken_time" => $quizResult->taken_time
                ]
            ], 201);
        } catch (Throwable $exception) {
            // log exception
            report($exception);
            // rollback database
            DB::rollBack();
            // return failed message
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 400);
        }
    }

    /**
     * @param $locale
     * @param Request $request
     * @return JsonResponse
     */
    public function getParticipatedAllQuiz($locale, Request $request){
        App::setLocale($locale);

        try {
            $customer = Auth::user();
            $customerId = $customer?->id;

            if(!$customerId ) {
                return response()->json([ 'message' => Lang::get('auth.not_allow') ], 401);
            }

            $quizs = Quiz::with(['translations'])->where('status', 'active')->get()->map(function($quiz) use ($customerId) {
                $quizResult=DB::table('quiz_results')
                            ->where('quiz_id', '=', $quiz->id)
                            ->where('customer_id', $customerId)
                            ->latest()->first();

                if($quizResult){
                    return [
                        'id'=>$quiz->id,
                        'title'=>$quiz->title,
                        'status'=>$quiz->status,
                        'image'=>$quiz->image,
                        'start_date'=>$quiz->start_date,
                        'end_date'=>$quiz->end_date,
                        'retry_allow'=>$quiz->retry_allow,
                        'slug'=>$quiz->slug,
                        'sub_title'=>$quiz->sub_title,
                        'meta_description'=>$quiz->meta_description,
                        'email'=>$quizResult->email,
                        'mobile'=>$quizResult->mobile,
                        'answerer_score'=>$quizResult->answerer_score,
                        'quiz_score'=>$quizResult->quiz_score,
                        'answerer_id'=>$quizResult->id
                    ];
                }           
            });
           
            return response()->json([
                    'data' => collect($quizs)->filter()->values()->toArray()
                ], 200);

        } catch (Throwable $exception) {
            // log exception
            report($exception);
            // return failed message
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 400);
        }
    }

    /**
     * @param $locale
     * @param Request $request
     * @return JsonResponse
     */
    public function checkParticipateQuiz($locale, Request $request): JsonResponse
    {
        $quizId = $request->get('quiz_id');
        $customer = Auth::user();
        $customerId = $customer?->id;

        if($customerId && $quizId) {
            $quizResult = QuizResult::where('customer_id', '=', $customerId)->where('quiz_id', '=', $quizId)->first(); 
             if(isset($quizResult?->id)) {
                return response()->json([
                    'message' => 'You have already participated in this quiz!',
                    'data'=>[
                        'customerId'=> $customerId,
                        'isParticipate' => true
                    ]
                ],200);
             }
        }

        return response()->json([
            'message' => 'Welcome to Togumogu quiz!',
            'data'=>[
                'customerId'=> $customerId,
                'isParticipate' => false
            ]
        ],200);        
    }

    /**
     * @param Request $request
     * @param $locale
     * @return AnonymousResourceCollection
     */
    public function getTrackerQuiz(Request $request, $locale)
    {
        App::setLocale($locale);
        $limit = (int)$request->query('limit', 1);
        $sort_by = $request->query('sort_by');
        $direction = $request->query('direction', 'asc');

        $quizs = Quiz::with(['translations'])
            ->where('tracker', '=', $request->get('tracker_type'))
            ->where('tracker_start_day', '<=', $request->get('tracker_day'))
            ->where('tracker_end_day', '>=', $request->get('tracker_day'))
            ->where('status', '=', 1);

        if($quizs->count() === 0 && !($request->get('tracker_type') ==='other')) {
            $quizs = Quiz::with(['translations'])
                ->where('tracker_end_day', '>', $request->get('tracker_day'))
                ->where('status', '=', 1);
        };
        
        if ($sort_by) {
            $quizs = $quizs->orderBy($sort_by, $direction);
        }

        $quizs = $quizs->inRandomOrder()->paginate($limit);

        return QuizAppResource::collection($quizs);
    }

}
