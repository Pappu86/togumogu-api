<?php

namespace App\Models\Common;

use App\Models\Blog\Article;
use App\Models\Product\Product;
use App\Models\Video\Video;
use App\Models\Quiz\Questions;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class Tag
 * @package App\Models\Common
 */
class Tag extends Model
{
    use SoftDeletes, Translatable, LogsActivity, Searchable;

    /**
     * Set the translated fields.
     *
     * @var array
     */
    public $translatedAttributes = [
        'name', 'slug',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status'
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
     * Each tag belongs to many products.
     *
     * @return MorphToMany
     */
    public function products(): MorphToMany
    {
        return $this->morphedByMany(Product::class, 'taggable');
    }

    /**
     * Each tag belongs to many articles.
     *
     * @return MorphToMany
     */
    public function articles(): MorphToMany
    {
        return $this->morphedByMany(Article::class, 'taggable');
    }

    /**
     * Each tag belongs to many videos.
     *
     * @return MorphToMany
     */
    public function videos(): MorphToMany
    {
        return $this->morphedByMany(Video::class, 'taggable');
    }

    /**
     * Each tag belongs to many questions.
     *
     * @return MorphToMany
     */
    public function questions(): MorphToMany
    {
        return $this->morphedByMany(Questions::class, 'taggable');
    }
}
