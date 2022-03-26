<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;

class UsuarioController extends Controller
{
    public function registrar(Request $request){
        $jdata = $request->getContent();
        $data = json_decode($jdata);
        $clave = $data->contraseña;
        $nombre = $data->nombre;
            if(!preg_match('`[0-9]`', $clave) || !preg_match('`[a-z]`', $clave) || strlen($clave) < 6){
                $response["msg"] = "La clave debe tener al menos una letra minúscula, un numero y tener mas de 6 caracteres";
                $status = 0;
                $response['status'] = $status;
            }else{
                if(preg_match('`[0-9]`', $nombre) || !preg_match('`[a-z]`', $nombre) || strlen($nombre) < 2){
                    $response["msg"] = "El nombre debe de ser solo letras y de minimo 2 caracteres";
                    $status = 0;
                    $response['status'] = $status;
                }else{
                    $user = Usuario::where('email',$data->email)->first();
                    if($user){
                            $response['status']= 0;
                            $response['msg']= "email ya existente";
                        }else{
                            if(!preg_match("^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$^", $data->email)) {
                                $response['status']= 0;
                                $response['msg']= "email no valido";
                            }else{
                                $user1 = Usuario::where('nombre',$data->nombre)->first();
                                if($user1){
                                    $response['status']= 0;
                                    $response['msg']= "nombre repetido";
                                }else{
                                    $usuario = new Usuario;
                                    $usuario->nombre = $data->nombre;
                                    $usuario->email = $data->email;
                                    $usuario->contraseña = Hash::make($data->contraseña);
                                    $usuario->clase = $data->clase;
                                    $usuario->save();
                                    $response["msg"] = $usuario;
                                    $response["msg1"] = "registrado con exito";
                                    $response['status']= 1;
                                }
                                
                            }
                            
                        }
                    }
                }
            return response()->json($response);
    }
    
    public function login(Request $request){
        $jdata = $request->getContent();
        $data = json_decode($jdata);
        $user = Usuario::where('nombre',$data->nombre)->first();
        if($user){
            $response['msg1']="nombre coincide";
            
            if (Hash::check($data->contraseña, $user->contraseña)) {
                $response['msg2']="contraseña coincide";
                $random = rand(1,10000);
                $user->token = Hash::make($random);
                $user->save();
                $response['token']=$user->token;
                $status = 1;
                $response['status'] = $status;
                //crear y asignar token
            }else{
                $response['msg2']="contraseña no coincide";
                $status = 0;
                $response['status'] = $status;
            }
        }else{
            $status = 0;
            $response['status'] = $status;
            $response['msg1']="no coincide nombre";
        }
        return response()->json($response);
    }
    public function recuperarcontra(Request $request){
        $jdata = $request->getContent();
        $data = json_decode($jdata);
        //necesita email
        $user = Usuario::where('email',$data->email)->first();
        if($user){
            $response['status']= 1;
            $response['msg']= "email enviado";
        }else{
            //no existe el email
            $response['status']= 0;
            $response['msg']= "email no encontrado";
        }
        //Mail::to($data->email)->send($user->contraseña);
        
        return response()->json($response);
    }
    //buscar como enviar email
}
