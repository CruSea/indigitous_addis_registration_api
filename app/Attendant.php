<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Attendant extends Model
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'coordinator_id', 'round_id', 'is_confirmed', 'age', 'sex', 'region', 'city', 'unique_code', 'contact_relative_name', 'contact_relative_phone',
        'denomination', 'status', 'is_married', 'is_disabled', 'profession', 'academic_status', 'conference_year'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

    ];

    public function qrImage()
    {
        return $this->belongsTo('App\UserQR', 'user_id', 'user_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function coordinator()
    {
        return $this->belongsTo('App\Coordinator', 'coordinator_id');
    }

    public function round()
    {
        return $this->belongsTo('App\Round', 'round_id');
    }
}
