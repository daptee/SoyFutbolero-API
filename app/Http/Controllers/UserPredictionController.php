<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{
    Match,
    UserPrediction
};
use App\Services\JwtService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserPredictionController extends Controller
{
    public function setPrediction(Request $request){
        try{
            $rules = [
                    'user_predictions.*.id_partido' => "required",
                    'user_predictions.*.goles_1' => "required",
                    "user_predictions.*.goles_2" => "required"
            ];

            $atributes = self::getAtributes();
            $messages = self::getMessages();

            $validator = Validator::make($request->all(), $rules, $messages, $atributes);


            if($validator->fails()){
                return response()->json([ 'message' => $validator->errors()->first() ],400);
            }

            $user_predictions = $request->user_predictions;
            $user_id = JwtService::getUser()->id;

            foreach($user_predictions as $prediction){
                $id_partido = $prediction['id_partido'];
                $goles_1 = $prediction['goles_1'];
                $goles_2 = $prediction['goles_2'];

                $user_prediction = UserPrediction::updateOrCreate([
                    "id_usuario" => $user_id,
                    "id_partido" => $id_partido
                ], [
                    "id_usuario" => $user_id,
                    "id_partido" => $id_partido,
                    "goles_1" => $goles_1,
                    "goles_2" => $goles_2
                ]);
            }

            $match_query = Match::query();
            $match_query->where('id_torneo', $user_prediction->partido->id_torneo);
            $match_query->with('estadio','equipo_local','equipo_visitante','estado','match_group');
            $matchs = $match_query->orderBy('id', 'DESC')->get();

            foreach($matchs as  $match){
                $file_path = $match->equipo_local->tipo->id == 1 ? 'teams/' . $match->equipo_local->id . '/' . $match->equipo_local->escudo : 'teams/' . $match->equipo_local->id . '/' . $match->equipo_local->bandera;
                $match->equipo_local->image_url = Storage::disk('public')->exists($file_path) ? Storage::disk('public')->url($file_path) : Storage::disk('public')->url('defaults-image/sin-imagen.png');

                $file_path = $match->equipo_visitante->tipo->id == 1 ? 'teams/' . $match->equipo_visitante->id . '/' . $match->equipo_visitante->escudo : 'teams/' . $match->equipo_visitante->id . '/' . $match->equipo_visitante->bandera;
                $match->equipo_visitante->image_url = Storage::disk('public')->exists($file_path) ? Storage::disk('public')->url($file_path) : Storage::disk('public')->url('defaults-image/sin-imagen.png');

                $file_path = 'stadiums/' . $match->estadio->id . '/' . $match->estadio->foto;
                $match->estadio->image_url = Storage::disk('public')->exists($file_path) ? Storage::disk('public')->url($file_path) : Storage::disk('public')->url('defaults-image/sin-imagen.png');

                $match->usuario_prediccion = UserPrediction::where("id_usuario",$user_id  )->where("id_partido",$match->id)->first();
            }

            return response()->json([
                "messages" => "Pronostico creado con éxito.",
                "data" =>  $matchs
            ]);
        }catch(Exception $error){
            return response()->json([
                'message' => $error->getMessage()
            ],500);
        }
    }


    private static function getAtributes(){
        $atributes = [
            'user_predictions.*.id_partido' => 'Partido Id',
            'user_predictions.*.goles_1' => 'Goles Local',
            "user_predictions.*.goles_2" => 'Goles Visitantes'
        ];

        return $atributes;
    }

    private static function getMessages(){
        $messages = [
            'required' => 'El campo :attribute es obligatorio.',
            'integer' => 'El campo :attribute debe ser un número entero.'
        ];

        return $messages;
    }
}
