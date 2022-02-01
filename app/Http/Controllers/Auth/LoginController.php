<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Code;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Rules\Recaptcha;
use App\Services\Auth\TwoFactorAuthentication;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
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

    use ThrottlesLogins;

    protected $twoFactorAuth;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(TwoFactorAuthentication $twoFactorAuth)
    {
        $this->middleware('guest')->except('logout');

        $this->twoFactorAuth = $twoFactorAuth;
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showCodeForm()
    {
        return view('auth.two-factor.login-code');
    }

    public function login(Request $request)
    {
        $this->validateForm($request);

        if (!$this->isValidCredentials($request)) {
            $this->incrementLoginAttempts($request);
            return $this->sendLoginFailedResponse();
        }

        $user = $this->getUser($request);

        if($user->hasTwoFactor()){
            $this->twoFactorAuth->requestCode($user);

            return $this->sendHasTwoFactoResponse();
        }

        auth()->login($user, $request->filled('remember'));

        return $this->sendSuccessResponse();
    }

    private function validateForm(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|exists:users',
            'password' => 'required|string',
            'g-recaptcha-response' => ['required', new Recaptcha()]
        ], [
            'g-recaptcha-response.required' => __('auth.recaptcha')
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

    public function username()
    {
        return 'email';
    }

    private function isValidCredentials(Request $request)
    {
        return auth()->validate($request->only('email', 'password'));
    }

    private function getUser(Request $request)
    {
        return User::where('email', $request->email)->first();
    }

    private function sendHasTwoFactoResponse()
    {
        return redirect()->route('auth.login.code.form');
    }

    public function confirmCode(Code $request)
    {
        $response = $this->twoFactorAuth->login();

        return $response === $this->twoFactorAuth::AUTHENTICATED
            ? $this->sendSuccessResponse()
            : back()->with('invalidCode', true);
    }
}
