<?php

namespace App\Models;

use App\Jobs\SendSms;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwoFactor extends Model
{
    use HasFactory;

    const CODE_EXPIRY = 60; // seconds

    protected $table = 'two_factor';

    protected $fillable = [
        'user_id',
        'code'
    ];

    public static function generateCodeFor(User $user)
    {
        $user->code()->delete();

        return static::create([
            'user_id' => $user->id,
            'code' => mt_rand(1000,9999)
        ]);
    }

    public function getCodeForSendAttribute()
    {
        return __('auth.codeForSend', ['code' => $this->code]);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function send()
    {
        SendSms::dispatch($this->user, $this->codeForSend);
    }

    public function isExpired()
    {
        return $this->created_at->diffInSeconds(now()) > static::CODE_EXPIRY;
    }

    public function isEqualWith(string $code)
    {
        return $this->code === $code;
    }
}
