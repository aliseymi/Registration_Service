<?php

namespace App\Services\Auth\Traits;

use App\Models\TwoFactor;

trait HasTwoFactorAuth
{
    public function code()
    {
        return $this->hasOne(TwoFactor::class);
    }
}
