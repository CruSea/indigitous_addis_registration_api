<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function qrImage()
    {
        return $this->hasOne('App\UserQR', 'user_id');
    }

    public function attendant()
    {
        return $this->hasOne('App\Attendant', 'user_id');
    }

    public function roles()
    {
        return $this->hasOne('App\UserRole', 'id', 'role_id');
    }
}
