<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class MainLoginBackground extends Model
{
    protected $table = 'main_login_background';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'image',
    ];

    protected $guarded = [];

        
}