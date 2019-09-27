<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainThemeProperties extends Model
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

        
}