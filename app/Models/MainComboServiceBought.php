<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laracasts\Presenter\PresentableTrait;
use App\Models\MainService;

use Carbon\Carbon;
use DB;
use App\Traits\StatisticsTrait;

use App\Models\MainUser;
use Gate;
use Auth;


class MainComboServiceBought extends Model
{
    use PresentableTrait,StatisticsTrait;

    protected $presenter = 'App\\Presenters\\ThemeMailPresenter';

    protected $table = "main_combo_service_bought";
    protected $fillable = [
        'id',
    	'csb_customer_id',
        'csb_place_id',
    	'csb_combo_service_id',
        'csb_trans_id',
    	'csb_amount',
    	'csb_charge',
    	'csb_cashback',
    	'csb_payment_method',
    	'csb_card_type',
    	'csb_amount_deal',
    	'csb_card_number',
    	'csb_status',
        'csb_note',
        'created_by',
        'updated_by',
        'bank_name',
        'account_number',
        'routing_number',
        'csb_last_call', //for remider cskh call for customer
        'csb_status_call',
        'csb_user_call',
        'csb_invoice', //invoice's file name
        'csb_token', //for customer rating
    ];
    public function getCustomer(){
        return $this->belongsTo(MainCustomer::class,'csb_customer_id','customer_id');
    }
    public function getPlace(){
        return $this->belongsTo(PosPlace::class,'csb_place_id','place_id')->withDefault();
    }
    public function getCreatedBy(){
        return $this->belongsTo(MainUser::class,'created_by','user_id');
    }
    public function getTasks(){
        return $this->hasMany(MainTask::class,'order_id','id');
    }
    public function getUpdatedBy(){
        return $this->belongsTo(MainUser::class,'updated_by','user_id');
    }
    public function getDemoPlace(){
        return $this->belongsTo(PosPlace::class,'csb_place_id','place_id')->withDefault()->where('place_demo','!=',1);
    }

    public static function getSumChargeByYear($current_month,$current_year){
        $sum_charge = self::select('csb_charge','created_by')
            ->whereYear('created_at',$current_year)
            ->whereMonth('created_at',$current_month);
        if(Gate::allows('permission','dashboard-admin')){
        }
        elseif(Gate::allows('permission','dashboard-leader')){
            $sum_charge = $sum_charge->whereIn('created_by',MainUser::getMemberTeam());
        }else{
            $sum_charge = $sum_charge->where('created_by',Auth::user()->user_id);
        }
        $sum_charge = $sum_charge->sum('csb_charge');

        return $sum_charge;
    }

    /**
     * get 10 popular services by monthe of the year , (year && month of $date)
     * @param  date $date ex: 2019-04-31
     * @return query
     */
    public static function get10popularServicesByMonth($date){
        $startDate = $date->format('Y-m')."-01";
        $endDate = $date->format('Y-m')."-31";
    }

    public static function getServicesByMonth($date){
        return self::getByMonth($date);
    }

    private static function getBetween2Date($startDate,$endDate){
        $services = self::getServiceByStartAndEndDate($startDate,$endDate);

        $formatArrServices = self::formatArrServices($services);

        $arrServices = self::sortTotalServices($formatArrServices);

        $arrServices = self::addNameServiceToArrServices($formatArrServices,$arrServices);

        return $arrServices;
    }

    private static function getServiceByStartAndEndDate($startDate,$endDate){
        $startDate = format_date_db($startDate)." 00:00:00";
        $endDate = format_date_db($endDate)." 23:59:59";
        // echo $startDate. " - ".$endDate; die();

        return self::select('csb_combo_service_id','created_at')
                        ->whereBetween('created_at',[$startDate,$endDate])
                        ->get();
    }

    private static function formatArrServices($services){
        $arrServices = [];
        foreach ($services as $key => $value) {
            $arr = explode(";", $value->csb_combo_service_id);
            foreach ($arr as $arrValue) {
                $arrServices[] = $arrValue;
            }
        }
        return $arrServices;
    }

    private static function sortTotalServices($services){
        $arr = [];

        foreach ($services as $value) {
            $checkExis = 0;
            foreach ($arr as $key => $valueArr) {
                if($value == $valueArr['idService']){
                    $arr[$key] = [
                        'count' => $arr[$key]['count'] + 1,
                        'idService' => $value,
                    ];
                    $checkExis = 1;
                }
            }

            if($checkExis == 0){
                $arr[] = [
                    'count' => 1,
                    'idService' => $value,
                ];
            }
        }
        arsort($arr);
        $arr = array_values($arr);

        return $arr;
    }

    private static function addNameServiceToArrServices($formatArrServices, $arrServices){
        $servicesName = MainComboService::getByArrId($formatArrServices);

        foreach ($arrServices as $key => $valueArrServices) {
            foreach ($servicesName as $valueArrServicesName) {
               if($valueArrServices['idService'] == $valueArrServicesName->id){
                    $arrServices[$key]['nameService'] = $valueArrServicesName->cs_name;
                    $arrServices[$key]['priceService'] = $valueArrServicesName->cs_price;
                    $arrServices[$key]['totalPrice'] = $valueArrServicesName->cs_price * $valueArrServices['count'];
               }
            }
        }
        return $arrServices;
    }

    public static function getDatatable($start, $length, $type,$valueQuarter = null, $date = null){
        if(!$date){
            $date = format_date_db(get_nowDate());
        }

        $arr = [];
        // choose by type, from StatisticsTrait
        switch ($type) {
            case 'Daily':
                $arr = self::getByDate($date);
                break;
            case 'Monthly':
                $arr = self::getByMonth($date);
                break;
            case 'Quarterly':
                $arr = self::getByQuarterly($date,$valueQuarter);
                break;
            case 'Yearly':
                $arr = self::getByYear($date);
                break;
        }

        // dd($arr);
// dd($a);
        $arrOut = self::getArrByStartAndLength($arr, $start, $length);

        return response()->json([
            'data'=>$arrOut,
            'recordsFiltered' => count($arr),
            'recordsTotal' => count($arr),

        ]);


       return response()->json($arr);
    }
    /**
     * get arr by start and length of datatable
     * @param  array    $arr
     * @param  int      $start
     * @param  int      $length
     * @return array    $arrOut
     */
    private static function getArrByStartAndLength($arr, $start, $length){
        $arrOut = [];
        for ($i = $start; $i < $start + $length; $i++) {
            try {
                $arrOut[] = $arr[$i];
            } catch (\Exception $e) {
                continue;
            }
        }
        return $arrOut;
    }

}




