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
        $data['status'] = GeneralHelper::getCustomerStatusList();
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

    public function customersDatatable(Request $request){

        $customer_arr = [];
        $team_id = Auth::user()->user_team;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $address = $request->address;
        $status_customer = $request->status_customer;

        $customers = MainCustomerTemplate::leftjoin('main_user',function($join){
                                            $join->on('main_customer_template.created_by','main_user.user_id');
                                        });

        if($start_date != "" && $end_date != ""){

            $start_date = Carbon::parse($start_date)->format('Y-m-d');
            $end_date = Carbon::parse($end_date)->format('Y-m-d');

            $customers->whereDate('main_customer_template.created_at','>=',$start_date)
                    ->whereDate('main_customer_template.created_at','<=',$end_date);
        }
        if($address != ""){
            $customers->where('ct_address','LIKE',"%".$address."%");
        }
            $customers = $customers->orderBy('main_customer_template.created_at','ASC')
                        ->select('main_user.user_nickname','main_customer_template.*')
                        ->get();

        //GET LIST TEAM CUSTOMER LIST 
        $team_customer_status = MainTeam::where('id',$team_id)->first()->team_customer_status;

        $customer_status_arr = json_decode($team_customer_status,TRUE);

        foreach ($customers as $key => $customer) {

            if(!isset($customer_status_arr[$customer->id])){
                $customer_status_arr[$customer->id] = 1;
                $ct_status = 'Arrivals';
            }
            else
                $ct_status = GeneralHelper::getCustomerStatus($customer_status_arr[$customer->id]);

            if($status_customer != "" && intval($customer_status_arr[$customer->id]) ==  intval($status_customer)){
                $customer_arr[] = [
                    'id' => $customer->id,
                    'ct_salon_name' => $customer->ct_salon_name,
                    'ct_fullname' => $customer->ct_fullname,
                    'ct_business_phone' => $customer->ct_business_phone,
                    'ct_cell_phone' => $customer->ct_cell_phone,
                    'ct_status' => $ct_status,
                    'created_at' => $customer->created_at,
                    'user_nickname' => $customer->user_nickname,
                    'ct_note' => $customer->ct_note
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
                    'created_at' => $customer->created_at,
                    'user_nickname' => $customer->user_nickname,
                    'ct_note' => $customer->ct_note
                ];
            }
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
                if($row['ct_status'] == 'Disabled')
                    return '<a class="btn btn-sm btn-secondary view" customer_id="'.$row['id'].'" href="javascript:void(0)"><i class="fas fa-eye"></i></a> <a class="btn btn-sm btn-secondary edit-customer" customer_id="'.$row['id'].'" href="javascript:void(0)"><i class="fas fa-edit"></i></a>
                    <a class="btn btn-sm btn-secondary deleted" customer_id="'.$row['id'].'" href="javascript:void(0)"><i class="fas fa-trash text-danger"></i></a>';
                else
                    return '<a class="btn btn-sm btn-secondary view" customer_id="'.$row['id'].'" href="javascript:void(0)"><i class="fas fa-eye"></i></a> <a class="btn btn-sm btn-secondary edit-customer" customer_id="'.$row['id'].'" href="javascript:void(0)"><i class="fas fa-edit"></i></a>
                        <a class="btn btn-sm btn-secondary delete-customer" customer_id="'.$row['id'].'" href="javascript:void(0)"><i class="fas fa-trash"></i></a>';
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
            if(!isset($customer_status_arr[$customer_list->id]))
                $customer_status = 'Arrivals';
            else
                $customer_status = GeneralHelper::getCustomerStatus($customer_status_arr[$customer_list->id]);

            $customer_list['ct_status'] = $customer_status;
            $customer_list['ct_business_phone'] = substr($customer_list->ct_business_phone,0,3)."########";
            $customer_list['ct_cell_phone'] = substr($customer_list->ct_cell_phone,0,3)."########";
            // $customer_list['created_at'] = Carbon::parse($customer_list->created_at)->format('m/d/Y');

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

        $user_id = Auth::user()->user_id;
        $team_id = Auth::user()->user_team;
        $customer_arr = [];

        $user_customer_list = MainUser::where('user_id',$user_id)->first()->user_customer_list;

        if($user_customer_list != NULL){

            $user_customer_arr = explode(";", $user_customer_list);

            $customer_list = MainCustomerTemplate::leftjoin('main_user',function($join){
                                                    $join->on('main_customer_template.created_by','main_user.user_id');
                                                })
                                                ->whereIn('main_customer_template.id',$user_customer_arr)
                                                ->select('main_customer_template.*','main_user.user_nickname')
                                                ->get();

            //GET LIST TEAM CUSTOMER LIST 
            $team_customer_status = MainTeam::where('id',$team_id)->first()->team_customer_status;

            $customer_status_arr = json_decode($team_customer_status,TRUE);

            foreach ($customer_list as $key => $customer) {

                $ct_status = GeneralHelper::getCustomerStatus($customer_status_arr[$customer->id]);

                $customer_arr[] = [
                    'id' => $customer->id,
                    'ct_salon_name' => $customer->ct_salon_name,
                    'ct_fullname' => $customer->ct_fullname,
                    'ct_business_phone' => $customer->ct_business_phone,
                    'ct_cell_phone' => $customer->ct_cell_phone,
                    'ct_status' => $ct_status,
                    'updated_at' => $customer->updated_at,
                    'user_nickname' => $customer->user_nickname
                ];
            }
        }else{
            $customer_arr = [];

            $customer_arr[] = [
                    'id' => "",
                    'ct_salon_name' => "",
                    'ct_fullname' => "",
                    'ct_business_phone' => "",
                    'ct_cell_phone' => "",
                    'ct_status' => "",
                    'updated_at' => "",
                    'user_nickname' => ""
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
            'ct_fullname' => $ct_contact_name,
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
    public function deleteCustomer(Request $request){

        $customer_id = $request->customer_id;
        $team_id = Auth::user()->user_team;

        $team_customer_status = MainTeam::where('id',$team_id)->first()->team_customer_status;
        $customer_status_arr = json_decode($team_customer_status,TRUE);
        $customer_status_arr[$customer_id] = 2;
        $customer_status_list = json_encode($customer_status_arr);
        $update_customer = MainTeam::where('id',$team_id)->update(['team_customer_status'=>$customer_status_list]);

        if(!isset($update_customer))
            return 0;
        else
            return 1;
    }
    public function exportCustomer(Request $request)
    {
        $customer_list = MainCustomerTemplate::latest()->get()->toArray();

        $date = Carbon::now()->format('Y_m_d_His');
        // dd($data);
        return \Excel::create('customer_list_'.$date,function($excel) use ($customer_list){

            $excel ->sheet('Customer List', function ($sheet) use ($customer_list)
            {
                $sheet->cell('A1', function($cell) {$cell->setValue('Business Name');   });
                $sheet->cell('B1', function($cell) {$cell->setValue('Business Phone');   });
                $sheet->cell('C1', function($cell) {$cell->setValue('Cell Phone');   });
                $sheet->cell('D1', function($cell) {$cell->setValue('Fullname');   });
                $sheet->cell('E1', function($cell) {$cell->setValue('Firstname');   });
                $sheet->cell('F1', function($cell) {$cell->setValue('Lastname');   });
                $sheet->cell('G1', function($cell) {$cell->setValue('Address');   });
                $sheet->cell('H1', function($cell) {$cell->setValue('Email');   });
                $sheet->cell('I1', function($cell) {$cell->setValue('Note');   });

                if (!empty($customer_list)) {
                    foreach ($customer_list as $key => $value) {
                        $i=$key+2;
                        $sheet->cell('A'.$i, $value['ct_salon_name']); 
                        $sheet->cell('B'.$i, $value['ct_business_phone']); 
                        $sheet->cell('C'.$i, $value['ct_cell_phone']);
                        $sheet->cell('D'.$i, $value['ct_fullname']);
                        $sheet->cell('E'.$i, $value['ct_firstname']);
                        $sheet->cell('F'.$i, $value['ct_lastname']);
                        $sheet->cell('G'.$i, $value['ct_address']);
                        $sheet->cell('H'.$i, $value['ct_email']);
                        $sheet->cell('I'.$i, $value['ct_note']);
                    }
                }
            });
        })->download("xlsx");
    }
    public function importCustomer(Request $request){

        if($request->hasFile('file')){
            $path = $request->file('file')->getRealPath();
            $begin_row = $request->begin_row;
            $end_row = $request->end_row;
            $update_exist = $request->check_update_exist;
            $insert_count = 0;

            DB::beginTransaction();
            try{
                $data = \Excel::load($path)->toArray();

                if(!empty($data)){

                    foreach($data as $key => $value){

                        if( $key >= $begin_row && $key <= $end_row){

                            if($value['business_name'] == ""||$value['fullname'] == ""
                                ||$value['firstname'] == ""||$value['lastname'] == ""
                                ||$value['business_phone'] == ""||$value['cell_phone'] == "")

                                return response([
                                    'status' => 'error',
                                    'message' => 'Import Error.(Busines Name, Fullname,Firstname, Lastname, Business Phone, Cell phone not empty. Check again!'
                                ]);

                            $customer_arr[] = [
                                'ct_salon_name' => $value['business_name'],
                                'ct_fullname' => $value['fullname'],
                                'ct_firstname' => $value['firstname'],
                                'ct_lastname' => $value['lastname'],
                                'ct_business_phone' => $value['business_phone'],
                                'ct_cell_phone' => $value['cell_phone'],
                                'ct_email' => $value['email'],
                                'ct_address' => $value['address'],
                                'ct_note' => $value['note'],
                                'created_by' => Auth::user()->user_id,
                                'updated_by' => Auth::user()->user_id
                            ];
                            $insert_count++;
                        }
                    }
                    if(isset($request->check_my_customer)){
                        $customer_id_max = MainCustomerTemplate::max('id');
                        $customer_id = $request->customer_id;
                        $team_id = Auth::user()->user_team;
                        $my_customer = [];
                        $user_customer_arr = [];
                        $user_id = Auth::user()->user_id;

                        //UPDATE CUSTOMER STATUS LIST
                        $team_customer_status = MainTeam::where('id',$team_id)->first()->team_customer_status;
                        $customer_status_arr = json_decode($team_customer_status,TRUE);
                        for ($i=$customer_id_max+1; $i < $customer_id_max+$insert_count+1; $i++) { 
                            $customer_status_arr[$i] = 1;
                            $my_customer[] = $i;
                        }
                        $customer_status_list = json_encode($customer_status_arr);
                        $update_customer = MainTeam::where('id',$team_id)->update(['team_customer_status'=>$customer_status_list]);

                        //UPDATE MY LIST
                        $user_customer_list = Auth::user()->user_customer_list;

                        if($user_customer_list == NULL){

                            $user_customer_arr = $my_customer;
                        }else{
                            $user_customer_arr = explode(";", $user_customer_list);

                            $user_customer_arr = array_merge($user_customer_arr,$my_customer);
                        }
                        $user_customer_list_after = implode(";", $user_customer_arr);
                    //UPDATE LIST CUSTOMER
                        $update_user = MainUser::where('user_id',$user_id)->update(['user_customer_list'=>$user_customer_list_after]);

                        if(!isset($insert_customer) || !isset($update_customer) || !isset($update_user)){
                            DB::callback();
                            return response([
                                'status' => 'error',
                                'message' => 'Import Error.File Empty. Check again!'
                            ]);
                        }
                            
                        else{
                            DB::commit();
                             return response([
                                'status' => 'success',
                                'message' => 'Success! Import '.$insert_count.' rows'
                            ]);
                        }
                    }
                    $insert_customer = MainCustomerTemplate::insert($customer_arr);
                    if(!isset($insert_customer)){
                        DB::callback();
                        return response([
                            'status' => 'error',
                            'message' => 'Import Error.File Empty. Check again!'
                        ]);
                    }
                        
                    else{
                        DB::commit();
                         return response([
                            'status' => 'success',
                            'message' => 'Success! Import '.$insert_count.' rows'
                        ]);
                    }
                }
            }catch(\Exception $e){
                \Log::info($e->getMessage());
                return response([
                            'status' => 'error',
                            'message' => 'Import Error. Check again!'
                        ]);
            }
        }
    }
    public function exportMyCustomer(Request $request)
    {
        $user_customer_list = Auth::user()->user_customer_list;

        if($user_customer_list != ""){

            $customer_list = explode(";", $user_customer_list);

            $customer_list = MainCustomerTemplate::whereIn('id',$customer_list)->latest()->get()->toArray();

            $date = Carbon::now()->format('Y_m_d_His');
            // dd($data);
            return \Excel::create('customer_list_'.$date,function($excel) use ($customer_list){

                $excel ->sheet('Customer List', function ($sheet) use ($customer_list)
                {
                    $sheet->cell('A1', function($cell) {$cell->setValue('Business Name');   });
                    $sheet->cell('B1', function($cell) {$cell->setValue('Business Phone');   });
                    $sheet->cell('C1', function($cell) {$cell->setValue('Cell Phone');   });
                    $sheet->cell('D1', function($cell) {$cell->setValue('Fullname');   });
                    $sheet->cell('E1', function($cell) {$cell->setValue('Firstname');   });
                    $sheet->cell('F1', function($cell) {$cell->setValue('Lastname');   });
                    $sheet->cell('G1', function($cell) {$cell->setValue('Address');   });
                    $sheet->cell('H1', function($cell) {$cell->setValue('Email');   });
                    $sheet->cell('I1', function($cell) {$cell->setValue('Note');   });

                    if (!empty($customer_list)) {
                        foreach ($customer_list as $key => $value) {
                            $i=$key+2;
                            $sheet->cell('A'.$i, $value['ct_salon_name']); 
                            $sheet->cell('B'.$i, $value['ct_business_phone']); 
                            $sheet->cell('C'.$i, $value['ct_cell_phone']);
                            $sheet->cell('D'.$i, $value['ct_fullname']);
                            $sheet->cell('E'.$i, $value['ct_firstname']);
                            $sheet->cell('F'.$i, $value['ct_lastname']);
                            $sheet->cell('G'.$i, $value['ct_address']);
                            $sheet->cell('H'.$i, $value['ct_email']);
                            $sheet->cell('I'.$i, $value['ct_note']);
                        }
                    }
                });
            })->download("xlsx");
        }else{
            return back()->with('error','No any customer to export!');
            
    }
        }
}