<?php

namespace App\Models\Brand;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kalnoy\Nestedset\NodeTrait;
use Laravel\Scout\Searchable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Category extends Model implements HasMedia
{
    use SoftDeletes, Translatable, NodeTrait, LogsActivity, InteractsWithMedia, Searchable {
        NodeTrait::usesSoftDelete insteadof Searchable;
    }

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'brand_categories';

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
        'status', 'image', 'meta_image', 'parent_id'
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
     * A category has many brands.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function brands()
    {
        return $this->belongsToMany(Brand::class, 'brand_category');
    }

    /**
     * Media collections for this model.
     *
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('brand_category')
            ->singleFile();

        $this->addMediaCollection('brand_category_meta')
            ->singleFile();
    }
}
