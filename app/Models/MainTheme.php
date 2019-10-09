<?php

namespace App\Models;

use App\Models\BaseModel;

class MainTheme extends BaseModel
{
    protected $table = 'main_theme';

    protected $primaryKey = 'theme_id';

    public $timestamps = true;

    protected $fillable = [
        'theme_id',
        'theme_name',
        'theme_image',
        'theme_url',
        'theme_price',
        'theme_descript',
        'theme_name_temp',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'theme_status',
        'theme_license',
    ];

    protected $guarded = [];

        
}