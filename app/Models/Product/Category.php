<?php

namespace App\Models\Product;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kalnoy\Nestedset\NodeTrait;
use Laravel\Scout\Searchable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\InteractsWithMedia;

class Category extends Model implements HasMedia
{
    use SoftDeletes, Translatable, NodeTrait, LogsActivity, InteractsWithMedia, Searchable {
//        Searchable::usesSoftDelete insteadof NodeTrait;
        NodeTrait::usesSoftDelete insteadof Searchable;
    }

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_categories';

    /**
     * Set the translated fields.
     *
     * @var array
     */
    public $translatedAttributes = [
        'name', 'slug', 'description', 'meta_title',
        'meta_description', 'meta_keyword'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status', 'image', 'meta_image', 'parent_id', 'google_category_id', 'google_category_name'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Selected fields to add into activity log.
     *
     * @var array
     */
    protected static $logAttributes = ['status'];

    /**
     * Media collections for this model.
     *
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('product_category')
            ->singleFile();

        $this->addMediaCollection('product_category_meta')
            ->singleFile();
    }

    /**
     * A category has many products.
     *
     * @return BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_category_product');
    }
}
