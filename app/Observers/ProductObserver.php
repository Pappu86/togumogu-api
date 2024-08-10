<?php

namespace App\Observers;

use App\Models\Product\Product;
use App\Traits\CommonHelpers;
use App\Jobs\DeepLink\AddProductDeepLink;
use App\Jobs\DeepLink\AddProductDeepLinkForFB;

class ProductObserver
{

    /**
     * Handle the Product "updating" event.
     *
     * @param Product $product
     * @return void
     */
    public function updating(Product $product)
    {
        $exProduct = Product::find($product->id);
        $commonHelpers = new CommonHelpers;
        if($commonHelpers->isChangedForDynamicLink($product, $exProduct)){
            // generate dynamic link
            AddProductDeepLink::dispatch($product);
            // AddProductDeepLinkForFB::dispatch($product);
        }

    }

}
