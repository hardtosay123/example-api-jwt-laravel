<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ErrorMessageResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{
    public function ChangePassword(Request $request)
    {
        $rules = [
            'old_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'confirmed']
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

        $auth_user = Auth::user();

        if (Hash::check($request->old_password, $auth_user->password)) {
            $auth_user->fill([
                'password' => Hash::make($request->new_password)
            ])->save();

            $code = 200;
            $data['data'] = [
                'message' => "Changed Password Successfully."
            ];

            return $this->responseJson($data, $code);
        }

        $code = 400;
        $error['status'] = $code;
        $error['title'] = 'Form validation failures.';
        $error['detail'] =  [
            'old_password' => ["Old Password is incorrect."]
        ];

        $response = new ErrorMessageResource($error);

        return $this->responseResourceJson($response, $code);
    }
}
