<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laracasts\Presenter\PresentableTrait;
use DB;
use DataTables;

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

        return self::select(
                    DB::raw('DATE_FORMAT(created_at, "%m") as month'),
                    DB::raw('COUNT("month") as count' ) 
                        )
                    ->where('customer_status',1)
                    ->whereBetween('created_at',[$startDate,$endDate])
                    ->groupBy('month')
                    ->get();
    }

    public static function getDatatableNewCustomerByYear($year){
        $startDate = $year."-01-01";
        $endDate = $year."-12-31";

        $customers = self::select(
                        'customer_id',
                        'customer_lastname',
                        'customer_firstname',
                        'customer_email',
                        'customer_phone',
                        'created_at'
                        )
                    ->where('customer_status',1)
                    ->whereBetween('created_at',[$startDate,$endDate])
                    ->get();
             // echo $customers; die();      


        return Datatables::of($customers)
        ->addColumn('customer_fullname',function($customers){
            return $customers->customer_firstname." ".$customers->customer_lastname;
        })
        ->editColumn('created_at',function($customers){
            return format_datetime($customers->created_at);
        })
        ->addColumn('created_month',function($customers){
            return format_month($customers->created_at);
        })
        ->make(true);
    }

    

}
