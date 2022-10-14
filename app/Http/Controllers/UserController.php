<?php

namespace App\Http\Controllers;

use App\Mail\ConfirmMail;
use App\Models\DesafioUsuario;
use App\Models\Turnament;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\JwtService;
use App\Mail\ResetPasword;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    private const BASEPATH = "/storage/";

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
            $user = User::where('id',$id)->with(['genero', 'medallero', 'medallero.torneo'])->first();

            foreach($user->medallero as $medallero) {
                $tournament = $medallero->torneo;
                $file_path =  'tournaments/'. $tournament->id . '/' . $tournament->directorio;
                $tournament->image_url = Storage::disk('public_proyect')->exists($file_path) ? self::BASEPATH . $file_path : self::BASEPATH . 'defaults-image/sin-imagen.png';
            }

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

            if(User::where('usuario',$request->usuario)->where("id","!=",$id)->exists() || User::where('mail',$request->mail)->where("id","!=",$id)->exists()){
                return response()->json([
                    'message' => "Usuarios y/o mail ya se encuentran registrados."
                ],400);
            }

            $path = 'users/'.$id;

            if( $request->hasFile('foto') ){
                $user = User::select('foto')->where('id',$id)->first();


                if (Storage::disk('public_proyect')->exists($path.'/'.$user->foto)) {
                    Storage::disk('public_proyect')->delete($path.'/'.$user->foto);
                }

                $file_foto    = $request->foto;
                $foto_name    = 'user_'.$id.'_profile.'.$file_foto->extension();
                Storage::disk('public_proyect')->putFileAs($path, $file_foto, $foto_name);
                $data['foto'] = $foto_name;
            }

            if($request->has('password') ){
                $data['password'] = bcrypt($request->password);
            }

            User::where('id',$id)->update($data);

            $user = User::where('id',$id)->with(['genero'])->first();
            $user->foto_url = Storage::disk('public_proyect')->exists($path.'/'.$user->foto) ? self::BASEPATH . $path.'/'.$user->foto : self::BASEPATH . 'defaults-image/sin-imagen.png';

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

            $userCreated = User::create($data);

//            DesafioUsuario::update(['usuario_mail', $data['mail']], ['usuario_id', $userCreated->id]);
            DesafioUsuario::where('usuario_mail', $data['mail'])->update(['usuario_id' => $userCreated->id]);

            Mail::to($data['mail'])->send(new ConfirmMail($userCreated));

//            $user = User::where('id',$user->id)->with(['genero'])->first();
            // TRAIGO TODA LA MISMA INFO QUE AL HACER LOGIN
            $user = User::where('id',$userCreated->id)->with(['genero', 'usuarios_torneo', 'usuarios_torneo.estado'])->first();
            $path = 'users/'.$user->id;
            $user->foto_url = Storage::disk('public_proyect')->exists($path.'/'.$user->foto) ? self::BASEPATH . $path.'/'.$user->foto : self::BASEPATH . 'defaults-image/sin-imagen.png';

            foreach($user->usuarios_torneo as $usuarioTorneo) {
                $tournament = Turnament::whereId($usuarioTorneo->id_torneo)->first();
                $file_path =  'tournaments/'. $tournament->id . '/' . $tournament->directorio;
                $tournament->image_url = Storage::disk('public_proyect')->exists($file_path) ? self::BASEPATH . $file_path : self::BASEPATH . 'defaults-image/sin-imagen.png';
                $usuarioTorneo->torneo = $tournament;
            }

            $credentials = [
                'usuario' => $user->usuario,
                'password' => $password,
            ];

            $token = auth()->attempt($credentials);

//            $data = array_merge($this->respondWithToken($token),[ 'message' => 'Usuario creado con exito.', 'usuario' => $user ], );
//            return response()->json($data);
            return $this->respondWithToken($token,$user);
        }catch(Exception $error){
            return response()->json([
                'message' => $error->getMessage()
            ],500);
        }
    }

    public function resetPassword(Request $request){
        try{
            if(!$request->has('mail')){
                return response()->json([
                    'message' => "Datos invalidos."
                ],400);
            }


            $mail = $request->mail;
            $password = Str::random(15);
//            $encryopted = bcrypt($password);

            $user = User::where('mail', $mail)->first();

            if (!$user) {
                return response()->json([
                    'message' => 'El correo ingresado es incorrecto'
                ],400);
            }

            $user_updated = $user->update([
                'password' => bcrypt($password)
            ]);

            if ($user_updated != 1) {
                return response()->json([
                    'message' => 'Error al restablecer la clave.'
                ],500);
            }

            $data = [
                'password' => $password
            ];

            Mail::to($mail)->send(new ResetPasword($data));

            return response()->json([
                'message' => 'Se restablecio la clave. Por favor revise su email.',
            ]);
        }catch(Exception $error){
            return response()->json([
                'message' => $error->getMessage()
            ],500);
        }
    }

    public function sendConfirmMail (Request $request) {
        try {
            if(!$request->has(['mail'])){
                return response()->json([
                    'message' => "Datos incompletos.",
                ], 400);
            }

            $mail = $request->mail;

            $user = User::where('mail', $mail)->first();
            if (!$user) {
                return response()->json([
                    'message' => "No existe usuario con este mail",
                ], 400);
            }

            Mail::to($mail)->send(new ConfirmMail($user));

            return response()->json([
                'message' => 'Mail enviado correctamente.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function confirmMail (Request $request) {
        try{
            if(!$request->has('id')){
                return response()->json([
                    'message' => "Datos invalidos."
                ],400);
            }

            $id = Crypt::decryptString($request->id);

            $user = User::where('id', $id)->first();

            if (!$user) {
                return response()->json([
                    'message' => 'El ID recibido es incorrecto'
                ],400);
            }

            $user_updated = $user->update([
                'confirma_mail' => 1,
                'email_verified_at' => date('Y-m-d h:i:s')
            ]);

            if ($user_updated != 1) {
                return response()->json([
                    'message' => 'Error al confirmar el mail.'
                ],500);
            }

            return response()->json([
                'message' => 'Se confirmo correctamente su cuenta.',
            ]);
        }catch(Exception $error){
            return response()->json([
                'message' => $error->getMessage()
            ],500);
        }
    }

    protected function respondWithToken($token, $user){
        $expire_in = config('jwt.ttl');

        return response()->json([
            'message' => 'Registro exitoso.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $expire_in * 60,
            'usuario' =>  $user
        ]);
    }

}
