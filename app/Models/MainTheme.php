<?php

namespace App\Models;

use App\Models\BaseModel;
use DataTables;

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
        'theme_booking_css',
        'theme_booking_js',
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

    public static function getDatatable(){
        $data = self::all();

        return DataTables::of($data)
        ->editColumn('theme_image',function($data){
            return "<img style='height: 4rem;' src='".env('URL_FILE_VIEW').$data->theme_image."' alt=''>";
        })
        ->editColumn('created_at',function($data){
            return format_datetime($data->created_at);
        })
        ->editColumn('theme_status',function($data){
            $checked = null;
            if($data->theme_status == 1)
                $checked = "checked";
            return '<input type="checkbox" class="js-switch-datatable changeStatus" '.$checked.' data="'.$data->theme_id.'" />';
        })
        ->addColumn('action', function ($data){
                    return '<a class="btn btn-sm btn-secondary" target="_blank" href="'.$data->theme_url.'" data-toggle="tooltip" title="Demo"><i class="fas fa-link"></i></a>
                    <a class="btn btn-sm btn-secondary edit" data="'.$data->theme_id.'" href="#" data-toggle="tooltip" title="Edit"><i   class="fas fa-edit"></i></a>
                    <a class="btn btn-sm btn-secondary delete" data="'.$data->theme_id.'" href="#" data-toggle="tooltip" ><i  title="Delete" class="fas fa-trash"></i></a>
                    <a class="btn btn-sm btn-secondary setup-properties" data="'.$data->theme_id.'" href="#" data-toggle="tooltip" title="Setup properties"><i   class="fas fa-cogs"></i> </a>
                    <a class="btn btn-sm btn-secondary add-order" data="'.$data->theme_id.'" href="javascript:void(0)" data-toggle="tooltip" title="Add Order"><i   class="fas fa-plus"></i> </a>';
            })
        ->rawColumns(['theme_image','theme_status','action'])
        ->make(true);
    }
}