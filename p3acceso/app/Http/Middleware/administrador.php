<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Exception;
use App\Models\Usuario;
class administrador
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

        $response["status"] = 1;

        try{
            if(!isset($data->api_token)){
                throw new Exception("Error: No hay token");
            }
            $user = Usuario::where('token', $data->token)->first();
            if(!($user)){
                throw new Exception("Error: Ese token no existe");
            }
            if($user->rol != "administrador"){
                throw new Exception("Error: No tienes suficientes permisos");
            }

            $request->attributes->add(['userMiddleware' => $user]);

            return $next($request);

        }catch(\Exception $e){
            $response["status"] = 0;
            $response["msg"] = $e->getMessage();
        }

        return response()->json($response);
    }
}
