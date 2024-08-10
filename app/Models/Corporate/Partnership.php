<?php

namespace App\Models\Corporate;

use App\Models\Marketing\Coupon;
use App\Models\Reward\Referral;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\hasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Scout\Searchable;
use Spatie\Activitylog\Traits\LogsActivity;
use Astrotomic\Translatable\Translatable;

class Partnership extends Model
{
    use Translatable, LogsActivity, Searchable;

    /**
     * Set the translated fields.
     * @var array
     */
    public $translatedAttributes = [
        'special_note', 'details', 'offer_text', 'offer_instruction',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status', 'company_id', 'discount', 'is_free_shipping',
        'free_shipping_spend', 'coupon_id', 'group_id', 'offer_image',
        'hotline_number', 'offer_code', 'pse', 'togumogu_customer_offer', 
        'start_date', 'expiration_date'
    ];

    /**
     * A partnership has referral.
     *
     * @return BelongsTo
     */
    public function referral(): BelongsTo
    {
        return $this->BelongsTo(Referral::class, 'id', 'partnership_id');
    }
    /**
     * A company has many employee.
     *
     * @return BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->BelongsTo(Company::class, 'company_id');
    }

    /**
     * A coupon info from a partnership.
     *
     * @return BelongsTo
     */
    public function coupon(): BelongsTo
    {
        return $this->BelongsTo(Coupon::class, 'coupon_id');
    }

    /**
     * A employee group has many employee.
     *
     * @return BelongsTo
     */
    public function employeeGroup(): BelongsTo
    {
        return $this->BelongsTo(EmployeeGroup::class, 'group_id');
    }
}
