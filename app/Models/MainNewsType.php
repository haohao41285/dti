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

    protected $primaryKey = 'news_type_id';

    protected $fillable = [
        'news_type_id',
        'title',
        'slug',
        'created_at',
        'updated_at',
        'news_type_status',
    ];

    protected $created_by = false;

    protected $updated_by = false;

    protected $guarded = [];

    public static function getDatatable(){
        $newsType = self::where('news_type_status',1)->get();

        return DataTables::of($newsType)
        ->editColumn('created_at',function($data){
            return format_datetime($data->created_at);
        })
        ->editColumn('action',function($data){
            return '<a class="btn btn-sm btn-secondary edit-news-type" data-id="'.$data->news_type_id.'" href="#" data-toggle="tooltip" title="Edit"><i class="fas fa-edit"></i></a>
            <a class="btn btn-sm btn-secondary delete-news-type" data-id="'.$data->news_type_id.'" href="#" data-toggle="tooltip" title="Delete"><i class="fas fa-trash"></i></a>';
        })
        ->rawColumns(['action'])
        ->make(true);
    }

    public static function getById($id){
        return self::where('news_type_id',$id)->first();
    }



        
}