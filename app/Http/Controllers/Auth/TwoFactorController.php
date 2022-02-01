<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\TwoFactorAuthentication;
use Illuminate\Http\Request;
use function GuzzleHttp\Promise\all;

class TwoFactorController extends Controller
{
    protected $twoFactorAuth;

    public function __construct(TwoFactorAuthentication $twoFactorAuth)
    {
        $this->middleware('auth');

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
        $response = $this->twoFactorAuth->requestCode();

        return $response === $this->twoFactorAuth::CODE_SENT
            ? redirect()->route('auth.two.factor.code.form')
            : back()->with('cantSendCode', true);
    }

    public function confirmCode(Request $request)
    {
        $this->validateForm($request);

        $response = $this->twoFactorAuth->activate();

        return $response === $this->twoFactorAuth::ACTIVATED
            ? redirect()->route('home')->with('twoFactorActivated', true)
            : back()->with('invalidCode', true);
    }

    private function validateForm(Request $request)
    {
        $request->validate([
            'code' => 'required|numeric|digits:4'
        ], [
            'code.digits' => __('auth.invalidCode')
        ]);
    }

    public function deactivate()
    {
        $this->twoFactorAuth->deactivate(auth()->user());

        return back()->with('twoFactorDeactivated', true);
    }
}
