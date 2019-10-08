<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Helpers\GeneralHelper;
use Auth;
use DataTables;

class MainNewsType extends BaseModel
{
    protected $table = 'main_news_type';

    public $timestamps = true;

    protected $fillable = [
        'news_type_id',
        'title',
        'slug',
        'created_at',
        'updated_at',
        'news_type_status',
    ];

    protected $guarded = [];

    public static function getDatatable(){
        $newsType = self::where('news_type_status',1)->get();

        return DataTables::of($newsType)
        ->editColumn('created_at',function($data){
            return format_datetime($data->created_at);
        })
        ->make(true);
    }



        
}