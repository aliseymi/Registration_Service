<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class SocialController extends Controller
{
    public function redirectToProvider($driver)
    {
        return Socialite::driver($driver)->redirect();
    }

    public function providerCallback($driver)
    {
        $user = Socialite::driver($driver)->user();

        auth()->login($this->findOrCreateUser($user, $driver));

        return redirect()->intended();
    }

    private function findOrCreateUser($user, $provider)
    {
        $providerUser = User::where([
            'email' => $user->getEmail()
        ])->first();

        if(!is_null($providerUser)) return $providerUser;

        return User::create([
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'provider' => $provider,
            'provider_id' => $user->getId(),
            'avatar' => $user->getAvatar(),
            'email_verified_at' => now()
        ]);
    }
}
