<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Turnament;
use App\Models\TournamentGroups;
use App\Models\TournamentTeam;

class GroupController extends Controller
{
    public function getByid($id){
        try{
            $groups = TournamentGroups::where('id_torneo', $id)->with('teams')->get();

            if ($groups->count() == 0) {
                return response()->json([
                    'message' => 'No existen grupos al torneo seleccionado',
                    'data' => $groups,
                ],404);
            }

            return response()->json([
                'message' => 'Grupos encontrados con exito.',
                'data' => $groups,
            ]);
        }catch(Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ],500);
        }
    }

    public function create(REquest $request){
        try{
            if(!$request->has(['groups','tournament_id'])){
                return response()->json([
                    'message' => 'Los datos enviados no son correctos.'
                ],400);
            }

            $data_groups = $request->groups;
            $tournament_id = $request->tournament_id;

            if (!Turnament::where('id',$tournament_id)->exists()) {
                return response()->json([
                    'message' => 'El Torneo al que quiere asignar grupos no existe.'
                ],404);
            }

            $groups_name = [];
            $groups_count = (65 + (count($data_groups)- 1));
            $group_index = 0;
            for($i=65; $i<= $groups_count; $i++) {

                $letra = chr($i);
                $tournament_group = TournamentGroups::create([
                    'id_torneo' => $tournament_id,
                    'grupo' => $letra
                ]);

                foreach ($data_groups[$group_index]  as $team_id) {
                    TournamentTeam::create([
                        'id_equipo' => $team_id,
                        'id_grupo' => $tournament_group->id
                    ]);
                }

                $group_index++;
            }

            $groups = TournamentGroups::where('id_torneo', $tournament_id)->with('teams')->get();


            return response()->json([
                'message' => 'Grupos creados con exito.',
                'data' => $groups,
            ]);
        }catch(Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ],500);
        }
    }
}
