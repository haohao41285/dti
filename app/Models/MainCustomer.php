<?php

namespace App\Models;

use App\Models\MainUser;
use Illuminate\Database\Eloquent\Model;
use Laracasts\Presenter\PresentableTrait;
use DB;

use DataTables;
use App\Traits\StatisticsTrait;

use Gate;


class MainCustomer extends Model
{
    use PresentableTrait, StatisticsTrait;
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
        'customer_birthday'
    ];

    public $timestamps = false;

    protected $guarded = [];

    public function getPlaces(){
        return $this->hasMany(PosPlace::class,'place_customer_id','customer_id');
    }
    public function getOrder(){
        return $this->hasMany(MainComboServiceBought::class,'csb_customer_id','customer_id');
    }
    public function getCustomerTemplate(){
        return $this->belongsTo(MainCustomerTemplate::class,'customer_customer_template_id','id');
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

    public static function getDatatableStatistic($type,$valueQuarter = null, $date = null){
        if(!$date){
            $date = format_date_db(get_nowDate());
        }

        $customers = null;
        // choose by type, from StatisticsTrait
        switch ($type) {
            case 'Daily':
                $customers = self::getByDate($date);
                break;
            case 'Monthly':
                $customers = self::getByMonth($date);
                break;
            case 'Quarterly':
                $customers = self::getByQuarterly($date,$valueQuarter);
                break;
            case 'Yearly':
                $customers = self::getByYear($date);
                break;
        }
        //echo $customers; die();

        return Datatables::of($customers)
        ->addColumn('customer_fullname',function($customers){
            return $customers->customer_firstname." ".$customers->customer_lastname;
        })
        ->editColumn('created_at',function($customers){
            return format_datetime($customers->created_at);
        })
        ->make(true);
    }

    private static function getBetween2Date($startDate,$endDate){
        return self::select(
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
    }


}
