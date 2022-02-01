<?php

namespace App\Services\Auth;

use App\Models\TwoFactor;
use App\Models\User;
use Illuminate\Http\Request;

class TwoFactorAuthentication
{
    const CODE_SENT = 'code.sent';
    const INVALID_CODE = 'code.invalid';
    const ACTIVATED = 'activated';
    const AUTHENTICATED = 'code.authenticated';

    protected $request;

    protected $code;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function requestCode(User $user)
    {
        $code = TwoFactor::generateCodeFor($user);

        $this->setSession($code);

        $code->send();

        return static::CODE_SENT;
    }

    protected function setSession(TwoFactor $code)
    {
        session([
            'code_id' => $code->id,
            'user_id' => $code->user_id,
            'remember' => $this->request->remember
        ]);
    }

    protected function forgetSession()
    {
        session(['code_id', 'user_id', 'remember']);
    }

    public function activate()
    {
        if(!$this->isValidCode()) return static::INVALID_CODE;

        $this->getToken()->delete();

        $this->getUser()->activateTwoFactor();

        $this->forgetSession();

        return static::ACTIVATED;
    }

    public function login()
    {
        if(!$this->isValidCode()) return static::INVALID_CODE;

        $this->getToken()->delete();

        auth()->login($this->getUser(), session('remember'));

        $this->forgetSession();

        return static::AUTHENTICATED;
    }

    protected function isValidCode()
    {
        return !$this->getToken()->isExpired() && $this->getToken()->isEqualWith($this->request->code);
    }

    protected function getToken()
    {
        return $this->code ?? $this->code = TwoFactor::findOrFail(session('code_id'));
    }

    protected function getUser()
    {
        return User::findOrFail(session('user_id'));
    }

    public function deactivate(User $user)
    {
        return $user->deactivateTwoFactor();
    }
}
