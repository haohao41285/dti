<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laracasts\Presenter\PresentableTrait;
use App\Models\MainService;

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
     * get 10 popular services by monthe of the year , (year && month of $date)
     * @param  date $date ex: 2019-04-31 
     * @return query
     */
    public static function get10popularServicesByMonth($date){
        $startDate = $date->format('Y-m')."-01";
        $endDate = $date->format('Y-m')."-31";
        
        return self::getServiceBetween2Date($startDate,$endDate);
    }

    public static function getServicesByYear($date){
        $startDate = $date->format('Y')."01-01";
        $endDate = $date->format('Y')."12-31";
        
        return self::getServiceBetween2Date($startDate,$endDate);
    }

    public static function getServicesByDate($date){
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
        $startDate = format_date_db($startDate);
        $endDate = format_date_db($endDate);

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
        $servicesName = MainService::select('service_id','service_name')
                                    ->whereIn('service_id',$formatArrServices)
                                    ->get();

        foreach ($arrServices as $key => $valueArrServices) {            
            foreach ($servicesName as $valueArrServicesName) {
               if($valueArrServices['idService'] == $valueArrServicesName->service_id){
                    $arrServices[$key]['nameService'] = $valueArrServicesName->service_name;
               }
            }
        }
        return $arrServices;
    }

    public static function getDatatable($date){
        $arr = [              
                [
                  "Airi",
                  "Satou",
                  "Accountant",
                  "Tokyo",
                  "28th Nov 08",
                  "$162,700"
                ],                
        ];


       return response()->json($arr);
    }

}
