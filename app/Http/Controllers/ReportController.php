<?php

namespace App\Http\Controllers;

use App\Helpers\GeneralHelper;
use App\Models\MainComboService;
use App\Models\MainComboServiceBought;
use App\Models\MainCustomer;
use App\Models\MainCustomerNote;
use App\Models\MainCustomerService;
use App\Models\MainCustomerTemplate;
use App\Models\MainTeam;
use App\Models\MainUser;
use App\Models\MainUserCustomerPlace;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Auth;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function customers(){
        $data['status'] = GeneralHelper::getCustomerStatusList();
        return view('reports.customers',$data);
    }
    public function customersDataTable(Request $request){

//        if(Gate::denies('permission','my-customer-read'))
//            return doNotPermission();

        $user_id = Auth::user()->user_id;
        $team_id = Auth::user()->user_team;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $address = $request->address;
        $status_customer = $request->status_customer;
        $customer_arr = [];

        $user_customer_list = MainUserCustomerPlace::where([
            ['user_id',$user_id],
            ['team_id',$team_id],
        ])->select('customer_id')->get()->toArray();

        if($user_customer_list != NULL){

            $user_customer_arr = array_values($user_customer_list);

            $customer_list = MainCustomerTemplate::with('getCreatedBy')->whereIn('main_customer_template.id',$user_customer_arr);

            if($start_date != "" && $end_date != ""){

                $start_date = Carbon::parse($start_date)->format('Y-m-d');
                $end_date = Carbon::parse($end_date)->format('Y-m-d');

                $customer_list->whereDate('main_customer_template.created_at','>=',$start_date)
                    ->whereDate('main_customer_template.created_at','<=',$end_date);
            }
            if($address != ""){
                $customer_list->where('ct_address','LIKE',"%".$address."%");
            }

            $customer_list = $customer_list->get();

            //GET LIST TEAM CUSTOMER LIST
            $team_customer_status = MainTeam::find($team_id)->getTeamType->team_customer_status;

            $customer_status_arr = json_decode($team_customer_status,TRUE);

            foreach ($customer_list as $key => $customer) {

                $discount_total = 0;
                $seller_name = "";
                $charged_total = 0;
                if(!isset($customer_status_arr[$customer->id])){
                    $customer_status_arr[$customer->id] = 1;
                    $ct_status = 'New Arrivals';
                }
                else{
                    $ct_status = GeneralHelper::getCustomerStatus($customer_status_arr[$customer->id]);
                    if($customer_status_arr[$customer->id] == 4){
                        $order_info = MainComboServiceBought::where('csb_customer_id',MainCustomer::where('customer_customer_template_id',$customer->id)->first()->customer_id)
                            ->with('getCreatedBy');
                        $seller_list  = $order_info->select('created_by')->distinct('created_by')->get();
                        foreach($seller_list as $seller){
                            $seller_name .= $seller->getCreatedBy->user_nickname."<br>";
                        }
                        $discount_total = $order_info->sum('csb_amount_deal');
                        $charged_total = $order_info->sum('csb_charge');
                    }
                }

                if($status_customer != "" && intval($customer_status_arr[$customer->id]) ==  intval($status_customer)){
                    $customer_arr[] = [
                        'id' => $customer->id,
                        'ct_salon_name' => $customer->ct_salon_name,
                        'ct_fullname' => $customer->ct_fullname,
                        'ct_business_phone' => $customer->ct_business_phone,
                        'ct_cell_phone' => $customer->ct_cell_phone,
                        'ct_status' => $ct_status,
                        'seller' => $seller_name,
                        'discount_total' => $discount_total,
                        'charged_total' => $charged_total
                    ];
                }
                if($status_customer == ""){
                    $customer_arr[] = [
                        'id' => $customer->id,
                        'ct_salon_name' => $customer->ct_salon_name,
                        'ct_fullname' => $customer->ct_fullname,
                        'ct_business_phone' => $customer->ct_business_phone,
                        'ct_cell_phone' => $customer->ct_cell_phone,
                        'ct_status' => $ct_status,
                        'seller' => $seller_name,
                        'discount_total' => $discount_total,
                        'charged_total' => $charged_total
                    ];
                }
            }
        }
        return Datatables::of($customer_arr)
            ->editColumn('id',function ($row){
                if($row['ct_status'] == 'Serviced')
                    return '<a href="'.route('customer-detail',$row['id']).'">'.$row['id'].'</a>';
                else
                    return '<a href="javascript:void(0)">'.$row['id'].'</a>';
            })
            ->rawColumns(['seller','id'])
            ->make(true);
    }
    public function services(){
        $data['sellers'] = MainUser::active()->get();
        return view('reports.services',$data);
    }
    public function servicesDataTable(Request $request){

        $combo_service_list = MainComboService::orderBy('cs_combo_service_type','asc')->get();
        $service_customer_result = [];

        foreach ($combo_service_list as $combo_service){

            $service_id = $combo_service->id;

            $service_customer = MainComboServiceBought::where(function($query) use ($service_id){
                $query->where('csb_combo_service_id',$service_id)
                    ->orWhere('csb_combo_service_id','like','%;'.$service_id)
                    ->orWhere('csb_combo_service_id','like','%;'.$service_id.';%')
                    ->orWhere('csb_combo_service_id','like',$service_id.';%');
            });
            if($request->address != ""){

                $address_customer = $request->address;

                $service_customer = $service_customer->join('main_customer',function($join) use ($address_customer){
                    $join->on('main_combo_service_bought.csb_customer_id','main_customer.customer_id')
                    ->where('main_customer.customer_address','like','%'.$address_customer.'%');
                });

                if($request->start_date != "" && $request->end_date != ""){
                    $start_date = format_date_db($request->start_date);
                    $end_date = format_date_db($request->end_date);
                    $service_customer = $service_customer->whereBetween('main_combo_service_bought.created_at',[$start_date,$end_date]);
                }

                if($request->seller_id != ""){
                    $service_customer = $service_customer->where('main_combo_service_bought.created_by',$request->seller_id);
                }

                $customer_total = $service_customer->distinct('main_combo_service_bought.csb_customer_id')->count('main_combo_service_bought.csb_customer_id');
                $order_total = $service_customer->count();

            }else{

                if($request->start_date != "" && $request->end_date != ""){
                    $start_date = format_date_db($request->start_date);
                    $end_date = format_date_db($request->end_date);
                    $service_customer = $service_customer->whereBetween('created_at',[$start_date,$end_date]);
                }
                if($request->seller_id != ""){
                    $service_customer = $service_customer->where('created_by',$request->seller_id);
                }
                $customer_total = $service_customer->distinct('csb_customer_id')->count('csb_customer_id');
                $order_total = $service_customer->count();
            }

            $service_customer_result[] = [
                'id' => $combo_service->id,
                'service_name' => $combo_service->cs_name,
                'service_price' => $combo_service->cs_price,
                'customer_total' => $customer_total,
                'order_total' => $order_total
            ];
        }
        return DataTables::of($service_customer_result)
            ->make(true);
    }
    public function sellers(){
        $data['sellers'] = MainUser::active()->get();
        return view('reports.sellers',$data);
    }
    public function sellersDataTable(Request $request){

        $user_list  = MainUser::active();

        if($request->start_date != "" && $request->end_date){
            $start_date = format_date_db($request->start_date);
            $end_date = format_date_db($request->end_date);
            $user_list->whereBetween('created_at',[$start_date,$end_date]);
        }
        if($request->seller_id != ""){
            $user_list->where('user_id',$request->seller_id);
        }
        $user_list =  $user_list->get();

        $user_customer_place = MainUserCustomerPlace::all();
        $user_customer_place = collect($user_customer_place);
        $order_list_collect = MainComboServiceBought::all();
        $order_list_collect = collect($order_list_collect);

        $seller_list = [];

        foreach ($user_list as $user){

            $total_assigned = $user_customer_place->where('user_id',$user->user_id)->where('place_id',null)->count();
            $total_serviced = $user_customer_place->where('user_id',$user->user_id)->where('place_id','!=',null)->count();
            $total_orders = $order_list_collect->where('created_by',$user->user_id)->count();
            $total_discount = $order_list_collect->where('created_by',$user->user_id)->sum('csb_amount_deal');
            $total_charged = $order_list_collect->where('created_by',$user->user_id)->sum('csb_charge');

                $seller_list[] = [
                    'id' => $user->user_id,
                    'user_nickname' => $user->user_nickname,
                    'user_fullname' => $user->getFullname(),
                    'email' => $user->user_email,
                    'total_assigned' => $total_assigned,
                    'total_serviced' => $total_serviced,
                    'total_orders' => $total_orders,
                    'total_discount' => $total_discount,
                    'total_charged' => $total_charged
                ];
        }
        return DataTables::of($seller_list)
            ->make(true);

    }
}
