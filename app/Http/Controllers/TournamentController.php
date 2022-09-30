<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use  App\Models\{
    Match,
    Medallero,
    TournamentGroups,
    TournamentTeam,
    Turnament,
    TurnamentStage,
    UserPrediction,
    UserTournamet
};
use Carbon\Carbon;
use App\Services\JwtService;
use Illuminate\Support\Facades\Storage;

class TournamentController extends Controller
{
    private const BASEPATH = "/storage/";

    public function list(){
        try{
            $turnaments = Turnament::with(['torneoFase','torneoFase.fase','estado','tipo','medallero'])->get();

            if($turnaments->count() == 0){
                return response()->json([
                    'message' => 'No se encontraron torneos.',
                ],404);
            }

            foreach ($turnaments as $tournament){
                $file_path =  'tournaments/'. $tournament->id . '/' . $tournament->directorio;
                $tournament->image_url = Storage::disk('public_proyect')->exists($file_path) ? self::BASEPATH . $file_path : self::BASEPATH . 'defaults-image/sin-imagen.png';

                foreach ($tournament->medallero as $medallero){
                    $medallero->id_usuario =  isset($medallero->usuarios->id) ? $medallero->usuarios->id :null;
                    $medallero->nombre_completo = isset($medallero->usuarios->id) ? $medallero->usuarios->apellido .' '.$medallero->usuarios->nombre : null;
                }
            }

            return response()->json([
                'message' => 'Torneos devueltos con exitos.',
                'data' => $turnaments
            ]);
        } catch (Exception $error) {
            return response()->json([
                'message' => $error->getMessage()
            ]);
        }
    }

    public function getById($id){
        try {
            $tournament = Turnament::whereId($id)->with(['torneoFase','torneoFase.fase','estado','tipo'])->first();

            if(is_null($tournament)){
                return response()->json([
                    'message' => 'No se encontro torneos con el Id: '.$id,
                ],404);
            }

            $file_path =  'tournaments/'. $tournament->id . '/' . $tournament->directorio;
            $tournament->image_url = Storage::disk('public_proyect')->exists($file_path) ? self::BASEPATH . $file_path : self::BASEPATH . 'defaults-image/sin-imagen.png';

            return response()->json([
                'message' => 'Torneo N: '.$id.' devuelto con éxito',
                'data' => $tournament
            ]);
        } catch (Exception $error) {
            return response()->json([
                'message' => $error->getMessage()
            ],500);
        }
    }

    public function create(Request $request){
        try{
            $data = $request->all();

            $data['tournament'] = (array)json_decode($data['tournament']);
            $data['stages'] = explode(',',$data['stages']);

            #Crear Torneo
            $data['tournament']['user_crea'] = JwtService::getUser()->id;

            # Agrego el archivo
            $file_base_name = strtolower(str_replace(" ", "_", trim($data['tournament']['nombre'])));
            $file_tournament    = $request->hasFile('tournament_file') ? $request->tournament_file : null;
            $turnament_name    = $request->hasFile('tournament_file') ? 'tournament_'.$file_base_name.'.'.$file_tournament->extension() : $file_base_name;
            $data['tournament']['directorio'] = $turnament_name;

            $tournament = Turnament::create($data['tournament']);

            $path = 'tournaments/'. $tournament->id;

            if(!is_null($file_tournament)){
                Storage::disk('public_proyect')->putFileAs($path, $file_tournament, $turnament_name);
            }

            $tournament->estado = $tournament->estado;
            $tournament->tipo = $tournament->tipo;

            #Crear Fase
            foreach ($data['stages'] as $stage_id) {
                $stage['id_torneo'] = $tournament->id;
                $stage['id_fase'] = $stage_id;
                TurnamentStage::create($stage);
            }

            $tournament = Turnament::whereId($tournament->id)->with(['torneoFase','torneoFase.fase','estado','tipo','medallero'])->first();
            $file_path =  'tournaments/'. $tournament->id . '/' . $tournament->directorio;
            $tournament->image_url = Storage::disk('public_proyect')->exists($file_path) ? self::BASEPATH . $file_path : self::BASEPATH . 'defaults-image/sin-imagen.png';


            foreach($tournament->medallero as $usuario){
                $usuario->nombre_completo = $usuario->usuarios->apellido .' '.$usuario->usuarios->nombre;
            }

            return response()->json([
                'message' => 'Torneo creado con exitos.',
                'data' => $tournament
            ]);
        }catch (Exception $error){
            return response()->json([
                'message' => $error->getMessage()
            ],500);
        }
    }

    public function update(Request $request){
        try{
            $id = $request->id;
            $tournament =Turnament::whereId($id)->first();
            $path = 'tournaments/'. $tournament->id;

            if (is_null($tournament)) {
                return response()->json([
                    'message' => "No se encontro el torneo: ". $id
                ],404);
            }

            if ($tournament->estado->id == 3) {
                return response()->json([
                    'message' => "No es posible modificar el torneo ya que su estado es: ". $tournament->estado->nombreEstado
                ],409);
            }

            $tournament_data = (array)json_decode($request->tournaments);

            if($request->hasFile('tournament_file')){
                if (Storage::disk('public_proyect')->exists($path.'/'.$tournament->directorio)) {
                    Storage::disk('public_proyect')->delete($path.'/'.$tournament->directorio);
                }

                $file_base_name = strtolower(str_replace(" ", "_", trim($tournament_data['nombre'])));
                $file_tournament    = $request->tournament_file ;
                $turnament_name    = 'tournament_'.$file_base_name.'.'.$file_tournament->extension();

                Storage::disk('public_proyect')->putFileAs($path, $file_tournament, $turnament_name);
                $tournament_data['directorio'] = $turnament_name;
            }

            if (!is_null($tournament_data)) {
                Turnament::updateOrCreate([
                    'id' => $id
                ], $tournament_data);
            }

            $stages = $request->stages;
            $stages_ids = $request->filled('stages') ? explode(',',$stages) : null;
            if (!is_null($stages_ids)) {
                #Agrego Los stages nuevos
                foreach ($stages_ids as $stage_id) {
                    if(!TurnamentStage::where('id_fase',$stage_id)->where('id_torneo', $id)->exists())
                        TurnamentStage::create([
                            'id_torneo' => $id,
                            'id_fase' => $stage_id
                        ]);
                }
                # Elimino los que corresponden
                $id_destroy = TurnamentStage::where('id_torneo',$id)->whereNotIn('id_fase',$stages_ids)->pluck('id');
                if (count($id_destroy) > 0) {
                    foreach($id_destroy as $ids) {
                        TurnamentStage::destroy($ids);
                    }
                }
            }

            $tournament = Turnament::whereId($id)->with(['torneoFase','torneoFase.fase','estado','tipo','medallero'])->first();
            $file_path =  'tournaments/'. $tournament->id . '/' . $tournament->directorio;
            $tournament->image_url = Storage::disk('public_proyect')->exists($file_path) ? self::BASEPATH . $file_path : self::BASEPATH . 'defaults-image/sin-imagen.png';


            foreach($tournament->medallero as $usuario){
                $usuario->nombre_completo = $usuario->usuarios->apellido .' '.$usuario->usuarios->nombre;
            }

            return response()->json([
                'message' => 'Torneo N: '.$id.' modificado con éxito',
                'data' => $tournament
            ]);
        }catch (Exception $error){
            return response()->json([
                'message' => $error->getMessage()
            ],500);
        }
    }

    public function changeState(Request $request){
        try{
            if (!$request->has(['id','id_estado'])){
                return response()->json([
                    'message' => "Id de Torneo no encontrado."
                ],400);
            }

            $tournament = Turnament::whereId($request->id)->first();

            if (is_null($tournament) || ($request->id_estado == 2 && $tournament->id_estado != 1) ||
            ($request->id_estado == 3 && $tournament->id_estado != 2)) {
                return response()->json([
                    'message' => "No se encontro el torneo: ". $request->id
                ],404);
            }
            $tournament->id_estado = $request->id_estado;
            $tournament->save();
            $tournament = Turnament::whereId($tournament->id)->with(['torneoFase','torneoFase.fase','estado','tipo'])->first();

            $file_path =  'tournaments/'. $tournament->id . '/' . $tournament->directorio;
            $tournament->image_url = Storage::disk('public_proyect')->exists($file_path) ? self::BASEPATH . $file_path : self::BASEPATH . 'defaults-image/sin-imagen.png';
            $state = $request->id == 2 ? 'iniciado' : 'finalizado';

            return response()->json([
                "message" => "Torneo $state con éxito.",
                'data' => $tournament
            ]);
        }catch (Exception $error){
            return response()->json([
                'message' => $error->getMessage()
            ],500);
        }
    }

    public function listWithGroup(){
        try{
            $turnaments = Turnament::with(['torneoGrupos','estado'])->get();

            if($turnaments->count() == 0){
                return response()->json([
                    'message' => 'No se encontraron torneos.',
                ],404);
            }

            return response()->json([
                'message' => 'Grupos devueltos con exito.',
                'data' => $turnaments
            ]);
        } catch (Exception $error) {
            return response()->json([
                'message' => $error->getMessage()
            ],500);
        }
    }

    public function getTournamentStages($id){
        try{
            $turnaments = TurnamentStage::where('id_torneo',$id )
            ->with(['fase'])->get();

            if ($turnaments->count() == 0 ) {
                return response()->json([
                    'message' => "No se encontraron Fases para el Torneo: ". $id
                ],404);
            }

            foreach ($turnaments as $stage){
                $stage->id_stage = $stage->fase->id;
                $stage->nombre = $stage->fase->tipoFase->tipo . ' - ' . $stage->fase->tipoPartido->partido;
            }

            return response()->json([
                'message' => 'Fases devueltos con exito',
                'data' => $turnaments
            ]);
        }catch (Exception $error){
            return response()->json([
                'message' => $error->getMessage()
            ],500);
        }
    }

    public function getTournamentTeams($id){
        try{
            $tournament_teams = TournamentTeam::whereHas('group', function ($query) use ($id) {
                $query->where('id_torneo',  $id);
            })
            ->with(['team'])->get();

            if ($tournament_teams->count() == 0 ) {
                return response()->json([
                    'message' => "No se encontraron equipos para el Torneo: ". $id
                ],404);
            }

            $teams = [];
            foreach ($tournament_teams as $team){
                $team->team->grupo_id = $team->group->id;
                $team->team->grupo = 'Grupo '. $team->group->grupo;
                $teams[] = $team->team;
            }


            return response()->json([
                'message' => 'Equipos devueltos con exito',
                'data' => $teams
            ]);
        }catch (Exception $error){
            return response()->json([
                'message' => $error->getMessage()
            ],500);
        }
    }

    public function getTournamentUsers($id){
        try{
            $tournament = UserTournamet::where('id_torneo',$id)->with(['usuario','estado'])->get();

            if($tournament->count() == 0){
                return response()->json([
                    'message' => "Torneo no tiene usuarios registrados."
                ],404);
            }

            foreach ($tournament as $users){
                $users->usuario_id =  $users->usuario->id;
                $users->nombre_completo = $users->usuario->apellido .' '.$users->usuario->nombre;
                $users->estado_descripcion =  $users->estado->nombreEstado;
            }

            return response()->json([
                'message' => 'Usuarios de Torneo devueltos con exito.',
                'data' => $tournament
            ]);
        } catch (Exception $error){
            return response()->json([
                'message' => $error->getMessage()
            ],500);
        }
    }

    public function setWinners(Request $request, $id){
        try{
            $tournament = Turnament::whereId($id)->first();

            if(!$tournament){
                return response()->json([
                    'message' => "Datos incorrectos."
                ],400);
            }

            $usuarios = $request->usuarios;

            if (count($usuarios) != $tournament->ganadores){
                return response()->json([
                    'message' => "La cantidad de ganadores no coincide con la del Torneo. Por favor revisar."
                ],400);
            }

            foreach($usuarios as $usuario){

                Medallero::create([
                    'torneo_id' => $tournament->id,
                    'usuario_id' => $usuario['id_usuario'],
                    'puesto' => $usuario['puesto'],
                ]);
            }

            $tournament = Turnament::whereId($id)->with(['torneoFase','torneoFase.fase','estado','tipo','medallero'])->first();
            $file_path =  'tournaments/'. $tournament->id . '/' . $tournament->directorio;
            $tournament->image_url = Storage::disk('public_proyect')->exists($file_path) ? self::BASEPATH . $file_path : self::BASEPATH . 'defaults-image/sin-imagen.png';

            foreach($tournament->medallero as $usuario){
                $usuario->nombre_completo = $usuario->usuarios->apellido .' '.$usuario->usuarios->nombre;
            }

            return response()->json([
                'message' => 'Los ganadores fueron asignados con exito.',
                'data' => $tournament
            ]);
        } catch (Exception $error){
            return response()->json([
                'message' => $error->getMessage()
            ],500);
        }
    }

    public function updateWinners(Request $request, $id){
        try{
            $tournament = Turnament::whereId($id)->first();

            if(!$tournament){
                return response()->json([
                    'message' => "Datos incorrectos."
                ],400);
            }

            $usuarios = $request->usuarios;

            if (count($usuarios) != $tournament->ganadores){
                return response()->json([
                    'message' => "La cantidad de ganadores no coincide con la del Torneo. Por favor revisar."
                ],400);
            }

            foreach($usuarios as $usuario){

                Medallero::updateOrCreate([
                    'torneo_id' => $tournament->id,
                    'puesto' => $usuario['puesto'],
                ],[
                    'usuario_id' => $usuario['id_usuario'],
                    'torneo_id' => $tournament->id,
                    'puesto' => $usuario['puesto'],
                ]);
            }

            $tournament = Turnament::whereId($id)->with(['torneoFase','torneoFase.fase','estado','tipo','medallero'])->first();
            $file_path =  'tournaments/'. $tournament->id . '/' . $tournament->directorio;
            $tournament->image_url = Storage::disk('public_proyect')->exists($file_path) ? self::BASEPATH . $file_path : self::BASEPATH . 'defaults-image/sin-imagen.png';

            foreach($tournament->medallero as $usuario){
                $usuario->nombre_completo = $usuario->usuarios->apellido .' '.$usuario->usuarios->nombre;
            }

            return response()->json([
                'message' => 'Los ganadores fueron actualizados con exito.',
                'data' => $tournament
            ]);
        } catch (Exception $error){
            return response()->json([
                'message' => $error->getMessage()
            ],500);
        }
    }

    public function getAllDataById($id){
        ini_set('max_execution_time', 180);
        try {
            $tournament = Turnament::whereId($id)->with(['torneoFase','torneoFase.fase','estado','tipo'])->first();
            $user_id = JwtService::getUser()->id;

            if(is_null($tournament)){
                return response()->json([
                    'message' => 'No se encontro torneos con el Id: '.$id,
                ],404);
            }

            $file_path =  'tournaments/'. $tournament->id . '/' . $tournament->directorio;
            $tournament->image_url = Storage::disk('public_proyect')->exists($file_path) ? self::BASEPATH . $file_path : self::BASEPATH . 'defaults-image/sin-imagen.png';
            $tournament->user_table = (array)$this->calculateTournamentPoints($tournament->id);

            foreach($tournament->torneoFase as $tournament_stage){
                $match_query = Match::query();
                $match_query->where('id_torneo', $id)
                ->where('id_fase', $tournament_stage->id_fase);
                $match_query->with('estadio','equipo_local','equipo_visitante','estado','match_group');
                $matchs = $match_query->orderBy('id', 'DESC')->get();
                $tournament_stage->partidos = $matchs;

                foreach($matchs as  $match){
                    $file_path = $match->equipo_local->tipo->id == 1 ? 'teams/' . $match->equipo_local->id . '/' . $match->equipo_local->escudo : 'teams/' . $match->equipo_local->id . '/' . $match->equipo_local->bandera;
                    $match->equipo_local->image_url = Storage::disk('public_proyect')->exists($file_path) ? self::BASEPATH . $file_path : self::BASEPATH . 'defaults-image/sin-imagen.png';

                    $file_path = $match->equipo_visitante->tipo->id == 1 ? 'teams/' . $match->equipo_visitante->id . '/' . $match->equipo_visitante->escudo : 'teams/' . $match->equipo_visitante->id . '/' . $match->equipo_visitante->bandera;
                    $match->equipo_visitante->image_url = Storage::disk('public_proyect')->exists($file_path) ? self::BASEPATH . $file_path : self::BASEPATH . 'defaults-image/sin-imagen.png';

                    $file_path = 'stadiums/' . $match->estadio->id . '/' . $match->estadio->foto;
                    $match->estadio->image_url = Storage::disk('public_proyect')->exists($file_path) ? self::BASEPATH . $file_path : self::BASEPATH . 'defaults-image/sin-imagen.png';
                }


                foreach($tournament_stage->partidos as $partido) {
                    $partido->usuario_prediccion = UserPrediction::where("id_usuario",$user_id  )->where("id_partido",$partido->id)->first();
                }
            }



            return response()->json([
                'message' => 'Torneo N: '.$id.' devuelto con éxito',
                'data' => $tournament
            ]);
        } catch (Exception $error) {
            return response()->json([
                'message' => $error->getMessage()
            ],500);
        }
    }

    private function calculateTournamentPoints($tournament_id){
        $matchs             = Match::where('id_torneo', $tournament_id)->with('prediccion')->get();
        $users_tournament   = UserTournamet::where("id_torneo", $tournament_id)->where('id_estado',3)->with(['usuario', 'usuario.genero'])->get();
//        $users_ids          = $users_tournament->pluck('usuario.id');
//        $matchs_ids         = $matchs->pluck('id');
//        $users_predictions  = UserPrediction::whereIn("id_usuario",$users_ids  )->whereIn("id_partido",$matchs_ids)->get();

        $users_table = [];

        foreach($users_tournament as $user){

            $path = 'users/'.$user->usuario->id;

            $user_table = [
                "usuario_id"        => $user->usuario->id,
                "nombre"            => $user->usuario->nombre,
                "apellido"          => $user->usuario->apellido,
                "genero"            => $user->usuario->genero,
                "foto_url"          => Storage::disk('public_proyect')->exists($path.'/'.$user->usuario->foto) ? self::BASEPATH . $path.'/'.$user->usuario->foto : self::BASEPATH . 'defaults-image/sin-imagen.png',
                "total_acertados"   => 0,
                "total_errados"     => 0,
                "puntos"            => 0
            ];
            foreach($matchs as $match){

                # Se suma puntos de los partidos que estan como finalizados.
                if($match->id_estado != 4){
                    continue;
                }

                $user_prediction = null;
                foreach($match->prediccion as $prediccion) {
                    if ($prediccion->id_usuario === $user->usuario->id) {
                        $user_prediction = $prediccion;
                        break;
                    }
                }

//                $user_prediction =  $users_predictions->where('id_usuario', $user->usuario->id)->where('id_partido', $match->id)->first();

                # Usuario no cargo prediccion
                if(!$user_prediction){
                    $user_table["total_errados"]++;
                    continue;
                }

                # Verificamos si acerto el resultado
                if( (($match->goles_1 ==  $match->goles_2)  && ($user_prediction->goles_1 == $user_prediction->goles_2)) ||
                (($match->goles_1 >  $match->goles_2)  && ($user_prediction->goles_1 > $user_prediction->goles_2)) ||
                (($match->goles_1 <  $match->goles_2)  && ($user_prediction->goles_1 < $user_prediction->goles_2)) ){
                    $user_table["total_acertados"]++;

                    $real_total_gol = $match->goles_1 + $match->goles_2;
                    $predictions_total_gol = $user_prediction->goles_1 + $user_prediction->goles_2;

                    $diferencia_goles = abs($real_total_gol -  $predictions_total_gol);

                    if( $diferencia_goles == 0){
                        $user_table["puntos"] += 10;
                    } else {
                        $user_table["puntos"] = $diferencia_goles > 5 ? ($user_table["puntos"] + 5) :  ($user_table["puntos"] + (10 - $diferencia_goles));
                    }

                } else {
                    $user_table["total_errados"]++;
                }

            }

            $users_table[] = $user_table;
        }

        return $users_table;
    }
}
