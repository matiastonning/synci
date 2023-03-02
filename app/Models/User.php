<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'uuid',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function inboundEmails()
    {
        return $this->hasMany(InboundEmail::class);
    }

    public function destinations()
    {
        return $this->hasMany(Destination::class);
    }

    public function sources()
    {
        return $this->hasMany(Source::class);
    }

    public function connectionLinks()
    {
        return $this->hasMany(ConnectionLink::class);
    }

    public function apiCredentials()
    {
        return $this->hasMany(ApiCredential::class);
    }

    public function transferLogs()
    {
        return $this->hasMany(TransferLog::class);
    }

    // this is a recommended way to declare event handlers
    public static function boot() {
        parent::boot();

        static::deleting(function($user) { // before delete() method call this
            $user->transactions()->delete();
            $user->inboundEmails()->delete();
            $user->connectionLinks()->delete();
            $user->sources()->delete();
            $user->destinations()->delete();
            $user->apiCredentials()->delete();
            $user->transferLogs()->delete();
        });
    }
}
