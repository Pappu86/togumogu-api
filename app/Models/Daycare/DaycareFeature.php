<?php

namespace App\Models\Daycare;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use Laravel\Scout\Searchable;

class DaycareFeature extends Model
{
    use HasFactory, Searchable, Translatable;

    /**
     * Set the translated fields.
     *
     * @var array
     */
    public $translatedAttributes = [
        'title'
    ];
    

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status'
    ];
}
