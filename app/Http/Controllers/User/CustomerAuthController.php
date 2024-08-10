<?php

namespace App\Http\Controllers\User;

use App\Models\User\Customer;
use App\Models\User\MessageCode;
use App\Models\Corporate\Employee;
use App\Http\Controllers\Controller;
use App\Http\Resources\User\CustomerAuthResource;
use App\Jobs\Customer\AddCorporateEmployee;
use App\Jobs\Customer\AddCustomerReward;
use App\Jobs\SendVerificationCode;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use App\Rules\Mobile;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use App\Mail\Code;
use App\Models\Reward\Referral;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;
use Throwable;

class CustomerAuthController extends Controller
{
    
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function sendMessageCode(Request $request): JsonResponse
    {
        DB::beginTransaction();
        $send_to = $request->get('sendTo');
        $type = $request->get('type');

        $request->validate([
            'email' => [$send_to === 'email'?'required':'nullable', 'email'],
            'mobile' => [$send_to === 'mobile'?'required':'nullable', new Mobile()],
        ]);

        try {
            $email = $request->input('email');
            $mobile = $request->input('mobile');

            if ($request->has('type') && $type === 'reset_password') {
                //Check has existing account by the mobile
                if($send_to === 'mobile') {
                    $customer = $this->findCustomerByMobile($mobile);
                    if (empty($customer) || $customer === null) {
                        return response()->json([ 'message' => Lang::get('auth.mobile_has_no_account', ['mobile' => $mobile])], 400);
                    }
                }

                //Check has existing account by the email
                if($send_to === 'email') {
                    $customer = $this->findCustomerByEmail($email);
                    if (empty($customer) || $customer === null) {
                        return response()->json([ 'message' => Lang::get('auth.email_has_no_account', ['email' => $email])], 400);
                    }
                }
            }

            if ($request->has('type') && $type === 'new_registration') {

                //Check existing mobile for registration token
                if($send_to === 'mobile') {
                    // $customer = $this->findCustomerByMobile($mobile);  
                    $customer = Customer::query()->where('mobile', '=', $mobile)->whereNotNull('password')->first();
                   
                    //When customer is inactive
                    if($customer?->status === 'inactive') {
                        return response()->json(['message' => Lang::get('auth.inactive_customer_account')], 422);
                    }

                    if (isset($customer)) {
                        return response()->json([ 'message' => Lang::get('customer.mobile_has_taken')], 400);
                    }
                }

                //Check existing email for registration token
                if($send_to === 'email') {
                     // $customer = $this->findCustomerByEmail($email);
                    $customer = Customer::query()->where('email', '=', $email)->whereNotNull('password')->first(); 
                              
                    //When customer is inactive
                    if($customer?->status === 'inactive') {
                        return response()->json(['message' => Lang::get('auth.inactive_customer_account')], 422);
                    }
                        
                    if (isset($customer)) {
                        return response()->json([ 'message' => Lang::get('customer.email_has_taken')], 400);
                    }
                }
    
            }
            
            // create code
            $code = MessageCode::create([
                'mobile' => $mobile?:null,
                'email' => $email?:null,
                'created_at' => now(),
                'type' => $type,
                'code' => mt_rand(1000, 9999),
            ]);

            DB::commit();
            
            // send code to sms/email
            $success_message = Lang::get('auth.check_mobile');
            if($send_to === 'email' && isset($email)) {
                $success_message = Lang::get('auth.check_email');
                $body['code'] = $code->code;
                $body['subject'] = $code->code ." is your ToguMogu OTP";
                Mail::to($email)->send(new Code($body));
            } else if($send_to === 'mobile') {
                SendVerificationCode::dispatch($code);
            }

            return response()->json([
                'message' => $success_message
            ]);
        } catch (Throwable $exception) {
            report($exception);
            DB::rollBack();

            return response()->json([
                'message' => Lang::get('crud.error'),
                'error' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * Register customer.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkMessageCode(Request $request): JsonResponse
    {

        $type = $request->get('type');
        $mobile = $request->get('mobile');
        $email = $request->get('email');
        $device_name = $request->get('device_name');

        $validate = [
            'code' => 'required',
            'email' => 'nullable|email',
            'mobile' => ['nullable', new Mobile()],
        ];
       
        $mobile_customer = $this->findCustomerByMobile($mobile);
        $email_customer = $this->findCustomerByEmail($email);
        
        if(isset($type) && $type === 'new_registration' && ($mobile_customer?->password || $email_customer?->pasword)) {
            $validate['email'] = 'nullable|email|unique:customers';
            $validate['mobile'] = ['nullable', new Mobile(),'unique:customers'];
        }

        $data = $request->validate($validate);

        //Mobile and email validation check
        if (!$mobile && !$email) {
            return response()->json(['message' => Lang::get('auth.mobile_email_required')], 422);
        }

        // begin database transaction
        DB::beginTransaction();
        try {
          
            // check if verification code is valid
            $code = $request->get('code');
            $message_code = MessageCode::latest()
                ->where('mobile', '=', $mobile)
                ->orWhere('email', '=', $email)
                ->get()
                ->filter(function ($message) use ($code, $type) { 
                    if( $message->code === $code && $message->type === $type) return $message;
                })->first();

            if (empty($message_code)) {
                return response()->json([
                    'message' => Lang::get('auth.code_invalid'),
                    'valid' => false,
                ], 422);
            }

            $created_at = $message_code->created_at->addMinutes(2);
            if ($created_at < now()) {
                return response()->json([
                    'message' => Lang::get('auth.code_expired'),
                    'valid' => false,
                ], 422);
            }
     
            // Return response data list
            $response = [ 'valid' => true, 'message' => Lang::get('auth.code_valid') ];

            if(isset($type) && $type ==='new_registration') {
              $response_reg = $this->registration($request);
              $response = $response_reg->original;
              $response['valid'] = true;
            }

            $message_code->forceDelete();
           // commit changes
            DB::commit();

            return response()->json($response);
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
     * Register customer.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function registration(Request $request): JsonResponse
    {
        $mobile = $request->get('mobile');
        $email = $request->get('email');
        $device_name = $request->get('device_name');
        $is_update = false;
        $data['email'] = $email;
        $data['mobile'] = $mobile;

        $mobile_customer = $this->findCustomerByMobile($mobile);
        $email_customer = $this->findCustomerByEmail($email);
        
        if($mobile_customer?->password || $email_customer?->password){
            $data = $request->validate([
                'email' => 'nullable|email|unique:customers',
                'mobile' => ['nullable', new Mobile(),'unique:customers'],
            ]);
        } else if(($mobile_customer && !$mobile_customer?->password) || ($email_customer && !$email_customer?->password)) {
            $is_update = true;
        }

        //Mobile and email validation check
        if (!$mobile && !$email) {
            return response()->json(['message' => Lang::get('auth.mobile_email_required')], 422);
        }

        // begin database transaction
        DB::beginTransaction();
        try {
          
            $employee = null;
            if(isset($email)) {
                $data['email_verified_at'] = now();
                $data['name'] = $email;
                $employee = Employee::where('email', '=', $email)->first();
            }

            if(isset($mobile)) {
                $data['mobile_verified_at'] = now();
                $data['name'] = $mobile;
                $employee = Employee::where('phone', '=', $mobile)->first();
            }
            
            if(isset($employee)) {
                $employee_id = $employee->id;
                $data['employee_id'] = $employee_id;
                $data['company_id'] = $employee->company_id;
                Employee::where('id', $employee_id)
                    ->where('status', 'active')
                    ->update(['is_registered'=> '1']);
            }

            // When create by otp verified code then the customer is active
            $data['status'] = 'active';

            //New customer create and login 
            if($is_update) {
                if(isset($mobile)) {
                    $customer =  Customer::where('mobile', '=', $mobile)->whereNull('password')->first();
                }

                if(isset($email)) {
                    $customer =  Customer::where('email', '=', $email)->whereNull('password')->first();
                }
            } else {
                $customer = Customer::create($data);
            }
            
            Auth::guard('customer')->login($customer);

            //Start Create new employee by Referral url 
            $referral_uid = $request->get('referral_uid');
            $referral_type = $request->get('referral_type');

            if(isset($referral_uid) && isset($referral_type)) {
                $link_data = [
                    'uid' => $referral_uid,
                    'type' => $referral_type
                ];

                $referral = Referral::with('partnership')
                    ->where('uid', $referral_uid)
                    ->where('type', $referral_type)
                    ->first();

                if(isset($referral) && $referral?->id) {
                    //Adding customer to corporate employee by new registration referral url
                    if($referral_type === 'partnership' && !isset($employee)) {
                        AddCorporateEmployee::dispatch($customer, $referral, $link_data);
                    }

                    //Adding customer reward by new registration referral url
                    if($customer?->id) {
                        Log::info('Generated referral link from here');
                        $link_data['partnership_id'] = $referral?->partnership?->id?:"";
                        $link_data['reference_id'] = $referral_type === 'partnership'?$link_data['partnership_id']:$referral?->customer_id;
                        AddCustomerReward::dispatch($customer, $link_data);
                    }
                }
            }
            //End Create new employee by Referral url 

            $response = [
                'message' => 'Successfully Registered',
                'expires' => config('session.lifetime') / (60 * 24),
                'user' => new CustomerAuthResource($customer)
            ];

            if(isset($device_name) && $device_name !=='web') {
                $token = $customer->createToken($device_name)->plainTextToken;
                $response['token'] = $token;
            }
            
           // commit changes
            DB::commit();

            return response()->json($response);
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
     * Login a customer with email and get token
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function login(Request $request): JsonResponse
    {

        $mobile = $request->get('mobile');
        $email = $request->get('email');

        $request->validate([
            'mobile' => !$email?'required':'nullable',
            'email' => !$mobile?'required|email':'nullable|email',
            'password' => 'required|min:4'
        ]);

        if($mobile) {
            $credentials = $request->only('mobile', 'password');
        } else if($email) {
            $credentials = $request->only('email', 'password');
        } 

        if (Auth::guard('customer')->attempt($credentials)) {

            $customer = Auth::guard('customer')->user();
            
            if($customer?->status === 'inactive') {
                return response()->json(['message' => Lang::get('auth.inactive_customer_account')], 422);
            }

            return response()->json([
                'message' => 'Successfully Logged In',
                'user' => new CustomerAuthResource($customer),
                'expires' => config('session.lifetime') / (60 * 24)
            ]);
            
        }
        throw ValidationException::withMessages([
            'email' => ['The provided credentials do not match our records.'],
        ]);
    }

    /**
     * Social login.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function socialLogin(Request $request): JsonResponse
    {
        $email = $request->get('email');
        $mobile = $request->get('mobile');

        $request->validate([
            'email' => 'nullable|email',
            'mobile' => 'nullable',
            'provider' => 'required',
            'provider_id' => 'required'
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
            $customer = Customer::where('email', '=', $email)->first();
        }
        if ($request->filled('mobile')) {
            $customer = Customer::where('mobile', '=', $mobile)->first();
        }

        //When customer is inactive
        if($customer?->status === 'inactive') {
            return response()->json(['message' => Lang::get('auth.inactive_customer_account')], 422);
        }

        if ($customer) {
            Auth::guard('customer')->login($customer);

            return response()->json([
                'message' => 'Successfully Logged In',
                'user' => new CustomerAuthResource(Auth::guard('customer')->user()),
                'expires' => config('session.lifetime') / (60 * 24)
            ]);
        } else {
            // begin database transaction
            DB::beginTransaction();
            try {

                $customer = Customer::create($request->all());

                $customer->providers()->create($request->all());
                // commit changes
                DB::commit();

                Auth::guard('customer')->login($customer);

                return response()->json([
                    'message' => 'Successfully Registered',
                    'user' => new CustomerAuthResource(Auth::guard('customer')->user()),
                    'expires' => config('session.lifetime') / (60 * 24)
                ]);
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

    /**
     * Get auth user response.
     *
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        return response()->json([
            'user' => new CustomerAuthResource(Auth::user()),
        ]);
    }

    /**
     * Logout user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        Auth::guard('customer')->logout();
        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Successfully Logged Out'
        ]);
    }

    /**
     * Generate token for auth user.
     *
     * @param Request $request
     * @return mixed
     * @throws ValidationException
     */
    public function token(Request $request): mixed
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required'
        ]);

        $user = Customer::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return $user->createToken($request->device_name)->plainTextToken;
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
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'nullable|email',
            'mobile' => ['nullable', new Mobile()],
            'password' => 'required|min:4|numeric|confirmed',
            'password_confirmation' => 'required|min:4|numeric',
        ]);

        $mobile = $request->get('mobile');
        $email = $request->get('email');
        $password = $request->get('password');

        //Mobile and email validation check
        if (!$mobile && !$email) {
            return response()->json(['message' => Lang::get('auth.mobile_email_required')], 422);
        }

        // begin database transaction
        DB::beginTransaction();
        try {
            $customer = null;

            if(isset($mobile)){
                $customer = $this->findCustomerByMobile($mobile);
            }

            if(isset($email)){
                $customer = $this->findCustomerByEmail($email);
            }

            if ($customer) {
                $customer->update([ 'password' => $password]);

                // commit changes
                DB::commit();

                // send success message
                //SendResetPasswordMessage::dispatch($customer);

                return response()->json([
                    'message' => Lang::get('auth.password_reset_success')
                ], 200);
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
    public function checkIsEmailMobileExist(Request $request): JsonResponse
    {
        $field = $request->query('username') ?? $request->query('email') ?? $request->query('mobile');
        $queries = collect([]);
        foreach ($request->query() as $key => $value) {
            $queries->push($key);
        }
        $name = $queries[0];
        if (Customer::query()->where($name, '=', $field)->exists()) {
            return response()->json([
                'message' => "The {$name} has already been taken.",
                'valid' => false
            ]);
        } else {
            return response()->json([
                'message' => "The {$name} is available.",
                'valid' => true
            ]);
        }
    }

    // private function sendResetMessageCode($mobile)
    // {
    //     DB::beginTransaction();
    //     try {
    //         // create code
    //         $code = MessageCode::create([
    //             'mobile' => $mobile,
    //             'created_at' => now(),
    //             'code' => mt_rand(100000, 999999)
    //         ]);
    //         DB::commit();
    //         // send sms
    //         SendVerificationCode::dispatch($code);

    //         return response()->json([
    //             'message' => Lang::get('auth.check_mobile')
    //         ], 200);
    //     } catch (Throwable $exception) {
    //         report($exception);
    //         DB::rollBack();

    //         return response()->json([
    //             'message' => $exception->getMessage()
    //         ], 400);
    //     }
    // }

    /**
     * @param $email
     * @return mixed
     */
    private function findCustomerByEmail($email)
    {
        if(isset($email)){
            return Customer::query()->where('email', '=', $email)->first();
        }  else {
            return null;
        }
    }

    /**
     * @param $mobile
     * @return mixed
     */
    private function findCustomerByMobile($mobile)
    {
        if(isset($mobile)){
            return Customer::query()->where('mobile', '=', $mobile)->first();
        }  else {
            return null;
        }
    }

    /**
     * @param Request $request
     * @param Customer $customer
     * @return JsonResponse
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $customer = Auth::user();

        $request->validate([
            'password' => 'required|min:4|numeric|confirmed',
            'password_confirmation' => 'required|min:4|numeric',
        ]);
        
        DB::beginTransaction();
        try {
            $customer->update($request->all());

            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.update')
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
     * @return mixed
     */

    public function broadcastLogin(Request $request): mixed
    {
        return Broadcast::auth($request);
    }
}
