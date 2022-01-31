<?php

namespace App\Services\Auth\Traits;

use App\Models\MagicToken;
use Illuminate\Support\Str;

trait MagicallyAuthenticable
{
    public function magicToken()
    {
        return $this->hasOne(MagicToken::class);
    }

    public function createMagicToken()
    {
        $this->magicToken()->delete();

        return $this->magicToken()->create([
            'token' => Str::random(50)
        ]);
    }
}
