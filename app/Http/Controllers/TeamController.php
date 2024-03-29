<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;
use Illuminate\Support\Facades\Storage;

class TeamController extends Controller
{
    private const BASEPATH = "/storage/";
    const PUBLIC_BASE_PATH = 'teams/';

    public function list() {
        try{
            $teams = Team::with('tipo')->get();

            foreach ($teams as $team){
                $file_path = $team->tipo->id == 1 ? self::PUBLIC_BASE_PATH . $team->id . '/' . $team->escudo : self::PUBLIC_BASE_PATH . $team->id . '/' . $team->bandera;
                $team->image_url = Storage::disk('public_proyect')->exists($file_path) ? self::BASEPATH . $file_path : self::BASEPATH . 'defaults-image/sin-imagen.png';
            }

            return response()->json([
                'message' => 'Equipos devueltos con exito.',
                'data' => $teams
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

            $file_base_name = strtolower(str_replace(" ", "_", trim($data['nombre'])));
            $file_bandera   = $request->hasFile('bandera') ? $request->bandera : null;
            $file_escudo    = $request->hasFile('escudo') ? $request->escudo : null;

            $bandera_name   = $request->hasFile('bandera') ? 'bandera_'.$file_base_name.'.'.$file_bandera->extension() : $file_base_name;
            $escudo_name    = $request->hasFile('escudo') ? 'escudo_'.$file_base_name.'.'.$file_escudo->extension() : $file_base_name;

            $data['bandera'] = $bandera_name;
            $data['escudo'] = $escudo_name;

            $team = Team::create($data);

            $path = 'teams/'. $team->id;

            if(!is_null($file_bandera)){
                Storage::disk('public_proyect')->putFileAs($path, $file_bandera, $bandera_name);
            }
            if(!is_null($file_escudo)){
                Storage::disk('public_proyect')->putFileAs($path, $file_escudo, $escudo_name);
            }



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

    public function update(Request $request){
        try{
            $data = $request->all();
            $id = $data['id'];
            $team_temp = Team::whereId($id)->first();

            $file_base_name = strtolower(str_replace(" ", "_", trim($data['nombre'])));
            $path = 'teams/'. $id;

            if ($request->hasFile('escudo')) {
                if (Storage::disk('public_proyect')->exists($path.'/'.$team_temp->escudo)) {
                    Storage::disk('public_proyect')->delete($path.'/'.$team_temp->escudo);
                }

                $file_escudo    = $request->escudo;
                $escudo_name    = 'escudo_'.$file_base_name.'.'.$file_escudo->extension();
                Storage::disk('public_proyect')->putFileAs($path, $file_escudo, $escudo_name);
                $data['escudo'] = $escudo_name;
            }else{
                if(isset($data['escudo'])){
                    unset($data['escudo']);
                }
            }

            if($request->hasFile('bandera')){
                if (Storage::disk('public_proyect')->exists($path.'/'.$team_temp->bandera)) {
                    Storage::disk('public_proyect')->delete($path.'/'.$team_temp->bandera);
                }

                $file_bandera   = $request->bandera ;
                $bandera_name   = 'bandera_'.$file_base_name.'.'.$file_bandera->extension();
                Storage::disk('public_proyect')->putFileAs($path, $file_bandera, $bandera_name);
                $data['bandera'] = $bandera_name;
            }else{
                if(isset($data['bandera'])){
                    unset($data['bandera']);
                }
            }

            Team::whereId($id)
            ->update($data);

            $Team = Team::whereId($id)->with('tipo')->first();

            return response()->json([
                'message' => 'Equipo actualizado.',
                'data' => $Team
            ]);
        }catch(\Exception $e){
            return response()->json([
                'message' => $e->getMessage(),
                'linea' => $e->getLine()
            ],500);
        }
    }
}
