<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{
    Match,
    Stadium,
    Stage,
    Team,
    Turnament
};
use Carbon\Carbon;

class MatchController extends Controller
{

    public function list(){
        try{

            $tournaments = Turnament::whereIn('id_estado', [1,2])
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
                ->with('fase','estadio','equipo_local','equipo_visitante')->get();
                foreach($matches as $match) {
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

            $tournament = Turnament::where('id', $tournament_id)->first();

            if(!$tournament) {
                return response()->json([
                    'message' => 'El torneo que desea buscar no se encuentra.',
                ]);
            }

            $matches = Match::where('id_torneo', $tournament_id)
            ->orderBy('id', 'DESC')
            ->with('fase','estadio','equipo_local','equipo_visitante')->get();
            foreach($matches as $match) {
                $match->partido_nombre = $match->equipo_local->nombre .' vs '. $match->equipo_visitante->nombre;
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

            foreach ($matches as $match){
                $fecha  =  $match['fecha'];
                $fecha = Carbon::createFromFormat('Y-m-d', $fecha);

                $fecha_vencimiento_pronostico = $match['fecha_vencimiento_pronostico'];
                $fecha_vencimiento_pronostico = Carbon::createFromFormat('Y-m-d', $fecha_vencimiento_pronostico);

                $hora = $match['hora'];
                $hora = Carbon::createFromFormat('H:i', $hora);

                Match::create([
                    'id_torneo' => $id_torneo,
                    'id_fase' => $id_fase,
                    'id_estadio' => $match['id_estadio'],
                    'id_equipo_1' => $match['id_equipo_1'],
                    'id_equipo_2' => $match['id_equipo_2'],
                    'fecha' => $fecha,
                    'fecha_vencimiento_pronostico' => $fecha_vencimiento_pronostico,
                    'hora' => $hora
                ]);
            }

            return response()->json([
                'message' => 'Partidos creados con exito.',
                'data' => $matches
            ]);
        }catch(\Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ],500);
        }
    }

    public function update(Request $request){
        try{
            if (!$request->has(['id_torneo','id_fase','matchs'])){
                return response()->json([
                    'message' => "Datos enviados son incorrectos."
                ],404);
            }

            $matches = $request->matchs;
            $id_torneo = $request->id_torneo;
            $id_fase = $request->id_fase;


            foreach ($matches as $match){
                $fecha  =  $match['fecha'];
                $fecha = Carbon::createFromFormat('Y-m-d', $fecha);

                $fecha_vencimiento_pronostico = $match['fecha_vencimiento_pronostico'];
                $fecha_vencimiento_pronostico = Carbon::createFromFormat('Y-m-d', $fecha_vencimiento_pronostico);

                $hora = $match['hora'];
                $hora = Carbon::createFromFormat('H:i', $hora);

                Match::create([
                    'id_torneo' => $id_torneo,
                    'id_fase' => $id_fase,
                    'id_estadio' => $match['id_estadio'],
                    'id_equipo_1' => $match['id_equipo_1'],
                    'id_equipo_2' => $match['id_equipo_2'],
                    'fecha' => $fecha,
                    'fecha_vencimiento_pronostico' => $fecha_vencimiento_pronostico,
                    'hora' => $hora
                ]);
            }

            return response()->json([
                'message' => 'Partidos creados con exito.',
                'data' => $matches
            ]);
        }catch(\Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ],500);
        }
    }
}
