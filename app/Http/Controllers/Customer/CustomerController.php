<?php

namespace App\Http\Controllers\Customer;

use App\Helpers\ImagesHelper;
use App\Models\MainCustomerBought;
use App\Models\MainCustomerNote;
use App\Models\MainFile;
use App\Models\MainTrackingHistory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Option;
use App\Helpers\GeneralHelper;
use App\Models\MainCustomer;
use App\Models\MainCustomerTemplate;
use App\Models\MainTeam;
use App\Models\MainUser;
use App\Models\MainComboService;
use App\Models\MainCustomerService;
use Carbon\Carbon;
use Auth;
use DataTables;
use DB;
use Validator;
use ZipArchive;

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

    public function listMyCustomer(){

        $team_id = Auth::user()->user_team;
        $team_list_id = [];

        $team_list = MainTeam::select('id')->where([['team_type',MainTeam::find($team_id)->team_type],['team_status',1]])->get();
        foreach ($team_list as $team_id){
            $team_list_id[] = $team_id->id;
        }
        $data['state'] = Option::state();
        $data['status'] = GeneralHelper::getCustomerStatusList();
        $data['user_list'] = MainUser::where('user_id','!=',Auth::user()->user_id)->whereIn('user_team',$team_list_id)->get();
        $customer_list = Auth::user()->user_customer_list;
        $customer_arr = explode(";",$customer_list);
        $data['customer_list'] = MainCustomerTemplate::whereIn('id',$customer_arr)->get();

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
        $team_customer_status = MainTeam::find($team_id)->getTeamType;
        $team_customer_status = $team_customer_status->team_customer_status;

        $customer_status_arr = json_decode($team_customer_status,TRUE);

        foreach ($customers as $key => $customer) {

            if(!isset($customer_status_arr[$customer->id])){
                $customer_status_arr[$customer->id] = 1;
                $ct_status = 'Arrivals';
            }
            else
                $ct_status = GeneralHelper::getCustomerStatus($customer_status_arr[$customer->id]);
            //ADMIN CAN SEE ALL
            if(Auth::user()->user_group_id == 1){
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
            elseif(Auth::user()->user_group_id != 1 && $ct_status = 'Arrivals'){
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
            ->editColumn('id',function ($row){
                if($row['ct_status'] == 'Serviced')
                    return '<a href="'.route('customer-detail',$row['id']).'">'.$row['id'].'</a>';
                else
                    return '<a href="javascript:void(0)">'.$row['id'].'</a>';
            })
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
                if(Auth::user()->user_id == 1)
                    return '<a class="btn btn-sm btn-secondary view" customer_id="'.$row['id'].'" href="javascript:void(0)"><i class="fas fa-eye"></i></a>
                        <a class="btn btn-sm btn-secondary edit-customer" customer_id="'.$row['id'].'" href="javascript:void(0)"><i class="fas fa-edit"></i></a>
                        <a class="btn btn-sm btn-secondary delete-customer" customer_id="'.$row['id'].'" href="javascript:void(0)"><i class="fas fa-trash"></i></a>';
                else
                    return '<a class="btn btn-sm btn-secondary view" customer_id="'.$row['id'].'" href="javascript:void(0)"><i class="fas fa-eye"></i></a>';
            })
            ->rawColumns(['action','id'])
            ->make(true);
    }
    public function getCustomerDetail(Request $request){

        $customer_id = $request->customer_id;
        $team_id = Auth::user()->user_team;

        $customer_list = MainCustomerTemplate::leftjoin('main_user',function($join){
                                                $join->on('main_customer_template.created_by','main_user.user_id');
                                            })
                                            ->where('main_customer_template.id',$customer_id)
                                            ->select('main_customer_template.*','main_user.user_nickname')
                                            ->first();

        if(!isset($customer_list))
            return 0;
        else{

            $team_customer_status = MainTeam::find($team_id)->getTeamType->team_customer_status;
            $customer_status_arr = json_decode($team_customer_status,TRUE);
            if(!isset($customer_status_arr[$customer_list->id]))
                $customer_status = 'Arrivals';
            else
                $customer_status = GeneralHelper::getCustomerStatus($customer_status_arr[$customer_list->id]);

            $customer_list['ct_status'] = $customer_status;

            $place_list = MainCustomerService::join('pos_place',function($join){
                $join->on('main_customer_service.cs_place_id','pos_place.place_id');
                })
                ->join('main_customer',function($join){
                    $join->on('main_customer_service.cs_customer_id','main_customer.customer_id');
                })
                ->join('main_combo_service',function($join){
                    $join->on('main_customer_service.cs_service_id','main_combo_service.id');
                })
                ->where('main_customer.customer_phone',$customer_list->ct_business_phone)
                ->select('pos_place.place_name','main_combo_service.cs_name')
                ->get();

            $place_arr = [];
            foreach ($place_list as $key => $place) {

                $place_arr[$place->place_name][] = $place->cs_name;
            }

            if(!isset($request->my_customer)){
                $customer_list['ct_business_phone'] = substr($customer_list->ct_business_phone,0,3)."########";
                $customer_list['ct_cell_phone'] = substr($customer_list->ct_cell_phone,0,3)."########";
            }
            //GET PALCE, SERVICE

            $customer_list['created_at'] = Carbon::parse($customer_list->created_at)->format('Y-m-d H:i:s');

            $data['customer_list'] = $customer_list;
            $data['place_arr'] = $place_arr;

            return $data;
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
        $team_customer_status = MainTeam::find($team_id)->getTeamType->team_customer_status;
        $customer_status_arr = json_decode($team_customer_status,TRUE);
        $customer_status_arr[$customer_id] = 1;
        $customer_status_list = json_encode($customer_status_arr);
        $update_customer = MainTeam::find($team_id)->getTeamType->update(['team_customer_status'=>$customer_status_list]);

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
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $address = $request->address;
        $status_customer = $request->status_customer;
        $customer_arr = [];

        $user_customer_list = MainUser::where('user_id',$user_id)->first()->user_customer_list;

        if($user_customer_list != NULL){

            $user_customer_arr = explode(";", $user_customer_list);

            $customer_list = MainCustomerTemplate::leftjoin('main_user',function($join){
                                                    $join->on('main_customer_template.created_by','main_user.user_id');
                                                })
                                                ->whereIn('main_customer_template.id',$user_customer_arr);

        if($start_date != "" && $end_date != ""){

            $start_date = Carbon::parse($start_date)->format('Y-m-d');
            $end_date = Carbon::parse($end_date)->format('Y-m-d');

            $customer_list->whereDate('main_customer_template.created_at','>=',$start_date)
                    ->whereDate('main_customer_template.created_at','<=',$end_date);
        }
        if($address != ""){
            $customer_list->where('ct_address','LIKE',"%".$address."%");
        }

            $customer_list = $customer_list->select('main_customer_template.*','main_user.user_nickname')->get();

            //GET LIST TEAM CUSTOMER LIST
            $team_customer_status = MainTeam::find($team_id)->getTeamType->team_customer_status;

            $customer_status_arr = json_decode($team_customer_status,TRUE);

            foreach ($customer_list as $key => $customer) {
                //GET CUSTOMER NOTE
                $customer_note_info = MainCustomerNote::where([
                                    ['customer_id',$customer->id],
                                    ['user_id',$user_id],
                                    ['team_id',$team_id]]
                                )->first();
                if(isset($customer_note_info)) $customer_note = $customer_note_info->content;
                else $customer_note = "";

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
                        'note' => $customer_note,
                        'updated_at' => $customer->updated_at,
                        'user_nickname' => $customer->user_nickname
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
                        'note' => $customer_note,
                        'updated_at' => $customer->updated_at,
                        'user_nickname' => $customer->user_nickname
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
                ->editColumn('updated_at',function($row){
                    return Carbon::parse($row['updated_at'])->format('m/d/Y H:i:s')." by ".$row['user_nickname'];
                })
                ->addColumn('action', function ($row){
                    return '
                          <a class="btn btn-sm btn-secondary add-note"  contact_name="'.$row['ct_fullname'].'" customer_id="'.$row['id'].'" href="javascript:void(0)" title="Add Customer Note"><i class="far fa-sticky-note"></i></a>
                          <a class="btn btn-sm btn-secondary view" customer_id="'.$row['id'].'" href="javascript:void(0)" title="View Customer"><i class="fas fa-eye"></i></a>
                    <a class="btn btn-sm btn-secondary order-service" href="'.route('add-order',$row['id']).'" title="Go To Order"><i class="fas fa-shopping-cart"></i></a>
                    <a class="btn btn-sm btn-secondary move-customer" contact_name="'.$row['ct_fullname'].'" customer_id="'.$row['id'].'" href="javascript:void(0)" title="Move Customer To User"><i class="fas fa-exchange-alt"></i></a>';
                })
                ->rawColumns(['action','id'])
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

        $team_customer_status = MainTeam::find($team_id)->getTeamType->team_customer_status;
        $customer_status_arr = json_decode($team_customer_status,TRUE);
        $customer_status_arr[$customer_id] = 2;
        $customer_status_list = json_encode($customer_status_arr);
        $update_customer = MainTeam::find($team_id)->getTeamType->update(['team_customer_status'=>$customer_status_list]);

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
                            //CHECK PHONE NUMBER EXIST
                            $check_phone = MainCustomerTemplate::where('ct_business_phone',$value['business_phone'])->where('ct_cell_phone',$value['cell_phone'])->count();

                            if($check_phone == 0){
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
                    }
                    if($insert_count == 0){
                        DB::callback();
                        return response([
                            'status' => 'error',
                            'message' => 'Import Error.This file had imported. Check again!'
                        ]);
                    }

                    if(isset($request->check_my_customer)){
                        $customer_id_max = MainCustomerTemplate::max('id');
                        $customer_id = $request->customer_id;
                        $team_id = Auth::user()->user_team;
                        $my_customer = [];
                        $user_customer_arr = [];
                        $user_id = Auth::user()->user_id;

                        //UPDATE CUSTOMER STATUS LIST
                        $team_customer_status = MainTeam::find($team_id)->getTeamType->first()->team_customer_status;
                        $customer_status_arr = json_decode($team_customer_status,TRUE);
                        for ($i=$customer_id_max+1; $i < $customer_id_max+$insert_count+1; $i++) {
                            $customer_status_arr[$i] = 1;
                            $my_customer[] = $i;
                        }
                        $customer_status_list = json_encode($customer_status_arr);
                        $update_customer = MainTeam::find($team_id)->getTeamType->update(['team_customer_status'=>$customer_status_list]);

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
    public function orderBuy($customer_id)
    {
        if(!$customer_id)
            return back()->with('error','Error!');

        $customer_info = MainCustomerTemplate::where('id',$customer_id)->first();

        $combo_service_list = MainComboService::where('cs_status',1)->get();

        $count = round($combo_service_list->count()/2);

        return view('orders.buy-service-combo',compact('customer_info','combo_service_list','count'));
    }
    public function saveMyCustomer(Request $request)
    {
        $rule = [
            'ct_firstname' => 'required',
            'ct_lastname' => 'required',
            'ct_salon_name' => 'required',
            'ct_business_phone' => 'required|unique:main_customer_template,ct_business_phone|numeric|digits_between:10,15',
            'ct_email' => 'required|email',
            'ct_address' => 'required',
        ];
        $message = [
            'ct_firstname.required' => 'Enter Firstname',
            'ct_lastname.required' => 'Enter Lastname',
            'ct_salon_name' => 'Enter Business Name',
            'ct_email.required' => 'Enter Email',
            'ct_email.email' =>'Enter a Email',
            'ct_address.required' => 'Enter Address',
            'ct_business_phone.required' => 'Enter Business Phone',
            'ct_business_phone.unique' => 'Business Phone has Existed',
            'ct_business_phone.numeric' => 'Enter Number',
            'ct_business_phone.between' => 'Enter True Number',
        ];
        $validator = Validator::make($request->all(),$rule,$message);
        if($validator->fails())
            return back()->withErrors($validator)->withInput();

        $customer_arr = [
            'ct_firstname' => $request->ct_firstname,
            'ct_lastname' => $request->ct_lastname,
            'ct_fullname' => $request->ct_firstname." ".$request->ct_lastname,
            'ct_salon_name' => $request->ct_salon_name,
            'ct_business_phone' => $request->ct_business_phone,
            'ct_cell_phone' => $request->ct_cell_phone,
            'ct_email' => $request->ct_email,
            'ct_address' => $request->ct_address,
            'ct_website' => $request->ct_website,
            'ct_note' => $request->ct_note,
            'created_by' => Auth::user()->user_id,
            'updated_by' => Auth::user()->user_id,
            'ct_active' => 1
        ];
        DB::beginTransaction();
        $customer_create = MainCustomerTemplate::create($customer_arr);

        //UPDATE STATUS CUSTOMER IN OWN TEAM
        $team_customer_status = MainTeam::find(Auth::user()->user_team)->getTeamType->team_customer_status;
        $customer_status_arr = json_decode($team_customer_status,TRUE);
        $customer_status_arr[$customer_create->id] = 1;
        $customer_status_list = json_encode($customer_status_arr);
        $update_customer = MainTeam::find(Auth::user()->user_team)->getTeamType->update(['team_customer_status'=>$customer_status_list]);

        //UPDATE CUSTOMER LIST IN MAIN USER
        $user_customer_list = Auth::user()->user_customer_list;

        if($user_customer_list == ""){
            $user_customer_arr = $customer_create->id;
        }else{
            $user_customer_arr = $user_customer_list.";".$customer_create->id;
        }
        $user_update = MainUser::where('user_id',Auth::user()->user_id)->update(['user_customer_list'=>$user_customer_arr]);

        if(!isset($customer_create) || !isset($update_customer) || !isset($user_update)){
            DB::callback();
            return back()->with(['error'=>'Create Customer Failed!']);
        }
        else{
            DB::commit();
            return redirect()->route('myCustomers')->with(['success'=>'Create Customer Successfully!']);
        }
    }
    public function customerDetail($customer_id){

        $data['template_customer_info'] = MainCustomerTemplate::find($customer_id);
        try{
            $customer_id = $data['template_customer_info']->getMainCustomer->customer_id;
        }catch(\Exception $e){
            \Log::info($e);
            return redirect()->route('customers')->with(['error'=>'Failed! Get information failed!']);
        }
        $data['place_service'] = MainCustomerService::where('cs_customer_id',$customer_id)->get()->groupBy('cs_place_id');
//        $data['place_service'] = $data['place_service'];

        $data['main_customer_info'] = MainCustomer::where('customer_id',$customer_id)->first();
        $data['id'] = $customer_id;
        return view('customer.customer-detail',$data);
    }
    public function customerTracking(Request $request){

        $customer_id = $request->customer_id;

        $tracking_history = MainTrackingHistory::where('customer_id',$customer_id)->get();
        return DataTables::of($tracking_history)
            ->editColumn('created_by',function ($row){
                return $row->getUserCreated->user_nickname
                    ."(<span class='text-capitalize'>".$row->getUserCreated->user_firstname." "
                    .$row->getUserCreated->user_lastname."</span>)<br>"
                    .format_datetime($row->created_at)."<br>";
            })
            ->editColumn('content',function($row){
                $file_list =$row->getFiles;
                $file_name = "<div class='row '>";

                    foreach ($file_list as $key => $file) {
                        $zip = new ZipArchive();

                        if ($zip->open($file->name, ZipArchive::CREATE) !== TRUE) {
                            $file_name .= '<form action="'.route('down-image').'" method="POST"><input type="hidden" value="'.csrf_token().'" name="_token" /><input type="hidden" value="'.$file->name.'" name="src" /><img class="file-comment ml-2" src="'.asset($file->name).'"/></form>';
                        }else{
                            $file_name .= '<form action="'.route('down-image').'" method="POST"><input type="hidden" value="'.csrf_token().'" name="_token" /><input type="hidden" value="'.$file->name.'" name="src" /><a href="javascript:void(0)" class="file-comment ml-2" /><i class="fas fa-file-archive"></i>'.$file->name_origin.'</a></form>';
                        }
                    }
                $file_name .= "</div>";
                return $row->content."<br>".$file_name;
            })
            ->rawColumns(['content','created_by'])
            ->make(true);
    }
    public function postCommentCustomer(Request $request){
        $rule = [
            'note' => 'required'
        ];
        $message = [
            'note.required' => 'Comment Empty!'
        ];
        $validator = Validator::make($request->all(),$rule,$message);
        if($validator->fails())
            return response([
                'status'=>'error',
                'message' => $validator->getMessageBag()->toArray()
            ]);
        $email_seller = $request->email_seller;
        $customer_id = $request->customer_id;
        $content = $request->note;
        $file_list = $request->file_image_list;
        $current_month = Carbon::now()->format('m');
        if($request->email_list == "")
            $email_list = $email_seller;
        else
            $email_list = $request->email_list.";".$email_seller;
        $file_arr = [];

        $tracking_arr = [
            'customer_id' => $customer_id,
            'content' => $content,
            'created_by' => Auth::user()->user_id,
            'email_list' => $email_list
        ];

        DB::beginTransaction();
        $tracking_create = MainTrackingHistory::create($tracking_arr);

        if($file_list != ""){
            //CHECK SIZE IMAGE
            $size_total = 0;
            foreach ($file_list as $key => $file){
                $size_total += $file->getSize();
            }
            $size_total = number_format($size_total / 1048576, 2); //Convert KB to MB
            if($size_total > 100){
                return response(['status'=>'error','message'=>'Total Size Image maximum 100M!']);
            }
            //Upload Image
            foreach ($file_list as $key => $file) {

                $file_name = ImagesHelper::uploadImage2($file,$current_month);
                $file_arr[] = [
                    'name' => $file_name,
                    'name_origin' => $file->getClientOriginalName(),
                    'tracking_id' => $tracking_create->id,
                ];
            }
            $file_create = MainFile::insert($file_arr);

            if(!isset($tracking_create) || !isset($file_create))
            {
                DB::callback();
                return response(['status'=>'error', 'message'=> 'Failed!']);
            }
            else{
                DB::commit();
                return response(['status'=> 'success','message'=>'Successly!']);
            }
        }
        if(!isset($tracking_create))
        {
            DB::callback();
            return response(['status'=>'error', 'message'=> 'Failed!']);
        }
        else{
            DB::commit();
            return response(['status'=> 'success','message'=>'Successly!']);
        }
    }
    public function getSeller(Request $request){

        $seller_id = $request->seller_id;

        $seller_info = MainUser::where('user_id',$seller_id)->first();

        if(!isset($seller_info)){
            return response(['status'=>'error','message'=>'Get Seller Failed!']);
        }else{
            return response([
                'fullname'=>strtoupper($seller_info->user_firstname). " ".strtoupper($seller_info->user_lastname),
                'email'=>$seller_info->user_email
            ]);
        }
    }
    public function moveCustomer(Request $request){

        $user_own = Auth::user()->user_id;
        $customer_id = $request->customer_id;
        $user_to = $request->user_id;

        //REMOVE CUSTOMER FORM CURRENT USER
        $user_customer_list = Auth::user()->user_customer_list;
        $user_customer_arr = explode(';',$user_customer_list);

        if (($key = array_search($customer_id, $user_customer_arr)) !== false) {
            unset($user_customer_arr[$key]);
        }
        $user_customer_list = implode(";",$user_customer_arr);

        //ADD CUSTOMER TO USER
        $user_customer_to = MainUser::where('user_id',$user_to)->first()->user_customer_list;
        if($user_customer_to == ""){
            $user_customer_list_to = $customer_id;
        }else{
            $user_customer_list_to = $user_customer_to.";".$customer_id;
        }
        DB::beginTransaction();

        $user_current_update = MainUser::where('user_id',$user_own)->update(['user_customer_list'=>$user_customer_list]);
        $user_to_update= MainUser::where('user_id',$user_to)->update(['user_customer_list'=>$user_customer_list_to]);

        if(!isset($user_current_update) || !isset($user_to_update)){
            DB::callback();
            return response(['status'=>'error','message'=>'Move Failed!']);
        }else{
            DB::commit();
            return response(['status'=>'success','message'=>'Move Successfully!']);
        }
    }
    public function addCustomerNote(Request $request){

        $rule = [
            'customer_note' => 'required'
        ];
        $message = [
            'customer_note.required' => 'Insert Note!'
        ];
        $validator = Validator::make($request->all(),$rule,$message);
        if($validator->fails()){
            return response([
                'status' => 'error',
                'message' => $validator->getMessagebag()->toArray()
            ]);
        }
        $customer_id = $request->customer_id_note;
        $customer_note = $request->customer_note;
        $team_id = Auth::user()->user_team;
        $user_id = Auth::user()->user_id;

        $note_arr = [
            'user_id' => $user_id,
            'team_id' => $team_id,
            'content' => $customer_note,
            'customer_id' => $customer_id
        ];
        //CHECK CUSTOMER NOTE EXIST
        $count = MainCustomerNote::where([
            ['customer_id',$customer_id],
            ['team_id',$team_id],
            ['user_id',$user_id]
        ])->first();

        if(!isset($count)){
            $update_customer_note = MainCustomerNote::create($note_arr);
        }else{
            $update_customer_note = MainCustomerNote::find($count->id)->update($note_arr);
        }
        if(!isset($update_customer_note))
            return response(['status'=>'error','message'=>'Add Note Failded!']);
        else
            return response(['status'=>'success','message'=>'Add Note Successfully!']);
    }
    public function moveCustomers(Request $request){

        $customer_id = $request->customer_id;
        $user_own = Auth::user()->user_id;
        $count = 0;

        DB::beginTransaction();
        foreach($request->user_id as $key => $user){
            if($user != 0){
                $count++;
                //REMOVE CUSTOMER FORM CURRENT USER
                $user_customer_list = Auth::user()->user_customer_list;
                $user_customer_arr = explode(';',$user_customer_list);

                if (($key = array_search($customer_id[$key], $user_customer_arr)) !== false) {
                    unset($user_customer_arr[$key]);
                }
                $user_customer_list = implode(";",$user_customer_arr);

                //ADD CUSTOMER TO USER
                $user_customer_to = MainUser::where('user_id',$user)->first()->user_customer_list;
                if($user_customer_to == ""){
                    $user_customer_list_to = $customer_id[$key];
                }else{
                    $user_customer_list_to = $user_customer_to.";".$customer_id[$key];
                }
                $user_current_update = MainUser::where('user_id',$user_own)->update(['user_customer_list'=>$user_customer_list]);
                $user_to_update= MainUser::where('user_id',$user)->update(['user_customer_list'=>$user_customer_list_to]);
            }
        }
        if($count == 0){
            return response(['status'=>'success','message'=>'Nothing to Move']);
        }else{
            if(!isset($user_current_update) || !isset($user_to_update)){
                DB::callback();
                return response(['status'=>'error','message'=>'Move Failed!']);
            }else{
                DB::commit();
                return response(['status'=>'success','message'=>'Move Successfully!']);
            }
        }
    }
}
