<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DataTables;
use App\Helpers\ImagesHelper;

class PosTemplate extends Model
{
    protected $table = 'pos_template';

    protected $primaryKey = 'template_id';

    public $timestamps = true;

    protected $fillable = [
        'template_id',
        'template_place_id',
        'template_title',
        'template_discount',
        'template_discount_type',
        'template_list_service',
        'template_linkimage',
        'created_at',
        'updated_at',
        'template_status',
        'template_type_id',
        'template_table_type',
    ];

    protected $guarded = [];

    public static function deleteByIdAndPlaceId($id, $placeId){
        return self::where('template_id',$id)
                    // ->where('template_place_id',$placeId)
                    ->update(['template_status'=>0]);
    }

    public static function getByPlaceIdAndType($placeId, $type){
        return self::where('template_place_id',$placeId)
                    ->where('template_status',1)
                    ->where('template_table_type',$type)
                    ->get();
    }

    public static function getByPlaceIdAndId($id ,$placeId){
        return self::where('template_place_id',$placeId)
                    ->where('template_status',1)
                    ->where('template_id',$id)
                    ->first();
    }

    public static function getDatatableByPlaceId($placeId, $type=null){
        $data = self::getByPlaceIdAndType($placeId,$type);

        return DataTables::of($data)
        ->editColumn('action',function($data){
            return '<a class="btn btn-sm btn-secondary editAutoCoupon" data-id="'.$data->template_id.'" href="#" data-toggle="tooltip" title="Edit"><i class="fas fa-edit"></i></a>
            <a class="btn btn-sm btn-secondary deleteAutoCoupon" data-id="'.$data->template_id.'" href="#" data-toggle="tooltip" title="Delete"><i class="fas fa-trash"></i></a>';
            
        })
        ->editColumn('template_linkimage',function($data){
                return "<img style='height: 5rem;' src=".env('URL_FILE_VIEW').$data->template_linkimage." >";            
        })
        ->editColumn('template_discount',function($data){
                if($data->template_discount_type === 0){
                    return $data->template_discount."(%)";
                } else {
                    return number_format($data->template_discount)."($)";
                }          
        })
        ->editColumn('template_type_id',function($data){
            $type = PosTemplateType::getById($data->template_type_id);
            return $type->template_type_name;
        })
        ->rawColumns(['action','template_linkimage'])
        ->make(true);
    }

    public static function saveAuto($id, $placeId, $title, $discount, $discountType, $image, $services, $couponType,$tableType){
        if($image){
            $image = ImagesHelper::uploadImageToAPI($image,'auto_template');
        }
        
        // $discountType = $discountType == "$" ? '1' : "0";

        $arr = [
            'template_place_id' => $placeId,
            'template_title' => $title,
            'template_discount' => $discount,
            'template_discount_type' => $discountType,
            'template_linkimage' => $image,
            'template_list_service' => $services,
            'template_type_id' => $couponType,
            'template_table_type' => $tableType, 
        ];

        if($id){
            $coupon = self::getByPlaceIdAndId($id ,$placeId);
            if(!$image){
                unset($arr['template_place_id']);
                unset($arr['template_linkimage']);
            }
            // dd($arr);
            $coupon->update($arr);
           
            return "updated successfully";
        } else {
            // $id = self::select('template_id')
            //             ->where('template_place_id',$placeId)
            //             ->max('template_id');

            // $arr['template_id'] = $id+1;
            // if(!$arr['template_id']) $arr['template_id'] = 1;
            // dd($arr);

            self::insert($arr);

            return "inserted successfully";
        }
    }
        
}