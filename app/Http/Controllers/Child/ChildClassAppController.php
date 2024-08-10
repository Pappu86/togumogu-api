<?php

namespace App\Http\Controllers\Child;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Models\Child\ChildClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;
use App\Http\Resources\Child\ChildClassResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Throwable;

class ChildClassAppController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse | AnonymousResourceCollection
     */
    public function getClasses(Request $request, $locale): JsonResponse | AnonymousResourceCollection
    {

        App::setLocale($locale);

        try {

            $limit = $request->query('limit') ?: '5';
            $sort = $request->query('short');
            $query = $request->query('query')?:'';

            $class = ChildClass::with('translations')
                ->whereTranslationLike('name', '%' . $query . '%')
                ->where('status', '=', 'active');

            $class = $class->paginate($limit);
            $class->appends([
                'limit' => $limit,
                'sort' => $sort,
            ]);

            return ChildClassResource::collection($class);
        } catch (Throwable $exception) {
            report($exception);
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 404);
        }
    }

}
