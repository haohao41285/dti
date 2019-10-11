<?php

namespace App\Models;

use App\Models\BaseModel;

class MainLoginBackground extends BaseModel
{
    protected $table = 'main_login_background';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'image',
    ];

    protected $guarded = [];

    protected $created_by = false;
    protected $updated_by = false;

        
}