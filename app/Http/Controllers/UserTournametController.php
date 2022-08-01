<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{
    UserTournamet,
    Turnament
};

class UserTournametController extends Controller
{

    public function list(){
        try{
            $users_tornaments = Turnament::whereNotIn('id_estado',[3])
            ->orderBy('id','desc')
            ->with('usuarios_torneo')
            ->get();

            if($users_tornaments->count() == 0){
                return response()->json([
                    'message' => 'No se encontro ningun usuario.'
                ],404);
            }

            foreach($users_tornaments as $usuarios_torneos){

                foreach($usuarios_torneos->usuarios_torneo as $user){
                    $user->usuario_id = $user->usuario->id;
                    $user->usuario_nombre = $user->usuario->apellido . ' '.$user->usuario->nombre;
                    $user->estado_pago_id = $user->estado->id;
                    $user->mail = $user->usuario->mail;
                    $user->estado_pago_nombre = $user->estado->nombreEstado;
                }

            }

            return response()->json([
                'message' => 'Torneos y Usuarios devueltos con exito.',
                'data' => $users_tornaments
            ]);
        }catch(Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ],500);
        }
    }

    public function create(request $request){
        try{
            if(!$request->has(['tournament_id','user_id'])){
                return response()->json([
                    'message' => 'Datos incompletos.'
                ],400);
            }

            $user_id = $request->user_id;
            $tournament_id = $request->tournament_id;

            $user_tournament = UserTournamet::create([
                'id_usuario' => $user_id,
                'id_torneo'  => $tournament_id
            ]);

            $user_tornament = Turnament::whereId($tournament_id)
            ->orderBy('id','desc')
            ->with('usuarios_torneo')
            ->first();

            foreach($user_tornament->usuarios_torneo as $user){
                $user->usuario_id = $user->usuario->id;
                $user->usuario_nombre = $user->usuario->apellido . ' '.$user->usuario->nombre;
                $user->estado_pago_id = $user->estado->id;
                $user->mail = $user->usuario->mail;
                $user->estado_pago_nombre = $user->estado->nombreEstado;
            }

            return response()->json([
                'message' => 'Se agrego el usuario al torneo N: '. $tournament_id,
                'data' => $user_tornament
            ]);
        }catch(Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ],500);
        }
    }

    public function update(request $request, $id){
        try{
            if(!$request->has(['estado_id'])){
                return response()->json([
                    'message' => 'Datos incompletos.'
                ],400);
            }

            $estado_id = $request->estado_id;

            UserTournamet::where('id',$id)->update([
                'id_estado' => $estado_id
            ]);

            $UserTournamet = UserTournamet::whereId($id)->first();

            $user_tornament = Turnament::whereId($UserTournamet->id_torneo)
            ->orderBy('id','desc')
            ->with('usuarios_torneo')
            ->first();

            foreach($user_tornament->usuarios_torneo as $user){
                $user->usuario_id = $user->usuario->id;
                $user->usuario_nombre = $user->usuario->apellido . ' '.$user->usuario->nombre;
                $user->estado_pago_id = $user->estado->id;
                $user->mail = $user->usuario->mail;
                $user->estado_pago_nombre = $user->estado->nombreEstado;
            }

            return response()->json([
                'message' => 'Se actualizo el registro. ',
                'data' => $user_tornament->usuarios_torneo
            ]);
        }catch(Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ],500);
        }
    }


    public function delete($id){
        try{
            $user_delete = UserTournamet::whereId($id)->first();

            if (!$user_delete){
                return response()->json([
                    'message' => 'No se encontro el usuario.'
                ],404);
            }

            $id_torneo  = $user_delete->id_torneo;

            $user_delete->delete();

            $user_tornament = Turnament::whereId($id_torneo)
            ->orderBy('id','desc')
            ->with('usuarios_torneo')
            ->first();

            foreach($user_tornament->usuarios_torneo as $user){
                $user->usuario_id = $user->usuario->id;
                $user->usuario_nombre = $user->usuario->apellido . ' '.$user->usuario->nombre;
                $user->estado_pago_id = $user->estado->id;
                $user->mail = $user->usuario->mail;
                $user->estado_pago_nombre = $user->estado->nombreEstado;
            }

            return response()->json([
                'message' => 'Usuario eliminado del Torneo N: '.$id_torneo ,
                'data' => $user_tornament->usuarios_torneo
            ]);
        }catch(Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ],500);
        }
    }

}
