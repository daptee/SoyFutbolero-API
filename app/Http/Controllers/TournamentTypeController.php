<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use  App\Models\{
    TurnamentType
};

class TournamentTypeController extends Controller
{
    public function list(){
        try{
            $types = TurnamentType::all();


            return response()->json([
                'data' => $types
            ]);
        } catch (Exception $error) {
            return response()->json([
                'message' => $error->getMessage()
            ]);
        }
    }
}
