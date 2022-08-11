<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ErrorMessageResource;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use App\Models\User;
use Laravel\Passport\TokenRepository;
use Laravel\Passport\RefreshTokenRepository;

class ResetPasswordController extends Controller
{
    public function ForgotPassword(Request $request)
    {
        $rules = [
            'email' => ['required', 'email']
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
        {
            $code = 400;

            $error['status'] = $code;
            $error['title'] = 'Form validation failures.';
            $error['detail'] =  $validator->errors();

            $response = new ErrorMessageResource($error);

            return $this->responseResourceJson($response, $code);
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT)
        {
            $code = 200;
            $data['data'] = [
                'message' => __($status)
            ];

            return $this->responseJson($data, $code);
        }
        else
        {
            $code = 400;

            $error['status'] = $code;
            $error['title'] = 'Password reset - Email does not exist in the record.';
            $error['detail'] =  [
                'email' => [__($status)]
            ];

            $response = new ErrorMessageResource($error);

            return $this->responseResourceJson($response, $code);
        }
    }

    public function ResetPasswordTokenCheck($token)
    {
        $code = 200;
        $data = [
            'token' => $token,
            'meta' => [
                'message' => 'Token',
                'status' => (string) $code
            ]
        ];
        return $this->responseJson($data, $code);
    }

    public function ResetPassword(Request $request)
    {
        $rules = [
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required','confirmed']
        ];

        $validator = Validator::make($request->all(), $rules);
    
        if ($validator->fails())
        {
            $code = 400;
            $error['status'] = $code;
            $error['title'] = 'Form validation failures.';
            $error['detail'] =  $validator->errors();

            $response = new ErrorMessageResource($error);

            return $this->responseResourceJson($response, $code);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));
    
                $user->save();
    
                event(new PasswordReset($user));
            }
        );
    
        if ($status === Password::PASSWORD_RESET)
        {
            $reset_user = User::where('email', $request->email)->firstOrFail();
            
            $reset_user->tokens->each(
                function ($token, $key) {
                    $tokenRepository = app(TokenRepository::class);
                    $refreshTokenRepository = app(RefreshTokenRepository::class);
                    $tokenRepository->revokeAccessToken($token->id);
                    $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($token->id);
                }
            );

            $code = 200;

            $data['data'] = [
                'message' => __($status)
            ];

            return $this->responseJson($data, $code);
        }
        else
        {
            $code = 400;
            $error['status'] = $code;
            $error['title'] = 'Password reset failed. (Email/Token wrong)';
            $error['detail'] =  [
                'email' => [__($status)]
            ];

            $response = new ErrorMessageResource($error);

            return $this->responseResourceJson($response, $code);
        }
    }
}
