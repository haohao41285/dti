<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class PosCateservice
 */
class PosCateservice extends BaseModel
{
    protected $table = 'pos_cateservice';

    public $timestamps = true;

    public static function boot()
    {
        parent::boot();
    }

    protected $fillable = [
        'cateservice_id',
        'cateservice_place_id',
        'cateservice_name',
        'cateservice_image',
        'cateservice_index',
        'cateservice_description',
        'created_at',
        'updated_at',
        'created_by',
        'cateservice_status'
    ];

    protected $guarded = [];

    // public function getCustomer(){
    //     return $this->belongsTo('App\PosCustomer','created_by','cateservice_place_id','cateservice_id');
    // }

        
}