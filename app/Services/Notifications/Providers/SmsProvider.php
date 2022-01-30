<?php

namespace App\Services\Notifications\Providers;

use App\Models\User;
use App\Services\Notifications\Exceptions\UserDoesNotHaveNumberException;
use App\Services\Notifications\Providers\Contracts\Provider;
use Ghasedak\Exceptions\ApiException;
use Ghasedak\Exceptions\HttpException;
use Ghasedak\GhasedakApi;

class SmsProvider implements Provider
{
    private $user;
    private $text;

    public function __construct(User $user, string $text)
    {
        $this->user = $user;

        $this->text = $text;
    }

    public function send()
    {
        try {

            $this->hasPhone();

            $receptor = $this->user->phone_number;
            $line_number = config('services.ghasedakSms.line_number');
            $api_key = config('services.ghasedakSms.key');

            $api = new GhasedakApi($api_key);
            $api->SendSimple(
                $receptor,  // receptor
                $this->text, // message
                $line_number    // choose a line number from your account
            );
        } catch (ApiException $e) {
            throw $e;
        } catch (HttpException $e) {
            throw $e;
        }
    }

    protected function hasPhone(): void
    {
        if (is_null($this->user->phone_number)) {
            throw new UserDoesNotHaveNumberException('User does not have phone number');
        }
    }
}
