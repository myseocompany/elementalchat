<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Support\Facades\Cookie;

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
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated(Request $request, $user)
    {
        
        $cookieValue = $request->cookie('unique_machine');
        // Verificar si la cookie existe
        if (is_null($cookieValue)) {
            // Crea un nuevo valor de cookie y obtén ese valor
            $cookieValue = md5(uniqid(rand(), true));
            // Coloca la cookie en la cola para que se adjunte a la próxima respuesta
            Cookie::queue('unique_machine', $cookieValue, 60 * 24 * 365);
        }

        // Guardar el valor de la cookie en el usuario
        $user->unique_machine = $cookieValue;
        $user->save();


        // Continuar con la redirección predeterminada
        return redirect()->intended($this->redirectPath());
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        // Intenta autenticar al usuario
        if ($this->attemptLogin($request)) {
            $user = $this->guard()->user();
            if ($user->status_id == 1) {
                // El usuario tiene status_id 1, permitir login
                return $this->sendLoginResponse($request);
            } else {
                // El usuario no tiene status_id 1, no permitir login
                $this->guard()->logout();
                $request->session()->invalidate();
                return redirect()->back()
                    ->withInput($request->only($this->username(), 'remember'))
                    ->withErrors(['active' => 'Your account is not active.']);
            }
        }

        // Si la autenticación falla, enviar de vuelta con errores
        return $this->sendFailedLoginResponse($request);
    }
}
