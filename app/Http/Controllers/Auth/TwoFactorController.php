<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Code;
use App\Services\Auth\TwoFactorAuthentication;
use Illuminate\Http\Request;
use function GuzzleHttp\Promise\all;

class TwoFactorController extends Controller
{
    protected $twoFactorAuth;

    public function __construct(TwoFactorAuthentication $twoFactorAuth)
    {
        $this->middleware('auth')->except('resend');

        $this->twoFactorAuth = $twoFactorAuth;
    }

    public function showToggleForm()
    {
        return view('auth.two-factor.toggle');
    }

    public function showEnterCodeForm()
    {
        return view('auth.two-factor.enter-code');
    }

    public function activate()
    {
        $response = $this->twoFactorAuth->requestCode(auth()->user());

        return $response === $this->twoFactorAuth::CODE_SENT
            ? redirect()->route('auth.two.factor.code.form')
            : back()->with('cantSendCode', true);
    }

    public function confirmCode(Code $request)
    {
        $response = $this->twoFactorAuth->activate();

        return $response === $this->twoFactorAuth::ACTIVATED
            ? redirect()->route('home')->with('twoFactorActivated', true)
            : back()->with('invalidCode', true);
    }

    public function deactivate()
    {
        $this->twoFactorAuth->deactivate(auth()->user());

        return back()->with('twoFactorDeactivated', true);
    }

    public function resend()
    {
        $this->twoFactorAuth->resend();

        return back()->with('codeResent', true);
    }
}
