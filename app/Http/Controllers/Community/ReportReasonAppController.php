<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Http\Resources\Community\ReportReasonEditResource;
use App\Models\Community\ReportReason;
use App\Models\Community\ReportReasonTranslation;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Throwable;

class ReportReasonAppController extends Controller
{

        /**
     * @param $locale
     * @return AnonymousResourceCollection
     */
    public function getAll($locale): AnonymousResourceCollection
    {
        App::setLocale($locale);

        $reportReasons = ReportReason::with('translations')
            ->where('status', '=', 'active')
            ->get();

        return ReportReasonEditResource::collection($reportReasons);
    }

}
