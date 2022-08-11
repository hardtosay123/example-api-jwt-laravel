<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\TokenRepository;
use Laravel\Passport\RefreshTokenRepository;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        $tokenRepository = app(TokenRepository::class);
        $refreshTokenRepository = app(RefreshTokenRepository::class);
        $tokenRepository->revokeAccessToken(Auth::user()->token()->id);
        $refreshTokenRepository->revokeRefreshTokensByAccessTokenId(Auth::user()->token()->id);

        $code = 200;
        $data['data'] = [
            'message' => 'Logout successfully.'
        ];
        return $this->responseJson($data, $code);
    }

    public function logout_all(Request $request)
    {
        Auth::user()->tokens->each(
            function ($token, $key) {
                $tokenRepository = app(TokenRepository::class);
                $refreshTokenRepository = app(RefreshTokenRepository::class);
                $tokenRepository->revokeAccessToken($token->id);
                $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($token->id);
            }
        );

        $code = 200;
        $data['data'] = [
            'message' => 'Logout all your accounts successfully.'
        ];
        return $this->responseJson($data, $code);
    }
}
