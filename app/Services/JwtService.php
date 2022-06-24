<?php

namespace App\Services;

use Illuminate\Http\Request;
use JWTAuth;
use Exception;
use Tymon\JWTAuth\Exceptions\JWTException;
use Log;
use JWT;

class JwtService{

    public static function getUser(){
        try {

            if (!$user = JWTAuth::parseToken()->authenticate()) {
                  return response()->json(['error' => 'Usuario no encontrado' ], 404);
          }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
                return response()->json(['token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
                return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
                return response()->json(['token_absent'], $e->getStatusCode());
        }

        return $user;
    }



}
