<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stadium;

class StadiumController extends Controller
{
    public function list() {
        try{
            $stadium = Stadium::with('team')->get();

            return response()->json([
                'message' => 'Estadios devueltos con exito.',
                'data' => $stadium
            ]);
        }catch(\Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ],500);
        }
    }

    public function getById($id){
        try{
            $stadium = Stadium::whereId($id)->with('team')->first();

            return response()->json([
                'message' => 'Estadio creado.',
                'data' => $stadium
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

            $stadium = Stadium::create($data);

            $stadium = Stadium::whereId($stadium->id)->with('team')->first();

            return response()->json([
                'message' => 'Estadio creado.',
                'data' => $stadium
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
            if(is_null($data['id_equipo'])) {
                $data['id_equipo'] = 0;
            }

            Stadium::whereId($id)
            ->update($data);

            $stadium = Stadium::whereId($id)->with('team')->first();

            return response()->json([
                'message' => 'Estadio actualizado.',
                'data' => $stadium
            ]);
        }catch(\Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ],500);
        }
    }
}
