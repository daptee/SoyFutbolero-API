<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use  App\Models\{
    Turnament,
    TournamentGroups,
    TurnamentStage,
    TournamentTeam
};
use Carbon\Carbon;
use App\Services\JwtService;

class TurnamentController extends Controller
{
    public function list(){
        try{
            $turnaments = Turnament::with(['torneoFase','torneoFase.fase','estado','tipo'])->get();

            if($turnaments->count() == 0){
                return response()->json([
                    'message' => 'No se encontraron torneos.',
                ],404);
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

            #Crear Torneo
            $data['turnament']['fecha_crea'] = date('Y-m-d');
            $data['turnament']['hora_crea'] = date('H:i:s');
            $data['turnament']['user_crea'] = JwtService::getUser()->id;
            $data['turnament']['id_estado'] = 1;

            $tournament = Turnament::create($data['turnament']);
            $tournament->estado = $tournament->estado;
            $tournament->tipo = $tournament->tipo;

            #Crear Fase
            foreach ($data['stages'] as $stage_id) {
                $stage['id_torneo'] = $tournament->id;
                $stage['id_fase'] = $stage_id;
                TurnamentStage::create($stage);
            }

            $tournament = Turnament::whereId($tournament->id)->with(['torneoFase','torneoFase.fase','estado','tipo'])->first();

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

    public function update(Request $request, $id){
        try{
            $tournament =Turnament::whereId($id)->first();

            if (is_null($tournament)) {
                return response()->json([
                    'message' => "No se encontro el torneo: ". $id
                ],404);
            }

            if ($tournament->estado->id != 1) {
                return response()->json([
                    'message' => "No es posible modificar el torneo ya que su estado es: ". $tournament->estado->nombreEstado
                ],409);
            }

            $tournament_data = $request->filled('tournament') ? $request->tournament : null;
            if (!is_null($tournament_data)) {
                Turnament::updateOrCreate([
                    'id' => $id
                ], $tournament_data);
            }

            $stages_ids = $request->filled('stages') ? $request->stages : null;
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

            $tournament = Turnament::whereId($id)->with(['torneoFase','torneoFase.fase','estado','tipo'])->first();

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
}
