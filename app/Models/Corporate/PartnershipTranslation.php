<?php

namespace App\Models\Corporate;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class PartnershipTranslation extends Model
{
    use Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'partnership_id', 'special_note', 'details',
        'offer_text', 'offer_instruction',
    ];

    /**
     * Selected fields to add into activity log.
     *
     * @var array
     */
    protected static $logAttributes = ['name'];

    /**
     * Set timestamps false.
     * @var bool
     */
    public $timestamps = false;
}
