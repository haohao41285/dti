<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DataTables;

class PosTemplate extends Model
{
    protected $table = 'pos_template';

    // protected $primaryKey = ['template_id','template_place_id'];

    // public $incrementing = false;

    public $timestamps = true;

    protected $fillable = [
        'template_id',
        'template_place_id',
        'template_title',
        'template_discount',
        'template_type',
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
                    ->where('template_place_id',$placeId)
                    ->update(['template_status'=>0]);
    }

    public static function getByPlaceIdAndType($placeId, $type){
        return self::where('template_place_id',$placeId)
                    ->where('template_status',1)
                    ->where('template_table_type',$type)
                    ->get();
    }

    public static function getDatatableByPlaceId($placeId){
        $data = self::getByPlaceIdAndType($placeId,1);

        return DataTables::of($data)
        ->editColumn('action',function($data){
            return '<a class="btn btn-sm btn-secondary editAutoCoupon" data-id="'.$data->template_id.'" href="#" data-toggle="tooltip" title="Edit"><i class="fas fa-edit"></i></a>
            <a class="btn btn-sm btn-secondary deleteAutoCoupon" data-id="'.$data->template_id.'" href="#" data-toggle="tooltip" title="Delete"><i class="fas fa-trash"></i></a>';
            
        })
        ->editColumn('template_linkimage',function($data){
                return "<img style='height: 5rem;' src=".env('URL_FILE_VIEW').$data->template_linkimage." >";            
        })
        ->editColumn('template_discount',function($data){
                if($data->template_type === 0){
                    return $data->template_discount."(%)";
                } else {
                    return number_format($data->template_discount)."($)";
                }          
        })
        ->rawColumns(['action','template_linkimage'])
        ->make(true);
    }
        
}