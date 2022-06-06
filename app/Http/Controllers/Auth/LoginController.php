<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use JWTAuth;
use JWT;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Services\JwtService;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return Response
     */
    public function login(Request $request)
    {
        if (Auth::attempt(['usuario' => $request->usuario , 'password' => $request->password, 'is_admin' => 1])) {
            // Authentication passed...
            return redirect()->intended('home');
        }

        return back()->withErrors([
            'usuario' => 'Usuario y/o clave invalidos.',
            'password' => 'Usuario y/o clave invalidos.'
        ]);
    }

    public function apiLogin(Request $request){
        $credentials = $request->only('usuario', 'password');

        if (! $token = JWTAuth::attempt($credentials))
            return response()->json(['message' => 'Usuario y/o clave no válidos.'], 400);

        return $this->respondWithToken($token);
    }

    public function logout(){
        Auth::logout();

        return redirect()->route('welcome');
    }

    public function username()
    {
        return 'usuario';
    }

    public function showLoginForm()
    {
      return view('auth.login');
    }




    protected function respondWithToken($token){
        $expire_in = config('jwt.ttl');

        return response()->json([
            'message' => 'Login exitoso.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $expire_in * 60
        ]);
    }


}
