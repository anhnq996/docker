<?php

namespace App\Http\Controllers\V1;

use App\Enums\Codes;
use App\Enums\OtpStatus;
use App\Enums\OtpType;
use App\Enums\ResponseCodes;
use App\Enums\TokenType;
use App\Enums\UserStatus;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\ForgotPasswordRequest;
use App\Http\Requests\API\LoginRequest;
use App\Http\Requests\API\Mobile\GoogleAccount\GoogleAccountCheckRequest;
use App\Http\Requests\API\Mobile\GoogleAccount\GoogleAccountUpdatePhoneRequest;
use App\Http\Requests\API\ResetPasswordRequest;
use App\Http\Requests\API\VerifyTokenRequest;
use App\Http\Requests\Auth\LoginRequest as AuthLoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Mail\ResetPasswordMailable;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\FirebaseToken;
use App\Models\Otp;
use App\Models\Plan;
use App\Models\Setting;
use App\Models\Staff;
use App\Models\Token;
use App\Models\User;
use App\Services\OtpService;
use App\Services\PermissionService;
use App\Traits\CommonTrait;
use App\Traits\ResponseTrait;
use Error;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    use ResponseTrait, CommonTrait;

    public function __construct()
    {
        //
    }

    /**
     * @param LoginRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function login(AuthLoginRequest $request): JsonResponse
    {
        $username = $request->get('username');
        $password = $request->get('password');

        // Verify input fields
        if (empty($username) || empty($password)) {
            return $this->response(ResponseCodes::E1066);
        }

        // Get user info
        /** @var User $user */
        /** @noinspection PhpUndefinedMethodInspection */
        $user = User::query()
            ->where([
                'email' => $request->get('username'),
            ])
            ->orWhere([
                'phone' => $request->get('username'),
            ])
            ->first();

        // Check user exists
        if (!$user) {
            return $this->loginFailed('username');
        }

        // Check password
        if (!Hash::check($password, $user->password)) {
            return $this->loginFailed('password');
        }

        return $this->getUserInfo($user);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->all();
        $data['password'] = bcrypt($request->get('password'));
        $plan = Plan::query()->find($request->get('plan_id'));
        // $data['start_date'] = $plan->
        User::query()->create($data);

        return $this->response(ResponseCodes::S1000);
    }

    // /**
    //  * @param ForgotPasswordRequest $request
    //  * @return JsonResponse
    //  * @throws Exception
    //  */
    // public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    // {
    //     // Check input
    //     if (!isset($request->username) || !isset($request->user_type)) {
    //         return $this->response(Codes::E2015());
    //     }

    //     $userType = UserType::tryFrom($request->user_type);
    //     $params = $request->only('username', 'user_type');
    //     $arrayUserStatus = [UserStatus::active()->value, UserStatus::otpVerified()->value];
    //     $conditions = UserType::user()->equals($userType)
    //         ? ['username' => $params['username'], 'type' => UserType::from($userType)]
    //         : ['email' => $params['username']];

    //     $dataUser = match ($userType?->value) {
    //         UserType::user()->value => $this->user->findUser($conditions, $arrayUserStatus),
    //         UserType::clinic()->value => $this->clinic->findClinic($conditions, $arrayUserStatus),
    //         UserType::staff()->value => $this->staff->findStaff($conditions, $arrayUserStatus),
    //         UserType::doctor()->value => $this->doctor->findDoctor($conditions, $arrayUserStatus),
    //     };

    //     if (!$dataUser) {
    //         return $this->response(
    //             UserType::user()->equals($userType) ? Codes::E2001() : Codes::E2002()
    //         );
    //     }

    //     [$codes, $data] = UserType::user()->equals($userType)
    //         ? $this->handleForgotPasswordUser($params, $dataUser)
    //         : $this->handleForgotPasswordAdmin($params, $dataUser);

    //     return $this->response($codes, $data);
    // }

    // /**
    //  * @param $params
    //  * @param $thisUser
    //  * @return array
    //  * @throws Exception
    //  */
    // public function handleForgotPasswordUser($params, $thisUser): array
    // {
    //     $otpType = OtpType::change_pw();
    //     if ($this->otpService->isBanned($params['username'], $otpType)) {
    //         return [Codes::E1068(), null];
    //     }

    //     // Send new OTP
    //     $this->otpService->send(
    //         $params['username'], $otpType, $thisUser->id
    //     );

    //     return [Codes::S1000(), [
    //         'user_id' => $thisUser->id,
    //         'username' => $thisUser->username,
    //         'user_type' => $thisUser->type,
    //     ]];
    // }

    // /**
    //  * @param Request $request
    //  * @return JsonResponse
    //  */
    // public function logout(Request $request): JsonResponse
    // {
    //     try {
    //         /** @noinspection PhpPossiblePolymorphicInvocationInspection */
    //         auth()->user()->currentAccessToken()->delete();

    //         // remove firebase token when logout
    //         if (!empty($request->headers?->get('authorization'))) {
    //             $token = array_reverse(explode(' ', $request->headers->get('authorization')))[0] ?? null;
    //             FirebaseToken::query()->where('user_token', $token)->delete();
    //         }

    //         return $this->response(Codes::S1000());
    //     } catch (Exception $exception) {
    //         Log::error($exception);
    //         return $this->response(Codes::E1001());
    //     }
    // }

    // /**
    //  * @return JsonResponse
    //  */
    // public function getPrefixMobile(): JsonResponse
    // {
    //     // Viettel, Vina, Mobi, VietnamMobile, GMobie
    //     /** @var Setting $setting */
    //     $setting = Setting::query()->where('key', 'allow_prefix_phone')->firstOrNew();
    //     $prefixPhone = $setting?->value ? explode(',', $setting->value) : [];
    //     return $this->response(Codes::S1000(), ['prefix' => $prefixPhone]);
    // }

    /**
     * @param string $type
     * @param bool $isUser
     * @return JsonResponse
     */
    private function loginFailed(string $type): JsonResponse
    {
        return $this->response(match ($type) {
            'username' => ResponseCodes::E1043,
            'password' => ResponseCodes::E1044,
            default => throw new Error('The type invalid.'),
        });
    }

    /**
     * @return string
     */
    private function getDeviceId(): string
    {
        return sha1(request()->header(
            'X-YCare-Device-Id', session()->getId() ?: request()->ip()
        ));
    }

    // /**
    //  * @param GoogleAccountCheckRequest $request
    //  * @return JsonResponse
    //  */
    // public function checkGoogleId(GoogleAccountCheckRequest $request): JsonResponse
    // {
    //     $userConnectGoogle = User::query()->where('google_id', $request->get('google_id'))->first();

    //     if (empty($userConnectGoogle)) {
    //         return $this->response(Codes::S1000(), [
    //             'status' => false,
    //             'google_id' => $request->get('google_id'),
    //         ]);
    //     }

    //     $userConnectGoogle->load('userInfo');
    //     $isUser = str_contains($request->userAgent(), '(dart:io)');
    //     $firebaseToken = $request->get('firebase_token');

    //     return $this->getUserInfo($userConnectGoogle, $isUser, $firebaseToken);
    // }

    // /**
    //  * @param GoogleAccountUpdatePhoneRequest $request
    //  * @return JsonResponse
    //  */
    // public function updatePhone(GoogleAccountUpdatePhoneRequest $request): JsonResponse
    // {
    //     $phone = $request->get('phone');
    //     $googleAccount = User::query()->where('google_id', $request->get('google_id'))->exists();
    //     $user = User::query()->where('username', $request->get('phone'))
    //         ->where('type', UserType::user())
    //         ->first();

    //     if (!$user) {
    //         $setting = Setting::query()->where('key', 'allow_prefix_phone')->first();
    //         $prefixPhone = $setting?->value ? explode(',', $setting->value) : [];

    //         if (count($prefixPhone) > 0 && !in_array(substr($phone, 0, 3), $prefixPhone)) {
    //             return $this->response(Codes::E2026());
    //         }

    //         // Get user by phone number and type
    //         /** @var User $user */
    //         $user = User::query()->firstOrNew([
    //             'username' => $phone,
    //             'type' => UserType::user()->value,
    //         ], [
    //             'status' => UserStatus::registered()->value,
    //         ]);

    //         // User registered
    //         if (!in_array($user->status->value, [
    //             UserStatus::registered()->value,
    //             UserStatus::otpVerified()->value,
    //         ], true)) {
    //             return $this->response(Codes::E1005());
    //         }

    //         // Update password
    //         $user->password = Str::random(10);
    //         $user->assignRole('user');
    //         $user->save();

    //         // Send OTP into phone number
    //         $this->otpService->send(
    //             $user->username, OtpType::register(), $user->id
    //         );

    //         return $this->response(Codes::S1000(), [
    //             'otp_type' => OtpType::register()->value,
    //             'user_id' => $user->id,
    //         ]);
    //     } else {
    //         // Send OTP into phone number
    //         $otp = $this->otpService->send(
    //             $user->username, OtpType::connect_google(), $user->id
    //         );

    //         if ($otp instanceof Codes) {
    //             return $this->response($otp);
    //         }

    //         return $this->response(Codes::S1000(), [
    //             'otp_type' => OtpType::connect_google(),
    //             'user_id' => $user->id,
    //         ]);
    //     }
    // }

    /**
     * @return string
     */
    private function getUserInfo(User $user): JsonResponse
    {
        // Remove failed cache key
        Cache::tags('auth-' . $this->getDeviceId())->flush();

        // Generate auth token
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->response(ResponseCodes::S1000, [
            'user' => $user,
            'token' => $token,
            'screens' => $this->getPermissionOfUser($user),
        ]);
    }

    /**
     * get all permission of user
     * @param null $user
     * @return array
     */
    public function getPermissionOfUser($user = null): array
    {
        $permissions = $user ? $user->getPermissionsViaRoles() : auth()->user()->getPermissionsViaRoles();
        $results = [];
        foreach ($permissions as $key => $permission){
            $results[$key] = $permission->name;
        }

        return $results;
    }
}
