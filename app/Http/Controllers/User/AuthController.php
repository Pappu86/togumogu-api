<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\User\AuthUserResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    /**
     * Login a user with email and get token
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:4'
        ]);

        $credentials = $request->only('email', 'password');


        if (Auth::attempt($credentials)) {
            return response()->json([
                'message' => Lang::get('auth.login'),
                'user' => new AuthUserResource(Auth::user())
            ]);
        }
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    /**
     * Check is email or mobile unique in database.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkIsEmailMobileExist(Request $request): JsonResponse
    {
        $user_id = $request->query('user_id');
        $field = $request->query('username') ?? $request->query('email') ?? $request->query('mobile');
        $queries = collect([]);
        foreach ($request->query() as $key => $value) {
            $queries->push($key);
        }
        $name = $queries[1];
        $user = User::find($user_id);
        if ($user) {
            if ($user->$name === $field) {
                return response()->json([
                    'message' => "The {$name} is available.",
                    'valid' => true
                ]);
            } else {
                if (User::where($name, '=', $field)->exists()) {
                    return response()->json([
                        'message' => "The {$name} has already taken.",
                        'valid' => false
                    ]);
                } else {
                    return response()->json([
                        'message' => "The {$name} is available.",
                        'valid' => true
                    ]);
                }
            }
        } else {
            if (User::where($name, '=', $field)->exists()) {
                return response()->json([
                    'message' => "The {$name} has already taken.",
                    'valid' => false
                ]);
            } else {
                return response()->json([
                    'message' => "The {$name} is available.",
                    'valid' => true
                ]);
            }
        }
    }

    /**
     * Get auth user response.
     *
     * @return AuthUserResource
     */
    public function me(): AuthUserResource
    {
        JsonResource::withoutWrapping();
        return new AuthUserResource(Auth::user());
    }

    /**
     * Logout user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->json([
            'message' => Lang::get('auth.logout')
        ]);
    }

    /**
     * Generate token for auth user.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function token(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return response()->json([
            'message' => 'Successfully Generated Token!',
            'token' => $user->createToken($request->device_name)->plainTextToken
        ]);
    }
}
