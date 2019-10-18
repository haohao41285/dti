<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MainCustomerTemplate;
use App\Models\MainNotification;
use Carbon\Carbon;
use Illuminate\Http\Response;

use App\Models\MainComboServiceBought;
use App\Models\MainTask;
use App\Models\MainCustomerBought;
use App\Models\MainCustomerService;
use App\Models\MainCustomer;
use Illuminate\Http\Request;
use Auth;



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
    public function searchCustomer(Request $request){

        $customer_phone = $request->customer_phone;

        $customer_list = Auth::user()->user_customer_list;

        if($customer_list == "")
            return response(['status'=>'error','message'=>'Customer empty!']);
        $customer_arr = explode(";",$customer_list);

        $customer_info =  MainCustomerTemplate::where('ct_active',1)
                        ->whereIn('id',$customer_arr)
                        ->where(function ($query) use ($customer_phone){
                            $query->where('ct_business_phone',$customer_phone)
                                ->orWhere('ct_cell_phone',$customer_phone);
                        })->first();
        if($customer_info == "")
            return response(['status'=>'error','message'=>'Customer empty!']);
        else
            return response(['status'=>'success','id'=>$customer_info->id]);
    }
    public function checkAllNotification(){

        $notification_update = MainNotification::where('receiver_id',Auth::user()->user_id)->notRead()->update(['read_not'=>1]);
        if(!isset($notification_update))
            return $this->getFailed();
        else
            return $this->getSuccess();
    }
    public function getNotification(Request $request){
        $number = $request->number;
        if ($number == 0){
            $notification_list = MainNotification::notRead()->where('receiver_id',Auth::user()->user_id)->latest()->get();
        }else{
            $notification_list = MainNotification::notRead()->where('receiver_id',Auth::user()->user_id)->latest()->skip(0)->take($number)->get();
        }
        if(!isset($notification_list))
            return $this->getFailed();
        else
            return response(['status'=>'success','notification_list'=>$notification_list]);
    }
    public function getFailed(){
        return response(['status'=>'error','message'=>'Processing Failed!']);
    }
    public function getSuccess(){
        return response(['status'=>'success','message'=>'Processing Successfully!']);
    }
}
