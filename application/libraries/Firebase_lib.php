<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require 'vendor/autoload.php';
use \Firebase\JWT\JWT;

class Firebase_lib {

    /**
     * Validar Token ID de FireBase
     * 
     */
    public function validate_token_ant($id_token, $firebase_user_id, $google_public_keys)
    {
        $data = $this->decode_token($id_token);
        $validation['data'] = $data;
        $validation['kid'] = $data['header']->kid;
        $validation['messages'] = array();
        $validation['errors'] = array();
        $validation['status'] = 0;

        //Algoritmo correcto
        if ( $data['header']->alg != 'RS256' ) $validation['errors'][] = 'El algoritmo NO es válido.';

        //Clave pública de Google
        if ( ! key_exists($data['header']->kid, (array) $google_public_keys) ) $validation['errors'][] = 'El kid NO existe';

        //Tiempo de expiración debe ser futuro.
        if ( $data['payload']->exp <= time() ) $validation['errors'][] = 'El token ya expiró';

        //Hora de emisión y fecha de autenticación deben ser en el pasado
        if ( $data['payload']->iat >= time() ) $validation['errors'][] = 'El token no puede haber sido emitido en el futuro';
        if ( $data['payload']->auth_time >= time() ) $validation['errors'][] = 'La autenticación debe ser en el pasado';

        //Verificar identificador del proyecto Firebase
        if ( $data['payload']->aud != K_FIB_AUD ) $validation['errors'][] = 'El identificador del proyecto es incorrecto';

        //Verificar emisor del Token
        if ( $data['payload']->iss != 'https://securetoken.google.com/' . K_FIB_AUD ) $validation['errors'][] = 'El emisor del proyecto no es válido';

        //Verificar ID de Usuario en Firebase
        if ( $data['payload']->sub != $firebase_user_id ) $validation['errors'][] = 'El ID de Usuario en Firebase no es correcto';

        if ( count($validation['errors']) == 0 ) $validation['status'] = 1;

        return $validation;
    }

    public function validate_token()
    {

    }

    /**
     * Decode Firebase Token (JWT)
     */
    public function decode_token($id_token)
    {
        $decoded['header'] = json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $id_token)[0]))));
        $decoded['payload'] = json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $id_token)[1]))));
        //$decoded = json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',$id_token))));

        return $decoded;
    }

    public function decode($id_token, $google_public_keys)
    {
        //Obtener del token.header el índice de la clave pública para decodificar el token
        $header = json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $id_token)[0]))));
        $kid = $header->kid;                        //Key ID de la clave pública
        $public_key = $google_public_keys->$kid;    //Clave pública

        $decoded = JWT::decode($id_token, $public_key, array('RS256'));

        return $decoded;
    }
}