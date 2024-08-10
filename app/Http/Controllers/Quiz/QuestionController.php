<?php

namespace App\Http\Controllers\Quiz;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Quiz\QuestionEditResource;
use App\Http\Resources\Quiz\QuestionResource;
use App\Models\Quiz\Question;
use App\Models\Quiz\QuestionTranslation;
use App\Models\Quiz\QuestionOption;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Throwable;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param $locale
     * @return AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function index(Request $request, $locale)
    {
        App::setLocale($locale);

        $this->authorize('viewAny question');

        $query = $request->query('query');
        $sortBy = $request->query('sortBy');
        $direction = $request->query('direction');
        $per_page = $request->query('per_page', 10);
        $category_slug = $request->query('category');

        $questions = Question::latest();

        if ($query) {
            $questions = Question::whereTranslationLike('title', '%' . $query . '%');
        }
        if ($sortBy) {
            $questions = Question::orderBy($sortBy, $direction);
        }
        if ($per_page === '-1') {
            $results = $questions->get();
            $questions = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $questions = $questions->paginate($per_page);
        }
        return QuestionResource::collection($questions);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $locale
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store($locale)
    {
        App::setLocale($locale);

        $this->authorize('create question');

        // begin database transaction
        DB::beginTransaction();
        try {
            $question = Question::create([
                'user_id' => auth()->id(),
                'datetime' => now()
            ]);

            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.create'),
                'questionId' => $question->id
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
     * @param Question $question
     * @return QuestionEditResource|JsonResponse
     * @throws AuthorizationException
     */
    public function show($locale, Question $question)
    {
        App::setLocale($locale);

        $this->authorize('view question');

        try {
            return new QuestionEditResource($question);
        } catch (Throwable $exception) {
            report($exception);
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 404);
        }
    }

    /**
     * Edit question.
     *
     * @param $locale
     * @param Question $question
     * @return QuestionEditResource|JsonResponse
     * @throws AuthorizationException
     */
    public function edit($locale, Question $question)
    {
        App::setLocale($locale);

        $this->authorize('update question');

        try {
            return new QuestionEditResource($question);
        } catch (Throwable $exception) {
            report($exception);
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $locale
     * @param Question $question
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(Request $request, $locale, Question $question)
    {

        App::setLocale($locale);

        $this->authorize('update question');

        // begin database transaction
        DB::beginTransaction();
        try {

            $question->update($request->all());

            // Update qeustions options
            if ($request->filled('options')) {
               // Remove all exists question options
               DB::table('question_options')->where('question_id', $question->id)->delete();

               // Create new question options
                $options = $request->input('options');
                foreach ($options as $option) {
                    QuestionOption::create([
                        "question_id" => $question?->id,
                        "text" => $option['text']?:'',
                        "image" => $option['image']?:'',
                        "audio" => $option['audio']?:'',
                        "video" => $option['video']?:'',
                        "description" => $option['description']?:'',
                        "hint" => $option['hint']?:'',
                        "is_answer" => $option['is_answer']?:0,
                        "status" => 'active',
                    ]);
                }
            }

            // Update qeustions tags
            if ($request->filled('tags')) {
                $items = collect($request->input('tags'))->pluck('id');
                $question->tags()->sync($items);
            }

            // commit database
            DB::commit();

            // return success message
            return response()->json([
                'message' => Lang::get('crud.update')
            ], 200);
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
     * Remove the specified resource from storage.
     *
     * @param $locale
     * @param Question $question
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy($locale, Question $question)
    {
        App::setLocale($locale);

        $this->authorize('delete question');

        // begin database transaction
        DB::beginTransaction();
        try {
            // delete question
            $question->delete();

            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.trash')
            ], 200);
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
     * Get trashed questions
     *
     * @param Request $request
     * @param $locale
     * @return AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function getTrashed(Request $request, $locale)
    {
        App::setLocale($locale);

        $this->authorize('viewAny question');

        // $per_page = empty($request->query('per_page')) ? 10 : (int)$request->query('per_page');
        // $articles = Question::onlyTrashed()->latest()->paginate($per_page);

        $query = $request->query('query');
        $sortBy = $request->query('sortBy');
        $direction = $request->query('direction');
        $per_page = $request->query('per_page', 10);

        $questions = Question::onlyTrashed();
        if ($query) {
            $questions = $questions->whereTranslationLike('title', '%' . $query . '%');
        }

        if ($sortBy) {
            $questions = $questions->orderBy($sortBy, $direction);
        } else {
            $questions = $questions->latest();
        }

        if ($per_page === '-1') {
            $results = $questions->get();
            $questions = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $questions = $questions->paginate($per_page);
        }

        return QuestionResource::collection($questions);
    }

    /**
     * Restore all trashed question
     *
     * @param Request $request
     * @param $locale
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function restoreTrashed(Request $request, $locale)
    {
        App::setLocale($locale);

        $this->authorize('restore question');

        $ids = explode(',', $request->get('ids'));

        // begin database transaction
        DB::beginTransaction();
        try {
            if (isset($ids)) {
                Question::onlyTrashed()
                    ->whereIn('id', $ids)
                    ->restore();
            } else {
                Question::onlyTrashed()->restore();
            }

            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.restore')
            ], 200);
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
     * Restore single trashed question
     *
     * @param $locale
     * @param $id
     * @return mixed
     * @throws AuthorizationException
     */
    public function restoreSingleTrashed($locale, $id)
    {
        App::setLocale($locale);

        $this->authorize('restore question');

        // begin database transaction
        DB::beginTransaction();
        try {
            Question::onlyTrashed()
                ->where('id', '=', $id)
                ->restore();

            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.restore')
            ], 200);
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
     * Permanently delete all trashed questions
     *
     * @param Request $request
     * @param $locale
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function forceDelete(Request $request, $locale)
    {
        App::setLocale($locale);

        $this->authorize('forceDelete questions');

        $ids = explode(',', $request->get('ids'));

        // begin database transaction
        DB::beginTransaction();
        try {
            if (isset($ids)) {
                $questions = Question::onlyTrashed()
                    ->whereIn('id', $ids)
                    ->get();
            } else {
                $questions = Question::onlyTrashed()->get();
            }
            foreach ($questions as $question) {
                // delete tag
                $question->tags()->detach();
                // delete question
                $question->forceDelete();
            }

            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.delete')
            ], 200);
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
     * Permanently delete single trashed question
     *
     * @param $locale
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function forceSingleDelete($locale, $id)
    {
        App::setLocale($locale);

        $this->authorize('forceDelete question');

        // begin database transaction
        DB::beginTransaction();
        try {
            $question = Question::onlyTrashed()
                ->where('id', '=', $id)
                ->first();

            // delete tag
            $question->tags()->detach();
            // delete question
            $question->forceDelete();

            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.delete')
            ], 200);
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

}