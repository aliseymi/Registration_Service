<?php

namespace App\Services\Notifications;

use App\Models\User;
use App\Services\Notifications\Providers\Contracts\Provider;
use Illuminate\Contracts\Mail\Mailable;

/**
 * @method sendEmail(User $user, Mailable $mailable)
 * @method sendSms(User $user, string $text)
 */

class Notification
{
    public function __call($name, $arguments)
    {
        $providerPath = __NAMESPACE__ . '\Providers\\' . substr($name, 4) . 'Provider';

        if(!class_exists($providerPath)){
            throw new \Exception("Class $providerPath does not exist!");
        }

        $providerInstance = new $providerPath(...$arguments);

        if(!is_subclass_of($providerInstance, Provider::class)){
            throw new \Exception("Class must implements \App\Services\Notifications\Providers\Contracts\Provider");
        }

        $providerInstance->send();
    }
}
