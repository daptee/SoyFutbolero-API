<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Desafio;
//use App\Models\DesafioEstado;
use App\Models\DesafioUsuario;
use App\Models\User;
//use App\Models\Notification;
use App\Services\JwtService;
use Illuminate\Support\Facades\Mail;
use App\Mail\DesafioMail;

class DesafioController extends Controller
{
    public function create(Request $request){
        try {
            if(!$request->has(['nombre','usuarios_desafiados', 'torneo_id'])){
                return response()->json([
                    'message' => "Datos incompletos.",
                ], 400);
            }

            $user_id = JwtService::getUser()->id;
            $mails = $request->usuarios_desafiados;

            $user_create = User::where('id', $user_id)->first();
            if (!$user_create) {
                return response()->json([
                    'message' => "No existe el usuario que intenta crear el desafio.",
                ], 400);
            }

            $challenge = Desafio::create([
                'nombre' => $request->nombre,
                'usuario_creacion_id' => $user_id,
                'torneo_id' => $request->torneo_id
            ]);

            $mails_invitations = [];
            foreach($mails as $mail){

                if(!User::where('mail', $mail)->exists()){
                    $mails_invitations[] = $mail;
                    Mail::to($mail)->send(new DesafioMail($user_create));
                } else {
                    $user = User::where('mail', $mail)->first();

                    DesafioUsuario::create([
                        "usuario_id" => $user->id,
                        "desafio_id" => $challenge->id
                    ]);

                    Mail::to($mail)->send(new DesafioMail($user_create));

                    /*$data = [
                        "titulo" => "Fuiste desafiado!",
                        "mensaje" => "Ve a tu menu de desafios para aceptar!",
                        "usuario_id"  => $user->id
                    ];

                    Notification::create($data);*/
                }

            }
//            Mail::to($mails_invitations)->send(new DesafioMail());
//            $challenge = Desafio::where("id",$challenge->id)->with('usuarios_desafio','estado')->first();
//            $challenge->invitations = $mails_invitations;

            $challengeCreated = Desafio::where('id',$challenge->id)->with(['usuarios_desafio','estado', 'torneo'])->first();

            return response()->json([
                'message' => 'Desafio creado con exito.',
                'data' => $challengeCreated,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateState(Request $request,$id){
        try {
            if(!$request->has(['estado'])){
                return response()->json([
                    'message' => "Datos incompletos.",
                ], 400);
            }

            Desafio::where("id",$id)->update([
                "desafio_estado_id" =>  $request->estado
            ]);

            $challenge = Desafio::where("id",$id)->with('usuarios_desafio','estado')->first();

            return response()->json([
                'message' => 'Desafio actualizado con exito.',
                'data' => $challenge,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function list(){
        try {
            $user_id = JwtService::getUser()->id;

            $challenge_user_create = Desafio::where("usuario_creacion_id", $user_id)
            ->with(['usuarios_desafio','estado']);

            $challenge_user_invitated = Desafio::whereHas("usuarios_desafio",function($query) use ($user_id){
                $query->where('usuario_id', $user_id);
            })
            ->with('usuarios_desafio','estado', 'torneo')->union($challenge_user_create)->orderBy('id','desc')->get();

            return response()->json([
                'message' => 'Desafio actualizado con exito.',
                'data' => $challenge_user_invitated,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
