<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use Closure;

class HealthMiddleware
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
        try{
            DB::connection()->getPdo();
            if(DB::connection()->getDatabaseName()){
                return $next($request);
            }
        }catch(\Exception $e){
            return response()->json(
                [
                    'errors' => $e->getMessage(),
                    'report' => 'Database Connection Error',
                    'details' => null,
                    'success' => false,
                    'responseCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR,
                [],
                JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT
            );
        }
    }
}
