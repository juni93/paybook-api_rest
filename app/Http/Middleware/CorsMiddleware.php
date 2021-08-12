<?php

namespace App\Http\Middleware;

use Closure;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(!empty($_SERVER['HTTP_ORIGIN'])){
            $origin = $_SERVER['HTTP_ORIGIN'];
            $allowedOrigins = config('allowedDomains.allowed_domains');
            if(in_array($origin, $allowedOrigins)){
                $headers = [
                    'Access-Control-Allow-Origin' => $origin,
                    'Access-Control-Allow-Methods' => 'POST, GET, PUT, DELETE, OPTIONS',
                    'Access-Control-Allow-Credentials' => 'true',
                    'Access-Control-Max-Age' => '86400',
                    'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With',
                ];

                if($request->isMethod('OPTIONS')){
                    return response()->json('{"method:"OPTIONS}', 200, $headers);
                }

                $response = $next($request);

                foreach($headers as $key => $value){
                    $response->header($key, $value);
                }

                return $response;
            }
        }
        $response = $next($request);
        return $response;
    }
}
