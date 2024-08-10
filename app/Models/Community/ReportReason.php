<?php

namespace App\Models\Community;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
class ReportReason extends Model
{
    use SoftDeletes, Translatable, Searchable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'report_reasons';

    /**
     * Set the translated fields.
     *
     * @var array
     */
    public $translatedAttributes = [
        'title', 'description', 'slug'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['status'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * A topic has many posts.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }

}
