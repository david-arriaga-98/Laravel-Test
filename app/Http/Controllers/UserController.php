<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class UserController extends Controller
{
    public function register(Request $request)
    {
        //Recoger los datos por post
        $json_register = $request->input('json_register', null);
        $params_register = json_decode($json_register, true);
        
        //Verificar si los datos recibidos son correctos
        if(!empty($params_register)){
            //Limpiar los datos
            $params_register = array_map('trim', $params_register);
            //Validar los datos
            $validate = \Validator::make($params_register, [
                'name'      =>  'required|alpha',
                'surname'   =>  'required|alpha',
                'email'     =>  'required|email|unique:users',
                'password'  =>  'required|confirmed'
            ]);
            if($validate->fails()){
                $data = [
                    'status'    =>  'error',
                    'code'      =>  400,
                    'message'   =>  'Parametros Incorrectos',
                    'errors'    =>  $validate->errors()
                ];
            }
            else {
                //Hashear la password
                $pwd = hash('sha256', $params_register['password']);
                //Guardar los datos
                $user = new User();
                $user->name = $params_register['name'];
                $user->surname = $params_register['surname'];
                $user->email = $params_register['email'];
                $user->password = $pwd;
                $user->role = 'ROLE_USER';

                $user->save();

                $data = [
                    'status'    =>  'success',
                    'code'      =>  201,
                    'message'   =>  'Usuario creado correctamente'
                ];
            }
        }
        else {
            $data = [
                'status'    =>  'error',
                'code'      =>  400,
                'message'   =>  'Error de Syntaxys! Revisa el código'
            ];
        }
        return response()->json($data, $data['code']);
    }
    public function login(Request $request)
    {
        $JwtAuth = new \JwtAuth();
        //Recoger datos por post
        $json_login = $request->input('json_login', null);
        $params_login = json_decode($json_login, true);
        
        //Verificar los datos
        if(!empty($params_login)){
            $validate = \Validator::make($params_login, [
                'email'     =>  'required|email',
                'password'  =>  'required|string'
            ]);

            if($validate->fails()){
                $data = [
                    'status'    =>  'error',
                    'code'      =>  400,
                    'errors'    =>  $validate->errors()
                ];
            }
            else {
                //hashear la password
                $pwd = hash('sha256', $params_login['password']);
                //Enviar los datos
                $data = $JwtAuth->signup($params_login['email'], $pwd);
                if(!empty($params_login['getToken'])){
                    $data = $JwtAuth->signup($params_login['email'], $pwd, true);
                }
            }
        }
        else {
            $data = [
                'status'    =>  'error',
                'code'      =>  400,
                'message'   =>  'Error de Syntaxys! Revisa el código'
            ];
        }

        return response()->json($data, $data['code']);
    }

    public function update(Request $request){

        $JwtAuth = new \JwtAuth();
        //recogemos el token
        $token = $request->header('Authorization');
        //recogemos los datos por put
        $json_update = $request->input('json_update', null);
        $params_update = json_decode($json_update, true);

        //Comprobar si los datos existen
        if(!empty($params_update)){

            //limpiar los datos
            $params_update = array_map('trim', $params_update);
            //obtener usuario
            $user = $JwtAuth->checkToken($token, true);
            //Validar datos
            $validate = \Validator::make($params_update, [
                'name'          =>  'alpha|string',
                'surname'       =>  'alpha|string',
                'description'   =>  'string'
            ]);
            if($validate->fails()){
                $data = [
                    'status'    =>  'error',
                    'code'      =>  400,
                    'errors'    =>  $validate->errors()
                ];
            }
            else {

                //Eliminar datos que no deseo
                unset($params_update['id']);
                unset($params_update['email']);
                unset($params_update['password']);
                unset($params_update['role']);
                unset($params_update['created_at']);
                unset($params_update['remember_token']);

                User::where('id', $user->sub)->update($params_update);

                $data = [
                    'status'    =>  'success',
                    'code'      =>  202,
                    'message'   =>  'Usuario actualizado correctamente',
                    'user'      =>  $user,
                    'changes'   =>  $params_update
                ];
            }
        }
        else {
            $data = [
                'status'    =>  'error',
                'code'      =>  400,
                'message'   =>  'Error de Syntaxys! Revisa el código'
            ];
        }

        return response()->json($data, $data['code']);
    }

}
