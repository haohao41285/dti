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
        'template_type_form'
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
     * get by type
     * @return mixed
     */
    public static function getDataTableByType($type){
    	$coupon = self::getByType($type);

    	return DataTables::of($coupon)
        ->addColumn('action', function ($coupon){
                    return '<a class="btn btn-sm btn-secondary edit-coupon-type" data="'.$coupon->template_type_id.'" href="#" data-toggle="tooltip" title="Edit"><i   class="fas fa-edit"></i></a>
                    <a class="btn btn-sm btn-secondary delete-coupon-type" data="'.$coupon->template_type_id.'" href="#" data-toggle="tooltip" ><i  title="Delete" class="fas fa-trash"></i></a>';
            })
        ->editColumn('template_type_form',function($coupon){
            $val = $coupon->template_type_form == 1 ? 'Default' : 'Form';
            return "<span data='$coupon->template_type_form'>$val</span>";
        })
        ->rawColumns(['template_type_form','theme_image','theme_status','action'])
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

    public static function deleteById($id){
        $counpon = self::getById($id);
        $counpon->template_type_status = 0;
        $counpon->save();
        return $counpon;
    }
}