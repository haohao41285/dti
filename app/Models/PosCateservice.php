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

    public static function getCateServicesByPlaceId($placeId){
        $services = self::select('cateservice_id','cateservice_name')
                        ->where('cateservice_place_id',$placeId)
                        ->where('cateservice_status',1)
                        // ->join('pos_service',function($joinService){
                        //     $joinService->on('cateservice_id','service_cate_id')
                        //     ->on('cateservice_place_id','service_place_id');
                        // })
                        ->get();

        // $cateServices = $services->distinct('cateservice_id');

        $arrCate = [];
        foreach ($services as $value) {
            $arrCate[] = [
                'cateservice_id' => $value->cateservice_id,
                'cateservice_name' => $value->cateservice_name,
            ];
        }
        // $arrCate = array_unique($arrCate,0);
        // dd($arrCate);
        // foreach ($arrCate as $key => $value) {
        //     foreach ($services as $valueServices) {
        //         if($value['cateservice_id'] == $valueServices->cateservice_id){
        //             $arrCate[$key]['services'][] = [
        //                 'service_id' => $valueServices->service_id,
        //                 'service_name' => $valueServices->service_name
        //             ]; 
        //         }
        //     }           
        // }

        // dd($arrCate);

        return $arrCate;             
    }

    public static function getByArrIdAndPlaceId($arrId,$placeId){
        return self::select("cateservice_id","cateservice_name")
                    ->where("cateservice_place_id",$placeId)
                    ->whereIn("cateservice_id",$arrId)
                    ->get();
    }
        
}