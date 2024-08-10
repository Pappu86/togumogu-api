<?php

namespace App\Models\Common;

use App\Models\Community\Post;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class Tag
 * @package App\Models\Common
 */
class Hashtag extends Model
{
    use SoftDeletes, LogsActivity, Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status', 'name'
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
     * Each tag belongs to many posts.
     *
     * @return MorphToMany
     */
    public function posts(): MorphToMany
    {
        return $this->morphedByMany(Post::class, 'hashtaggable');
    }

}
