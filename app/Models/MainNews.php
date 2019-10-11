<?php

namespace App\Models;

use App\Models\BaseModel;
use Auth;
use DataTables;

class MainNews extends BaseModel
{
    protected $table = 'main_news';

    public $timestamps = true;

    protected $primaryKey = 'news_id';

    protected $fillable = [
        'news_id',
        'title',
        'slug',
        'short_content',
        'content',
        'image',
        'created_at',
        'updated_at',
        'news_type_id',
        'news_status',
    ];

    protected $created_by = false;

    protected $updated_by = false;

    protected $guarded = [];

    public static function getDatatableByNewsTypeId($newsTypeId){
        $news = self::where('news_type_id',$newsTypeId)->where('news_status',1)->get();

        return DataTables::of($news)
        ->editColumn('image',function($data){
            return "<img style='height: 4rem;' src='".env('URL_FILE_VIEW').$data->image."' alt=' '>";
        })
        ->editColumn('created_at',function($data){
            return format_datetime($data->created_at);
        })
        ->editColumn('action',function($data){
            return '<a class="btn btn-sm btn-secondary edit-news" data-id="'.$data->news_id.'" href="#" data-toggle="tooltip" title="Edit"><i class="fas fa-edit"></i></a>
            <a class="btn btn-sm btn-secondary delete-news" data-id="'.$data->news_id.'" href="#" data-toggle="tooltip" title="Delete"><i class="fas fa-trash"></i></a>';
        })
        ->rawColumns(['action','image'])
        ->make(true);
    }

    public static function getById($id){
        return self::where('news_id',$id)->first();
    }

    

    

  
}