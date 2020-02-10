<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MainCustomerTemplate;
use App\Models\MainNotification;
use App\Models\MainUser;
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
use Gate;
use App\Models\MainUserCustomerPlace;
use App\Models\MainComboService;
use App\Models\MainUserReview;

class DashboardController extends Controller {

    public function __construct()
    {

    }

    public function index(){

        if(Gate::denies('permission','dashboard-read'))
            return doNotPermission();

        $yearNow = format_year(get_nowDate());
        $now = get_nowDate();

        $data['earnings'] = MainComboServiceBought::getSumChargeByYear($yearNow);
        $data['pendingTasks'] = MainTask::getPendingTasks();
        $data['popularServices'] = MainComboServiceBought::getServicesByMonth($now);

        $data['nearlyExpired'] = MainCustomerService::getNearlyExpired();


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

        // $customer_list = Auth::user()->user_customer_list;
        $customer_list = MainUserCustomerPlace::select('customer_id')->where('user_id',Auth::user()->user_id);

        if($customer_list->count() == 0)
            return response(['status'=>'error','message'=>'Customer empty!']);
        $customer_arr = $customer_list->get()->toArray();
        $customer_arr = array_values($customer_arr);
        // return $customer_arr;

        $customer_info =  MainCustomerTemplate::where('ct_active',1)
                        ->whereIn('id',$customer_arr)
                        ->where(function ($query) use ($customer_phone){
                            $query->where('ct_business_phone',$customer_phone)
                                ->orWhere('ct_cell_phone',$customer_phone);
                        })->first();
        if($customer_info == "")
            return response(['status'=>'error','message'=>'Customer Error!']);
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

        $customer_service_list = MainCustomerService::with('getUpdatedBy')->with('getCreatedBy')
            ->active()
            ->whereBetween('cs_date_expire',[$today,$date_expired])
            ->where('cs_customer_id','!=',null);

        if(Gate::allows('permission','dashboard-admin')){

        }
        elseif(Gate::allows('permission','dashboard-leader'))
            $customer_service_list = $customer_service_list->whereIn('created_by',MainUser::getCustomerOfTeam());
        else
            $customer_service_list = $customer_service_list->where('created_by',Auth::user()->user_id);

        $customer_service_list = $customer_service_list->get();
        $customer_service_list = $customer_service_list->groupBy('cs_customer_id');
//        return $customer_service_list;
        foreach ($customer_service_list  as $key => $services){

            $customer_info = MainCustomer::where('customer_id',$key)->first();
            $service_info = "";
            $seller_info = "";
            $expired_date = "";

            foreach ($services as $service){

                if(!empty($service->updated_by))
                    $seller_info .= $service->getUpdatedBy->user_firstname." ".$service->getUpdatedBy->user_lastname."<br>";
                elseif(!empty($service->created_by))
                    $seller_info .= $service->getCreatedBy->user_firstname." ".$service->getCreatedBy->user_lastname."<br>";

                if($service->getUpdatedBy->user_firstname)
                $service_info .= $service->getComboService->cs_name."<br>";

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
                return '<a class="order-service" href="'.route('add-order',$row['customer_customer_template_id']).'" title="Go To Order"><i class="fas fa-shopping-cart"></i></a>';
            })
            ->editColumn('customer_name',function($row){
                return '<a href="'.route('customer-detail',$row['customer_customer_template_id']).'">'.$row['cs_id']." ".$row['customer_name'].'</a>';
            })
            ->rawColumns(['action','seller_name','service_info','expired_date','customer_name'])
            ->make(true);
    }
    public function reviewDashboardDatatable(Request $request){

        $task_with_review = [];
        $review_total = 0;
        $percent_complete = 0;
        $review_this_month = 0;
        $successfully_total = 0;
        $current_month = date('m');
        $current_year = date('Y');
        $user_id = Auth::user()->user_id;

        //GET REVIEW SERVICE
        $review_service = MainComboService::where(function($query){
            $query->where('cs_form_type',1)
            ->orWhere('cs_form_type',3);
        })->select('id')->get()->toArray();

        $review_service_arr = array_values($review_service);

        $tasks = MainTask::with('getPlace')
            ->where('status','!=',3)
            ->where(function($query) use ($user_id){
            $query->where('assign_to',$user_id)
            ->orWhere('assign_to','like','%;'.$user_id)
            ->orWhere('assign_to','like','%;'.$user_id.';%')
            ->orWhere('assign_to','like',$user_id.';%');
        })->whereIn('service_id',$review_service_arr)->where('content','!=',null)

        ->where(function($query) use($current_year,$current_month){
            $query->whereDate('date_start','<=',$current_year."-".$current_month."-31")
            ->whereDate('date_end','>=',$current_year."-".$current_month."-1");
        })->get();

        foreach ($tasks as $key => $task) {


            $review_total_list = MainUserReview::where(function ($query) use ($user_id){
                    $query->where('user_id',$user_id)
                        ->orWhere('user_id','like','%;'.$user_id)
                        ->orWhere('user_id','like','%;'.$user_id.";%")
                        ->orWhere('user_id','like',$user_id.';%');
                })
            ->where('task_id',$task->id)
            ->latest();
            $review_total_list = $review_total_list->whereMonth('updated_at',$current_month)
                            ->whereYear('updated_at',$current_year)->get();

            $successfully_total  = $review_total_list->unique('review_id')->where('status',1)->count();


            $content = json_decode($task->content,TRUE);

            if(isset($content['number']) || isset($content['order_review'])){

                $start_month = Carbon::parse($task->date_start)->format('m');
                $end_month = Carbon::parse($task->date_end)->format('m');

                $year_start = Carbon::parse($task->date_start)->format('Y');
                $year_end = Carbon::parse($task->date_end)->format('Y');

                $count_year = $year_end - $year_start;

                if($count_year == 0)
                    $count_month = $end_month - $start_month +1;
                else
                    $count_month = ($count_year-1)*12+(12-$start_month+1)+$end_month;

                if(isset($content['order_review']))
                    $review_number = $content['order_review'];
                elseif(isset($content['number']))
                    $review_number = $content['number'];

                if($count_month == 0)
                    $review_total += intval($review_number);
                else{
                    $review_avg_per_month = ceil(intval($review_number)/$count_month);

                    if($current_month == $end_month)
                        $review_this_month = $review_number - $review_avg_per_month*($count_month-1);
                    else
                        $review_this_month = $review_avg_per_month;
                }
                if($successfully_total < $review_this_month){

                    $review_order = $review_this_month - $successfully_total;

                    $task_with_review[] = [
                        'id' => '<a href="'.route('task-detail',$task->id).'">#'.$task->id.'</a>',
                        'place_id' => $task->getPlace->place_name,
                        'business_phone' => $task->getPlace->place_phone,
                        'order_review' => $review_order,
                        'date_end' => format_date($task->date_end)
                    ];
                }
            }
        }

        return DataTables::of($task_with_review)
            
            ->rawColumns(['id'])
            ->make(true);
    }
}