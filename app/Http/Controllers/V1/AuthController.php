<?php

namespace App\Http\Controllers\V1;

use App\Enums\ResponseCodes;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\LoginRequest;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest as AuthLoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\UpdateUserRequest;
use App\Http\Resources\Auth\UserResource;
use App\Mail\ForgotPasswordMail;
use App\Models\Plan;
use App\Models\User;
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
        $plan = Plan::query()->find($request->get('plan_id'));

        $startTime          = new Carbon($plan->created_at);
        $data['password']   = bcrypt($request->get('password'));
        $data['start_date'] = new Carbon($plan->created_at);
        $data['end_date']   = $startTime->addMonth($plan->duration_time);

        User::query()->create($data);

        return $this->response(ResponseCodes::S1000);
    }

    /**
     * @param ForgotPasswordRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $password = Str::random(10);

        User::query()->where('email', $request->get('email'))
            ->update([
                'password' => bcrypt($password),
            ]);

        Mail::to($request->get('email'))->send(new ForgotPasswordMail($password));

        return $this->response(ResponseCodes::S1000);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            /** @noinspection PhpPossiblePolymorphicInvocationInspection */
            auth()->user()->currentAccessToken()->delete();

            return $this->response(ResponseCodes::S1000);
        } catch (Exception $exception) {
            Log::error($exception);
            return $this->response(ResponseCodes::E1001);
        }
    }

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

    /**
     * Get account information
     *
     * @return JsonResponse
     */
    public function getUser(): JsonResponse
    {
        $user = User::query()->find(auth()->user()->id);

        return $this->response(ResponseCodes::S1000, UserResource::make($user));
    }

    /**
     * Change account password
     *
     * @param ChangePasswordRequest $requests
     * @return JsonResponse
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $thisUser = auth()->user();
        if (Hash::check($request->get('current_password'), $thisUser->password)) {
            $thisUser->update(['password' => bcrypt($request->get('password'))]);
            return $this->response(ResponseCodes::S1000);
        }
        return $this->response(ResponseCodes::E2017);
    }

    /**
     * Update information for the account
     *
     * @param UpdateUserRequest $request
     * @return JsonResponse
     */
    public function updateUser(UpdateUserRequest $request): JsonResponse
    {
        $thisUser = auth()->user();
        $data = $request->only(['name', 'phone']);
        $thisUser->update($data);

        return $this->response(ResponseCodes::S1000);
    }
}
