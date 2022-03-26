<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class login
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $jdata = $request->getContent();
        $data = json_decode($jdata);
        $userVal = Usuario::where('token',$data->token)->first();
        if ($userVal){
            return $next($request);
        }else{
            $response['msg1']="no has iniciado sesion";
            return($response);
        }
    }
}
