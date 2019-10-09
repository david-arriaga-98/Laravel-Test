<?php

namespace App\Http\Middleware;

use Closure;

class ApiAuthMiddleware
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

        $JwtAuth = new \JwtAuth();
        $token = $request->header('Authorization');
        $checkToken = $JwtAuth->checkToken($token);

        if($checkToken){
            return $next($request);
        }
        else {
            $data = [
                'status'    =>  'error',
                'code'      =>  401,
                'message'   =>  'No Autorizado'
            ];
            return response()->json($data, $data['code']);
        }
        
    }
}
