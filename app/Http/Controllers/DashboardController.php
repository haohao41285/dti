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
use DataTables;


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
        $data['popularServices'] = MainComboServiceBought::getServicesByMonth($now);

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
    public function customerServiceDatatable(Request $request){

        $today = today();
        $date_expired = today()->addDays(15);
        $cs_arr = [];

        $customer_service_list = MainCustomerService::join('main_user',function($join){
            $join->on('main_customer_service.created_by','main_user.user_id');
        })
            ->active()
            ->whereBetween('cs_date_expire',[$today,$date_expired])
           ->where('main_customer_service.cs_customer_id','!=',null);

        if(Auth::user()->user_group_id != 1)
            $customer_service_list = $customer_service_list->where('main_customer_service.created_by',Auth::user()->user_id);

        $customer_service_list = $customer_service_list->get();
        $customer_service_list = $customer_service_list->groupBy('cs_customer_id');

        foreach ($customer_service_list  as $key => $services){

            $customer_info = MainCustomer::where('customer_id',$key)->first();
            $service_info = "";
            $seller_info = "";
            $expired_date = "";

            foreach ($services as $service){
                $service_info .= $service->getComboService->cs_name."<br>";
                $seller_info .= $service->user_firstname." ".$service->user_lastname."<br>";
                $expired_date .= $service->cs_date_expire."<br>";
            }
            $cs_arr[] = [
                'cs_id' => $key,
                'customer_name' => $customer_info->customer_firstname. " ". $customer_info->customer_lastname,
                'customer_phone' => $customer_info->customer_phone,
                'service_info' => $service_info,
                'seller_name' => $seller_info,
                'expired_date' => $expired_date,
                'customer_customer_template_id' => $customer_info->customer_customer_template_id
            ];
        }
        return DataTables::of($cs_arr)
            ->editColumn('cs_id',function ($row){
                return '<a href="'.route('customer-detail',$row['customer_customer_template_id']).'">'.$row['cs_id'].'</a>';
            })
            ->addColumn('action',function($row){
                return '<a class="btn btn-sm btn-secondary order-service" href="'.route('add-order',$row['customer_customer_template_id']).'" title="Go To Order"><i class="fas fa-shopping-cart"></i></a>';
            })
            ->rawColumns(['action','seller_name','service_info','expired_date','cs_id'])
            ->make(true);

    }
}
