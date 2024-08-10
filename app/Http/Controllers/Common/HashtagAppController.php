<?php

namespace App\Http\Controllers\Common;

use App\Models\Common\Tag;
use App\Models\Common\TagTranslation;
use App\Http\Controllers\Controller;
use App\Http\Resources\Common\TagResource;
use App\Models\Common\Hashtag;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class HashtagAppController extends Controller
{

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function index(Request $request)
    {

        $query = $request->query('query');
        $sortBy = $request->query('sortBy');
        $direction = $request->query('direction');
        $per_page = $request->query('per_page', 5);

        $hashtags = Hashtag::latest();
        
        if ($query) {
            $hashtags = $hashtags->whereLike('name', '%' . $query . '%');
        }
        if ($sortBy) {
            $hashtags = $hashtags->orderBy($sortBy, $direction);
        }
        if ($per_page === '-1') {
            $results = $hashtags->get();
            $hashtags = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $hashtags = $hashtags->paginate($per_page);
        }

        return TagResource::collection($hashtags);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store()
    {
        // begin database transaction
        DB::beginTransaction();
        try {
            $tag = Tag::create();

            // commit changes
            DB::commit();
            return response()->json([
                'message' => Lang::get('crud.create'),
                'tagId' => $tag->id
            ], 201);
        } catch (Throwable $exception) {
            report($exception);
            // rollback changes
            DB::rollBack();
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 400);
        }
    }

}
