<?php

namespace App\Models\Corporate;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Laravel\Scout\Searchable;
use Spatie\Activitylog\Traits\LogsActivity;

class Employee extends Model
{
    use LogsActivity, Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'status', 'join_date', 'designation', 'company_id', 'group_id',
        'company_employee_id', 'email', 'phone', 'is_registered'
    ];

    /**
     * Selected fields to add into activity log.
     *
     * @var array
     */
    protected static $logAttributes = ['status'];

    /**
     * A company has many employee.
     *
     * @return BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * A employee group has many employee.
     *
     * @return BelongsTo
     */
    public function employeeGroup(): BelongsTo
    {
        return $this->belongsTo(EmployeeGroup::class, 'group_id');
    }

    /**
     * A employee group has many employee.
     *
     * @return BelongsTo
     */
    public function partnership(): BelongsTo
    {
        return $this->belongsTo(Partnership::class, 'group_id', 'group_id');
    }
}
