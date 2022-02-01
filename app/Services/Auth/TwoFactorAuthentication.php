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

    protected $request;

    protected $code;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function requestCode()
    {
        $code = TwoFactor::generateCodeFor(auth()->user());

        $this->setSession($code);

        $code->send();

        return static::CODE_SENT;
    }

    protected function setSession(TwoFactor $code)
    {
        session([
            'code_id' => $code->id,
            'user_id' => $code->user_id
        ]);
    }

    public function activate()
    {
        if(!$this->isValidCode()) return static::INVALID_CODE;

        $this->getToken()->delete();

        $this->getUser()->activateTwoFactor();

        return static::ACTIVATED;
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
}
