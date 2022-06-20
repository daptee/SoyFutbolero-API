<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use  App\Models\Turnament;
use Carbon\Carbon;
use App\Services\JwtService;

class TurnamentController extends Controller
{
    public function list(){
        try{
            $turnaments = Turnament::with(['tipo','estado'])->get();


            return response()->json([
                'data' => $turnaments
            ]);
        } catch (Exception $error) {
            return response()->json([
                'message' => $error->getMessage()
            ]);
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



            return response()->json([
                'data' => $tournament
            ]);
        }catch (Exception $error){
            return response()->json([
                'message' => $error->getMessage()
            ],500);
        }
    }
}
