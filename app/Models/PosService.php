<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class PosService
 */
class PosService extends BaseModel
{
    protected $table = 'pos_service';

    public $timestamps = true;

    public static function boot()
    {
        parent::boot();
    }
    
    protected $fillable = [
        'service_id',
        'service_name',
        'service_tag',
        'service_short_name',
        'service_duration',
        'service_price',
        'service_price_extra',
        'service_price_repair',
        'service_price_hold',
        'service_updown',
        'service_image',
        'service_description',
        'service_descript_website',
        'booking_online_status',
        'service_cate_id',
        'service_place_id', 
        'service_turn',
        'service_tax',
        'created_at',
        'updated_at',
        'service_status',
        'service_enable_status',
        'service_list_image'
    ];

    protected $guarded = [];

    
    /**
     * Get List Service 
     * @param $arrIds (string array with ;)
     * @return array service (id & name)
     */
    public function getListByIds($placeId, $ids = null){
        
        $myModel =  $this->selectRaw('service_name, service_id')
                    ->where('service_place_id','=', $placeId);
        if(!empty($ids)){
            $myModel->whereIn('service_id', explode(';', $ids));
        }
        $listServiceResult = $myModel ->get();
        
        $newCollection = $listServiceResult->mapWithKeys(function ($item) {
            return [$item->service_id => $item->service_name];
        });
        
        return $newCollection->all();
    }

    
        
}