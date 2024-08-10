<?php

namespace App\Http\Controllers\Child;

use App\Http\Controllers\Controller;
use App\Http\Resources\Child\SchoolResource;
use App\Models\Child\School;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Throwable;

class SchoolAppController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse | AnonymousResourceCollection
     */
    public function getSchools(Request $request, $locale): JsonResponse | AnonymousResourceCollection
    {

        App::setLocale($locale);

        try {

            $limit = $request->query('limit') ?: '5';
            $sort = $request->query('short');
            $query = $request->query('query')?:'';

            $school = School::with('translations')
                ->whereTranslationLike('name', '%' . $query . '%')
                ->where('status', '=', 'active');

            $school = $school->paginate($limit);
            $school->appends([
                'limit' => $limit,
                'sort' => $sort,
            ]);

            return SchoolResource::collection($school);
        } catch (Throwable $exception) {
            report($exception);
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 404);
        }
    }

}
