<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\{
    Match,
    MatchGroup,
    Stadium,
    Stage,
    Team,
    Turnament
};
use Carbon\Carbon;

class MatchController extends Controller
{
    private const BASEPATH = "/storage/";

    public function list(){
        try{

            $tournaments = Turnament::whereIn('id_estado', [1,2])
            ->with('estado')
            ->orderBy('id', 'DESC')
            ->get();

            if($tournaments->count() == 0) {
                return response()->json([
                    'message' => 'no se encontraron torneos abiertos o iniciados.',
                ]);
            }

            foreach($tournaments as $tournament){
                $matches = Match::where('id_torneo', $tournament->id)
                ->orderBy('id', 'DESC')
                ->with('fase','estadio','equipo_local','equipo_visitante','estado','match_group')->get();
                foreach($matches as $match) {
                    $match->ronda =  isset($match->match_group) && !is_null($match->match_group) ? $match->match_group->ronda : null;
                    $match->fase_nombre    = $match->fase->tipoPartido->partido .' - '. $match->fase->tipoFase->tipo;
                    $match->partido_nombre = $match->equipo_local->nombre .' vs '. $match->equipo_visitante->nombre;
                }

                $tournament->partidos = $matches;
            }

            return response()->json([
                'message' => 'Torneos devueltos con exito.',
                'data' => $tournaments
            ]);
        }catch(\Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ],500);
        }
    }

    public function tournamentMatchsById($tournament_id){
        try{

            $tournament = Turnament::where('id', $tournament_id)->with('estado')->first();

            if(!$tournament) {
                return response()->json([
                    'message' => 'El torneo que desea buscar no se encuentra.',
                ]);
            }

            $matches = Match::where('id_torneo', $tournament_id)
            ->with('fase','estadio','equipo_local','equipo_visitante','estado')->get();
            foreach($matches as $match) {
                $match->fase_nombre    = $match->fase->tipoPartido->partido .' - '. $match->fase->tipoFase->tipo;
                $match->partido_nombre = $match->equipo_local->nombre .' vs '. $match->equipo_visitante->nombre;

                $teamLocal = $match->equipo_local;
                $teamVisitante = $match->equipo_visitante;

                $file_path = $teamLocal->tipo->id == 1 ?
                    TeamController::PUBLIC_BASE_PATH . $teamLocal->id . '/' . $teamLocal->escudo :
                    TeamController::PUBLIC_BASE_PATH . $teamLocal->id . '/' . $teamLocal->bandera;
                $match->equipo_local->image_url = Storage::disk('public_proyect')->exists($file_path) ?
                    self::BASEPATH . $file_path :
                    self::BASEPATH . 'defaults-image/sin-imagen.png';

                $file_path = $teamVisitante->tipo->id == 1 ?
                    TeamController::PUBLIC_BASE_PATH . $teamVisitante->id . '/' . $teamVisitante->escudo :
                    TeamController::PUBLIC_BASE_PATH . $teamVisitante->id . '/' . $teamVisitante->bandera;
                $match->equipo_visitante->image_url = Storage::disk('public_proyect')->exists($file_path) ?
                    self::BASEPATH . $file_path :
                    self::BASEPATH . 'defaults-image/sin-imagen.png';
            }

            $tournament->partidos = $matches;

            return response()->json([
                'message' => 'Partidos devueltos con exito.',
                'data' => $tournament
            ]);
        }catch(\Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ],500);
        }
    }


    public function create(Request $request){
        try{
            if (!$request->has(['id_torneo','id_fase','matchs'])){
                return response()->json([
                    'message' => "Datos enviados son incorrectos."
                ],404);
            }

            $matches = $request->matchs;
            $id_torneo = $request->id_torneo;
            $id_fase = $request->id_fase;
            $ronda = $request->filled('ronda') ? $request->ronda : null;
            $grupo_id = $request->filled('id_grupo') ? $request->id_grupo : null;

            foreach ($matches as $match){
                $fecha  =  $match['fecha'];
//                $fecha = Carbon::createFromFormat('Y-m-d', $fecha);

                $fecha_vencimiento_pronostico = $match['fecha_vencimiento_pronostico'];
//                $fecha_vencimiento_pronostico = Carbon::createFromFormat('Y-m-d', $fecha_vencimiento_pronostico);

                $hora = $match['hora'];
                $hora = Carbon::createFromFormat('H:i', $hora);

                $match = Match::create([
                    'id_torneo' => $id_torneo,
                    'id_fase' => $id_fase,
                    'id_estadio' => $match['id_estadio'],
                    'id_equipo_1' => $match['id_equipo_1'],
                    'id_equipo_2' => $match['id_equipo_2'],
                    'fecha' => $fecha,
                    'fecha_vencimiento_pronostico' => $fecha_vencimiento_pronostico,
                    'hora' => $hora
                ]);

                if (!is_null($ronda) && !is_null($grupo_id)) {
                    MatchGroup::create([
                        'id_grupo' => $grupo_id,
                        'id_partido' => $match->id,
                        'ronda' => $ronda
                    ]);
                }

            }

            $tournament = Turnament::where('id', $id_torneo)->with('estado')->first();

            $matches = Match::where('id_torneo', $id_torneo)
            ->orderBy('id', 'DESC')
            ->with('fase','estadio','equipo_local','equipo_visitante','estado','match_group')->get();

            foreach($matches as $match) {
                $match->ronda =  isset($match->match_group) && !is_null($match->match_group) ? $match->match_group->ronda : null;
                $match->fase_nombre    = $match->fase->tipoPartido->partido .' - '. $match->fase->tipoFase->tipo;
                $match->partido_nombre = $match->equipo_local->nombre .' vs '. $match->equipo_visitante->nombre;
            }

            $tournament->partidos = $matches;

            return response()->json([
                'message' => 'Partidos creados con exito.',
                'data' => $tournament
            ]);
        }catch(\Exception $e){
            return response()->json([
                'message' => $e->getMessage() . ' - '. $e->getLine()
            ],500);
        }
    }

    public function update(Request $request, $id){
        try{
            $data = $request->all();
            $match = Match::where('id', $id)->first();
            $ronda = $request->filled('ronda') ? $request->ronda : null;
            $grupo_id = $request->filled('id_grupo') ? $request->id_grupo : null;


            if (is_null($match)) {
                return response()->json([
                    'message' => 'Error al buscar el partido N: '. $id.' no es posible actualizar',
                ]);
            }

            Match::updateOrCreate([
                'id' => $match->id
            ],[
                'id_estadio' => $data['id_estadio'],
                'id_equipo_1' => $data['id_equipo_1'],
                'id_equipo_2' => $data['id_equipo_2'],
                'goles_1' => $data['goles_1'],
                'goles_2' => $data['goles_2'],
                'penales_1' => $data['penales_1'],
                'penales_2' => $data['penales_2'],
                'fecha' => $data['fecha'],
                'fecha_vencimiento_pronostico' => $data['fecha_vencimiento_pronostico'],
                'hora' => $data['hora'],
                'id_estado' => $data['id_estado']
            ]);

            if (!is_null($ronda) && !is_null($grupo_id)) {
                MatchGroup::updateOrCreate([
                    'id_grupo' => $grupo_id,
                    'id_partido' => $match->id,
                ],[
                    'ronda' => $ronda,
                    'id_grupo' => $grupo_id,
                    'id_partido' => $match->id,
                ]);
            }

            $tournament = Turnament::where('id', $match->id_torneo)->with('estado')->first();

            $matches = Match::where('id_torneo', $match->id_torneo)
            ->orderBy('id', 'DESC')
            ->with('fase','estadio','equipo_local','equipo_visitante','estado','match_group')->get();

            foreach($matches as $match) {
                $match->ronda =  isset($match->match_group) && !is_null($match->match_group) ? $match->match_group->ronda : null;
                $match->fase_nombre    = $match->fase->tipoPartido->partido .' - '. $match->fase->tipoFase->tipo;
                $match->partido_nombre = $match->equipo_local->nombre .' vs '. $match->equipo_visitante->nombre;
            }

            $tournament->partidos = $matches;

            return response()->json([
                'message' => 'Partido editado con exito.',
                'data' => $tournament
            ]);
        }catch(\Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ],500);
        }
    }
}
