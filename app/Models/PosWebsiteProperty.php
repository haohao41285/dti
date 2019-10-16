<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\MainThemeProperties;

class PosWebsiteProperty extends Model
{
    protected $table = 'pos_website_properties';

	public $timestamps = true;

    // protected $primaryKey = "wp_variable";

    protected $fillable = [
        'wp_variable',
        'wp_place_id',
        'wp_name',
        'wp_value',
        'wp_type',
        'created_at',
        'updated_at',
    ];

    protected $guarded = [];

    /**
     * clone data field from MainThemeProerties by $propertiesId
     * @param  $propertiesId || null
     * @param  $placeId   
     * @return 
     */
    public static function cloneUpdate($propertiesId = null, $placeId){
        $themePropertiesValue = MainThemeProperties::getThemePropertiesValueById($propertiesId);

        self::deleteByPlaceId($placeId);

        if(!$themePropertiesValue){
            return;
        }        

        $arrThemePropertiesValue = json_decode($themePropertiesValue,true); 

        $arrFormat = [];
        foreach ($arrThemePropertiesValue as $key => $value) {
            $checkImage = strpos($value['value'],"/images/");

            $type = 1;
            if($checkImage === 0){
                $type = 2;
            }

            $arrFormat[] = [
                'wp_variable' => $value['name'],
                'wp_place_id' => $placeId,
                'wp_name' => '',
                'wp_value' => $value['value'],
                'wp_type' => $type,

            ];
        } 
        PosWebsiteProperty::insert($arrFormat);
    }

    public static function deleteByPlaceId($placeId){
        $properties = self::where('wp_place_id',$placeId)->delete();
    }




        
}