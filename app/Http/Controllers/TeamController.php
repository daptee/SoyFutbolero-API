<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;

class TeamController extends Controller
{
    public function list() {
        try{
            $team = Team::with('tipo')->get();

            return response()->json([
                'message' => 'Equipos devueltos con exito.',
                'data' => $team
            ]);
        }catch(\Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ],500);
        }
    }

    public function getById($id){
        try{
            $team = Team::whereId($id)->with('tipo')->first();

            if(is_null($team)){
                return response()->json([
                    'message' => 'No se encontro equipos con el Id: '.$id,
                ],404);
            }

            return response()->json([
                'message' => 'Equipo devuelto con exito.',
                'data' => $team
            ]);
        }catch(\Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ],500);
        }
    }

    public function create(Request $request){
        try{
            $data = $request->all();

            $team = Team::create($data);

            $team = Team::whereId($team->id)->with('tipo')->first();

            return response()->json([
                'message' => 'Equipo creado.',
                'data' => $team
            ]);
        }catch(\Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ],500);
        }
    }

    public function update(Request $request, $id){
        try{
            $data = $request->all();

            Team::whereId($id)
            ->update($data);

            $Team = Team::whereId($id)->with('tipo')->first();

            return response()->json([
                'message' => 'Equipo actualizado.',
                'data' => $Team
            ]);
        }catch(\Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ],500);
        }
    }
}
