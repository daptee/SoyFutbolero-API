<?php

namespace App\Http\Controllers;

use App\Models\TournamentGroups;
use App\Models\TournamentTeam;
use App\Models\TurnamentStage;
use App\Models\Turnament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GroupController extends Controller
{
    public function getByid($id)
    {
        try {
            $groups = TournamentGroups::where('id_torneo', $id)->with('teams')->get();

            if ($groups->count() == 0) {
                return response()->json([
                    'message' => 'No existen grupos al torneo seleccionado',
                    'data' => $groups,
                ], 404);
            }

            return response()->json([
                'message' => 'Grupos encontrados con exito.',
                'data' => $groups,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function create(Request $request)
    {
        try {
            if (!$request->has(['groups', 'tournament_id'])) {
                return response()->json([
                    'message' => 'Los datos enviados no son correctos.',
                ], 400);
            }

            $data_groups = $request->groups;
            $tournament_id = $request->tournament_id;

            if (!Turnament::where('id', $tournament_id)->exists()) {
                return response()->json([
                    'message' => 'El Torneo al que quiere asignar grupos no existe.',
                ], 404);
            }

            $groups_name = [];
            $groups_count = (65 + (count($data_groups) - 1));
            $group_index = 0;
            foreach ($data_groups as $group) {
                $tournament_group = TournamentGroups::create([
                    'id_torneo' => $tournament_id,
                    'grupo' => $group['group_name'],
                ]);

                $teams_ids = collect($group['teams'])->pluck('id');

                foreach ($teams_ids as $team_id) {
                    TournamentTeam::create([
                        'id_equipo' => $team_id,
                        'id_grupo' => $tournament_group->id,
                    ]);
                }
            }

            $turnament = Turnament::where('id', $tournament_id)->with(['torneoGrupos', 'estado'])->first();

            return response()->json([
                'message' => 'Grupos creados con exito.',
                'data' => $turnament,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $groups = TournamentGroups::where('id_torneo', $id)->with('teams')->get();

            if ($groups->count() == 0) {
                return response()->json([
                    'message' => 'No existen grupos al torneo seleccionado',
                    'data' => $groups,
                ], 404);
            }

            Log::info(json_encode($request->all()));
            $data_groups = $request->groups;
            $groups_ids = [];
            foreach ($data_groups as $group) {
                # Si no existe el grupo lo creo
                if (!TournamentGroups::where('grupo', $group['group_name'])->where('id_torneo', $id)->exists()) {
                    $tournament_group = TournamentGroups::create([
                        'id_torneo' => $id,
                        'grupo' => $group['group_name'],
                    ]);
                } else {
                    $tournament_group = TournamentGroups::where('grupo', $group)->where('id_torneo', $id)->first();
                }

                $teams_ids = collect($group['teams'])->pluck('id');
                foreach ($teams_ids as $team_id) {

                    # Agrego los equipos nuevos
                    if (!TournamentTeam::where('id_grupo', $tournament_group->id)->where('id_equipo', $team_id)->exists()) {
                        TournamentTeam::create([
                            'id_equipo' => $team_id,
                            'id_grupo' => $tournament_group->id,
                        ]);
                    }

                }

                $ids_teams_destroy = TournamentTeam::where('id_grupo', $tournament_group->id)->whereNotIn('id_equipo',$teams_ids)->pluck('id');
                # Elimino equipos no esten
                if (count($ids_teams_destroy) > 0) {
                    foreach ($ids_teams_destroy as $ids) {
                        TournamentTeam::destroy($ids);
                    }
                }

                $groups_ids[] = $tournament_group->id;
            }

            $id_destroy = TournamentGroups::where('id_torneo', $id)->whereNotIn('id', $groups_ids)->pluck('id');
            if (count($id_destroy) > 0) {
                foreach ($id_destroy as $ids) {
                    if (TournamentTeam::where('id_grupo', $ids)->exists() ) {
                            $ids_teams_destroy = TournamentTeam::where('id_grupo', $ids)->pluck('id');
                            # Elimino equipos no esten
                            if (count($ids_teams_destroy) > 0) {
                                foreach ($ids_teams_destroy as $id_team) {
                                    TournamentTeam::destroy($id_team);
                                }
                            }
                    }
                    TournamentGroups::destroy($ids);
                }
            }

            $turnament = Turnament::where('id', $id)->with(['torneoGrupos', 'estado'])->first();

            return response()->json([
                'message' => 'Grupos editado con exito.',
                'data' => $turnament,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function _deletetTeams($id_grupo, $teams_id, $in_group = false){
        $query = TournamentTeam::where('id_grupo', $id_grupo);

        $id_destroy = $in_group ? $query->whereIn('id_equipo', $teams_id)->pluck('id') : $query->whereNotIn('id_equipo', $teams_id)->pluck('id');
        $id_destroy = $query->pluck('id');
        if (count($id_destroy) > 0) {
            foreach ($id_destroy as $ids) {
                TournamentTeam::destroy($ids);
            }
        }
    }
}
