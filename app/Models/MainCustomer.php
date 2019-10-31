<?php

namespace App\Models;

use App\Models\MainUser;
use Illuminate\Database\Eloquent\Model;
use Laracasts\Presenter\PresentableTrait;
use DB;
use Gate;

class MainCustomer extends Model
{
    use PresentableTrait;
    protected  $presenter = 'App\\Presenters\\MainCustomerPresenter';

    protected $table = 'main_customer';

    protected $updated_at = false;

    protected $fillable = [
        'customer_id',
        'customer_lastname',
        'customer_firstname',
        'customer_email',
        'customer_phone',
        'customer_phone_introduce',
        'customer_address',
        'customer_city',
        'customer_zip',
        'customer_state',
        'customer_agent',
        'customer_type',
        'customer_status',
        'customer_customer_template_id',
        'created_at',
    ];

    protected $guarded = [];

    public function getPlaces(){
        return $this->hasMany(PosPlace::class,'place_customer_id','customer_id');
    }
    public function getOrder(){
        return $this->hasMany(MainComboServiceBought::class,'csb_customer_id','customer_id');
    }

    public static function getTotalNewCustomersEveryMonthByYear($year){
        $startDate = $year."-01-01";
        $endDate = $year."-12-31";

        $new_customer_list = self::select(
                    DB::raw('DATE_FORMAT(created_at, "%m") as month'),
                    DB::raw('COUNT("month") as count' )
                        )
                    ->where('customer_status',1)
                    ->whereBetween('created_at',[$startDate,$endDate])
                    ->groupBy('month');
        if(Gate::allows('permission','dashboard-admin')){
        }
        elseif(Gate::allows('permission','dashboard-leader'))
            $new_customer_list = $new_customer_list->whereIn('customer_id',MainUser::getCustomerOfTeam());
        else
            $new_customer_list = $new_customer_list->whereIn('customer_id', MainUser::getCustomerOfUser());

        $new_customer_list = $new_customer_list->get();

        return $new_customer_list;
    }
    public function getFullname(){
        return  $this->customer_firstname. " ".$this->customer_lastname;
    }



}
