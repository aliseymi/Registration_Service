<?php

namespace App\Services\Notifications\Constants;

use App\Mail\ForgetPassword;
use App\Mail\TopicCreated;
use App\Mail\UserRegistered;

class EmailTypes
{
    const USER_REGISTERED = 1;
    const TOPIC_CREATED = 2;
    const FORGET_PASSWORD = 3;

    public static function toString(): array
    {
        return [
            self::USER_REGISTERED => 'ثبت نام کاربر',
            self::TOPIC_CREATED => 'ارسال مقاله جدید',
            self::FORGET_PASSWORD => 'فراموشی رمزعبور'
        ];
    }

    public static function toMailable(int $type)
    {
        try {
            return [
                self::USER_REGISTERED => UserRegistered::class,
                self::TOPIC_CREATED => TopicCreated::class,
                self::FORGET_PASSWORD => ForgetPassword::class
            ][$type];
        }catch(\Throwable $throwable){
            throw new \InvalidArgumentException('Mailable class does not exist');
        }
    }
}
