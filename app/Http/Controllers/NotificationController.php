<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{
    Notification,
    UserNotification
};
use App\Services\JwtService;

class NotificationController extends Controller
{
    public function list(){
        try{
            $notificationes = Notification::with('torneo')->get();

            if ($notificationes->count() == 0){
                return response()->json([
                    'message' => 'No se encontraron notificaciones.',
                ],404);
            }

            return response()->json([
                'message' => 'Notificaciones devuelta con exito.',
                'data' => $notificationes
            ]);
        }catch(Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ],500);
        }
    }

    public function create(Request $request){
        try{
            if(!$request->has(['titulo','mensaje'])){
                return response()->json([
                    'message' => "Datos incompletos."
                ],400);
            }

            $titulo = $request->titulo;
            $mensaje = $request->mensaje;
            $torneo_id = $request->filled('torneo_id') ? $request->torneo_id : null;

            $data = [
                "titulo" => $titulo,
                "mensaje" => $mensaje,
            ];

            $data = !is_null($torneo_id) ? array_merge($data, [ "torneo_id" => $torneo_id] ) : $data;

            $notification = Notification::create($data);

            $notification->torneo =  $notification->torneo;

            return response()->json([
                'message' => 'Se creao exitosamente la notificacion.',
                'data' => $notification
            ]);
        }catch(Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ],500);
        }
    }

    public function update(Request $request, $id){
        try{
            if(!$request->has(['titulo','mensaje'])){
                return response()->json([
                    'message' => "Datos incompletos."
                ],400);
            }

            $titulo = $request->titulo;
            $mensaje = $request->mensaje;
            $torneo_id = $request->filled('torneo_id') ? $request->torneo_id : null;

            $data = [
                "titulo" => $titulo,
                "mensaje" => $mensaje,
            ];

            $data = !is_null($torneo_id) ? array_merge($data, [ "torneo_id" => $torneo_id] ) : $data;

            Notification::where('id', $id)->update($data);


            $notification = Notification::where('id', $id)->with('torneo')->first();

            return response()->json([
                'message' => 'Se actualizo exitosamente la notificacion.',
                'data' => $notification
            ]);
        }catch(Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ],500);
        }
    }

    public function delete($id){
        try{
            $notification = Notification::where('id', $id)->with('torneo')->first();

            if(!$notification){
                return response()->json([
                    'message' => "La notificacion a eliminar no exite."
                ],400);
            }

            $notification->delete();

            return response()->json([
                'message' => 'Se elimino exitosamente la notificacion.',
                'data' => ['id' => $id ]
            ]);
        }catch(Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ],500);
        }
    }

    public function readNotification($id){
        try{
            $notification = Notification::where('id', $id)->with('torneo')->first();

            if(!$notification){
                return response()->json([
                    'message' => "La notificacion a no exite."
                ],400);
            }

            UserNotification::create([
                'notificacion_id' => $id,
                'usuario_id'    => JwtService::getUser()->id
            ]);

            $notification->delete();

            return response()->json([
                'message' => 'Notificacion marcada como leida.'
            ]);
        }catch(Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ],500);
        }
    }

}
