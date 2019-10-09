<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use App\User;
use Illuminate\Support\Facades\DB;

class JwtAuth
{
    private $key;

    public function __construct(){
        $this->key = 'kakjdadsa_dsaiuds124277sa';
    }

    public function signup($email, $password, $getToken = null){

        $user = User::where([
            'email'     =>     $email,
            'password'  =>     $password,
        ])->first();

        $signup = false;

        if(is_object($user)){
            $signup = true;
        }
        

        if($signup){
            $token = [
                'sub'       =>  $user->id,
                'name'      =>  $user->name,
                'surname'   =>  $user->surname,
                'email'     =>  $user->email,
                'iat'       =>  time(),
                'exp'       =>  time() + 60 * 60,
            ];
            $jwt = JWT::encode($token, $this->key, 'HS256');
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);

            if(is_null($getToken)){
                $data = [
                    'status'    =>  'success',
                    'code'      =>  202,
                    'token'     =>  $jwt,
                ];
            }
            else {
                $data = [
                    'status'    =>  'success',
                    'code'      =>  202,
                    'token'     =>  $decoded,
                ]; 
            }
            
        }
        else {
            $data = [
                'status'    =>  'error',
                'code'      =>  401,
                'error'     =>  'Usuario y/o Contraseña Incorrectos',
            ];
        }
        return $data;
        
    }

    public function checkToken($jwt, $getIdentity = false)
    {
        $auth = false;

        try {
            $jwt = str_replace('"', '', $jwt);
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);
        }
        catch(\UnexpectedValueException $e){
            $auth = false;
        } 
        catch(\DomainException $e){
            $auth = false;
        }

        if(!empty($decoded) && is_object($decoded) && isset($decoded->sub)){
            $auth = true;
        }
        else {
            $auth = false;
        }

        if ($getIdentity) {
            return $decoded;
        }
        
        return $auth;
    }

}