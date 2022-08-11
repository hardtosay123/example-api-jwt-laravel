<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserResource;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $user = Auth::user();
        $code = 200;

        $response = (new UserResource($user))
                            ->additional([
                                'links' => [
                                    'self' => $request->fullUrl()
                                ],
                                'meta' => [
                                    'message' => 'Login successfully.',
                                    'status' => (string) $code
                                ]
                            ]);
        
        return $this->responseResourceJson($response, $code);
    }
}
