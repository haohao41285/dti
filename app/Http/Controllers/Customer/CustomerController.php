<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Option;
use App\Helpers\GeneralHelper;
use App\Models\MainCustomer;
use App\Models\MainCustomerTemplate;
use App\Models\MainTeam;
use App\Models\MainUser;
use Carbon\Carbon;
use Auth;
use DataTables;
use DB;

class CustomerController extends Controller 
{
    public function listCustomer()
    {
        $data['state'] = Option::state();
        $data['status'] = Option::status();
        return view('customer.all-customers',$data);
    }
    
    public function listMerchant()
    {
        return view('customer.all-merchants');
    }
    
    public function addCustomer()
    {
        return view('customer.customer-add');
    }
    
    // public function editCustomer()
    // {
    //     return view('customer.customer-edit');
    // }

    public function listMyCustomer(){
        $data['state'] = Option::state();
        $data['status'] = Option::status();
        return view('customer.my-customers',$data);
    }

    public function customersDatatable(){

        $customer_arr = [];
        $team_id = Auth::user()->user_team;
        $customers = MainCustomerTemplate::leftjoin('main_user',function($join){
                                            $join->on('main_customer_template.created_by','main_user.user_id');
                                        })
                                        ->select('main_user.user_nickname','main_customer_template.*')
                                        ->get();

        //GET LIST TEAM CUSTOMER LIST 
        $team_customer_status = MainTeam::where('id',$team_id)->first()->team_customer_status;

        $customer_status_arr = json_decode($team_customer_status,TRUE);

        foreach ($customers as $key => $customer) {

            $ct_status = GeneralHelper::getCustomerStatus($customer_status_arr[$customer->id]);

            $customer_arr[] = [
                'id' => $customer->id,
                'ct_salon_name' => $customer->ct_salon_name,
                'ct_contact_name' => $customer->ct_contact_name,
                'ct_business_phone' => $customer->ct_business_phone,
                'ct_cell_phone' => $customer->ct_cell_phone,
                'ct_status' => $ct_status,
                'created_at' => $customer->created_at,
                'user_nickname' => $customer->user_nickname,
                'ct_note' => $customer->ct_note
            ];
        }
        return Datatables::of($customer_arr)
            ->editColumn('created_at',function($row){
                return Carbon::parse($row['created_at'])->format('m/d/Y H:i:s')." by ".$row['user_nickname'];
            }) 
            ->editColumn('ct_business_phone',function($row){
                return substr($row['ct_business_phone'],0,3)."########";
            }) 
            ->editColumn('ct_cell_phone',function($row){
                return substr($row['ct_cell_phone'],0,3)."########";
            })     
            ->addColumn('action', function ($row){
                return '<a class="btn btn-sm btn-secondary view" customer_id="'.$row['id'].'" href="javascript:void(0)"><i class="fas fa-eye"></i></a> <a class="btn btn-sm btn-secondary edit-customer" customer_id="'.$row['id'].'" href="javascript:void(0)"><i class="fas fa-edit"></i></a>
                    <a class="btn btn-sm btn-secondary" href="#"><i class="fas fa-trash"></i></a>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    public function getCustomerDetail(Request $request){

        $customer_id = $request->customer_id;
        $team_id = Auth::user()->user_team;

        $customer_list = MainCustomerTemplate::leftjoin('main_user',function($join){
                                                $join->on('main_customer_template.updated_by','main_user.user_id');
                                            })
                                            ->where('main_customer_template.id',$customer_id)
                                            ->select('main_customer_template.*','main_user.user_nickname')
                                            ->first();

        if(!isset($customer_list))
            return 0;
        else{

            $team_customer_status = MainTeam::where('id',$team_id)->first()->team_customer_status;
            $customer_status_arr = json_decode($team_customer_status,TRUE);
            $customer_status = GeneralHelper::getCustomerStatus($customer_status_arr[$customer_list->id]);

            $customer_list['ct_status'] = $customer_status;
            $customer_list['ct_business_phone'] = substr($customer_list->ct_business_phone,0,3)."########";
            $customer_list['ct_cell_phone'] = substr($customer_list->ct_cell_phone,0,3)."########";

            return $customer_list;
        }
    }
    public function addCustomerToMy(Request $request){

        $customer_id = $request->customer_id;
        $user_customer_arr = [];
        $user_id = Auth::user()->user_id;
        $team_id = Auth::user()->user_team;

        DB::beginTransaction();

        $user_customer_list = Auth::user()->user_customer_list;

        if($user_customer_list == NULL){

            $user_customer_arr[] = $customer_id;
        }else{
            $user_customer_arr = explode(";", $user_customer_list);

            array_push($user_customer_arr,$customer_id);
        }
        $user_customer_list_after = implode(";", $user_customer_arr);
    //UPDATE LIST CUSTOMER
        $update_user = MainUser::where('user_id',$user_id)->update(['user_customer_list'=>$user_customer_list_after]);
    //UPDATE CUSTOMER STATUS
        $team_customer_status = MainTeam::where('id',$team_id)->first()->team_customer_status;
        $customer_status_arr = json_decode($team_customer_status,TRUE);
        $customer_status_arr[$customer_id] = 1;
        $customer_status_list = json_encode($customer_status_arr);
        $update_customer = MainTeam::where('id',$team_id)->update(['team_customer_status'=>$customer_status_list]);

        if(!isset($update_user) || !isset($update_customer)){
            DB::callback();
            return 0;
        }
        else{
            DB::commit();
            return 1;
        }
    }
    public function getMyCustomer(Request $request){

        $user_id = 1;
        $team_id = Auth::user()->user_team;

        $user_customer_list = MainUser::where('user_id',$user_id)->first()->user_customer_list;

        if($user_customer_list != NULL){

            $user_customer_arr = explode(";", $user_customer_list);

            $customer_list = MainCustomerTemplate::whereIn('id',$user_customer_arr)->get();

            //GET LIST TEAM CUSTOMER LIST 
            $team_customer_status = MainTeam::where('id',$team_id)->first()->team_customer_status;

            $customer_status_arr = json_decode($team_customer_status,TRUE);

            foreach ($customer_list as $key => $customer) {

                $ct_status = GeneralHelper::getCustomerStatus($customer_status_arr[$customer->id]);

                $customer_arr[] = [
                    'id' => $customer->id,
                    'ct_salon_name' => $customer->ct_salon_name,
                    'ct_contact_name' => $customer->ct_contact_name,
                    'ct_business_phone' => $customer->ct_business_phone,
                    'ct_cell_phone' => $customer->ct_cell_phone,
                    'ct_status' => $ct_status,
                    'updated_at' => $customer->updated_at,
                    'user_nickname' => $customer->user_nickname
                ];
            }
            return Datatables::of($customer_arr)
                ->editColumn('updated_at',function($row){
                    return Carbon::parse($row['updated_at'])->format('m/d/Y')." by ".$row['user_nickname'];
                })     
                ->addColumn('action', function ($row){
                    return '<a class="btn btn-sm btn-secondary view" customer_id="'.$row['id'].'" href="javascript:void(0)"><i class="fas fa-eye"></i></a>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }
    public function editCustomer(Request $request){

        $customer_id = $request->customer_id;

        $customer_info = MainCustomerTemplate::find($customer_id);

        if(!isset($customer_info)){
            return 0;
        }else
            return $customer_info;
    }
    public function saveCustomer(Request $request){

        $ct_salon_name = $request->ct_salon_name;
        $ct_contact_name = $request->ct_contact_name;
        $ct_business_phone = $request->ct_business_phone;
        $ct_cell_phone = $request->ct_cell_phone;
        $ct_email = $request->ct_email;
        $ct_website = $request->ct_website;
        $ct_address = $request->ct_address;
        $ct_note = $request->ct_note;
        $customer_id = $request->customer_id;

        $customer_info = [
            'ct_salon_name' => $ct_salon_name,
            'ct_contact_name' => $ct_contact_name,
            'ct_business_phone' => $ct_business_phone,
            'ct_cell_phone' => $ct_cell_phone,
            'ct_email' => $ct_email,
            'ct_website' => $ct_website,
            'ct_address' => $ct_address,
            'ct_note' => $ct_note,
            'updated_by' => Auth::user()->user_id,
        ];

        $customer_update = MainCustomerTemplate::where('id',$customer_id)->update($customer_info);

        if(!isset($customer_update))
            return 0;
        else
            return 1;
    }
}