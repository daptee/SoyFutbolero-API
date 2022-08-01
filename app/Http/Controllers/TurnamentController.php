<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use  App\Models\{
    Turnament,
    TournamentGroups,
    TurnamentStage,
    TournamentTeam,
    Medallero,
    UserTournamet
};
use Carbon\Carbon;
use App\Services\JwtService;
use Illuminate\Support\Facades\Storage;

class TurnamentController extends Controller
{
    public function list(){
        try{
            $turnaments = Turnament::with(['torneoFase','torneoFase.fase','estado','tipo','medallero'])->get();

            if($turnaments->count() == 0){
                return response()->json([
                    'message' => 'No se encontraron torneos.',
                ],404);
            }

            foreach ($turnaments as $tournament){
                foreach ($tournament->medallero as $medallero){
                    $medallero->id_usuario =  $medallero->usuarios->id;
                    $medallero->nombre_completo = $medallero->usuarios->apellido .' '.$medallero->usuarios->nombre;
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
            $data['turnament'] = json_decode($data['turnament']);
            $data['stages'] = explode(',',$data['stages']);
            $data['turnament'] = (array) $data['turnament'];

            #Crear Torneo
            $data['turnament']['user_crea'] = JwtService::getUser()->id;

            # Agrego el archivo
            $file_base_name = strtolower(str_replace(" ", "_", trim($data['turnament']['nombre'])));
            $file_tournament    = $request->hasFile('tournament_file') ? $request->tournament_file : null;
            $turnament_name    = $request->hasFile('tournament_file') ? 'tournament_'.$file_base_name.'.'.$file_tournament->extension() : $file_base_name;
            $data['turnament']['directorio'] = $turnament_name;

            $tournament = Turnament::create($data['turnament']);

            $path = 'tournaments/'. $tournament->id;

            if(!is_null($file_tournament)){
                Storage::disk('public')->putFileAs($path, $file_tournament, $turnament_name);
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
            $turnament_req = json_decode($request->tournaments);
            $tournament_data = (array)$turnament_req;

            if($request->hasFile('tournament_file')){
                if (Storage::disk('public')->exists($path.'/'.$tournament->directorio)) {
                    Storage::disk('public')->delete($path.'/'.$tournament->directorio);
                }

                $file_base_name = strtolower(str_replace(" ", "_", trim($tournament_data['nombre'])));
                $file_tournament    = $request->tournament_file ;
                $turnament_name    = 'tournament_'.$file_base_name.'.'.$file_tournament->extension();

                Storage::disk('public')->putFileAs($path, $file_tournament, $turnament_name);
                $tournament_data['directorio'] = $turnament_name;
            }

            if (!is_null($tournament_data)) {
                Turnament::updateOrCreate([
                    'id' => $id
                ], $tournament_data);
            }

            $stages = explode(',',$request->stages);
            $stages_ids = $request->filled('stages') ? $stages : null;
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

            return $turnaments;
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


            return $teams;
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
                'message' => 'Los ganadores fueron asignados con exito.',
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
}
