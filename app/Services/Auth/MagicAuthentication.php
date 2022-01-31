<?php

namespace App\Services\Auth;

use App\Models\MagicToken;
use App\Models\User;
use Illuminate\Http\Request;

class MagicAuthentication
{
    const INVALID_TOKEN = 'token.invalid';
    const AUTHENTICATED = 'authenticated';

    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function requestLink()
    {
        $user = $this->getUser();

        $user->createMagicToken()->send(['remember' => $this->request->has('remember')]);
    }

    private function getUser()
    {
        return User::where('email', $this->request->email)->firstOrFail();
    }

    public function authenticate(MagicToken $token)
    {
        $token->delete();

        if($token->isExpired()){
            return self::INVALID_TOKEN;
        }

        auth()->login($token->user, $this->request->query('remember'));

        return self::AUTHENTICATED;
    }
}
