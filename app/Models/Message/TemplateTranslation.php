<?php

namespace App\Models\Message;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class TemplateTranslation extends Model
{
    use Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'template_id', 'subject', 'content'
    ];

    /**
     * Selected fields to add into activity log.
     *
     * @var array
     */
    protected static $logAttributes = ['subject'];

    /**
     * Set timestamps false.
     * @var bool
     */
    public $timestamps = false;
}
