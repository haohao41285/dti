<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PosUsergroup
 */
class PosForgotPassword extends Model
{
    protected $table = 'pos_forgot_password';

    public $timestamps = false;

    public static function boot()
    {
        parent::boot();
    }

    protected $fillable = [
        'fp_id',
        'fp_place_id',
        'fp_user_id',
        'fp_address_ip',
        'fp_datetime',
        'fp_status'
    ];

    protected $guarded = [];

        
}