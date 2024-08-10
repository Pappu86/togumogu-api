<?php

namespace App\Http\Controllers\Quiz;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Quiz\QuizEditResource;
use App\Http\Resources\Quiz\QuizResource;
use App\Models\Quiz\Quiz;
use App\Models\Quiz\QuizTranslation;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Throwable;

class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param $locale
     * @return AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function index(Request $request, $locale): AnonymousResourceCollection
    {
        App::setLocale($locale);
        $this->authorize('viewAny quiz');

        $query = $request->query('query');
        $sortBy = $request->query('sortBy');
        $direction = $request->query('direction');
        $per_page = $request->query('per_page', 10);
        $category_slug = $request->query('category');

        $quiz = Quiz::latest();

        // Query by search value
        if ($query) {
            $quiz = Quiz::whereTranslationLike('title', '%' . $query . '%');
        }
        if ($sortBy) {
            $quiz = Quiz::orderBy($sortBy, $direction);
        }

        if ($per_page === '-1') {
            $results = $quiz->get();
            $quiz = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $quiz = $quiz->paginate($per_page);
        }

        return QuizResource::collection($quiz);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $locale
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store($locale): JsonResponse
    {
        App::setLocale($locale);
        $this->authorize('create quiz');

        // begin database transaction
        DB::beginTransaction();
        try {
            $quiz = Quiz::create(['user_id' => auth()->id()]);

            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.create'),
                'quizId' => $quiz->id
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
     * Display the specified resource.
     *
     * @param $locale
     * @param Quiz $quiz
     * @return QuizEditResource|JsonResponse
     * @throws AuthorizationException
     */
    public function show($locale, Quiz $quiz)
    {
        App::setLocale($locale);
        $this->authorize('view quiz');

        try {
            return new QuizEditResource($quiz);
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
     * @param Quiz $quiz
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(Request $request, $locale, Quiz $quiz): JsonResponse
    {
        App::setLocale($locale);
        $this->authorize('update quiz');

        // begin database transaction
        DB::beginTransaction();
        try {
            $quiz->update($request->all());

            // commit database
            DB::commit();
            
            // return success message
            return response()->json([
                'message' => Lang::get('crud.update')
            ]);
        } catch (Throwable $exception) {
            // log exception
            report($exception);
            // rollback database
            DB::rollBack();
            // return failed message
            return response()->json([
                'message' => $exception->getMessage()
                // 'message' => Lang::get('crud.error')
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $locale
     * @param Quiz $quiz
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy($locale, Quiz $quiz): JsonResponse
    {
        App::setLocale($locale);
        $this->authorize('delete quiz');

        // begin database transaction
        DB::beginTransaction();
        try {
            // delete product
            $quiz->delete();

            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.trash')
            ]);
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
     * Create slug for quiz.
     *
     * @param $locale
     * @param $title
     * @return JsonResponse
     */
    public function checkSlug($locale, $title): JsonResponse
    {
        try {
            $slug = Str::slug($title, '-', $locale);

            # slug repeat check
            $latest = QuizTranslation::where('slug', '=', $slug)
                ->latest('id')
                ->value('slug');

            if ($latest) {
                $pieces = explode('-', $latest);
                $number = intval(end($pieces));
                $slug .= '-' . ($number + 1);
            }

            return response()->json([
                'slug' => $slug
            ]);
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
     * @return JsonResponse | AnonymousResourceCollection
     */
    public function getActiveQuizs(Request $request): JsonResponse | AnonymousResourceCollection
    {
        try {

            $quizs = Quiz::with('translation')
                ->latest()
                ->get()
                ->map(function ($quiz) {
                    return [
                        'id'            => $quiz->id,
                        'value'            => $quiz->id,
                        'text'            => $quiz->title,
                        'name'         => $quiz->title,
                    ];
                });
            return response()->json([
                'data' => $quizs
            ], 200);

        } catch (Throwable $exception) {
            report($exception);
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 404);
        }
    }
}
