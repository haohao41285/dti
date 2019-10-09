<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainApp extends BaseModel
{
    protected $table = 'main_app';

    protected $primaryKey = 'app_id';

    public $timestamps = false;

    protected $fillable = [
        'app_id',
        'app_name',
        'app_desc',
    ];

    protected $guarded = [];

    protected $created_by = false;
    protected $updated_by = false;

        
}