<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\MainTeam;
use App\Models\MainFile;
use App\Models\MainTrackingHistory;
use Auth;

class MainUser extends Model
{
	protected $table = 'main_user';

	public $timestamps = true;

	protected $primaryKey = 'user_id';

	protected $fillable = [
		'user_id',
		'user_nickname',
		'user_firstname',
		'user_lastname',
		'user_phone',
		'user_country_code',
		'user_password',
		'user_email',
		'user_group_id',
		'user_avatar',
		'user_status',
		'user_token',
        'user_customer_list',
        'user_birthdate',
        'user_team'
	];

	protected $guarded = [];

	public function getFullname(){
		return $this->user_firstname." ".$this->user_lastname;
	}
	public function getTeam(){
		return $this->belongsTo(MainTeam::class,'user_team','id');
	}
	public function getFiles(){
		return $this->hasManyThrough(MainFile::class,MainTrackingHistory::class,'created_by','tracking_id','user_id','id');
	}
	public function getUserGroup(){
	    return $this->belongsTo(MainGroupUser::class,'user_group_id','gu_id');
    }
    public function scopeActive($query){
	    return $query->where('user_status',1);
    }

    public static function getMemberTeam()
    {
        $member_list = self::where('user_team',Auth::user()->user_team)->select('user_id')->get()->toArray();
        return array_values($member_list);
    }
    public static function getCustomerOfUser(){
        $customer_list = Auth::user()->user_customer_list;
        return explode(';',$customer_list);
    }
    public static function getCustomerOfTeam(){
	    $customer_list = self::whereIn('user_id',self::getMemberTeam())->select('user_customer_list')->get();
//	    return $customer_list;
	    $customer_arr = "";
	    foreach ($customer_list as $key => $customer){
	        if($customer->user_customer_list != ""){
                $customer_arr .= $customer->user_customer_list.";";
            }
        }
        $customer_arr = array_unique(explode(';',$customer_arr));
	    return $customer_arr;
    }

}
