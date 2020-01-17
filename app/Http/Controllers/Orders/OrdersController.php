<?php

namespace App\Http\Controllers\Orders;

use App\Models\MainComboServiceType;
use App\Models\MainCustomerAssign;
use App\Models\MainGroupUser;
use App\Models\MainOrder;
use App\Models\MainUserCustomerPlace;
use App\Models\PosCustomertag;
use App\Models\PosSubjectWeb;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Option;
use App\Helpers\GeneralHelper;
use App\Helpers\ImagesHelper;
use App\Models\MainCustomerTemplate;
use App\Models\MainComboService;
use App\Models\MainComboServiceBought;
use App\Models\MainCustomerService;
use App\Models\MainTeam;
use App\Models\PosPlace;
use App\Models\MainCustomer;
use App\Models\PosUser;
use App\Models\MainUser;
use App\Models\MainTrackingHistory;
use App\Models\MainTask;
use App\Models\MainFile;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use App\Jobs\SendNotification;
use Laracasts\Presenter\PresentableTrait;
use Carbon\Carbon;
use DataTables;
use Validator;
use Auth;
use DB;
use Hash;
use ZipArchive;
use Dompdf\Dompdf;
use Gate;

class OrdersController extends Controller
{
    use PresentableTrait;
    protected $presenter = 'App\\Presenters\\ThemeMailPresenter';

    /**
     * get all orders
     * return
     */
    public function index()
    {

        if (Gate::denies('permission', 'all-orders-read'))
            return doNotPermission();

        $data['state'] = Option::state();
        $data['status'] = GeneralHelper::getOrdersStatus();
        return view('orders.orders', $data);
    }

    public function getMyOrders()
    {

        if (Gate::denies('permission', 'my-orders-read'))
            return doNotPermission();

        $data['state'] = Option::state();
        $data['status'] = GeneralHelper::getOrdersStatus();
        return view('orders.my-orders', $data);
    }

    public function getSellers()
    {

        if (Gate::denies('permission', "sellers-orders-read"))
            return doNotPermission();

        $data['state'] = Option::state();
        $data['status'] = GeneralHelper::getOrdersStatus();
        $team = Auth::user()->user_team;
        //CHECK TEAM LEADER
        $team_leader = MainTeam::where('team_leader', Auth::user()->user_id)->first();
        if (isset($team_leader) && $team_leader != "") {
            $data['user_teams'] = MainUser::where('user_team', $team)->get();
        } else
            $data['user_teams'] = MainUser::all();

        $data['services'] = MainComboService::where('cs_status', 1)->get();
        return view('orders.sellers', $data);
    }

    public function add($customer_id = 0)
    {

        if (Gate::denies('permission', 'new-order-create'))
            return doNotPermission();

        if ($customer_id != 0) {
        }
        $user_id = Auth::user()->user_id;
        $team_id = Auth::user()->user_team;

        //GET CUSTOMER ASSIGN
        $data['place_list_assign'] = MainCustomerAssign::where([
            ['user_id',Auth::user()->user_id],
            ['customer_id',$customer_id],
        ])->get();

        $data['customer_info'] = MainCustomerTemplate::find($customer_id);

        if ( isset($data['customer_info']) && !is_null($data['customer_info']->getMainCustomer)) {

            //GET PLACES'S CUSTOMER OF USER
            $places_arr = MainUserCustomerPlace::where([
                ['user_id',$user_id],
                ['customer_id',$customer_id],
                ['place_id','!=',null]
            ])->select('place_id')->get()->toArray();

            $places_arr = array_values($places_arr);

            $data['place_list'] = PosPlace::where('place_customer_id',$data['customer_info']->getMainCustomer->customer_id)
                ->whereIn('place_id',$places_arr)
                ->where('place_status',1)
                ->get();
        }
        //GET COMBO SERVICE WITH ROLE
        $service_permission_list = MainTeam::find(Auth::user()->user_team)->getTeamType->service_permission;
        $service_permission_arr = explode(';', $service_permission_list);
        $data['service_permission_arr'] = $service_permission_arr;

        //GET COMBO SERVICE NOT TYPE
        $data['combo_service_orther'] = MainComboService::where('cs_status', 1)->whereIn('id', $service_permission_arr)->whereNull('cs_combo_service_type')->get();

        //GET SERVICE TYPE
        $service_type = MainComboService::whereIn('id',$service_permission_arr)->select('cs_combo_service_type')->get()->toArray();
        $service_type_arr = array_values($service_type);

        $data['combo_service_type'] = DB::table('main_combo_service_type')->whereIn('id',$service_type_arr)->where('status',1)->get();

        //GET COMBO SERVICE COLLECTION INSIDE TYPE
        $combo_service = MainComboService::whereIn('id',$service_permission_arr)->get();
        $data['combo_service_list'] = collect($combo_service);

        return view('orders.add', $data);
    }

    function addOrder(Request $request)
    {
        if (Gate::denies('permission', 'new-order-create'))
            return doNotPermission();

        $rule = [
            'payment_amount' => 'required',
            'cs_id' => 'required',
            'customer_phone' => 'required',
            'customer_id' => 'required',
            'place_id' => 'required'
        ];

        $validator = Validator::make($request->all(), $rule);
        if ($validator->fails())
            // return back()->with('error' => $validator->getMessageBag()->toArray());
            return redirect()->back()->withErrors($validator)->withInput();

        DB::beginTransaction();

        //GET CUSTOMER INFORMATION
        $customer_phone = $request->customer_phone;

        $customer_info = MainCustomerTemplate::where('ct_cell_phone', $customer_phone)
            ->where('ct_active', 1)
            ->first();
        //CHECK CUSTOMER IN MAIN_CUSTOMER
        $check_customer = MainCustomer::where('customer_phone', $customer_phone)->first();

        if (isset($check_customer))
            $customer_id = $check_customer->customer_id;

        else {
            $customer_id = MainCustomer::max('customer_id') + 1;
            $main_customer_arr = [
                'customer_id' => $customer_id,
                'customer_lastname' => $customer_info->ct_lastname,
                'customer_firstname' => $customer_info->ct_firstname,
                'customer_email' => $customer_info->ct_email,
                'customer_address' => $customer_info->ct_address,
                'customer_phone' => $customer_info->ct_business_phone,
                'customer_city' => "",
                'customer_zip' => "",
                'customer_state' => 1,
                'customer_customer_template_id' => $customer_info->id
            ];
            MainCustomer::create($main_customer_arr);
        }

        $service_arr = [];
        $combo_service_list = implode(";", $request->cs_id);
        $today = Carbon::today();

        if ($request->place_id != 0) {
            $place_id = $request->place_id;
        } else {
            $place_id = PosPlace::max('place_id') + 1;
        }
        $cs_id = MainCustomerService::where('cs_place_id', $place_id)->max('cs_id') + 1;

        $request->service_price_hidden == 0?$status = 1:$status = 0;

        //INSERT MAIN_COMBO_SERVICE_BOUGHT
        $order_history_arr = [
            'id' => MainComboServiceBought::max('id')+1,
            'csb_customer_id' => $customer_id,
            'csb_combo_service_id' => $combo_service_list,
            'csb_amount' => $request->service_price_hidden,
            'csb_charge' => $request->payment_amount_hidden,
            'csb_cashback' => 0, 
            'csb_status' => $status,
            'created_by' => Auth::user()->user_id,
        ];
        //CREATE NEW PLACE IN POS_PLACE, NEW USER IN POS_USER IF CHOOSE NEW PLACE
        if ($request->place_id == 0) {
            //CREATE PLACE IP LICENSE
            if(strlen($place_id) < 6){
                $place_ip_license = "DEG-".str_repeat("0",(6 - strlen($place_id))).$place_id;
            }else
                $place_ip_license = "DEG-".$place_id;
            //INSERT POS_PLACE
            $place_arr = [
                'place_id' => $place_id,
                'place_customer_id' => $customer_id,
                'place_code' => 'place-' . $place_id,
                'place_logo' => 'logo',
                'place_name' => $request->business_name,
                'place_actiondate' => '{"mon": {"start": "09:00", "end": "21:00", "closed": false}, "tue": {"start": "09:00", "end": "21:00", "closed": false}, "wed": {"start": "09:00", "end": "21:00", "closed": false}, "thur": {"start": "09:00", "end": "21:00", "closed": false}, "fri": {"start": "09:00", "end": "21:00", "closed": false}, "sat": {"start": "09:00", "end": "21:00", "closed": false},"sun": {"start": "09:00", "end": "21:00", "closed": false} }',
                'place_actiondate_option' => 0,
                'place_period_overtime' => 1,
                'place_hour_overtime' => '08:00',
                'place_address' => $request->address!=""?$request->address:"",
                'place_website' => $request->website!=""?$request->website:"",
                'place_phone' => $request->business_phone!=""?$request->business_phone:"",
                'place_taxcode' => 'tax-code',
                'place_customer_type' => 'customer type',
                'place_url_plugin' => 'url plugin',
                'created_by' => Auth::user()->user_id,
                'updated_by' => Auth::user()->user_id,
                'place_ip_license' => $place_ip_license,
                'place_status' => 1,
                'place_bill_export' => 2,
                'place_email' => $request->email
            ];
            // return $place_arr;
            PosPlace::insert($place_arr);
            //DELETE CUSTOMER ASSIGN (Main_customer_assign)
            MainCustomerAssign::where([
                ['business_phone',$request->business_phone],
                ['business_name',$request->business_name],
                ['user_id',Auth::user()->user_id]
            ])->delete();
            //INSERT MAIN_USER_CUSTOMER_PLACE
            $user_customer = [
                'user_id' => Auth::user()->user_id,
                'team_id' => Auth::user()->user_team,
                'customer_id' => $customer_info->id,
                'place_id' =>$place_id,
            ];
//            return $user_customer;
            $check_user_customer_exist = MainUserCustomerPlace::where([
                ['user_id',Auth::user()->user_id],
                ['team_id',Auth::user()->user_team],
                ['customer_id',$customer_info->id],
                ['place_id',null]
            ]);
            if($check_user_customer_exist->count() == 0)
                MainUserCustomerPlace::create($user_customer);
            else
                $check_user_customer_exist->update(['place_id'=>$place_id]);
            //INSERT POS_CUSTOMERTAG
            $arrCustomerTag = [
                ['customertag_id' => 1, 'customertag_name' => 'Vip', 'customertag_place_id' => $place_id, 'customertag_status' => 1],
                ['customertag_id' => 2, 'customertag_name' => 'Royal', 'customertag_place_id' => $place_id, 'customertag_status' => 1]
            ];
            PosCustomertag::insert($arrCustomerTag);

            $listImageGiftcode = [
                [
                    "sub_id" => 1, "sub_place_id" => $place_id, "sub_name" => "Happy birthday to you",
                    "sub_image" => "images/giftcard-template/1.jpg", "sub_type" => 0
                ],
                [
                    "sub_id" => 2, "sub_place_id" => $place_id, "sub_name" => "Happy birthday",
                    "sub_image" => "images/giftcard-template/2.jpg", "sub_type" => 0
                ],
                [
                    "sub_id" => 3, "sub_place_id" => $place_id, "sub_name" => "Happy wedding",
                    "sub_image" => "images/giftcard-template/3.jpg", "sub_type" => 0
                ],
                [
                    "sub_id" => 4, "sub_place_id" => $place_id, "sub_name" => "Life good",
                    "sub_image" => "images/giftcard-template/4.jpg", "sub_type" => 0
                ],
                [
                    "sub_id" => 5, "sub_place_id" => $place_id, "sub_name" => "Beaty",
                    "sub_image" => "images/giftcard-template/5.jpg", "sub_type" => 0
                ],
                [
                    "sub_id" => 6, "sub_place_id" => $place_id, "sub_name" => "Merry christmas 01",
                    "sub_image" => "images/giftcard-template/6.jpg", "sub_type" => 0
                ],
                [
                    "sub_id" => 7, "sub_place_id" => $place_id, "sub_name" => "Wedding day",
                    "sub_image" => "images/giftcard-template/7.jpg", "sub_type" => 0
                ],
                [
                    "sub_id" => 8, "sub_place_id" => $place_id, "sub_name" => "Single",
                    "sub_image" => "images/giftcard-template/8.jpg", "sub_type" => 0
                ],
                [
                    "sub_id" => 9, "sub_place_id" => $place_id, "sub_name" => "Employee",
                    "sub_image" => "images/giftcard-template/9.jpg", "sub_type" => 0
                ],
                [
                    "sub_id" => 10, "sub_place_id" => $place_id, "sub_name" => "Mery christmas 02",
                    "sub_image" => "images/giftcard-template/10.jpg", "sub_type" => 0
                ],
                [
                    "sub_id" => 11, "sub_place_id" => $place_id, "sub_name" => "Valentines",
                    "sub_image" => "images/giftcard-template/11.jpg", "sub_type" => 0
                ],
                [
                    "sub_id" => 12, "sub_place_id" => $place_id, "sub_name" => "Beautiful",
                    "sub_image" => "images/giftcard-template/12.jpg", "sub_type" => 0
                ]
            ];
            PosSubjectWeb::insert($listImageGiftcode);

            //INSERT POS_USER
            //FORMAT PHONE NUMBER
            $phone = preg_replace("/[^0-9]/", "", $customer_info->ct_business_phone);
            $start_phone = substr($phone, 0, 1);
            if ($start_phone == '0')
                $phone = "1" . substr($phone, 1);
            else
                $phone = "1" . $phone;

            //CHECK USER EXISTED
            $check_user = PosUser::where('user_phone', $phone)->count();
            if ($check_user == 0) {
                $user_arr = [
                    'user_id' => 1,
                    'user_place_id' => $place_id,
                    'user_default_place_id' => 0,
                    'user_usergroup_id' => 1,
                    'user_password' => Hash::make('abc123'),
                    'user_pin' => 123456,
                    'user_fullname' => $customer_info->ct_firstname . ";" . $customer_info->ct_lastname,
                    'user_phone' => $phone,
                    'user_email' => $customer_info->ct_email,
                    'user_token' => csrf_token(),
                    'remember_token' => csrf_token(),
                    'created_by' => Auth::user()->user_id,
                    'updated_by' => Auth::user()->user_id,
                    'enable_status' => 1,
                    'user_status' => 1
                ];
                PosUser::create($user_arr);
            } else {
                $user_info = PosUser::where('user_phone', $phone)->first();
                if ($user_info->user_places_id == null) {
                    $user_places_id = $user_info->user_place_id . ',' . $place_id;
                    $user_default_place_id = $place_id;
                    PosUser::where('user_phone', $phone)->update(['user_places_id' => $user_places_id, 'user_default_place_id' => $user_default_place_id]);
                } else {
                    $user_places_id = $user_info->user_places_id . ',' . $place_id;
                    PosUser::where('user_phone', $phone)->update(['user_places_id' => $user_places_id]);
                }
            }
        }
        //UPDATE CUSTOMER STATUS
        $team_customer_status = MainTeam::find(Auth::user()->user_team)->getTeamType->team_customer_status;

        if ($team_customer_status == "") {

            $team_customer_status_arr[][$request->customer_id] = 4;

        } else {
            $team_customer_status_arr = json_decode($team_customer_status, TRUE);
            $team_customer_status_arr[$request->customer_id] = 4;
        }
        $team_customer_status_list = json_encode($team_customer_status_arr);

        $update_team_customr_status = MainTeam::find(Auth::user()->user_team)->getTeamtype->update(['team_customer_status' => $team_customer_status_list]);

        if (!isset($update_team_customr_status) ) {
            DB::callback();
            return back()->with(['error' => 'Save Order Failed!. Check again']);
        } else {
            $order_history_arr['csb_place_id'] = $place_id;
            //INSERT NEW ORDER
            $insert_order = MainComboServiceBought::create($order_history_arr);

            if (!isset($insert_order)
                || !isset($update_team_customr_status)) {

                return back()->with(['error' => 'Save Order Failed. Check again!']);
            } else {
                DB::commit();
                return redirect()->route('my-orders')->with(['success' => 'Transaction Successfully!']);
            }
        }
    }

    public function getCustomerInfor(Request $request)
    {
        $customer_phone = $request->customer_phone;
        $user_id = Auth::user()->user_id;
        $team_id = Auth::user()->user_team;

        $customer_list = MainUserCustomerPlace::where('user_id',$user_id);

        if ($customer_list->count() ==  0)
            return response(['status' => 'error', 'message' => 'You dont have any customer']);
        //GET CUSTOMER
        $customer_info  = MainCustomer::where('customer_phone',$customer_phone)->first();
        $customer_list = $customer_list->where('place_id','!=',null)->select('place_id')->get()->toArray();
        $customer_arr = array_values($customer_list);

        $place_list = PosPlace::where('place_customer_id',$customer_info->customer_id)
            ->whereIn('place_id',$customer_arr)
            ->where('place_status',1)->get();

        if (!isset($customer_info) || !isset($place_list))
            return response(['status' => 'error', 'message' => 'Get Customer Error']);
        else {
            if ($customer_info == "")
                return response(['status' => 'error', 'message' => 'Get Customer Error']);
            else
                return response(['customer_info' => $customer_info, 'place_list' => $place_list]);
        }
    }

    public function myOrderDatatable(Request $request)
    {
        $my_order_arr = [];

        $my_order_list = MainComboServiceBought::with('getPlace')->join('main_customer', function ($join) {
            $join->on('main_combo_service_bought.csb_customer_id', 'main_customer.customer_id');
        })
            ->join('main_user', function ($join) {
                $join->on('main_combo_service_bought.created_by', 'main_user.user_id');
            });

        if (isset($request->my_order)) {
            $my_order_list = $my_order_list->where('main_combo_service_bought.created_by', Auth::user()->user_id);
        }
        if($request->start_date != "" && $request->end_date != ""){
            $start_date = Carbon::parse($request->start_date)->subDay(1)->format('Y-m-d');
            $end_date = Carbon::parse($request->end_date)->addDay(1)->format('Y-m-d');
            $my_order_list = $my_order_list->whereBetween('main_combo_service_bought.created_at', [$start_date,$end_date]);
        }

        $my_order_list = $my_order_list->select('main_combo_service_bought.*', 'main_customer.customer_lastname', 'main_customer.customer_firstname','main_customer.customer_phone','main_customer.customer_email', 'main_user.user_nickname')
            ->get();
        //GET COLLECTION COMBO SERVICE
        $service_all = MainComboService::select('cs_name','id')->get();
        $service_all = collect($service_all);
        //GET USER COLLECT USER
        $user_list = MainUser::all();
        $user_list = collect($user_list);

        foreach ($my_order_list as $key => $order) {

            //GET INFORMATION CARD
            if (!isset($request->my_order)){
                if($order->csb_status == 1)
                    $infor = "<span>ID: " . $order->csb_trans_id . "</span><br><span>Name: " . $order->csb_card_type . "</span><br><span>Number: " . $order->csb_card_number . "</span>";
                else
                    $infor = "<span>Account Number: " . $order->account_number . "</span><br><span>Name: " . $order->routing_number . "</span><br><span>Bank Name: " . $order->bank_name . "</span>";
            }
            else
                $infor = "";

            $services = explode(";", $order->csb_combo_service_id);

            $service_list = $service_all->whereIn('id', $services);
            $service_name = "";
            foreach ($service_list as $service) {
                $service_name .= "-" . $service->cs_name . "<br>";
            }

            if (!isset($request->my_order))
                $order_date = "<a href='" . route('order-view', $order->id) . "'>" . format_datetime($order->created_at) . "<br> by " . $order->user_nickname . "</a>";
            else
                $order_date = "<a href='" . route('order-view', $order->id) . "'>" . format_datetime($order->created_at) . "</a>";

            //GET CUSTOMER INFORMATION
            $customer = "<span>Customer: <b>" . $order->customer_firstname . " " . $order->customer_lastname . "</b></span><br><span>Business Phone: <b>" . $order->getPlace->place_phone . "</b></span><br><span>Email: <b>" . $order->getPlace->place_email . "</b></span>";

            $updated_at = "";
            if($order->updated_by != ""){

                $updated_by = $user_list->where('user_id',$order->updated_by)->first();
                $updated_at = format_datetime($order->updated_at)."<br> by ".$updated_by->user_nickname;
            }

            $my_order_arr[] = [
                'id' => $order->id,
                'order_date' => $order_date,
                'customer' => $customer,
                'servivce' => $service_name,
                'subtotal' => $order->csb_amount,
                'discount' => $order->csb_amount_deal,
                'total_charge' => $order->csb_charge,
                'status' => $order->csb_status == 0 ? "NOTPAYMENT" : "PAID",
                'information' => $infor,
                'updated_at' => $updated_at
            ];
        }
        return DataTables::of($my_order_arr)
            ->editColumn('total_charge',function ($row){
                return '<span class="text-danger"><b>'.$row['total_charge'].'</b></span>';
            })
            ->rawColumns(['servivce', 'information', 'customer', 'order_date','updated_at','total_charge'])
            ->make(true);
    }

    public function sellerOrderDatatable(Request $request)
    {
        if (Gate::denies('permission', 'new-order-create'))
            return doNotPermission();

        $service_id = $request->service_id;
        $seller_id = $request->seller_id;
        $team_id = Auth::user()->user_team;
        $order_arr = [];

        $order_list = MainComboServiceBought::join('main_user', function ($join) {
            $join->on('main_combo_service_bought.created_by', 'main_user.user_id');
        });
        //CHECK LEADER TEAM
        $team_leader = MainTeam::where('team_leader', Auth::user()->user_id)->first();
        if (isset($team_leader) && $team_leader != "") {
            $order_list = $order_list->where('main_user.user_team', $team_id);
        }
        if($request->start_date != "" && $request->end_date != ""){
            $start_date = Carbon::parse($request->start_date)->subDay(1)->format('Y-m-d');
            $end_date = Carbon::parse($request->end_date)->addDay(1)->format('Y-m-d');
            $order_list = $order_list->whereBetween('main_combo_service_bought.created_at',[$start_date,$end_date]);
        }
        if ($seller_id != "") {
            $order_list = $order_list->where('main_combo_service_bought.created_by', $seller_id);
        }
        if ($service_id != "") {
            $order_list = $order_list->where(function ($query) use ($service_id) {
                $query->where('csb_combo_service_id', $service_id)
                    ->orWhere('csb_combo_service_id', 'like', '%;' . $service_id)
                    ->orWhere('csb_combo_service_id', 'like', $service_id . ";%")
                    ->orWhere('csb_combo_service_id', 'like', '%;' . $service_id . ';%');
            });
        }
        $order_list = $order_list->select('main_combo_service_bought.*', 'main_user.user_nickname')->get();

        //GET COMBO SERVICE COLLECTION
        $combo_service_list = MainComboService::select('cs_name','id')->get();
        $combo_service_list = collect($combo_service_list);

        //GET CUSTOMER COLLECTION
        $customer_list = MainCustomer::select('customer_firstname','customer_lastname','customer_id')->get();
        $customer_list = collect($customer_list);

        foreach ($order_list as $key => $order) {

            $services = explode(";", $order->csb_combo_service_id);

            $service_list = $combo_service_list->whereIn('id', $services);
            $service_name = "";
            foreach ($service_list as $service) {
                $service_name .= "-" . $service->cs_name . "<br>";
            }

            //GET CUSTOMER INFO
            $customer_info = $customer_list->where('customer_id',$order->csb_customer_id)->first();
            $customer_info = $customer_info->customer_firstname. " " . $customer_info->customer_lastname;

            $order_arr[] = [
                'id' => $order->id,
                'order_date' => Carbon::parse($order->created_at)->format('m/d/Y H:i:s'),
                'customer' => $customer_info,
                'servivce' => $service_name,
                'subtotal' => $order->csb_amount,
                'discount' => $order->csb_amount_deal,
                'total_charge' => $order->csb_charge,
                'seller' => $order->user_nickname,
            ];
        }
        return DataTables::of($order_arr)
            ->rawColumns(['servivce', 'information'])
            ->make(true);
    }

    public function orderView($id)
    {
        if (Gate::denies('permission', 'order-view'))
            return doNotPermission();
        $data['id'] = $id;
        $data['order_info'] = MainComboServiceBought::join('main_customer', function ($join) {
            $join->on('main_combo_service_bought.csb_customer_id', 'main_customer.customer_id');
        })
            ->join('main_user', function ($join) {
                $join->on('main_combo_service_bought.created_by', 'main_user.user_id');
            })
            ->where('main_combo_service_bought.id', $id)
            ->select('main_combo_service_bought.*',
                'main_user.user_nickname',
                'main_user.user_email',
                'main_customer.customer_phone',
                'main_customer.customer_lastname',
                'main_customer.customer_firstname',
                'main_customer.customer_id'
            )
            ->first();

        $data['customer_id'] = MainCustomer::where('customer_id', $data['order_info']->customer_id)->first()->customer_customer_template_id;

        $combo_service_list = $data['order_info']->csb_combo_service_id;
        $combo_service_arr = explode(";", $combo_service_list);
        $service_arr = [];

        //GET TASK LIST
        $data['task_list'] = MainTask::with('getService')->leftjoin('main_user', function ($join) {
            $join->on('main_task.updated_by', 'main_user.user_id');
        })
            ->where('main_task.order_id', $id)
            ->where(function ($query) {
                $query->where('main_task.created_by', Auth::user()->user_id)
                    ->orWhere('main_task.assign_to', Auth::user()->user_id)
                    ->orWhere('main_task.updated_by', Auth::user()->user_id);
            })
            ->select('main_task.*', 'main_user.user_nickname')
            ->get();
            // return $data;

        return view('orders.order-view', $data);
    }

    public function orderTracking(Request $request)
    {

        $order_id = $request->order_id;

        $order_tracking = MainUser::join('main_tracking_history', function ($join) {
            $join->on('main_tracking_history.created_by', 'main_user.user_id');
        })
            ->where('main_tracking_history.order_id', $order_id)
            ->whereNull('main_tracking_history.subtask_id')
            ->select('main_tracking_history.*', 'main_user.user_firstname', 'main_user.user_lastname', 'main_user.user_team', 'main_user.user_nickname')->get();

        return DataTables::of($order_tracking)
            ->addColumn('user_info', function ($row) {
                return '<span>' . $row->user_nickname . '(' . $row->getFullname() . ')</span><br>
		                <span>' . format_datetime($row->created_at) . '</span><br>
		                <span class="badge badge-secondary">' . $row->getTeam->team_name . '</span>';
            })
            ->addColumn('task', function ($row) {
                return "<a href='' >Task#" . $row->task_id . "</a>";
            })
            ->editColumn('content', function ($row) {
                $file_list = MainFile::where('tracking_id', $row->id)->get();
                $file_name = "<div class='row '>";
                if ($file_list->count() > 0) {

                    foreach ($file_list as $key => $file) {
                        $zip = new ZipArchive();

                        if ($zip->open($file->name, ZipArchive::CREATE) !== TRUE) {
                            $file_name .= '<form action="' . route('down-image') . '" method="POST"><input type="hidden" value="' . csrf_token() . '" name="_token" /><input type="hidden" value="' . $file->name . '" name="src" /><img class="file-comment ml-2" src="' . asset($file->name) . '"/></form>';
                        } else {
                            $file_name .= '<form action="' . route('down-image') . '" method="POST"><input type="hidden" value="' . csrf_token() . '" name="_token" /><input type="hidden" value="' . $file->name . '" name="src" /><a href="javascript:void(0)" class="file-comment ml-2" /><i class="fas fa-file-archive"></i>' . $file->name_origin . '</a></form>';
                        }
                    }
                }
                $file_name .= "</div>";
                return $row->content . "<br>" . $file_name;
            })
            ->rawColumns(['user_info', 'task', 'content'])
            ->make(true);
    }

    public function orderService(Request $request)
    {

        $order_id = $request->order_id;

        $service_list = MainTask::join('main_combo_service', function ($join) {
            $join->on('main_task.service_id', 'main_combo_service.id');
        })
            ->where('main_task.order_id', $order_id)
            ->select('main_combo_service.*', 'main_task.id', 'main_combo_service.id as csb_id', 'main_task.note', 'main_task.content');
//        return $service_list;

        return DataTables::of($service_list)
            ->addColumn('action', function ($row) {
                return '<button type="button" form_type_id="' . $row->cs_form_type . '" task_id="' . $row->id . '" class="btn btn-sm btn-secondary input-form">INPUT FORM</button>';
            })
            ->addColumn('infor', function ($row) {

                //GET FILES
                $file_name = "<div class='row'>";
                $file_list = $row->getFiles;
                foreach ($file_list as $key => $file) {

                    $allowedMimeTypes = ['image/jpeg','image/gif','image/png','image/bmp','image/svg+xml'];
                    $contentType = mime_content_type($file->name);
                    if(! in_array($contentType, $allowedMimeTypes) ){
                        $file_name .= '<form action="' . route('down-image') . '" method="POST"><input type="hidden" value="' . csrf_token() . '" name="_token" /><input type="hidden" value="' . $file->name . '" name="src" /><a href="javascript:void(0)" class="file-comment ml-2" /><i class="fas fa-file-archive"></i>' . $file->name_origin . '</a></form>';

                    }else{
                        $file_name .= '<form action="' . route('down-image') . '" method="POST"><input type="hidden" value="' . csrf_token() . '" name="_token" /><input type="hidden" value="' . $file->name . '" name="src" /><img class="file-comment ml-2" src="' . asset($file->name) . '"/></form>';

                    }

                }
                $file_name .= "</div>";

                $content = '';

                if ($row->content != NULL) {
                    $content_arr = json_decode($row->content, TRUE);

                    if ($row->cs_form_type == 1) {
                        $content = '<span>Google Link: <b>' . $content_arr['google_link'] . '</b></span><br>
	                    <span>Tên thợ nails: ' . $content_arr['worker_name'] . '</span><br>
	                    <div class="row">
	                        <span class="col-md-6">Number of starts: <b>' . $content_arr['star'] . '</b></span>
	                        <span class="col-md-6">Số review hiện tại: <b>' . $content_arr['current_review'] . '</b></span>
	                        <span class="col-md-6">Conplete date: <b>' . $content_arr['complete_date'] . '</b></span>
	                        <span class="col-md-6">Số review yêu cầu: <b>' . $content_arr['order_review'] . '</b></span>
	                    </div>
	                    <span>Note: <b>' . $row->note . '</span>
	                    <a href="javascript:void(0)">' . $file_name . '</a>';
                    }
                    if ($row->cs_form_type == 2) {
                        $content = '<span>Tên sản phẩm: <b>' . $content_arr['product_name'] . '</b></span><br>
	                    <span>Màu chủ đạo: <b>' . $content_arr['main_color'] . '</b></span><br>
	                    <span>Thể loại hoặc phong cách khách hàng: <b>' . $content_arr['style_customer'] . '</b></span><br>
	                    <span>Facebook Link: <b>' . $content_arr['link'] . '</b></span><br>
	                    <span>Website: <b>' . $content_arr['website'] . '</b></span><br>
	                    <span>Note: <b>' . $row->note . '</span>
	                    <a href="javascript:void(0)">' . $file_name . '</a>';
                    }
                    if ($row->cs_form_type == 3) {
                        if (isset($content_arr['admin'])) $admin = "YES";
                        else $admin = "NO";
                        if (isset($content_arr['image'])) $image = "YES";
                        else $image = "NO";
                        $content = '<span>Facebook Link: <b>' . $content_arr['link'] . '</b></span><br>
	                    <span>Promotion: <b>' . $content_arr['promotion'] . '</b></span><br>
	                    <span>Số lượng bài viết: <b>' . $content_arr['number'] . '</b></span><br>
	                    <div class="row">
	                        <span class="col-md-6">Đã có admin chưa: <b>' . $admin . '</b></span>
	                        <span class="col-md-6">Username: <b>' . $content_arr['user'] . '</b></span>
	                        <span class="col-md-6">Có lấy được hình ảnh: <b>' . $image . '</b></span>
	                        <span class="col-md-6">Password: <b>' . $content_arr['password'] . '</b></span>
	                    </div>
	                    <span>Note: <b>' . $row->note . '</span>
	                    <a href="javascript:void(0)">' . $file_name . '</a>';
                    }
                    if ($row->cs_form_type == 4) {

                        if (isset($content_arr['show_price'])) $show_price = "YES";
                        else $show_price = "NO";

                        $content = '<span>Domain: <b>' . $content_arr['domain'] . '</b></span><br>
	                    <div class="row">
	                        <span class="col-md-6">Theme: <b>' . $content_arr['theme'] . '</b></span>
	                        <span class="col-md-6">Show Price: <b>' . $show_price . '</b></span>
	                        <span class="col-md-6">Business Name: <b>' . $content_arr['business_name'] . '</b></span>
	                        <span class="col-md-6">Business Phone: <b>' . $content_arr['business_phone'] . '</b></span>
	                        <span class="col-md-6">Email: <b>' . $content_arr['email'] . '</b></span>
	                        <span class="col-md-6">Address: <b>' . $content_arr['address'] . '</b></span>
	                    </div>
	                    <span>Note: <b>' . $row->note . '</span>
	                    <a href="javascript:void(0)">' . $file_name . '</a>';
                    }
                }
                return $content;
            })
            ->rawColumns(['action', 'infor'])
            ->make(true);
    }

    public function submitInfoTask(Request $request){
        $input = $request->all();
        $current_month = Carbon::now()->format('m');

        unset($input['list_file']);
        unset($input['_token']);
        unset($input['list_file']);
        unset($input['task_id']);
        unset($input['note']);

        $content = json_encode($input);

        DB::beginTransaction();
        //ADD TRACKING HISTORY
        $tracking_arr = [
            'order_id' => $request->order_id,
            'task_id' => $request->task_id,
            'desription' => $request->note,
            'created_by' => Auth::user()->user_id,
        ];
        $tracking_create = MainTrackingHistory::create($tracking_arr);

        $task_info = MainTask::find($request->task_id);
        if($task_info->content != "" || $task_info->content != null){

            $content_arr = json_decode($task_info->content);
            $change = 0;

            if($task_info->getService->cs_form_type == 1){
                if(isset($request->google_link)) {$content_arr->google_link = $request->google_link;$change++;}
                if(isset($request->worker_name)) {$content_arr->worker_name = $request->worker_name;$change++;}
                if(isset($request->star)) {$content_arr->star = $request->star;$change++;}
                if(isset($request->current_review)) {$content_arr->current_review = $request->current_review;$change++;}
                if(isset($request->order_review)) {$content_arr->order_review = $request->order_review;$change++;}
                if(isset($request->complete_date)) {$content_arr->complete_date = $request->complete_date;$change++;}
            }
            elseif($task_info->getService->cs_form_type == 2){
                if(isset($request->product_name)) {$content_arr->product_name = $request->product_name;$change++;}
                if(isset($request->main_color)) {$content_arr->main_color = $request->main_color;$change++;}
                if(isset($request->style_customer)) {$content_arr->style_customer = $request->style_customer;$change++;}
                if(isset($request->link)) {$content_arr->link = $request->link;$change++;}
                if(isset($request->website)) {$content_arr->website = $request->website;$change++;}
            }
            elseif($task_info->getService->cs_form_type == 3){
                if(isset($request->link)) {$content_arr->link = $request->link;$change++;}
                if(isset($request->promotion)) {$content_arr->promotion = $request->promotion;$change++;}
                if(isset($request->number)) {$content_arr->number = $request->number;$change++;}
                if(isset($request->admin)) {$content_arr->admin = $request->admin;$change++;}
                if(isset($request->user)) {$content_arr->user = $request->user;$change++;}
                if(isset($request->password)) {$content_arr->password = $request->password;$change++;}
                if(isset($request->image)) {$content_arr->image = $request->image;$change++;}
            }
            elseif($task_info->getService->cs_form_type == 4){
                if(isset($request->domain)) {$content_arr->domain = $request->domain;$change++;}
                if(isset($request->theme)) {$content_arr->theme = $request->theme;$change++;}
                if(isset($request->show_price)) $content_arr->show_price = $request->show_price;
                if(isset($request->business_name)) {$content_arr->business_name = $request->business_name;$change++;}
                if(isset($request->business_phone)) {$content_arr->business_phone = $request->business_phone;$change++;}
                if(isset($request->email)) {$content_arr->email = $request->email;$change++;}
                if(isset($request->address)) {$content_arr->address = $request->address;$change++;}
            }
            $content = json_encode($content_arr);
        }
        //UPDATE TASK
        if($task_info->status == 3 && $change > 0)
            $task_update = MainTask::where('id', $request->task_id)
                ->update(['status'=>2,'content' => $content, 'desription' => $request->desription, 'updated_by' => Auth::user()->user_id]);
        else
            $task_update = MainTask::where('id', $request->task_id)
                ->update(['content' => $content, 'desription' => $request->desription, 'updated_by' => Auth::user()->user_id]);

        //DELETE OLD FILE
        $file_delete = MainFile::where('task_id', $request->task_id)->delete();

        //UPDATE FILE
        if ($request->list_file != "") {
            foreach ($request->list_file as $key => $file) {

                $file_name = ImagesHelper::uploadImage2($file, $current_month, 'images/comment/');
                $file_arr[] = [
                    'name' => $file_name,
                    'name_origin' => $file->getClientOriginalName(),
                    'tracking_id' => $tracking_create->id,
                    'task_id' => $request->task_id
                ];
            }
            //INSERT NEW FILE
            $file_create = MainFile::insert($file_arr);
            if (!isset($file_create) || !isset($task_update) || !isset($tracking_create) || !isset($file_delete)) {
                DB::callback();
                return response(['status' => 'error', 'message' => 'Failed!']);
            } else {
                DB::commit();

                $task_info = MainTask::find($request->task_id);
                $name_created = $task_info->getUpdatedBy->user_nickname;
                $content = "Dear Sir/Madam,<br>";
                $content .= $name_created . " have just input order form on order#" . $task_info->order_id . "Service: " . $task_info->getService->cs_name . "<hr>";
                $content .= "<a href='" . route('order-view', $task_info->order_id) . "'  style='color:#e83e8c'>Click here to view ticket detail</a><br>";
                $content .= "WEB MASTER (DTI SYSTEM)";

                $input['subject'] = 'INPUT ORDER FORM';
                $input['email'] = $task_info->getAssignTo->user_email;
                $input['name'] = $task_info->getAssignTo->user_firstname . " " . $task_info->getAssignTo->user_lastname;
                $input['email_arr'][] = $task_info->getUpdatedBy->user_email;
                $input['message'] = $content;
                dispatch(new SendNotification($input))->delay(now()->addSecond(3));

                return response(['status' => 'success', 'message' => 'Successfully']);
            }
        }

        if (!isset($task_update) || !isset($tracking_create)) {
            DB::callback();
            return response(['status' => 'error', 'message' => 'Failed!']);
        } else {
            DB::commit();

            $task_info = MainTask::find($request->task_id);
            $name_created = $task_info->getUpdatedBy->user_nickname;
            $content = "Dear Sir/Madam,<br>";
            $content .= $name_created . " have just input order form on order#" . $task_info->order_id . "Service: " . $task_info->getService->cs_name . "<hr>";
            $content .= "<a href='" . route('order-view', $task_info->order_id) . "'  style='color:#e83e8c'>Click here to view ticket detail</a><br>";
            $content .= "WEB MASTER (DTI SYSTEM)";

            $input['subject'] = 'INPUT ORDER FORM';
            $input['email'] = $task_info->getAssignTo->user_email;
            $input['name'] = $task_info->getAssignTo->user_firstname . " " . $task_info->getAssignTo->user_lastname;
            $input['email_arr'][] = $task_info->getUpdatedBy->user_email;
            $input['message'] = $content;
            dispatch(new SendNotification($input))->delay(now()->addSecond(3));

            return response(['status' => 'success', 'message' => 'Successfully']);
        }
    }

    public function changeStatusOrder(Request $request)
    {

        $order_id = $request->order_id;
        $order_status = $request->order_status;

        if($order_status == 0){
            $status_update = 1;
            $status_text = "PAID";
        }
        else{ 
            $status_update = 2;
            $status_text = "DELIVERED";
        }
        if($status_update == 2)
            $order_update = MainComboServiceBought::find($order_id)->update(['csb_status' => $status_update,'csb_last_call'=>now()]);
        else
            $order_update = MainComboServiceBought::find($order_id)->update(['csb_status' => $status_update]);

        if (!isset($order_update))
            return response(['status' => 'error', 'message' => 'Failed!']);
        else
            return response(['status' => 'success', 'message' => 'Successfully','status_text'=>$status_text]);
    }

    public function resendInvoice(Request $request)
    {

        $order_id = $request->order_id;
        $input = [];

        $order_info = MainComboServiceBought::find($order_id);

        $customer_email = $order_info->getCustomer->customer_email;
        if ($customer_email == "") {
            return response(['status' => 'error', 'message' => 'Send Mail Failed!']);
        }
        $service_list = $order_info->csb_combo_service_id;
        $service_arrray = explode(";", $service_list);
        $order_info['combo_service_list'] = MainComboService::whereIn('id', $service_arrray)->get();

        $content = $order_info->present()->getThemeMail;

        $input['subject'] = 'INVOICE';
        $input['email'] = $customer_email;
        $input['name'] = $order_info->getCustomer->customer_firstname . " " . $order_info->getCustomer->customer_lastname;
        $input['message'] = $content;

        dispatch(new SendNotification($input));

        return response(['status' => 'success', 'message' => 'Send Mail Successfully!']);
    }

    public function dowloadInvoice($order_id)
    {

        $order_info = MainComboServiceBought::find($order_id);
        $service_list = $order_info->csb_combo_service_id;
        $service_arrray = explode(";", $service_list);
        $order_info['combo_service_list'] = MainComboService::whereIn('id', $service_arrray)->get();

        $content = $order_info->present()->getThemeMail;

        $dompdf = new Dompdf();

        $dompdf->loadHtml($content);
        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'landscape');
        // Render the HTML as PDF
        $dompdf->render();
        // Output the generated PDF to Browser
        $dompdf->stream();
    }

    public function paymentOrderList()
    {

        if (Gate::denies('permission', 'payment-orders'))
            return doNotPermission();

        return view('orders.payment-orders-list');
    }

    public function paymentOrder($id)
    {
        if (Gate::denies('permission', 'payment-orders'))
            return doNotPermission();

        if (!$id)
            return back()->with('error', 'Payment Failed!');
        //CHECK ID ORDER EXIST
        $order_info = MainComboServiceBought::find($id);
        if (!$order_info)
            return back()->with('error', 'Order not Exist');
        if($order_info->csb_status == 1)
            return back()->with('success','This order has been paid!');

        $data['order_id'] = $id;
        $data['order_info'] = $order_info;
        $data['customer_info'] = $order_info->getPlace;
        $service_list = $order_info->csb_combo_service_id;
        $service_arr = explode(';', $service_list);
        $data['service_list'] = MainComboService::whereIn('id', $service_arr)->select('cs_name', 'cs_price')->get();

        return view('orders.payment-order', $data);
    }

    function authorizeCreditCard(Request $request)
    {
        if (Gate::denies('permission', 'payment-orders'))
            return doNotPermission();

        if ($request->credit_card_type != 'E-CHECK') {
            $rule = [
                'payment_amount' => 'required',
                'credit_card_type' => 'required',
                'credit_card_number' => 'required',
                'experation_month' => 'required',
                'experation_year' => 'required',
                'cvv_number' => 'required',
                'fullname' => 'required',
                'order_id' => 'required'
            ];
        } else {
            $rule = [
                'payment_amount' => 'required',
                'credit_card_type' => 'required',
                'routing_number' => 'required',
                'account_number' => 'required',
                'bank_name' => 'required',
                'fullname' => 'required',
                'order_id' => 'required'
            ];
        }
        $validator = Validator::make($request->all(), $rule);
        if ($validator->fails())
            return redirect()->back()->withErrors($validator)->withInput();

        $input = $request->all();
        $input['csb_trans_id'] = "";

        if ($input['credit_card_type'] != 'E-CHECK') {
            $response = self::authorizePayment($input);
            if ($response['status'] == 'error'){
                $message = $response['message'];
                return back()->with(['error'=>$message]);
            }
            else
                $input['csb_trans_id'] = $response['csb_trans_id'];
            $input['csb_payment_method'] = 2;
        }else
            $input['csb_payment_method'] = 3;

        if($input['credit_card_number'] != ""){
            $input['credit_card_number'] = substr($input['credit_card_number'],0,4)."####".substr($input['credit_card_number'],-4);
        }
        if($input['routing_number'] != ""){
            $input['routing_number'] = substr($input['routing_number'],0,4)."####".substr($input['routing_number'],-4);
        }
        $order_arr = [
            'csb_payment_method' => $input['csb_payment_method'],
            'csb_card_type' => $input['credit_card_type'],
            'csb_card_number' => $input['credit_card_number'],
            'csb_status' => 1,
            'csb_note' => $input['note'],
            'updated_by' => Auth::user()->user_id,
            'bank_name' => $input['bank_name'],
            'account_number' => $input['account_number'],
            'routing_number' => $input['routing_number'],
            'csb_trans_id' => $input['csb_trans_id']
        ];

        DB::beginTransaction();

        $order_info = MainComboServiceBought::find($input['order_id']);
        $update_order = $order_info->update($order_arr);

        //CHECK SERVICE OR COMBO
        $combo_service_arr = explode(';',$order_info->csb_combo_service_id);
        $service_arr = [];
        $place_id = $order_info->csb_place_id;
        $customer_id = $order_info->csb_customer_id;
        $today = today();

        foreach ($combo_service_arr as $key => $value) {
            $service_list = MainComboService::where('id', $value)->first();
            if ($service_list->cs_type == 1)
                $service_arr = array_merge(explode(";", $service_list->cs_service_id), $service_arr);
            else
                $service_arr[] = $value;
        }
        $cs_id = MainCustomerService::where('cs_place_id', $place_id)->max('cs_id') + 1;

        //UPDATE MAIN_CUSTOMER_SERVICE
        foreach ($service_arr as $key => $service) {
            //GET EXPIRY PERIOD OF SERVICE
            $service_expiry_period = MainComboService::where('id', $service)->first()->cs_expiry_period;
            //CHECK CUSTOMER SERVICE EXIST
            $check = MainCustomerService::where('cs_place_id', $place_id)
                ->where('cs_customer_id', $customer_id)
                ->where('cs_service_id', $service)
                ->first();
            if (isset($check)) {
                $cs_date_expire = $check->cs_date_expire;
                if ($cs_date_expire >= $today) {
                    $cs_date_expire = Carbon::parse($cs_date_expire)->addMonths($service_expiry_period)->format('Y-m-d');
                } else
                    $cs_date_expire = Carbon::parse($today)->addMonths($service_expiry_period)->format('Y-m-d');

                //UPDATE SERVICE IN MAIN CUSTOMER SERVICE
                $customer_service_update = MainCustomerService::where('cs_place_id', $place_id)
                    ->where('cs_service_id', $service)
                    ->update(['cs_date_expire' => $cs_date_expire, 'updated_by' => $order_info->created_by]);
            } else {
                $cs_date_expire = Carbon::parse($today)->addMonths($service_expiry_period)->format('Y-m-d');

                $order_arr = [
                    'cs_id' => $cs_id,
                    'cs_place_id' => $place_id,
                    'cs_customer_id' => $customer_id,
                    'cs_service_id' => $service,
                    'cs_date_expire' => $cs_date_expire,
                    'cs_type' => 0,
                    'created_at' => Carbon::now(),
                    'created_by' => $order_info->created_by,
                    'cs_status' => 1,
                ];
                $customer_service_update = MainCustomerService::insert($order_arr);
                $cs_id++;
            }
        }
        //END UPDATE MAIN_CUSTOMER_SERVICE

        //CHANGE CUSTOMER STATUS IN MAIN_CUSTOMER_TEMPLATE
        $customer_teamplate_id = MainCustomer::where('customer_id',$customer_id)->first()->customer_customer_template_id;
        $team_info = MainTeam::find(Auth::user()->user_team)->getTeamType;
        $team_slug = $team_info->slug;

        //CHECK CUSTOMER TEMPLATE
        $check_customer = DB::table('main_customer_template')->where('id',$customer_teamplate_id);
        if($check_customer->first()->$team_slug != 4)
            $check_customer->update([$team_slug=>4]);

        //ADD TASK FOR SATFF
        $service_arr = array_unique($service_arr);

        foreach ($service_arr as $key => $service) {
            $service_info = MainComboService::find($service);

            //GET DATE END TASK
            if($service_info->cs_type_time == 1)
                $date_end = today()->addMonths($service_info->cs_expiry_period)->format('Y-m-d');
            else
                $date_end = today()->addDays($service_info->cs_expiry_period)->format('Y-m-d');

            $date_start = today()->format('Y-m-d');

            $task_arr = [
                'id' => MainTask::max('id')+1,
                'subject' => $service_info->cs_name,
                'priority' => 2,
                'status' => 1,
                'order_id' => $order_info->id,
                'created_by' => Auth::user()->user_id,
                'updated_by' => Auth::user()->user_id,
                'service_id' => $service,
                'place_id' => $place_id,
                'category' => 1,
                'assign_to' => $service_info->cs_assign_to,
                'content' => "",
                'date_start' => $date_start,
                'date_end' => $date_end
            ];
            $task_create = MainTask::create($task_arr);
        }

        if (!isset($update_order) || !isset($task_create) || !isset($customer_service_update)){
            DB::callback();
            return redirect()->route('payment-order-list')->with(['error'=>'Payment Successfully, but Save Information Payment Failed!']);
        }
        else{
            DB::commit();
            return redirect()->route('payment-order-list')->with(['success'=>'Payment Successfully']);
        }
    }

    public static function authorizePayment($input)
    {
        /* Create a merchantAuthenticationType object with authentication details
           retrieved from the constants file */
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName(env('MERCHANT_LOGIN_ID'));
        $merchantAuthentication->setTransactionKey(env('MERCHANT_TRANSACTION_KEY'));

        // Set the transaction's refId
        $refId = 'ref' . time();
        $experation_date = $input['experation_year'] . "-" . $input['experation_month'];

        // Create the payment data for a credit card
        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber($input['credit_card_number']); //"4111111111111111"
        $creditCard->setExpirationDate($experation_date); //"2038-12"
        $creditCard->setCardCode($input['cvv_number']); // "123"
        // Add the payment data to a paymentType object
        $paymentOne = new AnetAPI\PaymentType();
        $paymentOne->setCreditCard($creditCard);
        // Create a TransactionRequestType object and add the previous objects to it
        $transactionRequestType = new AnetAPI\TransactionRequestType();
        $transactionRequestType->setTransactionType("authOnlyTransaction");
        $transactionRequestType->setAmount($input['payment_amount']);
        $transactionRequestType->setPayment($paymentOne);
        // Assemble the complete transaction request
        $request = new AnetAPI\CreateTransactionRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setRefId($refId);
        $request->setTransactionRequest($transactionRequestType);
        // Create the controller and get the response
        $controller = new AnetController\CreateTransactionController($request);
        $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
        if ($response != null) {
            // Check to see if the API request was successfully received and acted upon
            if ($response->getMessages()->getResultCode() == "Ok") {
                $tresponse = $response->getTransactionResponse();

                if ($tresponse != null && $tresponse->getMessages() != null) {
                    $result = [
                        'status' => 'success',
                        'message' => 'Transaction Successfully!',
                        'csb_trans_id' => $tresponse->getTransId()
                    ];
                    return $result;
                } else {
                    $error = $tresponse->getErrors()[0]->getErrorText();
                    $result = [
                        'status' => 'error',
                        'message' => $error
                    ];
                    return $result;
                }
            } else {
                $tresponse = $response->getTransactionResponse();
                if ($tresponse != null && $tresponse->getErrors() != null) {
                    $error =  $tresponse->getErrors()[0]->getErrorText();

                } else {
                    $error = $response->getMessages()->getMessage()[0]->getText();
                }
                $result = [
                    'status' => 'error',
                    'message' => $error
                ];
                return $result;
            }
        } else {
            $result = [
                'status' => 'error',
                'message' => 'No response returned'
            ];
            return $result;
        }
    }
    public function paymentOrderDatatable(Request $request)
    {
        $my_order_arr = [];

        $my_order_list = MainComboServiceBought::join('main_customer',function($join){
                $join->on('main_combo_service_bought.csb_customer_id','main_customer.customer_id');
            })
            ->join('main_user',function($join){
                $join->on('main_combo_service_bought.created_by','main_user.user_id');
            })
            ->leftjoin('pos_place',function($join){
                $join->on('main_combo_service_bought.csb_place_id','pos_place.place_id');
            })
            ->where(function ($query){
                $query->where('pos_place.place_demo','=',null)
                    ->orWhere('pos_place.place_demo','=',0);
            })
            ->where('main_combo_service_bought.csb_status',0);

            if(Gate::allows('permission','admin-payment-orders')){

            }else{
                //GET USER OF TEAM CSKH
                $team_id = MainTeam::where('team_cskh_id',Auth::user()->user_team)->first();
                $user_list = MainUser::where('user_team',$team_id->id)->select('user_id')->get()->toArray();
                $user_arr = array_values($user_list);
                $my_order_list = $my_order_list->whereIn('main_combo_service_bought.created_by',$user_arr);
            }

        if($request->start_date != "" && $request->end_date != ""){
            $start_date = Carbon::parse($request->start_date)->subDay(1)->format('Y-m-d');
            $end_date = Carbon::parse($request->end_date)->addDay(1)->format('Y-m-d');
            $my_order_list = $my_order_list->whereBetween('main_combo_service_bought.created_at',[$start_date,$end_date]);
        }

        $my_order_list = $my_order_list->select('main_combo_service_bought.*','main_customer.customer_lastname','main_customer.customer_firstname','main_user.user_nickname')
            ->get();

        foreach ($my_order_list as $key => $order) {

            $services = explode(";",$order->csb_combo_service_id);

            $service_list = MainComboService::whereIn('id',$services)->select('cs_name')->get();
            $service_name = "";
            foreach ($service_list as $service) {
                $service_name .= "-".$service->cs_name."<br>";
            }

            if(!isset($request->my_order))
                $order_date = "<span>".format_datetime($order->created_at)."<br>by ".$order->user_nickname."</span>";
            else
                $order_date = "<span>".format_datetime($order->created_at)."</span>";

            //GET CUSTOMER INFORMATION
            $customer = "<span>Customer: <b>".$order->customer_firstname. " " .$order->customer_lastname."</b></span><br><span>Business Phone: <b>".$order->getPlace->place_phone."</b></span><br><span>Email: <b>".$order->getPlace->place_email."</b></span>";

            $my_order_arr[] = [
                'id' => $order->id,
                'order_date' => $order_date,
                'customer' => $customer,
                'servivce' => $service_name,
                'subtotal' => $order->csb_amount,
                'discount' => $order->csb_amount_deal,
                'total_charge' => $order->csb_charge,
            ];
        }
        return  DataTables::of($my_order_arr)
            ->editColumn('total_charge',function($row){
                return '<span class="text-danger"><b>'.$row['total_charge'].'</b></span>';
            })
            ->addColumn('action',function ($row){
                return '<a class="btn btn-sm"  href="'.route('payment-order',$row['id']).'" title="Payment Order"><i class="fas fa-wallet"></i></a>';
            })
            ->rawColumns(['servivce','information','customer','order_date','action','total_charge'])
            ->make(true);
    }
    public function getDataInputForm(Request $request){

        $task_info = MainTask::where('id',$request->task_id)->with('getService')->first();
        if(!isset($task_info))
            return response(['status'=>'error','message'=>'Failed!']);

        return response(['status'=>'success','content'=>$task_info->content]);
    }
    public function deliveredOrderDatatable(Request $request)
     {
        $my_order_arr = [];

        $my_order_list = MainComboServiceBought::join('main_customer',function($join){
                $join->on('main_combo_service_bought.csb_customer_id','main_customer.customer_id');
            })
            ->join('main_user',function($join){
                $join->on('main_combo_service_bought.created_by','main_user.user_id');
            })
            ->leftjoin('pos_place',function($join){
                $join->on('main_combo_service_bought.csb_place_id','pos_place.place_id');
            })
            ->where(function ($query){
                $query->where('pos_place.place_demo','=',null)
                    ->orWhere('pos_place.place_demo','=',0);
            })
            ->where('main_combo_service_bought.csb_status',0);

            if(Gate::allows('permission','admin-payment-orders')){

            }else{
                //GET USER OF TEAM CSKH
                $team_id = MainTeam::where('team_cskh_id',Auth::user()->user_team)->first();
                $user_list = MainUser::where('user_team',$team_id->id)->select('user_id')->get()->toArray();
                $user_arr = array_values($user_list);
                $my_order_list = $my_order_list->whereIn('main_combo_service_bought.created_by',$user_arr);
            }

        if($request->start_date != "" && $request->end_date != ""){
            $start_date = Carbon::parse($request->start_date)->subDay(1)->format('Y-m-d');
            $end_date = Carbon::parse($request->end_date)->addDay(1)->format('Y-m-d');
            $my_order_list = $my_order_list->whereBetween('main_combo_service_bought.created_at',[$start_date,$end_date]);
        }
        //GET 2 WEEKS AGO
        $date_before = today()->subWeeks(2);
        $my_order_list = $my_order_list->where('main_combo_service_bought.csb_last_call','<=',$date_before)
                        ->where(function($query){
                            $query->where('csb_status_call',null)
                            ->orWhere('csb_status_call','!=',3);
                        });

        $my_order_list = $my_order_list->select('main_combo_service_bought.*','main_customer.customer_lastname','main_customer.customer_firstname','main_user.user_nickname')
            ->get();

        foreach ($my_order_list as $key => $order) {

            $services = explode(";",$order->csb_combo_service_id);

            $service_list = MainComboService::whereIn('id',$services)->select('cs_name')->get();
            $service_name = "";
            foreach ($service_list as $service) {
                $service_name .= "-".$service->cs_name."<br>";
            }

            if(!isset($request->my_order))
                $order_date = "<span>".format_datetime($order->created_at)."<br>by ".$order->user_nickname."</span>";
            else
                $order_date = "<span>".format_datetime($order->created_at)."</span>";

            //GET CUSTOMER INFORMATION
            $customer = "<span>Customer: <b>".$order->customer_firstname. " " .$order->customer_lastname."</b></span><br><span>Business Phone: <b>".$order->getPlace->place_phone."</b></span><br><span>Email: <b>".$order->getPlace->place_email."</b></span>";

            $my_order_arr[] = [
                'id' => $order->id,
                'order_date' => $order_date,
                'customer' => $customer,
                'service' => $service_name,
                'subtotal' => $order->csb_amount,
                'discount' => $order->csb_amount_deal,
                'total_charge' => $order->csb_charge,
                'status_calling' => $order->csb_status_call,
                'user_call' => $order->csb_user_call
            ];
        }
        return  DataTables::of($my_order_arr)
            ->editColumn('total_charge',function($row){
                return '<span class="text-danger"><b>'.$row['total_charge'].'</b></span>';
            })
            ->addColumn('action',function ($row){
                if($row['status_calling'] == 1 || $row['status_calling'] == null)
                    return '<a class="btn btn-sm call-btn text-danger" id="'.$row['id'].'" title="Payment Order"><i class="fas fa-phone"></i></a>';
                elseif($row['status_calling'] == 2){
                    if($row['user_call'] == Auth::user()->user_id)
                        return '<a class="btn btn-sm check-btn text-danger" id="'.$row['id'].'" title="Payment Order"><i class="fas fa-check"></i></a>';
                    else
                        return '<img src="'.asset('images/calling.png').'" style="width:50px;height:50px" alt="">';
                }
            })
            ->rawColumns(['service','information','customer','order_date','action','total_charge'])
            ->make(true);
    }
    public function orderCalling(Request $request){
        $order_id = $request->order_id;

        if(!$order_id)
            return response(['status'=>'error','message'=>'Failed...!']);
        $update_order = MainComboServiceBought::find($order_id)->update(['csb_status_call'=>2,'csb_user_call'=>Auth::user()->user_id]); //Calling

        if(!isset($update_order))
            return response(['status'=>'error','message'=>'Failed!']);

        return response(['status'=>'success','message'=>'Successfully!']);
    }
    public function finishCall(Request $request){
        $order_id = $request->order_id;
        if(!$order_id)
            return response(['status'=>'error','message'=>'Failed!']);

        $update_order = MainComboServiceBought::find($order_id)
        ->update([
            'csb_status_call'=>3,
            'csb_user_call'=>Auth::user()->user_id,
            'csb_last_call' => now()
        ]);

        if(!isset($update_order))
            return response(['status'=>'error','message'=>'Failed!']);

        return response(['status'=>'success','message'=>'Successfully!']); 
    }
}

