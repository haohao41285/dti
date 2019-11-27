<?php

namespace App\Http\Controllers;

use App\Helpers\GeneralHelper;
use App\Models\MainComboService;
use App\Models\MainComboServiceBought;
use App\Models\MainCustomer;
use App\Models\MainCustomerTemplate;
use App\Models\MainTeam;
use App\Models\MainUser;
use App\Models\MainUserCustomerPlace;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Auth;
use Carbon\Carbon;
use Gate;

class ReportController extends Controller
{
    public function customers(){
        $data['status'] = GeneralHelper::getCustomerStatusList();
        if(Gate::allows('permission','customer-report-admin'))
             $data['teams'] = MainTeam::active()->get();
        else
             $data['teams'] = MainTeam::where('id',Auth::user()->user_team)->get();
        return view('reports.customers',$data);
    }
    public function getCustomerList($request){

        if(Gate::denies('permission','customer-report'))
            return doNotPermission();

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $address = $request->address;
        $status_customer = $request->status_customer;
        $customer_arr = [];

        if(Gate::allows('permission','customer-report-admin')){
            $team_id = $request->team_id;
            $customer_list = MainCustomerTemplate::select('*');
        }
        else{
            $team_id = Auth::user()->user_team;
            if(Gate::allows('permission','customer-report-leader')){
                $user_customer_list = MainUserCustomerPlace::where([
                    ['team_id',$team_id],
                ])->select('customer_id')->get()->toArray();
            }
            else{
                $user_id = Auth::user()->user_id;
                $user_customer_list = MainUserCustomerPlace::where([
                    ['user_id',$user_id],
                    ['team_id',$team_id],
                ])->select('customer_id')->get()->toArray();
            }
            $user_customer_arr = array_values($user_customer_list);
            $customer_list = MainCustomerTemplate::whereIn('id', $user_customer_arr);
        }

            if($start_date != "" && $end_date != ""){

                $start_date = Carbon::parse($start_date)->format('Y-m-d');
                $end_date = Carbon::parse($end_date)->format('Y-m-d');

                $customer_list->whereDate('created_at','>=',$start_date)
                    ->whereDate('created_at','<=',$end_date);
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
                            $seller_name .= $seller->getCreatedBy->user_nickname.";";
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
         return $customer_arr;

    }
    public function customersDataTable( Request $request){
        $customer_list = self::getCustomerList($request);
        return Datatables::of($customer_list)
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
        if(Gate::denies('permission','service-report'))
            return doNotPermission;
        if(Gate::allows('permission','service-report-admin'))
            $data['sellers'] = MainUser::all();
        elseif(Gate::allows('permission','service-report-leader'))
            $data['sellers'] = MainUser::where('user_team',Auth::user()->user_team)->get();
        else
            $data['sellers'] = MainUser::where('user_id',Auth::user()->user_id)->get();

        return view('reports.services',$data);
    }
    public function getServiceList($request){

        if(Gate::allows('permission','service-report-admin'))
            $combo_service_list = MainComboService::orderBy('cs_combo_service_type','asc')->get();
        else{
            $service_list = MainTeam::find(Auth::user()->user_team)->getTeamType->service_permission;
            $service_list = explode(';',$service_list);
            $combo_service_list = MainComboService::whereIn('id',$service_list)->orderBy('cs_combo_service_type','asc')->get();
        }

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
        return $service_customer_result;
    }
    public function servicesDataTable(Request $request){

        $service_list = self::getServiceList($request);
        return DataTables::of($service_list)
            ->make(true);
    }
    public function sellers(){

        if(Gate::denies('permission','seller-report'))
            return doNotPermission;
        if(Gate::allows('permission','seller-report-admin'))
            $data['sellers'] = MainUser::all();
//        elseif(Gate::allows('permission','seller-report-leader'))
        else
            $data['sellers'] = MainUser::where('user_team',Auth::user()->user_team)->get();
        return view('reports.sellers',$data);
    }
    public function getSellerList($request){

        if(Gate::allows('permission','seller-report-admin'))
            $user_list = MainUser::select('*');
//        elseif(Gate::allows('permission','seller-report-leader'))
        else
            $user_list = MainUser::where('user_team',Auth::user()->user_team);

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
        return $seller_list;
    }
    public function sellersDataTable(Request $request){
        $seller_list = self::getSellerList($request);
        return DataTables::of($seller_list)
            ->make(true);
    }

    public function customersTotal(Request $request){
        $team_id = $request->team_id;

        $arrivals_total = 0;
        $assigned_total = 0;
        $serviced_total = 0;
        $disabled_total = 0;

        if(Gate::allows('permission','customer-report-admin')){
            $customer_list = MainCustomerTemplate::select('*');
        }
        else{
            if(Gate::allows('permission','customer-report-leader')){
                $user_customer_list = MainUserCustomerPlace::where([
                    ['team_id',$team_id],
                ])->select('customer_id')->get()->toArray();
            }
            else{
                $user_id = Auth::user()->user_id;
                $user_customer_list = MainUserCustomerPlace::where([
                    ['user_id',$user_id],
                    ['team_id',$team_id],
                ])->select('customer_id')->get()->toArray();
            }
                $user_customer_arr = array_values($user_customer_list);
                $customer_list = MainCustomerTemplate::with('getCreatedBy')->whereIn('id', $user_customer_arr);
        }

            if($request->start_date != "" && $request->end_date != ""){
                $start_date = format_date_db($request->start_date);
                $end_date = format_date_db($request->start_date);

                $customer_list->whereDate('created_at','>=',$start_date)
                    ->whereDate('created_at','<=',$end_date);
            }
            if($request->address != ""){
                $customer_list->where('ct_address','LIKE',"%".$request->address."%");
            }

            $customer_list = $customer_list->get();

            //GET LIST TEAM CUSTOMER LIST
            $team_customer_status = MainTeam::find($team_id)->getTeamType->team_customer_status;

            $customer_status_arr = json_decode($team_customer_status,TRUE);

            foreach ($customer_list as $key => $customer) {
                if(!isset($customer_status_arr[$customer->id])){
                    //New Arrivals
                    $arrivals_total++;
                }
                else{
                    switch ($customer_status_arr[$customer->id]) {
                        case 1:
                            //Assigned
                            $assigned_total++;
                            break;
                        case 4:
                            //Serviced
                            $serviced_total++;
                            break;

                        default:
                            //Disabled
                            $disabled_total++;
                            break;
                    }
                }
            }

        return response([
            'arrivals_total' => $arrivals_total,
            'assigned_total' => $assigned_total,
            'serviced_total' => $serviced_total,
            'disabled_total' => $disabled_total
        ]);
    }
    public function customersExport(Request $request){

        $customer_list = self::getCustomerList($request);
        $date = Carbon::now()->format('Y_m_d_His');
        //GET TEAM NAME
        $team_name = MainTeam::find($request->team_id)->team_name;

        return \Excel::create('customer_list_'.$team_name."_".$date,function($excel) use ($customer_list){

            $excel ->sheet('Customer List', function ($sheet) use ($customer_list)
            {
                $sheet->cell('A1', function($cell) {$cell->setValue('ID');   });
                $sheet->cell('B1', function($cell) {$cell->setValue('Nail Shop');   });
                $sheet->cell('C1', function($cell) {$cell->setValue('Contact Name');   });
                $sheet->cell('D1', function($cell) {$cell->setValue('Business Phone');   });
                $sheet->cell('E1', function($cell) {$cell->setValue('Cell Phone');   });
                $sheet->cell('F1', function($cell) {$cell->setValue('Status');   });
                $sheet->cell('G1', function($cell) {$cell->setValue('Seller');   });
                $sheet->cell('H1', function($cell) {$cell->setValue('Discount Total');   });
                $sheet->cell('I1', function($cell) {$cell->setValue('Charged Total');   });

                if (!empty($customer_list)) {
                    foreach ($customer_list as $key => $value) {
                        $i=$key+2;
                        $sheet->cell('A'.$i, $value['id']);
                        $sheet->cell('B'.$i, $value['ct_salon_name']);
                        $sheet->cell('C'.$i, $value['ct_fullname']);
                        $sheet->cell('D'.$i, $value['ct_business_phone']);
                        $sheet->cell('E'.$i, $value['ct_cell_phone']);
                        $sheet->cell('F'.$i, $value['ct_status']);
                        $sheet->cell('G'.$i, $value['seller']);
                        $sheet->cell('H'.$i, $value['discount_total']);
                        $sheet->cell('I'.$i, $value['charged_total']);
                    }
                }
            });
        })->download("xlsx");
    }
    public function serviceExport(Request $request){

        $service_list = self::getServiceList($request);
        $date = Carbon::now()->format('Y_m_d_His');
        return \Excel::create('service_list_'.$date,function($excel) use ($service_list){

            $excel ->sheet('Service List', function ($sheet) use ($service_list)
            {
                $sheet->cell('A1', function($cell) {$cell->setValue('ID');   });
                $sheet->cell('B1', function($cell) {$cell->setValue('Serivce');   });
                $sheet->cell('C1', function($cell) {$cell->setValue('Service Price($)');   });
                $sheet->cell('D1', function($cell) {$cell->setValue('Total Customers');   });
                $sheet->cell('E1', function($cell) {$cell->setValue('Total Orders');   });

                if (!empty($service_list)) {
                    foreach ($service_list as $key => $value) {
                        $i=$key+2;
                        $sheet->cell('A'.$i, $value['id']);
                        $sheet->cell('B'.$i, $value['service_name']);
                        $sheet->cell('C'.$i, $value['service_price']);
                        $sheet->cell('D'.$i, $value['customer_total']);
                        $sheet->cell('E'.$i, $value['order_total']);
                    }
                }
            });
        })->download("xlsx");
    }
    public function sellerExport(Request $request){

        $seller_list = self::getSellerList($request);
        $date = Carbon::now()->format('Y_m_d_His');
        return \Excel::create('seller_list'.$date,function($excel) use ($seller_list){

            $excel ->sheet('Seller List', function ($sheet) use ($seller_list)
            {
                $sheet->cell('A1', function($cell) {$cell->setValue('ID');   });
                $sheet->cell('B1', function($cell) {$cell->setValue('Username');   });
                $sheet->cell('C1', function($cell) {$cell->setValue('Fulllname');   });
                $sheet->cell('D1', function($cell) {$cell->setValue('Email');   });
                $sheet->cell('E1', function($cell) {$cell->setValue('Total Assigned Customers');   });
                $sheet->cell('F1', function($cell) {$cell->setValue('Total Serviced Customers');   });
                $sheet->cell('G1', function($cell) {$cell->setValue('Total Order');   });
                $sheet->cell('H1', function($cell) {$cell->setValue('Total Discount($)');   });
                $sheet->cell('I1', function($cell) {$cell->setValue('Total Charged($)');   });

                if (!empty($seller_list)) {
                    foreach ($seller_list as $key => $value) {
                        $i=$key+2;
                        $sheet->cell('A'.$i, $value['id']);
                        $sheet->cell('B'.$i, $value['user_nickname']);
                        $sheet->cell('C'.$i, $value['user_fullname']);
                        $sheet->cell('D'.$i, $value['email']);
                        $sheet->cell('E'.$i, $value['total_assigned']);
                        $sheet->cell('F'.$i, $value['total_serviced']);
                        $sheet->cell('G'.$i, $value['total_orders']);
                        $sheet->cell('H'.$i, $value['total_discount']);
                        $sheet->cell('I'.$i, $value['total_charged']);
                    }
                }
                else{
                    return back('error','Empty Data for Exporting!');
                }
            });
        })->download("xlsx");
    }
}
