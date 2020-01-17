<?php

namespace App\Http\Controllers\Customer;

use App\Helpers\ImagesHelper;
use App\Models\MainCustomerAssign;
use App\Models\MainCustomerBought;
use App\Models\MainCustomerNote;
use App\Models\MainFile;
use App\Models\MainTeamType;
use App\Models\MainTrackingHistory;
use App\Models\MainUserCustomerPlace;
use App\Models\PosPlace;
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
use Gate;

class CustomerController extends Controller
{
    public function listCustomer()
    {
        if(Gate::denies('permission','all-customers-read'))
            return doNotPermission();

        $data['state'] = Option::state();
        $data['status'] = GeneralHelper::getCustomerStatusList();

        if(Gate::allows('permission','customer-admin')){
            $data['teams'] = MainTeam::active()->get();
            $data['customer_status'] = GeneralHelper::getCustomerStatusList();
        }
        else
            $data['customer_status'] = ['3'=>'New Arrivals'];

        return view('customer.all-customers',$data);
    }

    public function listMerchant()
    {
        return view('customer.all-merchants');
    }

    public function addCustomer()
    {
        $customer_arr = MainUserCustomerPlace::where('user_id',Auth::user()->user_id)->select('customer_id')->groupBy('customer_id')->get()->toArray();
        $customer_list = MainCustomerTemplate::whereIn('id',$customer_arr)->get();
        return view('customer.customer-add',compact('customer_list'));
    }

    public function listMyCustomer(){

        if(Gate::denies('permission','my-customer-read'))
            return doNotPermission();

        $team_id = Auth::user()->user_team;
        $team_list_id = [];

        if(!isset(MainTeam::find($team_id)->team_type)){
            return back()->with(['error'=>'Choose your Team, first!']);
        }
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

        if(Gate::denies('permission','all-customers-read'))
            return doNotPermission();

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $address = $request->address;
        $status_customer = $request->status_customer;

        //GET LIST NAME OF TEAM TYPE'S COLUMN
        if(!is_null($request->team_id))
            $team_id = $request->team_id;
        else
            $team_id = Auth::user()->user_team;

        $team_customer_status = MainTeam::find($team_id)->getTeamType;
        $team_slug = $team_customer_status->slug;

        $customers = MainCustomerTemplate::leftjoin('main_user',function($join){
            $join->on('main_customer_template.created_by','main_user.user_id');
        });

        if($start_date != "" && $end_date != ""){

            $start_date = Carbon::parse($request->start_date)->subDay(1)->format('Y-m-d');
            $end_date = Carbon::parse($request->end_date)->addDay(1)->format('Y-m-d');

            $customers->whereDate('main_customer_template.created_at','>=',$start_date)
                    ->whereDate('main_customer_template.created_at','<=',$end_date);
        }
        if($address != ""){
            $customers->where('ct_address','LIKE',"%".$address."%");
        }
        if(!is_null($status_customer)){
            if($status_customer == 3)
                $customers->where(function($query) use ($team_slug,$status_customer){
                    $query->where($team_slug,$status_customer)
                    ->orWhere($team_slug,0);
                });
            else
                $customers->where($team_slug,$request->status_customer);
        }

        $list_customers = $customers->orderBy('main_customer_template.created_at','ASC')
            ->select('main_user.user_nickname','main_customer_template.ct_salon_name','main_customer_template.ct_fullname','main_customer_template.ct_business_phone','main_customer_template.ct_cell_phone','main_customer_template.ct_note','main_customer_template.created_by','main_customer_template.created_at','main_customer_template.id','main_customer_template.'.$team_slug);
                       
        return Datatables::of($list_customers)
            ->editColumn('id',function ($row) use ($team_slug){
                if($row->$team_slug == 4)
                    return '<i class="fas fa-plus-circle details-control text-danger" id="'.$row->id.'" ></i><a href="'.route('customer-detail',$row->id).'"> '.$row->id.'</a>';
                else
                    return '<a href="javascript:void(0)">'.$row->id.'</a>';
            })
            ->editColumn('created_at',function($row){
                return format_date($row->created_at)." by ".$row->user_nickname;
            })
            ->editColumn('ct_business_phone',function($row){
                if($row->ct_business_phone != null && Gate::denies('permission','customer-admin'))
                    return substr($row->ct_business_phone,0,3)."########";
                else return $row->ct_business_phone;
            })
            ->editColumn('ct_cell_phone',function($row){
                if($row->ct_cell_phone != null  && Gate::denies('permission','customer-admin'))
                    return substr($row->ct_cell_phone,0,3)."########";
                else return $row->ct_cell_phone;
            })
            ->addColumn('ct_status',function($row) use ($team_slug){
                if($row->$team_slug == 0)
                    return 'New Arrivals';
                else
                    return GeneralHelper::getCustomerStatus($row->$team_slug);
            })
            ->addColumn('action', function ($row){
                if( Gate::allows('permission','customer-admin'))
                    return '<a class="btn btn-sm btn-secondary view" customer_id="'.$row->id.'" href="javascript:void(0)"><i class="fas fa-eye"></i></a>
                        <a class="btn btn-sm btn-secondary edit-customer" customer_id="'.$row->id.'" href="javascript:void(0)"><i class="fas fa-edit"></i></a>
                        <a class="btn btn-sm btn-secondary delete-customer" customer_id="'.$row->id.'" href="javascript:void(0)"><i class="fas fa-trash"></i></a>';
                else
                    return '<a class="btn btn-sm btn-secondary view" customer_id="'.$row->id.'" href="javascript:void(0)"><i class="fas fa-eye"></i></a>';
            })
            ->rawColumns(['action','id'])
            ->make(true);
    }
    public function getCustomerDetail(Request $request){

        $customer_id = $request->customer_id;

        if(isset($request->team_id) && is_null($request->team_id))
            $team_id = $request->team_id;
        else
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

            // $team_customer_status = MainTeam::find($team_id)->getTeamType->team_customer_status;
            // $customer_status_arr = json_decode($team_customer_status,TRUE);
            // if(!isset($customer_status_arr[$customer_list->id]))
            //     $customer_status = 'New Arrivals';
            // else
            //     $customer_status = GeneralHelper::getCustomerStatus($customer_status_arr[$customer_list->id]);

            //GET CUSTOMER STATUS IN CURRENT TEAM
            $slug = MainTeam::find($team_id)->getTeamType->slug;
            $customer_status = MainCustomerTemplate::find($customer_id)->$slug;

            if($customer_status == 0)
                $status = 'New Arrivals';
            else
                $status = GeneralHelper::getCustomerStatus($customer_status);

            $customer_list['ct_status'] = $status;

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
                if($customer_list->ct_business_phone != null  && Gate::denies('permission','customer-admin'))
                    $customer_list['ct_business_phone'] = substr($customer_list->ct_business_phone,0,3)."########";
                if($customer_list->ct_cell_phone != null  && Gate::denies('permission','customer-admin'))
                $customer_list['ct_cell_phone'] = substr($customer_list->ct_cell_phone,0,3)."########";
                //CHECK CUSTOMER ASSIGN OR NOT YET
                $data['count_customer_user'] = MainUserCustomerPlace::where('user_id',Auth::user()->user_id)
                    ->where('team_id',Auth::user()->user_team)
                    ->where('customer_id',$customer_id)
                    ->count();
            }
            //GET PALCE, SERVICE

            $customer_list['created_at'] = Carbon::parse($customer_list->created_at)->format('Y-m-d H:i:s');

            $data['customer_list'] = $customer_list;
            $data['place_arr'] = $place_arr;

            return $data;
        }
    }
    public function addCustomerToMy(Request $request){
//        return $request->all();

        $customer_id = $request->customer_id;
        $user_id = Auth::user()->user_id;
        $team_id = Auth::user()->user_team;

        DB::beginTransaction();

        if(count($request->all()) == 3){
            $rule = [
                'business_name' => 'required',
                'business_phone' => 'required|digits_between:10,15|unique:main_customer_template,ct_business_phone|unique:main_customer_assigns,business_phone|unique:pos_place,place_phone'
            ];
            $validator = Validator::make($request->all(),$rule);
            if($validator->fails())
                return response([
                    'status'=>'error',
                    'message'=> $validator->getMessageBag()->toArray()
                ]);
            $customer_assign = [
                'id' => MainCustomerAssign::max('id')+1,
                'user_id' => Auth::user()->user_id,
                'customer_id' => $customer_id,
                'business_name' => $request->business_name,
                'business_phone' => $request->business_phone,
            ];
            MainCustomerAssign::create($customer_assign);
        }
        elseif(count($request->all()) == 1) {
            //CHECK NEW CUSTOMER FOR TEAM TYPE
            $team_id_arr = MainTeam::active()->where('team_type', MainTeam::find(Auth::user()->user_team)->team_type)->select('id')->get()->toArray();
            $team_id_arr = array_values($team_id_arr);
            $user_customer = MainUserCustomerPlace::whereIn('team_id', $team_id_arr)->where('customer_id', $customer_id)->count();
            if ($user_customer == 0) {
                $customer_info = MainCustomerTemplate::find($customer_id);
                $customer_assign = [
                    'user_id' => Auth::user()->user_id,
                    'customer_id' => $customer_id,
                    'business_name' => $customer_info->ct_salon_name,
                    'business_phone' => $customer_info->ct_business_phone,
                    'email' =>$customer_info->ct_email,
                    'website'=>$customer_info->ct_website,
                    'address'=> $customer_info->ct_address
                ];
                MainCustomerAssign::create($customer_assign);
            }
        }
        //ADD CUSTOMER TO USER LIST
        $user_customer_arr = [
            'id' => MainUserCustomerPlace::max('id')+1,
            'user_id' => $user_id,
            'team_id' => $team_id,
            'customer_id' => $customer_id
        ];
        $update_user = MainUserCustomerPlace::create($user_customer_arr);
        //UPDATE CUSTOMER STATUS FOR USER'S TEAM
        $slug_team = MainTeam::find($team_id)->getTeamType->slug;

        $customer_info = DB::table('main_customer_template')->where('id',$customer_id);

        if($customer_info->first()->$slug_team == 0 || $customer_info->first()->$slug_team == 3)
            $update_customer = $customer_info->update([$slug_team=>1]);
        else
            $update_customer = "nothing to update";

        if(!isset($update_user) || !isset($update_customer)){
            DB::callback();
            return response(['status'=>'error','message'=>'Getting Failed!']);
        }
        else{
            DB::commit();
            return response(['status'=>'success','message'=>'Getting Successfully!']);
        }
    }
    public function getMyCustomer(Request $request){

        if(Gate::denies('permission','my-customer-read'))
            return doNotPermission();

        $user_id = Auth::user()->user_id;
        $team_id = Auth::user()->user_team;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $address = $request->address;
        $status_customer = $request->status_customer;
        $customer_arr = [];
        $list_customers = [];

        //GET LIST NAME OF TEAM TYPE'S COLUMN
        if(!is_null($request->team_id))
            $team_id = $request->team_id;
        else
            $team_id = Auth::user()->user_team;

        $team_customer_status = MainTeam::find($team_id)->getTeamType;
        $team_slug = $team_customer_status->slug;

        //GET PRIVATE NOTE'S CUSTOMER
        $note_customers = MainCustomerNote::where([
                                    ['user_id',$user_id],
                                    ['team_id',$team_id]]
                                )->get();
        $note_customers = collect($note_customers);

        $user_customer_list = MainUserCustomerPlace::where([
            ['user_id',$user_id],
            ['team_id',$team_id],
        ])->select('customer_id')->get()->toArray();

        if($user_customer_list != NULL){

                $user_customer_arr = array_values($user_customer_list);

                $customers = MainCustomerTemplate::with('getCreatedBy')->whereIn('main_customer_template.id',$user_customer_arr);

            if($start_date != "" && $end_date != ""){

                $start_date = Carbon::parse($start_date)->subDay(1)->format('Y-m-d');
                $end_date = Carbon::parse($end_date)->addDay(1)->format('Y-m-d');

                $customers->whereDate('main_customer_template.created_at','>=',$start_date)
                        ->whereDate('main_customer_template.created_at','<=',$end_date);
            }
            if($address != ""){
                $customers->where('ct_address','LIKE',"%".$address."%");
            }
            if(!is_null($status_customer)){
                if($status_customer == 3)
                    $customers->where(function($query) use ($team_slug,$status_customer){
                        $query->where($team_slug,$status_customer)
                        ->orWhere($team_slug,0);
                    });
                else
                    $customers->where($team_slug,$request->status_customer);
            }
            $list_customers = $customers->orderBy('main_customer_template.created_at','ASC')
                ->select('main_customer_template.ct_salon_name','main_customer_template.ct_fullname','main_customer_template.ct_business_phone','main_customer_template.ct_cell_phone','main_customer_template.ct_note','main_customer_template.created_by','main_customer_template.created_at','main_customer_template.id','main_customer_template.'.$team_slug);
        }
        return Datatables::of($list_customers)
            ->editColumn('id',function ($row) use ($team_slug){
                if($row->$team_slug == 4)
                    return '<i class="fas fa-plus-circle details-control text-danger" id="'.$row->id.'" ></i><a href="'.route('customer-detail',$row->id).'"> '.$row->id.'</a>';
                else
                    return '<a href="javascript:void(0)">'.$row->id.'</a>';
            })
            ->editColumn('created_at',function($row){
                return format_date($row->created_at);
            })
            ->addColumn('ct_status',function($row) use ($team_slug){
                if($row->$team_slug == 0)
                    return 'New Arrivals';
                else
                    return GeneralHelper::getCustomerStatus($row->$team_slug);
            })
            ->editColumn('ct_note',function($row) use ($note_customers){

                $private_note_customer = $note_customers->where('customer_id',$row->id)->first();

                if(isset($private_note_customer) && !is_null($private_note_customer->content))
                    return $private_note_customer->content;
                else
                    return $row->ct_note;
            })
            ->addColumn('action', function ($row){
                    return '
                          <a class="btn btn-sm btn-secondary add-note"  contact_name="'.$row->ct_fullname.'" customer_id="'.$row->id.'" href="javascript:void(0)" title="Add Customer Note"><i class="far fa-sticky-note"></i></a>
                          <a class="btn btn-sm btn-secondary view" customer_id="'.$row->id.'" href="javascript:void(0)" title="View Customer"><i class="fas fa-eye"></i></a>
                    <a class="btn btn-sm btn-secondary order-service" href="'.route('add-order',$row->id).'" title="Go To Order"><i class="fas fa-shopping-cart"></i></a>';
                })
            ->rawColumns(['action','id'])
            ->make(true);
    }
    public function editCustomer(Request $request){

        if(Gate::denies('permission','customer-update'))
            return doNotPermission();

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

        if(Gate::denies('permission','customer-delete'))
            return doNotPermission();

        $customer_id = $request->customer_id;
        $team_id = Auth::user()->user_team;

        $team_slug = MainTeam::find($team_id)->getTeamType->slug;
        $update_customer = MainCustomerTemplate::where('id',$customer_id)->update([$team_slug=>2]);

        // $team_customer_status = MainTeam::find($team_id)->getTeamType->team_customer_status;
        // $customer_status_arr = json_decode($team_customer_status,TRUE);
        // $customer_status_arr[$customer_id] = 2;
        // $customer_status_list = json_encode($customer_status_arr);
        // $update_customer = MainTeam::find($team_id)->getTeamType->update(['team_customer_status'=>$customer_status_list]);

        if(!isset($update_customer))
            return 0;
        else
            return 1;
    }
    public function exportCustomer(Request $request)
    {
        if(Gate::denies('permission','export-customers'))
            return doNotPermission();

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

        if(Gate::denies('permission','import-customers'))
            return doNotPermissionAjax();

        if($request->hasFile('file')){
            $path = $request->file('file')->getRealPath();
            $begin_row = $request->begin_row;
            $end_row = $request->end_row;
            $update_exist = $request->check_update_exist;
            $insert_count = 0;
            
            DB::beginTransaction();

            //GET ALL CUSTOMER IN MAIN_CUSTOMER_TEMPLATE
            $customers = MainCustomerTemplate::select('*')->get();
            $customers = collect($customers);

            try{
                $data = \Excel::load($path)->toArray();

                if(!empty($data)){

                    foreach($data as $key => $value){

                        if( $key >= $begin_row && $key <= $end_row){

                            if($value['business_name'] == ""||$value['fullname'] == ""
                                ||$value['firstname'] == ""||$value['lastname'] == ""
                                ||$value['business_phone'] == ""||$value['cell_phone'] == "")

                                /*return response([
                                    'status' => 'error',
                                    'message' => 'Import Error.(Busines Name, Fullname,Firstname, Lastname, Business Phone, Cell phone not empty. Check again!'
                                ]);*/
                            $check_phone = 0;
                            //CHECK PHONE NUMBER EXIST

                            if($customers->where('ct_business_phone',$value['business_phone'])->where('ct_cell_phone',$value['cell_phone'])->count()  == 0){
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
                        // DB::callback();
                        return response([
                            'status' => 'error',
                            'message' => 'Import Error.This file had imported. Check again!'
                        ]);
                    }

                    /*if(isset($request->check_my_customer)){
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
                    }*/
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
                \Log::info($e);
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
            'ct_cell_phone' => 'required|unique:main_customer_template,ct_cell_phone|digits_between:10,15'
        ];
        $message = [
            'ct_firstname.required' => 'Enter Firstname',
            'ct_lastname.required' => 'Enter Lastname',
            'ct_salon_name.required' => 'Enter Business Name',
            'ct_email.required' => 'Enter Email',
            'ct_email.email' =>'Enter a Email',
            'ct_address.required' => 'Enter Address',
            'ct_business_phone.required' => 'Enter Business Phone',
            'ct_business_phone.unique' => 'Business Phone has Existed',
            'ct_business_phone.numeric' => 'Enter Number',
            'ct_business_phone.between' => 'Enter True Number',
            'ct_cell_phone.required' => 'Enter Cell Phone',
            'ct_cell_phone.unique' => 'Cell Phone has Taken',
            'ct_cell_phone.digits_between' => "Enter a Cell Phone"
        ];
        $validator = Validator::make($request->all(),$rule,$message);
        if($validator->fails())
            return back()->withErrors($validator)->withInput();

        //GET COLUMN FOR UPDATE CUSTOMER STATUS
        $team_slug = MainTeam::find(Auth::user()->user_team)->getTeamType->slug;

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
            'ct_active' => 1,
            $team_slug => 1
        ];
        DB::beginTransaction();
        $customer_create = DB::table('main_customer_template')->insert($customer_arr);
        $customer_id = MainCustomerTemplate::max('id');

        //ADD CUSTOMER INFO TO WAITING
        $customer_user_assign = [
            'user_id' => Auth::user()->user_id,
            'business_phone' => $request->ct_business_phone,
            'business_name' => $request->ct_salon_name,
            'customer_id' => $customer_id,
            'email' => $request->ct_email,
            'address' => $request->ct_address,
            'website' => $request->ct_website,
        ];
        $customer_assign = MainCustomerAssign::create($customer_user_assign);

        //UPDATE MY CUSTOMER
        $user_customer_arr = [
            'user_id' => Auth::user()->user_id,
            'team_id' => Auth::user()->user_team,
            'customer_id' => $customer_id,
        ];
        $user_customer_update = MainUserCustomerPlace::create($user_customer_arr);

        if(!isset($customer_create) || !isset($user_customer_update) || !isset($customer_assign)){
            DB::callback();
            return back()->with(['error'=>'Create Customer Failed!']);
        }
        else{
            DB::commit();
            return redirect()->route('myCustomers')->with(['success'=>'Create Customer Successfully!']);
        }
    }
    public function customerDetail($customer_id =0){

        if(!isset($customer_id) || $customer_id == 0)
            return back()->with(['error'=>'Get CustomerDetail Failed!']);

        $data['template_customer_info'] = MainCustomerTemplate::find($customer_id);
        try{
            $customer_id = $data['template_customer_info']->getMainCustomer->customer_id;
        }catch(\Exception $e){
            \Log::info($e);
            return redirect()->route('customers')->with(['error'=>'Failed! Get information failed!']);
        }
        $data['place_service'] = MainCustomerService::where('cs_customer_id',$customer_id)->get()->groupBy('cs_place_id');

        $data['main_customer_info'] = MainCustomer::where('customer_id',$customer_id)->first();
        $data['id'] = $customer_id;

        $data['user_list'] = MainUser::where('user_id','!=',Auth::user()->user_id)->with('getTeam')->get();
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
            'email_list' => $email_list,
            'receiver_id' => $request->receiver_id
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
            if($size_total > 50){
                return response(['status'=>'error','message'=>'Total Size Image maximum 50M!']);
            }
            //Upload Image
            foreach ($file_list as $key => $file) {

                $file_name = ImagesHelper::uploadImage2($file,$current_month,'images/comment/');
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
        if($seller_id == Auth::user()->user_id)
            $receiver_id = "";
        else
            $receiver_id = Auth::user()->user_id;

        $seller_info = MainUser::where('user_id',$seller_id)->first();

        if(!isset($seller_info)){
            return response(['status'=>'error','message'=>'Get Seller Failed!']);
        }else{
            return response([
                'fullname'=>strtoupper($seller_info->user_firstname). " ".strtoupper($seller_info->user_lastname),
                'email'=>$seller_info->user_email,
                'receiver_id' => $receiver_id
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
    public function moveCustomerAll(){

        if(Gate::denies('permission','move-customer'))
            return doNotPermission();

        $data['team_type_list'] = MainTeamType::active()->get();
        return view('customer.move-customer',$data);
    }
    public function getUserTeam(Request $request){

        $team_type_id = $request->team_type_id;
        $team_arr = [];

        $team_list = MainTeam::where('team_type',$team_type_id)->get();
        foreach ($team_list as $team){
            $team_arr[] = $team->id;
        }

        $user_list = MainUser::active()->whereIn('user_team',$team_arr)->get();

        if(!isset($user_list))
            return response(['status'=>'error','message'=>'Get Team Error!']);
        else
            return response(['status'=>'success','user_list'=>$user_list]);
    }
    public function getCustomer1(Request $request){

        $user_id = $request->user_1;
        $customer_list = MainUserCustomerPlace::where('user_id',$user_id)->select('customer_id')->get()->toArray();
        $customer_arr = array_values($customer_list);

        $customer_list = MainCustomerTemplate::whereIn('id',$customer_arr);

        return DataTables::of($customer_list)
            ->make(true);
    }
    public function getCustomer2(Request $request){

        $user_id = $request->user_2;
        $customer_list = MainUserCustomerPlace::where('user_id',$user_id)->select('customer_id')->get()->toArray();
        $customer_arr = array_values($customer_list);

        $customer_list = MainCustomerTemplate::whereIn('id',$customer_arr);

        return DataTables::of($customer_list)
            ->make(true);
    }
    public function moveCustomersAll(Request $request){

        $customer_input = $request->customer_array;
        //GET TEAM USER 2
        $user_team_2 = MainUser::where('user_id',$request->user_2)->first()->user_team;
        $update_user_customer = MainUserCustomerPlace::where('user_id',$request->user_1)
            ->whereIn('customer_id',$customer_input)
            ->update(['team_id'=>$user_team_2,'user_id'=>$request->user_2]);
        if(!$update_user_customer)
            return response(['status'=>'error','message'=>'Failed! Move Customer Failed!']);
        else
            return response(['status'=>'success','message'=>'Successfully! Move Customer Successfully!']);
    }
    public function getPlaceCustomer(Request $request){

        $customer_template_id = $request->customer_template_id;
        $team_id = $request->team_id;
        $customer_place = MainUserCustomerPlace::with('getPlace')->with('getUser')
            ->where('customer_id',$customer_template_id)
            ->where('place_id','!=',null)
            ->where('team_id',$team_id)->get();

        return $customer_place;
    }
    public function getPlaceMyCustomer(Request $request){

        $customer_template_id = $request->customer_template_id;
        $team_id = $request->team_id;
        $customer_place = MainUserCustomerPlace::with('getPlace')->with('getUser')
            ->where('customer_id',$customer_template_id)
            ->where('user_id',Auth::user()->user_id)
            ->where('place_id','!=',null)
            ->where('team_id',$team_id)->get();

        return $customer_place;
    }
    public function movePlace(Request $request){
        $place_id = $request->place_id;
        $user_id = $request->user_id;
        $current_user = $request->current_user;
        $team_id = $request->team_id;

        //GET TEAM'S MOVING USER
        $moving_team = MainUser::where('user_id',$user_id)->first()->user_team;

        $user_customer_place_update = MainUserCustomerPlace::where([
                                        ['team_id',$team_id],
                                        ['user_id',$current_user],
                                        ['place_id',$place_id]
                                    ])->update(['user_id'=>$user_id,'team_id'=>$moving_team]);

        if(!$user_customer_place_update)
            return response(['status'=>'error','message'=>'Move Place Failed!']);
        else
            return response(['status'=>'success','message'=>'Move Place Successfully!']);
    }
    public function getUserFromTeam(Request $request){
//        return $request->all();

        $team_id = $request->team_id;
        $team_type = MainTeam::find($team_id)->team_type;
        $team_list = MainTeam::active()->where('team_type',$team_type)->select('id')->get()->toArray();
        $team_arr = array_values($team_list);

        $user_list = MainUser::active()
            ->whereIn('user_team',$team_arr)
            ->where('user_id','!=',$request->user_id)
            ->select('user_id','user_nickname','user_firstname','user_lastname')->get();

        if(!$user_list)
            return response(['status'=>'error','message'=>'Get User List Failed!']);
        else
            return response(['status'=>'success','user_list'=>$user_list]);
    }
    public function saveMyBusiness(Request $request){

        $rule = [
            'business_name' => 'required',
            'business_phone' => 'required|unique:main_customer_template,ct_business_phone|unique:main_customer_assigns,business_phone|unique:pos_place,place_phone|digits_between:10,15',
        ];
        $message = [
            'business_phone.digits_between' => 'Enter a Number Phone',
        ];
        $validator = Validator::make($request->all(),$rule,$message);
        if($validator->fails())
            return back()->withErrors($validator)->withInput();
        $input = $request->all();
        $input['user_id'] = Auth::user()->user_id;
        unset($input['_token']);
        $customer_assign_update = MainCustomerAssign::create($input);
        if(!$customer_assign_update)
            return back()->with('error','Create Busines Failed!');
        else
            return redirect()->route('myCustomers')->with('success','Create Business Successfully!');
    }
    public function getImportTemplateCustomer(){
        //PDF file is stored under project/public/download/info.pdf
        if(file_exists(public_path(). "/file/template_import.xlsx")){
            $file= public_path(). "/file/template_import.xlsx";

            $headers = array(
                      'Content-Type: application/pdf',
                    );
            return response()->download($file, 'template_import.xlsx', $headers);
        }
    }
     public function serviceCustomerDatatable(Request $request){

        if(Gate::denies('permission','all-customers-read'))
            return doNotPermission();

        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $address = $request->address;

        //GET LIST NAME OF TEAM TYPE'S COLUMN
        if(!is_null($request->team_id))
            $team_id = $request->team_id;
        else
            $team_id = Auth::user()->user_team;

        $team_customer_status = MainTeam::find($team_id)->getTeamType;
        $team_slug = $team_customer_status->slug;

        $customers = MainCustomerTemplate::leftjoin('main_user',function($join){
            $join->on('main_customer_template.created_by','main_user.user_id');
        });

        if($start_date != "" && $end_date != ""){

            $start_date = Carbon::parse($request->start_date)->subDay(1)->format('Y-m-d');
            $end_date = Carbon::parse($request->end_date)->addDay(1)->format('Y-m-d');

            $customers->whereDate('main_customer_template.created_at','>=',$start_date)
                    ->whereDate('main_customer_template.created_at','<=',$end_date);
        }
        if($address != ""){
            $customers->where('ct_address','LIKE',"%".$address."%");
        }
        //GET ONLY SERVICED CUSTOMER
        $customers->where($team_slug,4);
        

        $list_customers = $customers->orderBy('main_customer_template.created_at','ASC')
            ->select('main_user.user_nickname','main_customer_template.ct_salon_name','main_customer_template.ct_fullname','main_customer_template.ct_business_phone','main_customer_template.ct_cell_phone','main_customer_template.ct_note','main_customer_template.created_by','main_customer_template.created_at','main_customer_template.id','main_customer_template.'.$team_slug);
                       
        return Datatables::of($list_customers)
            ->editColumn('id',function ($row) use ($team_slug){
                if($row->$team_slug == 4)
                    return '<i class="fas fa-plus-circle details-control text-danger" id="'.$row->id.'" ></i><a href="'.route('customer-detail',$row->id).'"> '.$row->id.'</a>';
                else
                    return '<a href="javascript:void(0)">'.$row->id.'</a>';
            })
            ->editColumn('created_at',function($row){
                return format_date($row->created_at)." by ".$row->user_nickname;
            })
            ->editColumn('ct_business_phone',function($row){
                if($row->ct_business_phone != null && Gate::denies('permission','customer-admin'))
                    return substr($row->ct_business_phone,0,3)."########";
                else return $row->ct_business_phone;
            })
            ->editColumn('ct_cell_phone',function($row){
                if($row->ct_cell_phone != null  && Gate::denies('permission','customer-admin'))
                    return substr($row->ct_cell_phone,0,3)."########";
                else return $row->ct_cell_phone;
            })
            ->addColumn('ct_status',function($row) use ($team_slug){
                if($row->$team_slug == 0)
                    return 'New Arrivals';
                else
                    return GeneralHelper::getCustomerStatus($row->$team_slug);
            })
            ->addColumn('action', function ($row){
                if( Gate::allows('permission','customer-admin'))
                    return '<a class="btn btn-sm btn-secondary view" customer_id="'.$row->id.'" href="javascript:void(0)"><i class="fas fa-eye"></i></a>
                        <a class="btn btn-sm btn-secondary edit-customer" customer_id="'.$row->id.'" href="javascript:void(0)"><i class="fas fa-edit"></i></a>
                        <a class="btn btn-sm btn-secondary delete-customer" customer_id="'.$row->id.'" href="javascript:void(0)"><i class="fas fa-trash"></i></a>';
                else
                    return '<a class="btn btn-sm btn-secondary view" customer_id="'.$row->id.'" href="javascript:void(0)"><i class="fas fa-eye"></i></a>';
            })
            ->rawColumns(['action','id'])
            ->make(true);
    }
}
