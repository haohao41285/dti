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

    public static function getThemePropertiesByThemeId($themeId){
        return self::where('theme_id',$themeId)->get();
    }

    public static function getThemePropertiesValueById($id){
        try {
            return self::select('theme_properties_value')
                    ->where('theme_properties_id',$id)
                    ->first()->theme_properties_value;
        } catch (\Exception $e) {
            return;
        }
        
    }
}