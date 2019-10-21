<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\MainThemeProperties;
use DataTables;
use App\Helpers\ImagesHelper;

class PosWebsiteProperty extends Model
{
    protected $table = 'pos_website_properties';

	public $timestamps = true;

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

    public static function deleteByIdAndPlaceId($id,$placeId){
        $property = self::where('wp_variable',$id)
                        ->where('wp_place_id',$placeId)
                        ->delete();
    }


    public static function getDatatableByPlaceId($placeId){
        $data = self::where('wp_place_id',$placeId)->get(); 

        return DataTables::of($data)
        ->editColumn('action',function($data){
            return '<a class="btn btn-sm btn-secondary editValueProperty" data-id="'.$data->wp_variable.'" href="#" data-toggle="tooltip" title="Edit"><i class="fas fa-edit"></i></a>
            <a class="btn btn-sm btn-secondary deleteValueProperty" data-id="'.$data->wp_variable.'" href="#" data-toggle="tooltip" title="Delete"><i class="fas fa-trash"></i></a>';
            
        })
        ->editColumn('wp_value',function($data){
            $check = strpos($data->wp_value,"images/theme/properties");
            // dd($check);
            if($check === 1){
                return "<img style='height: 5rem;' src=".env('URL_FILE_VIEW').$data->wp_value." >";
            }
            else {
                return $data->wp_value;
            }
        })
        ->rawColumns(['action','wp_value'])
        ->make(true);
    }

    public static function saveValue($variable,$name,$requestValue = null,$image = null,$action,$placeId){
        $value = null;
        $type = null;

        if($requestValue) {
            $value = $requestValue;
            $type = 1;
        }
        if ($image) {
                $value = ImagesHelper::uploadImageToAPI($image,"theme/properties");
                $type = 2;
        }

        if($action == "create"){
        // create 
            $arr = [
                'wp_variable' => $variable,
                'wp_place_id' => $placeId,
                'wp_name' => $name,
                'wp_value' => $value,
                'wp_type' => $type,
                
            ];
            self::create($arr);
        } else {
        // update
             $arr = [                       
                'wp_place_id' => $placeId,
                'wp_name' => $name,
                'wp_value' => $value,
                'wp_type' => $type,
                
            ];
            if(empty($arr['wp_value'])) {
                unset($arr['wp_value']);
                unset($arr['wp_type']);
            }

            self::where('wp_variable',$variable)->update($arr);
        }
    }




        
}