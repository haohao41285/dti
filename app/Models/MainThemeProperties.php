<?php

namespace App\Models;

use App\Models\BaseModel;

class MainThemeProperties extends BaseModel
{
    protected $table = 'main_theme_properties';

    protected $primaryKey = "theme_properties_id";

    public $timestamps = true;

    protected $fillable = [
        'theme_properties_id',
        'theme_id',
        'theme_properties_name',
        'theme_properties_value',
        'theme_properties_image',
        'created_at',
        'updated_at',
    ];

    protected $guarded = [];

    protected $created_by = false;

    protected $updated_by = false;
}