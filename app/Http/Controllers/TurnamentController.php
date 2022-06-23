<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use  App\Models\{
    Turnament,
    TournamentGroups,
    TurnamentStage
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

            $tournament = Turnament::create($data['turnament']);
            $tournament->estado = $tournament->estado;
            $tournament->tipo = $tournament->tipo;

            #Crear Fase
            foreach ($data['stages'] as $stage) {
                $stage['id_torneo'] = $tournament->id;
                TurnamentStage::create($stage);
            }

            #Crear Grupos
            if ($request->has('groups')) {
                $i = $data['groups']['is_num'] ? 0 : 65;
                $count = $data['groups']['is_num'] ? $data['groups']['count'] - 1 : 64 + $data['groups']['count'];

                for($i; $i<= $count; $i++) {
                    $group = $data['groups']['is_num'] ? $i + 1 : chr($i);
                    TournamentGroups::create([
                        'id_torneo' => $tournament->id,
                        'grupo' => $group
                    ]);
                }
            }

            $tournament = Turnament::whereId($tournament->id)->with(['torneoFase','torneoFase.fase','estado','tipo'])->first();

            return response()->json([
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

            $stages = $request->filled('stages') ? $request->stages : null;
            if (!is_null($stages)) {
                $ids_stages = collect($stages)->pluck('id_fase');
                #Agrego Los stages nuevos
                foreach ($ids_stages as $id_stage) {
                    if(!TurnamentStage::where('id_fase',$id_stage)->exists())
                        TurnamentStage::create([
                            'id_torneo' => $id,
                            'id_fase' => $id_stage
                        ]);
                }
                # Elimino los que corresponden
                $id_destroy = TurnamentStage::where('id_torneo',$id)->whereNotIn('id_fase',$ids_stages)->pluck('id');
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
}
