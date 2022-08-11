<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ErrorMessageResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $rules = [
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed'],
            'name' => ['required', 'string', 'max:255']
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
        else
        {
            $code = 201;
            $newUser = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'name' => $request->name
            ]);

            $response = (new UserResource($newUser))
                                    ->additional([
                                        'links' => [
                                            'self' => $request->fullUrl()
                                        ],
                                        'meta' => [
                                            'message' => 'Registered an account successfully.',
                                            'status' => (string)$code
                                        ]
                                    ]);
            return $this->responseResourceJson($response, $code);
        }
    }
}
