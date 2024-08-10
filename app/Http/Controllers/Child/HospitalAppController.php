<?php

namespace App\Http\Controllers\Child;

use App\Http\Controllers\Controller;
use App\Http\Resources\Child\HospitalResource;
use App\Models\Child\Hospital;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Throwable;

class HospitalAppController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse | AnonymousResourceCollection
     */
    public function getHospitals(Request $request, $locale): JsonResponse | AnonymousResourceCollection
    {

        App::setLocale($locale);

        try {

            $limit = $request->query('limit') ?: '5';
            $sort = $request->query('short');
            $query = $request->query('query')?:'';

            $hospital = Hospital::with('translations')
                ->whereTranslationLike('name', '%' . $query . '%')
                ->where('status', '=', 'active');

            $hospital = $hospital->paginate($limit);
            $hospital->appends([
                'limit' => $limit,
                'sort' => $sort,
            ]);

            return HospitalResource::collection($hospital);
        } catch (Throwable $exception) {
            report($exception);
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 404);
        }
    }

}
