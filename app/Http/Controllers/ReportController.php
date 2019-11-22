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
        return view('reports.services');
    }
    public function servicesDataTable(Request $request){
//        $service_list = MainComboServiceBought::with('getCustomer')->with('getCreatedBy')->get();
        $combo_service_list = MainComboService::orderBy('cs_combo_service_type','asc')->get();
        return $combo_service_list;
//        return DataTables::of($service_list)
//            ->make(true);
    }
}
