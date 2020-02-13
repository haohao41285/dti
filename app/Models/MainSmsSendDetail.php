<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use DataTables;

class MainSmsSendDetail extends Model
{
    protected $table ="main_sms_send_detail";

    protected $fillable = [
    	'id',
    	'sms_send_id',
    	'datetime',
    	'phone',
    	'content',
    	'created_at',
    	'updated_at'    	
    ];

    public function createByArr($arr){
    	return $this->create($arr);
    }

    public function datatable($smsSendId){
    	$data = $this->where('sms_send_id',$smsSendId);

    	return Datatables::of($data)
    	->editColumn('datetime',function($row){
                return Carbon::parse($row->datetime)->format('m/d/Y H:i:s');
        })
        ->make(true);
    }

    public function getByDateTime($datetime){
        return $this->where('datetime',$datetime)
        ->get();
    }
}
