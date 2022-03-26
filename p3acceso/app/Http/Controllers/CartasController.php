<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Carta;
use App\Models\Coleccion;
use App\Models\Cartascole;
use App\Models\Usuario;
use App\Models\Oferta;
use App\Models\CartasUsuario;
use Exception;
use Illuminate\Support\Facades\DB;

class CartasController extends Controller
{
    public function subirCarta(Request $request){
        //hacer restriccion de permiso
        //que una carta necesite incluir una coleccion existente al crearse
        $jdata = $request->getContent();
        $data = json_decode($jdata);
        $userVal = Usuario::where('token',$data->token)->first();
        if($userVal){
            if ($userVal->clase == "administrador"){
                $carta = new Carta;
                $carta->nombre = $data->nombre;
                $carta->descripcion = $data->descripcion;
                $carta->save();
                $response["msg"] = $carta;
                $status = 1;
                $response['status'] = $status;
            }else{
                $status = 0;
                $response['status'] = $status;
                $response["msg"] = "no tienes permisos";
            }
        }else{
            $status = 0;
            $response['status'] = $status;
            $response["msg"] = "token no valido";
        }
        return response()->json($response); 
    }
    public function subirColeccion(Request $request){
        $jdata = $request->getContent();
        $data = json_decode($jdata);
        $userVal = Usuario::where('token',$data->token)->first();
        if($userVal){
            if ($userVal->clase == "administrador"){
                $coleccion = new Coleccion;
                $coleccion->nombre = $data->nombre;
                $coleccion->simbolo = $data->simbolo;
                //haceer que cree lineas en la tabla de  coleccionesxcarta
                $coleccion->save();
                $response["msg"] = $coleccion;
                $status = 1;
                $response['status'] = $status;
            }else{
                $status = 0;
                $response['status'] = $status;
                $response["msg"] = "no tienes permisos";
            }
        }else{
            $status = 0;
            $response['status'] = $status;
            $response["msg"] = "token no valido";
        } 
        return response()->json($response); 

    }
    public function cartaAColeccion(Request $request){
        $jdata = $request->getContent();
        $data = json_decode($jdata);
        $userVal = Usuario::where('token',$data->token)->first();
        if($userVal){
            if ($userVal->clase == "administrador"){
                $carta = Carta::where('id',$data->id_carta)->first();
                if($carta){
                    $coleccion = Coleccion::where('id',$data->id_coleccion)->first();
                        if($coleccion){
                            $cartascole = new Cartascole;
                            $cartascole->id_carta = $data->id_carta;
                            $cartascole->id_coleccion = $data->id_coleccion;
                            $cartascole->save();
                            $response["msg"] = $cartascole;
                            $status = 1;
                            $response['status'] = $status;
                        }else{ 
                            $status = 0;
                            $response['status'] = $status;
                            $response["msg"] = "no existe la coleccion";
                        }
                    }else{
                        $status = 0;
                        $response['status'] = $status;
                        $response["msg"] = "no existe la carta";
                    }
            }else{
                $status = 0;
                $response['status'] = $status;
                $response["msg"] = "no tienes permisos";
            }
        }else{
            $status = 0;
            $response['status'] = $status;
            $response["msg"] = "token no valido";
        }
        return response()->json($response); 
    }
    public function altaCarta(Request $request){
        $jdata = $request->getContent();
        $data = json_decode($jdata);
        $userVal = Usuario::where('token',$data->token)->first();
        if($userVal){
            if ($userVal->clase == "administrador"){
                //al dar de alta comprobar q tenga coleccion
                $cartaid = Cartascole::where('id_carta',$data->id_carta)->first();
                if($cartaid){
                    $carta = Carta::find($data->id_carta);
                    $carta->alta = 1;
                    $carta->save();
                    $response["msg1"] = $carta;
                    //cambiar boolean
                    $status = 1;
                    $response['status'] = $status;
                }else{
                    $response["msg"] = "no hay ninguna coleccion asociada para subirla";
                    $status = 0;
                    $response['status'] = $status;
                }
            }else{
                $status = 0;
                $response['status'] = $status;
                $response["msg"] = "no tienes permisos";
            }
        }else{
            $status = 0;
            $response['status'] = $status;
            $response["msg"] = "token no valido";
        }
        return response()->json($response);
        
    }
    public function altaColeccion(Request $request){
        $jdata = $request->getContent();
        $data = json_decode($jdata);
        $userVal = Usuario::where('token',$data->token)->first();
        if($userVal){
            if ($userVal->clase == "administrador"){
                //al dar de alta comprobar que tenga alguna carta asociada
                $coleid = Cartascole::where('id_coleccion',$data->id_coleccion)->first();
                if($coleid){
                    $coleccion = Coleccion::find($data->id_coleccion);
                    $coleccion->alta = 1;
                    $coleccion->save();
                    $response["msg1"] = $coleccion;
                    $status = 1;
                    $response['status'] = $status;
                }
                else{
                    $response["msg"] = "no hay ninguna carta asociada para subirla";
                    $status = 0;
                    $response['status'] = $status;
                }
            }else{
                $status = 0;
                $response['status'] = $status;
                $response["msg"] = "no tienes permisos";
            }
        }else{
            $status = 0;
            $response['status'] = $status;
            $response["msg"] = "token no valido";
        }
        return response()->json($response);
    }
    public function busquedaNombre(Request $request){
        $jdata = $request->getContent();
        $data = json_decode($jdata);
        $userVal = Usuario::where('token',$data->token)->first();
        if($userVal){
            $cartas = Carta::where('nombre', 'LIKE', '%'.$data->nombre.'%')->get();
                $listaRespuesta = [];
                $listaRespuesta2 = [];
                foreach ($cartas as $key => $carta) {
                    $response["msg"]="Cartas encontradas";
                    $listaRespuesta["id"] = $carta->id;
                    $listaRespuesta["nombre"] = $carta->nombre;
                    array_push($listaRespuesta2, $listaRespuesta);
                }
                $response["coincidencias"] = $listaRespuesta2;
                $status = 1;
                $response['status'] = $status;
        }else{
            $response["msg"]="token no valido";
            $status = 0;
            $response['status'] = $status;
        }
        
        return response()->json($response);
        //busqueda por nombre para vender cartas que esten dadas de alta
    }
    public function subirOferta(Request $request){
        $jdata = $request->getContent();
        $data = json_decode($jdata);
        $userVal = Usuario::where('token',$data->token)->first();
        if($userVal){
            $carta = Carta::where('id',$data->idcarta)->first();
            if($carta->alta == 1){
                $oferta = new Oferta;
                $oferta->idvendedor = $userVal->id;
                $oferta->idcarta = $data->idcarta;
                $oferta->cantidad = $data->cantidad;
                $oferta->precio = $data->precio;
                $oferta->save();
                $response["msg"] = $oferta;
                $status = 1;
                $response['status'] = $status;
            }else{
                $response["msg"] = "carta no encontrada";
                $status = 0;
                $response['status'] = $status;
            }
            
        }else{
            $response["msg"]="token no valido";
            $status = 0;
            $response['status'] = $status;
        }
        return response()->json($response);
        //recibe id de carta, cantidad, precio total y id del vendedor a traves del token
    }
    public function busquedacompra(Request $req){ 
        $jdata = $req->getContent();
        $data = json_decode($jdata);

        $response["status"]=1;
        try{
            if(isset($data->nombre)){
                $coincidenciasColeccion = DB::table('ofertas')
                                        ->leftjoin('cartas', 'ofertas.idcarta', '=', 'cartas.id')
                                        ->where('cartas.nombre', 'like','%'.$data->nombre.'%')
                                        ->get()->toArray();

                if(count($coincidenciasColeccion) == 0){
                    throw new Exception("No hay ninguna coincidencia");
                }
                $coincidencias = [];
                foreach ($coincidenciasColeccion as $key => $coincidencia) {
                    array_push($coincidencias, $coincidencia);
                }

                $response["msg"]="Articulos encontrados";

                usort($coincidencias, function($object1, $object2) {
                    return $object1->precio > $object2->precio;
                });

                $listaRespuesta = [];
                $listaRespuesta2 = [];
                foreach ($coincidencias as $key => $anuncio) {
                    $listaRespuesta["id_anuncio"] =  $anuncio->id;
                    $listaRespuesta["id_carta"] =  $anuncio->idcarta;
                    $listaRespuesta["Carta"] =  $anuncio->nombre;
                    $listaRespuesta["Cantidad"] = $anuncio->cantidad;
                    $listaRespuesta["Precio"] = $anuncio->precio;
                    $listaRespuesta["Vendedor"] = (Usuario::find($anuncio->idvendedor))->nombre;
                    $listaRespuesta["id_vendedor"] =  $anuncio->idvendedor;
                    array_push($listaRespuesta2, $listaRespuesta);
                }
                $response["coincidencias"] = $listaRespuesta2;
            }else{
                throw new Exception("Error: Introduce un nombre de una carta (name)");
            }
        }catch(\Exception $e){
            $response["status"]=0;
            $response["msg"]=$e->getMessage();
        }
        return response()->json($response);
    }
}
