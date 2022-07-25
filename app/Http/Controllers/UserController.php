<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function list(){
        try{
            $users = User::with(['genero'])->orderBy('usuario','asc')->get();

            if($users->count() == 0){
                return response()->json([
                    'message' => 'No se encontro ningun usuario.'
                ],404);
            }

            foreach($users as $user){
                $user->nombre_completo = $user->apellido . ' '.$user->nombre;
            }

            return response()->json([
                'message' => 'Usuarios devueltos con exito.',
                'data' => $users
            ]);
        }catch(Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ],500);
        }
    }

    public function getById($id){
        try{
            $user = User::where('id',$id)->with(['genero'])->first();

            if(!$user){
                return response()->json([
                    'message' => 'No se encontro ningun usuario.'
                ],404);
            }

            return response()->json([
                'message' => 'Usuario devuelto con exito.',
                'data' => $user
            ]);
        }catch(Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ],500);
        }
    }

    public function create(Request $request){
        try{
            $data = $request->all();

            $data['password'] = $data['is_admin'] == 1 ? bcrypt($data['password']) : md5($data['password']);

            $user = User::create($data);

            $user = User::where('id',$user->id)->with(['genero'])->first();

            return response()->json([
                'message' => 'Usuario creado con exito.',
                'data' => $user
            ]);
        }catch(Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ],500);
        }
    }

    public function update(Request $request, $id){
        try{
            $data = $request->all();

            User::where('id',$id)->update($data);

            $user = User::where('id',$id)->with(['genero'])->first();

            if(!$user){
                return response()->json([
                    'message' => 'No se encontro ningun usuario.'
                ],404);
            }

            return response()->json([
                'message' => 'Usuario actualizado con exito.',
                'data' => $user
            ]);
        }catch(Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ],500);
        }
    }


}
