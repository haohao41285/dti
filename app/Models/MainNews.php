<?php

namespace App\Models;

use App\Models\BaseModel;
use Auth;
use DataTables;

class MainNews extends BaseModel
{
    protected $table = 'main_news';

    public $timestamps = true;

    protected $fillable = [
        'news_id',
        'title',
        'slug',
        'content',
        'image',
        'created_at',
        'updated_at',
        'news_type_id',
    ];

    protected $guarded = [];

    public static function getDatatableByNewsTypeId($newsTypeId){
        $news = self::where('news_type_id',$newsTypeId)->where('news_status',1)->get();

        return DataTables::of($news)
        ->editColumn('created_at',function($data){
            return format_datetime($data->created_at);
        })
        ->make(true);
    }



        
}