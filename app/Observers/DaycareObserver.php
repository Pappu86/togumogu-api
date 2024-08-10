<?php

namespace App\Observers;

use App\Jobs\DeepLink\AddDayCareDeepLink;
use App\Jobs\DeepLink\AddDayCareDeepLinkForFB;
use App\Traits\CommonHelpers;
use App\Models\Daycare\Daycare;

class DaycareObserver
{

    /**
     * Handle the Daycare "updating" event.
     *
     * @param Daycare $daycare
     * @return void
     */
    public function updating(Daycare $daycare)
    {
        $exDaycare = Daycare::find($daycare->id);
        $commonHelpers = new CommonHelpers;
        if($commonHelpers->isChangedForDynamicLink($daycare, $exDaycare)){
            // generate dynamic link
            AddDayCareDeepLink::dispatch($daycare);
            AddDayCareDeepLinkForFB::dispatch($daycare);
        }

    }

}
