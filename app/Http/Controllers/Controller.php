<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function responseJson($data, $code)
    {
        return response()->json($data, $code, ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
    }

    protected function responseResourceJson($response, $code)
    {
        $headers = [
            'Content-Type' => 'application/json;charset=UTF-8',
            'Charset' => 'utf-8'
        ];

        return $response->response()->setStatusCode($code)
                                    ->withHeaders($headers)
                                    ->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }
}
