<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DataTables;

class PosTemplateType extends Model
{
    protected $table = 'pos_template_type';

    protected $primaryKey = 'template_type_id';

    public $timestamps = false;

    protected $fillable = [
        'template_type_id',
        'template_type_name',
        'template_type_status',
        'template_type_table_type',
    ];

    protected $guarded = [];
    /**
     * get by template_type_table_type
     * int $type
     * @return mixed
     */
    public static function getByType($type){
    	return self::where('template_type_table_type',$type)
    				->where('template_type_status',1)
    				->get();
    }
    /**
     * get by template_type_table_type = 1
     * @return mixed
     */
    public static function getCouponDataTable(){
    	$coupon = self::getByType(1);

    	return DataTables::of($coupon)
        ->addColumn('action', function ($coupon){
                    return '<a class="btn btn-sm btn-secondary edit-coupon-type" data="'.$coupon->template_type_id.'" href="#" data-toggle="tooltip" title="Edit"><i   class="fas fa-edit"></i></a>
                    <a class="btn btn-sm btn-secondary delete-coupon-type" data="'.$coupon->template_type_id.'" href="#" data-toggle="tooltip" ><i  title="Delete" class="fas fa-trash"></i></a>';
            })
        ->rawColumns(['theme_image','theme_status','action'])
        ->make(true);
    }

    public static function getById($id){
    	return self::where('template_type_id',$id)
    				->where('template_type_status',1)
    				->first();
    }

    public static function getAll(){
        return self::where('template_type_status',1)
                    ->get();
    }
}