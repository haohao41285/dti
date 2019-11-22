<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\MainComboService;
use Gate;
use Auth;
use Carbon\Carbon;

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
	    return $this->belongsTo(MainUser::class,'created_by','user_id')->withDefault();
    }
    public function scopeActive($query){
	    return $query->where('cs_status',1);
    }
    public function getCustomer(){
	    return $this->belongsTo(MainCustomer::class,'cs_customer_id','customer_id')->withDefault();
    }

    public function getNewDateExpireAttribute(){
        return $this->attributes['cs_date_expire'];
    }
    public function getCsDateExpireAttribute($value){
	   return format_date($value);
    }

    public static function getNearlyExpired(){
	    $today = today();
	    $date_expire = today()->addDays(15);

        $customer_list = self::select('cb_id')
            ->whereBetween('cs_date_expire',[$today,$date_expire])
            ->active();
        if(Gate::allows('permission','dashboard-admin')){

        }elseif(Gate::allows('pemission','dashboard-leader'))
            $customer_list = $customer_list->whereIn('created_by',MainUser::getMemberTeam());
        else
            $customer_list = $customer_list->where('created_by',Auth::user()->user_id);

        $customer_count = $customer_list->count();

        return $customer_count;
    }

}
