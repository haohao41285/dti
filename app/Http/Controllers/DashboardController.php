<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Response;
use App\Models\MainComboServiceBought;
use App\Models\MainTask;
use App\Models\MainCustomerBought;
use App\Models\MainCustomerService;
use App\Models\MainCustomer;


class DashboardController extends Controller {

    public function __construct()
    {

    }

    public function index(){   
        $yearNow = format_year(get_nowDate());
        $now = get_nowDate();

        $data['earnings'] = MainComboServiceBought::getSumChargeByYear($yearNow);
        $data['pendingTasks'] = MainTask::getPendingTasks();
        $data['nearlyExpired'] = MainCustomerBought::getNearlyExpired();
        $data['popularServices'] = MainComboServiceBought::get10popularServicesByMonth($now);

        $newCustomer = MainCustomer::getTotalNewCustomersEveryMonthByYear($yearNow);
   
        $data['newCustomer'] = $this->formatCustomersArr($newCustomer);      
        
        return view('dashboard',$data);
    }
    public function confirmEvent(){
        try{
            //subHours(11) to get time American
            $now = Carbon::parse(now())->subHours(11);
            $end_time = Carbon::parse(now())->subHours(11)->endOfDay();
            $minutes = $end_time->diffInMinutes($now);
            $response = new Response;
            $response->withCookie( 'event', 'confirm', $minutes);
            return $response;

        }catch(\Exception $e){
            \Log::info($e);
            return 'Confirm Failed!';
        }
    }


    private function formatCustomersArr($customers){
        $arr = [];

        foreach ($customers as $key => $value) {
            $arr[$value->month] = $value->count;
        }

        return $arr;
    }

    public function confirmBirthday(){
        try{
            //subHours(11) to get time American
            $now = Carbon::parse(now());
            $end_time = Carbon::parse(now())->endOfDay();
            $minutes = $end_time->diffInMinutes($now);
            $response = new Response;
            $response->withCookie( 'birthday', 'confirm', $minutes);
            return $response;

        }catch(\Exception $e){
            \Log::info($e);
            return 'Confirm Failed!';
        }
    }
}
