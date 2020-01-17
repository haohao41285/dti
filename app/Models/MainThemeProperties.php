<?php

namespace App\Models;

use App\Models\BaseModel;
use DataTables;

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

    public static function getDatatablebyThemeId($themeId){
        $data = self::getThemePropertiesByThemeId($themeId);

        return DataTables::of($data)
        ->editColumn('theme_properties_image',function($data){
            return "<img height='150' src='".config('app.url_file_view').$data->theme_properties_image."' alt=''/>";
        })
         ->addColumn('action',function($data){
                return '<a href="#" data-id="'.$data->theme_properties_id.'"" class="update btn btn-sm btn-secondary" ><i class="fa fa-edit"></i></a>';
                        
            })
        ->rawColumns(['theme_properties_image','action'])
        ->make(true);
        //
    }

    public static function getBythemePropertiesId($id){
        return self::select("theme_properties_id","theme_properties_name","theme_id")
                    ->where("theme_properties_id",$id)
                    ->first();
    }
}