<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PosCustomer
 */
class PosNews extends Model
{
    protected $table = 'pos_news';

    public $timestamps = true;

    protected $fillable = [
        'news_id',
        'news_place_id',
        'news_title',
        'news_content',
        'news_catenews_id',
        'news_dateposted',
        'created_at',
        'updated_at',
        'news_status'
    ];

    protected $guarded = [];

        
}