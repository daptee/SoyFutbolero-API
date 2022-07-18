<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use  App\Models\{
    Stage
};

class StageController extends Controller
{
    public function list(){
        try{
            $stages = Stage::with(['tipoFase','tipoPartido'])->get();

            foreach( $stages as $stage){
                $stage->nombre = $stage->tipoFase->tipo .' - ' . $stage->tipoPartido->partido;
            }

            return response()->json([
                'data' => $stages
            ]);
        } catch (Exception $error) {
            return response()->json([
                'message' => $error->getMessage()
            ]);
        }
    }
}
