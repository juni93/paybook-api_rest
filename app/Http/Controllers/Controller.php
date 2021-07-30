<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class Controller extends BaseController
{
    protected function createNewToken($token)
    {
        return [
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ];
    }

    protected function responser(bool $isSuccess, string $report, $details, $errors, $responseCode)
    {
        return response()->json([
            'success' => $isSuccess,
            'responseCode' => $responseCode,
            'timestamp' => Carbon::createFromFormat('Y-m-d H:i:s', Carbon::now())->toDateTimeString(), //Carbon::now(),
            'errors' => $errors,
            'report' => $report,
            'details' => $details
        ], $responseCode, [], JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
    }
}
