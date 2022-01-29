<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

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

//    use AuthenticatesUsers;

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

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $this->validateForm($request);

        if($this->attemptToLogin($request)){
            return $this->sendSuccessResponse();
        }

        return $this->sendLoginFailedResponse();
    }

    private function validateForm(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|exists:users',
            'password' => 'required|string'
        ]);
    }

    private function attemptToLogin(Request $request)
    {
        return auth()->attempt($request->only('email', 'password'), $request->filled('remember'));
    }

    private function sendSuccessResponse()
    {
        session()->regenerate();

        return redirect()->intended();
    }

    private function sendLoginFailedResponse()
    {
        return back()->with('wrongCredentials', true);
    }

    public function logout()
    {
        session()->invalidate();

        auth()->logout();

        return redirect()->route('home');
    }
}
