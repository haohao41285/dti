<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    
    protected $primaryKey = 'user_id';
    protected $table = 'main_user';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_nickname', 'user_phone', 'user_password', 'user_email',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'user_password', 'user_token',
    ];

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->user_password;
    }
    public function setPasswordAttribute($value)
    {
        $this->attributes['user_password'] = bcrypt($value);
    }
    public function getRememberTokenName(){
        return 'user_token';
    }
}
