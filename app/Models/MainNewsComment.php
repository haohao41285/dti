<?php

namespace App\Models;

use App\Models\BaseModel;

class MainNewsComment extends BaseModel
{
    protected $table = 'main_news_comment';

    // public $timestamps = true;

    protected $primaryKey = 'comment_id';

    protected $fillable = [
        'comment_id',
        'comment_user_phone',
        'comment_news_id',
        'comment_news_content',
        'comment_status',
        'created_at',
        'updated_at',
    ];

    protected $guarded = [];

    protected $created_by = false;

    protected $updated_by = false;

    public static function getByNewsId($newsId){
        return self::select("main_news_comment.*","user_nickname","user_avatar","user_phone")
                    ->where('comment_status',1) 
                    ->where('comment_news_id',$newsId)
                    ->orderBy('comment_id',"desc")
                    ->join('pos_user',"user_phone","comment_user_phone")
                    ->get();
    }





        
}