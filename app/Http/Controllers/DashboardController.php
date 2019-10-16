<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MainCustomerTemplate;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Auth;
use OneSignal;


class DashboardController extends Controller {

    public function __construct()
    {

    }

    public function index()
    {
        return view('dashboard');
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
    public function testOnesignal(){
        OneSignal::sendNotificationUsingTags("Cậu Thiệu mới thêm một comment trong order #11",
            array(["field" => "tag", "key" => "user_id", "relation" => "=", "value" => '2']),
            $url = route('my-task'), $data = ['name'=>'thieu'], $buttons = null, $schedule = null
        );
//        OneSignal::sendNotificationToAll("message !", $url = 'https://www.youtube.com/', $data = null, $buttons = null, $schedule = null);
    }
}
