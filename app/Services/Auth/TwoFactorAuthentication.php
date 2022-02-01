<?php

namespace App\Services\Auth;

use App\Models\TwoFactor;
use Illuminate\Http\Request;

class TwoFactorAuthentication
{
    const CODE_SENT = 'code.sent';

    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function requestCode()
    {
        $code = TwoFactor::generateCodeFor(auth()->user());

        $code->send();

        return static::CODE_SENT;
    }
}
