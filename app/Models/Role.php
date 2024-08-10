<?php

namespace App\Models;

use Laravel\Scout\Searchable;
use Spatie\Activitylog\Traits\LogsActivity;

class Role extends \Spatie\Permission\Models\Role
{
    use Searchable, LogsActivity;

    /**
     * Monitor every fields and add to activity log.
     *
     * @var bool
     */
    protected static $logFillable = true;

    /**
     * Each role has many menus.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function menus()
    {
        return $this->belongsToMany(Menu::class);
    }
}
