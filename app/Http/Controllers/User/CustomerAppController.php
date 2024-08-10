<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Resources\User\CustomerAuthAppResource;
use App\Http\Resources\User\CustomerAuthResource;
use App\Models\Product\Product;
use App\Http\Resources\Product\ProductSingleApiResource;
use App\Http\Resources\User\CustomerSettingResource;
use App\Jobs\DeepLink\AddReferralDeepLink;
use App\Models\User\Customer;
use App\Models\User\CustomerDevice;
use App\Models\Corporate\Company;
use App\Rules\Mobile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\ValidationException;
use App\Mail\Subscription;
use App\Models\Order\Order;
use App\Models\Reward\Referral;
use App\Models\Reward\RewardTransaction;
use App\Models\User\CustomerSetting;
use App\Models\User\MessageCode;
use App\Traits\CommonHelpers;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class CustomerAppController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
            'device_name' => 'required'
        ]);

        if (filter_var($request->get('username'), FILTER_VALIDATE_EMAIL)) {
            $field = 'email address';
            $customer = Customer::query()->where('email', '=', $request->get('username'))->first();
        } else {
            $field = 'mobile number';
            $customer = Customer::query()->where('mobile', '=', $request->get('username'))->first();
        }

        //When customer is inactive
        if($customer?->status === 'inactive') {
            return response()->json(['message' => Lang::get('auth.inactive_customer_account')], 422);
        }

        // check if customer exist
        if (!$customer) {
            throw ValidationException::withMessages([
                'username' => ["We can't find a user with that {$field}."],
            ]);
        }
       
        if($customer?->status === 'inactive') {
            return response()->json(['message' => Lang::get('auth.inactive_customer_account')], 422);
        }

        // check customer password
        if (!Hash::check($request->get('password'), $customer->password)) {
            throw ValidationException::withMessages([
                'password' => ["The provided password is incorrect."],
            ]);
        }

        return response()->json([
            'token' => $customer->createToken($request->get('device_name'))->plainTextToken,
            'user' => new CustomerAuthAppResource($customer)
        ]);
    }

   /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $customer = Auth::user();
        $request->validate([
            'password' => 'required|min:4|numeric|confirmed',
            'password_confirmation' => 'required|min:4|numeric',
        ]);

        // begin database transaction
        DB::beginTransaction();
        try {

            $customer = DB::table('customers')
                ->where('id', '=', $customer->id)
                ->update([
                    'password' => Hash::make($request->get('password'))
                ]);

            // commit changes
            DB::commit();

            return response()->json([
                'message' => Lang::get('auth.pin_update')
            ], 200);
           
        } catch (Throwable $exception) {
            report($exception);
            // rollback changes
            DB::rollBack();
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 400);
        }
    }

    /**
     * Social login.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function socialLogin(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'nullable|email',
            'mobile' => 'nullable',
            'provider' => 'required',
            'provider_id' => 'required',
            'device_name' => 'required'
        ]);

        // find provider
        $provider = DB::table('customer_social_providers')
            ->where('provider_id', '=', $request->get('provider_id'))
            ->where('provider', '=', $request->get('provider'))
            ->first();

        // get customer
        $customer = null;
        if (isset($provider?->customer_id)) {
            $customer = Customer::find($provider?->customer_id);
        }
        
        if ($request->filled('email')) {
            $customer = Customer::where('email', '=', $request->get('email'))->first();
        }
        
        if ($request->filled('mobile')) {
            $customer = Customer::where('mobile', '=', $request->get('mobile'))->first();
        }

       
        //When customer is inactive
        if($customer?->status === 'inactive') {
            return response()->json(['message' => Lang::get('auth.inactive_customer_account')], 422);
        }

        if ($customer) {
            Auth::login($customer);

            return response()->json([
                'token' => $customer->createToken($request->get('device_name'))->plainTextToken,
                'user' => new CustomerAuthAppResource($customer)
            ]);

        } else {
            // begin database transaction
            DB::beginTransaction();
            try {

                $customer = Customer::create($request->all());
                $customer->providers()->create($request->all());

                // commit changes
                DB::commit();

                Auth::login($customer);

                return response()->json([
                    'token' => $customer->createToken($request->get('device_name'))->plainTextToken,
                    'user' => new CustomerAuthAppResource($customer)
                ]);
            } catch (Throwable $exception) {
                report($exception);
                // rollback changes
                DB::rollBack();
                return response()->json([
                    'message' => Lang::get('crud.error'),
                    'error' => $exception->getMessage()
                ], 400);
            }
        }

    }

    /**
     * Check exist provider.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkExistSocialProvider(Request $request): JsonResponse
    {
        $request->validate([
            'provider' => 'required',
            'provider_id' => 'required',
        ]);

        // find provider
        $provider = DB::table('customer_social_providers')
            ->where('provider_id', '=', $request->get('provider_id'))
            ->where('provider', '=', $request->get('provider'))
            ->first();
    
        return response()->json([
            'isExist' => !!$provider,
        ]);

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getOrderList(Request $request): JsonResponse
    {
        try {
            $limit = (int)$request->query('limit', 10);
            $customer_id = Auth::id();
            $columns = [
                'id',
                'order_no as orderNo',
                'invoice_no as invoiceNo',
                'total_amount as total',
                'total_quantity as quantity',
                'payment_method as paymentMethod',
                'payment_status as paymentStatus',
                'order_status as orderStatus',
                'created_at as createdAt',
            ];
            $orders = DB::table('orders')
                ->select($columns)
                ->whereNull('deleted_at')
                ->where('customer_id', '=', $customer_id)
                ->latest()
                ->limit($limit)
                ->get();

            return response()->json($orders);
        } catch (Throwable $exception) {
            report($exception);
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 404);
        }
    }

    /**
     * @param Order $order
     * @return JsonResponse
     */
    public function getOrder(Order $order): JsonResponse
    {
        try {

            $order_id = $order?->id;
            $customer_id = Auth::id();
            $columns = [
                'id',
                'order_no as orderNo',
                'invoice_no as invoiceNo',
                'total_amount as total',
                'special_discount as specialDiscount',
                'coupon_discount as couponDiscount',
                'shipping_cost as shippingCost',
                'total_quantity as quantity',
                'static_address as staticAddress',
                'payment_method as paymentMethod',
                'payment_status as paymentStatus',
                'order_status as orderStatus',
                'created_at as createdAt',
            ];
            $order = DB::table('orders')
                ->select($columns)
                ->whereNull('deleted_at')
                ->where('customer_id', '=', $customer_id)
                ->where('id', '=', $order_id)
                ->first();
            if ($order) {
                $subtotal = 0;

                $products = DB::table('order_products')
                    ->where('order_id', '=', $order_id)
                    ->get()
                    ->map(function ($item) use (&$subtotal, &$total) {
                        // calculate subtotal
                        $subtotal += $item->selling_price * $item->quantity;

                        $product = Product::find($item->product_id);
                        $item->total = $item->selling_price * $item->quantity;
                        $item->product = $product ? new ProductSingleApiResource($product) : null;
                        return $item;
                    });
                $order->subtotal = round($subtotal, 2);
                $order->items = $products;
                $order->staticAddress = json_decode($order->staticAddress);
            }

            return response()->json($order);
        } catch (Throwable $exception) {
            report($exception);
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 404);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function storeParentQuestion(Request $request): JsonResponse
    {
        $customer = Auth::user();
        $dateFormat = 'Y-m-d H:i:s';
        $now = Carbon::now();

        // validate request
        $request->validate([
            'parent_type' => 'required'
        ]);

        $parent_id = $customer->id;
        $date_of_birth = $request->get('date_of_birth');
        $expecting_date = $request->get('expecting_date');

         //Compare DOB with current date of child
         if(isset($date_of_birth) && $now->lte(Carbon::parse($date_of_birth))) {
            return response()->json([ 'message' => Lang::get('customer.dob_validation') ], 400);
        };

        //Compare EDD with current date of child
        if(isset($expecting_date) && $now->gt(Carbon::parse($expecting_date))) {
            return response()->json([ 'message' => Lang::get('customer.edd_validation') ], 400);
        };

        // Date formating
        if($date_of_birth) {
            $date_of_birth = Carbon::parse($date_of_birth)->format($dateFormat);
        };
        
        if($expecting_date) {
            $expecting_date = Carbon::parse($expecting_date)->format($dateFormat);
        };

        // begin database transaction
        DB::beginTransaction();
        try {
            
            // Update customer table for parent type
            DB::table('customers')
                ->where('id', $parent_id)
                ->update([
                    'parent_type' => $request->get('parent_type'),
                ]);

            // Insert document in customer/parent children table
            if($request->get('parent_type') !== 'other') {
                $customer->children()->create([
                    'name'=> $request->get('name')?:null,
                    'date_of_birth'=> $date_of_birth?:null,
                    'expecting_date'=> $expecting_date?:null,
                    'gender'=> $request->get('gender')?:null,
                    'parent_status' => $expecting_date?'expecting':'parent',
                    'is_default' => $customer->children()->count()?0:1,
                ]);
            }

            // commit database
            DB::commit();
            // return success message
            $customer = Customer::where('id', '=', $parent_id)->first();
            return response()->json([
                'message' => Lang::get('crud.create'),
                'user' => new CustomerAuthResource($customer)
            ], 201);
            
        } catch (\Exception $exception) {
            // log exception
            report($exception);
            // rollback database
            DB::rollBack();
            // return failed message
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 400);
        }
    }


    /**
     * @param Request $request
     * @param Customer $customer
     * @return JsonResponse
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $customer = Auth::user();

        $request->validate([
            'name' => 'nullable',
            'email' => 'nullable|email|unique:customers,email,' . $customer->id,
            'mobile' => ['nullable', new Mobile(), 'unique:customers,mobile,' . $customer->id],
            'shipping_email' => 'nullable|email',
        ]);
        
        // Check email is empty
        $email = $request->get('email');
        if($request->has(['email']) && ($email === '' || $email === null)) {
            return response()->json([ 'message' => 'Email is empty!'], 411);
        }

        // Check mobile is empty
        $mobile = $request->get('mobile');
        if($request->has(['mobile']) && ($mobile === '' || $mobile === null)) {
            return response()->json([ 'message' => 'Mobile is empty!'], 411);
        }

        DB::beginTransaction();
        try {

            $company_name = $request->get('company');
            if (isset($company_name)) {
                $company_info = Company::query()
                ->where('id', $company_name)
                ->orWhereTranslation('name', $company_name)
                ->first();
                $company_id = $company_info?->id ?: null;

                if (!$company_id) {
                    $new_company = Company::create([
                        'status' => 'active',
                        'name' => $company_name
                    ]);
                    $company_id = $new_company->id;
                }

                $request->merge(['company_id' => $company_id]);
            }

            // Update customer info
            $customer->update($request->all());
            $user = new CustomerAuthResource(Customer::find(Auth::id()));
            
            DB::commit();
          
            // Added Reward points for profile updating
            $commonHelpers = new CommonHelpers;
            $progressPoint = $commonHelpers->getProfileProgress($customer);
            $rewardPoints = 0;

            if($progressPoint && $progressPoint >= 80) {
               $existReward = RewardTransaction::where('customer_id', $customer->id)
                    ->where('category', 'customer')
                    ->where('action', 'profile_updated')
                    ->where('reference_id', $customer->id)
                    ->first();

                if(!isset($existReward)) {
                    $rewardPoints = $commonHelpers->addProfileUpdateRewardPoints($customer);
                }
            }
            
            // return success message
            return response()->json([
                'message' => Lang::get('crud.update'),
                'user' => $user,
                'rewardPoints' => $rewardPoints
            ]);
        } catch (Throwable $exception) {
            // log exception
            report($exception);
            // rollback database
            DB::rollBack();
            // return failed message
            return response()->json([
                'error' => $exception->getMessage(),
                'message' => Lang::get('crud.error')
            ], 400);
        }
    }

    /**
     * Upload customer avatar.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException|\Exception
     */
    public function updateAvatar(Request $request)
    {
        $customer = Auth::user();

        $this->validate($request, [
            'avatar' => 'required|image'
        ]);
        // begin database transaction
        DB::beginTransaction();
        try {
            $image = $request->file('avatar');
            $url = $customer->addMedia($image)->toMediaCollection('avatar')->getFullUrl();
            // update customer
            $customer->update([
                'avatar' => $url
            ]);
            // commit changes
            DB::commit();

            // Added Reward points for profile updating
            $commonHelpers = new CommonHelpers;
            $progressPoint = $commonHelpers->getProfileProgress($customer);
            $rewardPoints = 0;

            if($progressPoint && $progressPoint >= 80) {
                $existReward = RewardTransaction::where('customer_id', $customer->id)
                    ->where('category', 'customer')
                    ->where('action', 'profile_updated')
                    ->where('reference_id', $customer->id)
                    ->first();

                if(!isset($existReward)) {
                    $rewardPoints = $commonHelpers->addProfileUpdateRewardPoints($customer);
                }
            }
            
            // return success message
            return response()->json([
                'message' => Lang::get('crud.update'),
                'id' => $customer->id,
                'avatar' =>  CustomerAuthResource::make($customer)->avatar,
                'rewardPoints' => $rewardPoints
            ], 200);
        } catch (\Exception $exception) {
            // log exception
            report($exception);
            // rollback changes
            DB::rollBack();
            // return failed message
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        if ($request->has('email')) {
            $request->validate([
                'email' => 'email'
            ]);

            $status = Password::broker('customers')
                ->sendResetLink($request->only('email'));

            return $status === Password::RESET_LINK_SENT
                ? response()->json([
                    'message' => __($status)
                ])
                : response()->json([
                    'message' => __($status)
                ], 404);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function resetPasswordByEmail(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:4|numeric|confirmed',
        ]);

        $status = Password::broker('customers')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) use ($request) {
                $user->forceFill([
                    'password' => $password
                ])->save();

                $user->setRememberToken(Str::random(60));

                event(new PasswordReset($user));
            }
        );

        return $status == Password::PASSWORD_RESET
            ? response()->json([
                'message' => __($status)
            ])
            : response()->json([
                'message' => __($status)
            ], 404);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'code' => 'required|min:4',
            'mobile' => 'filled',
            'password' => 'required|min:4|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/'
        ]);
        // begin database transaction
        DB::beginTransaction();
        try {
            // check if verification code is valid
            $code = $request->get('code');
            $message_code = MessageCode::where('mobile', '=', $request->get('mobile'))
                ->where('code', '=', $code)->first();
            if (empty($message_code)) {
                return response()->json([
                    'message' => Lang::get('auth.code_expired')
                ], 401);
            }
            $created_at = $message_code->created_at->addMinutes(2.5);
            if ($created_at < now()) {
                return response()->json([
                    'message' => Lang::get('auth.code_expired')
                ], 401);
            }

            $customer = $this->findCustomerByMobile($request->get('mobile'));

            if ($customer) {
                $customer->update([
                    'password' => $request->get('password')
                ]);

                // commit changes
                DB::commit();

                // send success message
                //SendResetPasswordMessage::dispatch($customer);

                return response()->json([
                    'message' => Lang::get('auth.password_reset_success')
                ], 200);
            } else {
                // rollback changes
                DB::rollBack();
                throw ValidationException::withMessages([
                    'username' => [Lang::get('auth.is_mobile_available')],
                ]);
            }
        } catch (Throwable $exception) {
            report($exception);
            // rollback changes
            DB::rollBack();
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function changePassword(Request $request): JsonResponse
    {

        $customer = Auth::user();
        $request->validate([
            'password_current' => 'required',
            'password' => 'required|min:4|numeric|confirmed',
            'password_confirmation' => 'required|min:4|numeric',
        ]);
 
        //Check current password 
        if (!Hash::check($request->get('password_current'), $customer->password)) {
            throw ValidationException::withMessages([
                'password_current' => ['The current password is not correct!']
            ]);
        }

        // begin database transaction
        DB::beginTransaction();
        try {

            $customer = DB::table('customers')
                ->where('id', '=', $customer->id)
                ->update([
                    'password' => Hash::make($request->get('password'))
                ]);

            // commit changes
            DB::commit();

            return response()->json([
                'message' => Lang::get('auth.pin_update')
            ], 200);
           
        } catch (Throwable $exception) {
            report($exception);
            // rollback changes
            DB::rollBack();
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 400);
        }
    }

    /**
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        Auth::user()->tokens()->delete();

        return response()->json([
            'message' => 'Successfully Logged Out'
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function createDevice(Request $request): JsonResponse
    {
        $customer_id = Auth::id();

        $request->validate([
            'token' => 'required',
            'platform' => 'required',
            'uuid' => 'required',
        ]);

        $uuid = $request->get('uuid');

        //Check unique device token
        $isExistToken = CustomerDevice::where('token', '=', $request->get('token'))->first();

        if(isset($isExistToken) && $isExistToken?->id) {
            return response()->json([ 
                'message' => Lang::get('customer.already_taken')
             ], 201);
        }

        DB::beginTransaction();
        try {

            $request->merge(['customer_id' => $customer_id]);
            $device = CustomerDevice::where('customer_id', '=', $customer_id)
                    ->where('uuid', '=', $uuid)
                    ->first();
            
            $message = Lang::get('crud.create');
            if(isset($device)) {
                // Update customer device. If already added this device.
                $device = $device->update(['token' => $request->get('token')]);
                $message = Lang::get('crud.update');
            } else {
                // Create customer device
                $device = CustomerDevice::create($request->all());
            }

            DB::commit();
            // return success message
            return response()->json([
                'message' => $message,
            ]);
        } catch (Throwable $exception) {
            // log exception
            report($exception);
            // rollback database
            DB::rollBack();
            // return failed message
            return response()->json([
                'error' => $exception->getMessage(),
                'message' => Lang::get('crud.error')
            ], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getDevices(Request $request): JsonResponse
    {

        $platfrom = $request->get('platform');
        $customer_id = $request->get('customer_id');

        $devices = CustomerDevice::latest();

        if(isset($platfrom)) {
            $devices = $devices->where('platform', '=', $platfrom);
        }

        if(isset($customer_id)) {
            $devices = $devices->where('customer_id', '=', $customer_id);
        }

        $devices = $devices->get();

        return response()->json([
            'data' => $devices
        ]);
    }

     /**
     * @param Request $request
     * @return JsonResponse
     */
    public function storeSubscription(Request $request): JsonResponse
    {
        $customer = Auth::user();

        $request->validate([
            'name' => 'required',
            'mobile' => 'required',
            'child_dob' => 'required',
            'frequent_want' => 'required',
            'price' => 'required',
            'address' => 'required',
        ]);

        // begin database transaction
        DB::beginTransaction();
        try {
          
            //Prepare subscription data for email
            $type = $request->get('type');
            $email = $request->get('email');
            $child_dob = Carbon::parse($request->get('child_dob'))->toDateString();;

            $body = array();
            $body['type'] = $type;
            $body['name'] = $request->get('name');
            $body['mobile'] = $request->get('mobile');
            $body['email'] = $email;
            $body['child_name'] = $request->get('child_name');
            $body['child_dob'] = $child_dob;
            $body['frequent_want'] = $request->get('frequent_want');
            $body['price_range'] = $request->get('price');
            $body['pieces'] = $request->get('pieces');
            $body['brand'] = $request->get('brand');
            $body['size'] = $request->get('size');
            $body['nanny_service'] = $request->get('nanny_service');
            $body['address'] = $request->get('address');
            $body['is_customer_mail'] = false;

            if($type === 'book') {
                $body['subject'] = "ToguMogu Book Box Subscription";
                $body['support_mobile'] = '01402134677';
            } else if ($type === 'toy') {
                $body['subject'] = "ToguMogu Toy Box Subscription";
                $body['support_mobile'] = '01944665577';
            } else if ($type === 'nanny') {
                $body['subject'] = "Nanny Service Subscription";
                $body['support_mobile'] = '01558637840';
            } else {
                $body['subject'] = "ToguMogu Diaper Subscription";
                $body['support_mobile'] = '01944665577';
            }

            //Now sending to togumogu team
            Mail::to(config('helper.mail_to_subscription'))
            ->send(new Subscription($body));

            //Now sending to togumogu customer 
            if(isset($email)) {
                $body['subject'] = "Service Registration Successful";
                $body['is_customer_mail'] = true;
                Mail::to($email)->send(new Subscription($body));
            }

            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.sent'),
            ]);

        } catch (Throwable $exception) {
            // log exception
            report($exception);
            // rollback database
            DB::rollBack();
            // return failed message
            return response()->json([
                'message' => Lang::get('crud.error'),
                'error' => $exception->getMessage(),
            ], 400);
        }
    }

    /**
     * @return JsonResponse
     */
    public function generateDynamicLinks(): JsonResponse
    {
 
        $customer = Auth::user();
        $referral = Referral::where('customer_id', $customer->id)
                        ->where('type', 'customer');

        //Check already generated referral link of this customer
        if($referral->count()){
            return response()->json([ 'message' => Lang::get('customer.referral_has_link')], 422);
        }

        if(!$referral?->count()){
            $customer['referral_type'] = 'customer';
            AddReferralDeepLink::dispatch($customer);
        }

        return response()->json([
                'message' => Lang::get('customer.referral_link'),
            ], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updateSetting(Request $request): JsonResponse
    {
 
        $customer_id = Auth::id();
        $value = $request->get('value');
        $key = $request->get('key');
        $rq_customer_id = $request->get('customer_id');

        if($rq_customer_id !== $customer_id ) {
            return response()->json([ 'message' => Lang::get('auth.not_allow') ], 401);
        }

        // begin database transaction
        DB::beginTransaction();
        try {
            
            unset($request->is_enabled);
            $setting = CustomerSetting::where('customer_id', '=', $customer_id)
                        ->where('key', '=', $key)
                        ->first();

            if(isset($setting)){
                $setting->update(['value' => $value]);
            } else {
                $setting = CustomerSetting::create($request->all());
            }

            // commit changes
            DB::commit();

            return response()->json([
                'data' => new CustomerSettingResource($setting),
                'message' => Lang::get('crud.update')
            ], 200);
           
        } catch (Throwable $exception) {
            report($exception);
            // rollback changes
            DB::rollBack();
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 400);
        }
    }

}
