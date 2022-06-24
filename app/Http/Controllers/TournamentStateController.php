<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use  App\Models\{
    TurnamentState
};

class TournamentStateController extends Controller
{
    public function list(){
        try{
            $states = TurnamentState::all();


            return response()->json([
                'data' => $states
            ]);
        } catch (Exception $error) {
            return response()->json([
                'message' => $error->getMessage()
            ]);
        }
    }
}
