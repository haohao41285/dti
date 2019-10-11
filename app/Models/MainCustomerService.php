<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\MainComboService;

/**
 * Class MainCustomerService
 */
class MainCustomerService extends Model
{
	protected $table = 'main_customer_service';

	public $timestamps = true;

	protected $fillable = [
		'cs_id',
		'cs_place_id',
		'cs_customer_id',
		'cs_service_id',
		'cs_date_expire',
		'cs_type',
		'created_by',
		'created_at',
		'updated_by',
		'updated_at',
		'cs_status'
	];

	protected $guarded = [];

	public function getComboService(){
		return $this->belongsTo(MainComboService::class,'cs_service_id','id');
	}
	public function getPlace(){
	    return $this->belongsTo(PosPlace::class,'cs_place_id','place_id');
    }
    public function getCreatedBy(){
	    return $this->belongsTo(MainUser::class,'created_by','user_id');
    }

    public static function get10popularServices(){
    	return self::select('cs_service_id')
    				->where('cs_status',1)
    				->groupBy('cs_service_id')
    				->get();
    }

}
