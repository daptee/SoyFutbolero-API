<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Services\JwtService;
use App\Mail\ResetPasword;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

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

            if( User::where('usuario',$data['usuario'])->exists() || User::where('mail',$data['mail'])->exists() ){
                return response()->json([
                    'message' => "Usuarios y/o mail ya se encuentran registrados."
                ],400);
            }

            $data['password'] = bcrypt($data['password']);

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

    public function editProfile(Request $request){
        try{
            $id = JwtService::getUser()->id;
            $data = $request->all();

            if(User::where('usuario',$request->usuario)->exists() || User::where('mail',$request->mail)->exists()){
                return response()->json([
                    'message' => "Usuarios y/o mail ya se encuentran registrados."
                ],400);
            }

            $path = 'users/'.$id;

            if( $request->hasFile('foto') ){
                $user = User::select('foto')->where('id',$id)->first();


                if (Storage::disk('public')->exists($path.'/'.$user->foto)) {
                    Storage::disk('public')->delete($path.'/'.$user->foto);
                }

                $file_foto    = $request->foto;
                $foto_name    = 'user_'.$id.'_profile.'.$file_foto->extension();
                Storage::disk('public')->putFileAs($path, $file_foto, $foto_name);
                $data['foto'] = $foto_name;
            }

            if($request->has('password') ){
                $data['password'] = bcrypt($request->password);
            }

            User::where('id',$id)->update($data);

            $user = User::where('id',$id)->with(['genero'])->first();
            $user->foto_url = Storage::disk('public')->exists($path.'/'.$user->foto) ? Storage::disk('public')->url($path.'/'.$user->foto) : null;

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

    public function register(Request $request){
        try{
            $data = $request->all();

            if( User::where('usuario',$data['usuario'])->exists() || User::where('mail',$data['mail'])->exists() ){
                return response()->json([
                    'message' => "Usuarios y/o mail ya se encuentran registrados."
                ],400);
            }

            $password = $data['password'];
            $data['password'] = bcrypt($password);

            $user = User::create($data);

            $user = User::where('id',$user->id)->with(['genero'])->first();

            $credentials = [
                'usuario' => $user->usuario,
                'password' => $password,
            ];

            $token = auth()->attempt($credentials);

            $data = array_merge($this->respondWithToken($token),[ 'message' => 'Usuario creado con exito.', 'usuario' => $user ], );

            return response()->json($data);
        }catch(Exception $error){
            return response()->json([
                'message' => $error->getMessage()
            ],500);
        }
    }

    public function resetPassword(Request $request){
        try{
            if(!$request->has('usuario')){
                return response()->json([
                    'message' => "Datos invalidos."
                ],400);
            }


            $usuario = $request->usuario;
            $password = Str::random(15);
            $encryopted = bcrypt($password);

            $user_updated = User::where('usuario',$usuario)
            ->update([
                'password' => bcrypt($password)
            ]);

            if ($user_updated != 1) {
                return response()->json([
                    'message' => 'Error al restablecer la clave.'
                ],500);
            }

            $user = User::where('usuario',$usuario)->first();

            $data = [
                'password' => $password
            ];

            Mail::to($user->mail)->send(new ResetPasword($data));

            return response()->json([
                'message' => 'Se restablecio la clave. Por favor revise su email.',
            ]);
        }catch(Exception $error){
            return response()->json([
                'message' => $error->getMessage()
            ],500);
        }
    }

    protected function respondWithToken($token){
        $expire_in = config('jwt.ttl');

        return[
            'message' => 'Login exitoso.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $expire_in * 60
        ];
    }

}
