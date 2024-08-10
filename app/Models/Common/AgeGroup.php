<?php

namespace App\Models\Common;

use App\Models\Community\Post;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Scout\Searchable;
use Astrotomic\Translatable\Translatable;

class AgeGroup extends Model
{
    use Translatable, LogsActivity, Searchable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'age_groups';

    /**
     * Set the translated fields.
     *
     * @var array
     */
    public $translatedAttributes = [
        'name',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', 'status', 'start', 'end'
    ];

}
