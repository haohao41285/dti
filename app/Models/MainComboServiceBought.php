<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laracasts\Presenter\PresentableTrait;
use App\Models\MainService;
use Carbon\Carbon;
use DB;

class MainComboServiceBought extends Model
{
    use PresentableTrait;
    protected $presenter = 'App\\Presenters\\ThemeMailPresenter';

    protected $table = "main_combo_service_bought";
    protected $fillable = [
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
    ];
    public function getCustomer(){
        return $this->belongsTo(MainCustomer::class,'csb_customer_id','customer_id');
    }
    public function getPlace(){
        return $this->belongsTo(PosPlace::class,'csb_place_id','place_id');
    }
    public function getCreatedBy(){
        return $this->belongsTo(MainUser::class,'created_by','user_id');
    }
    public function getTasks(){
        return $this->hasMany(MainTask::class,'order_id','id');
    }

    public static function getSumChargeByYear($year){
        return self::select('csb_charge')
                    ->whereYear('created_at',$year)
                    ->sum('csb_charge');
    }
    /**
     * get services by monthe of the year 
     * @param  date $date ex: 2019-04-31 
     * @return query
     */
    public static function getServicesByMonth($date){
        $startDate = getStartMonthByDate($date);
        $endDate = getEndMonthByDate($date);
        
        return self::getServiceBetween2Date($startDate,$endDate);
    }

    private static function getServicesByYear($date){
        $startDate = format_year($date)."-01-01";
        $endDate = format_year($date)."-12-31";

        return self::getServiceBetween2Date($startDate,$endDate);
    }

    private static function getServicesByQuarterly($date, $valueQuarter){
        switch ($valueQuarter) {
            case 'first':
                $startDate = getStartMonthByDate(format_year($date)."-01-01");
                $endDate = getEndMonthByDate(format_year($date)."-03-01");
                break;
            case 'second':
                $startDate = getStartMonthByDate(format_year($date)."-04-01");
                $endDate = getEndMonthByDate(format_year($date)."-06-01");
                break;
            case 'third':
                $startDate = getStartMonthByDate(format_year($date)."-07-01");
                $endDate = getEndMonthByDate(format_year($date)."-09-01");
                break;
            case 'fourth':
                $startDate = getStartMonthByDate(format_year($date)."-10-01");
                $endDate = getEndMonthByDate(format_year($date)."-12-01");
                break;
        }        
        
        return self::getServiceBetween2Date($startDate,$endDate);
    }

    private static function getServicesByDate($date){
        $startDate = $date;
        $endDate =  $date;
        
        return self::getServiceBetween2Date($startDate,$endDate);
    }

    private static function getServiceBetween2Date($startDate,$endDate){
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
        
        // choose type by search 
        $arr = [];
        switch ($type) {
            case 'Daily':
                $arr = self::getServicesByDate($date);
                break;
            case 'Monthly':
                $arr = self::getServicesByMonth($date); 
                break;
            case 'Quarterly':
                $arr = self::getServicesByQuarterly($date,$valueQuarter); 
                break;
            case 'Yearly':
                $arr = self::getServicesByYear($date); 
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




