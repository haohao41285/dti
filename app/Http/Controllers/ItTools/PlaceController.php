<?php

namespace App\Http\Controllers\ItTools;

use App\Models\MainComboServiceBought;
use App\Models\MainCustomerService;
use App\Models\MainTask;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\PosPlace;
use App\Models\PosUser;
use DataTables;
use App\Helpers\GeneralHelper;
use App\Helpers\RunShFileHelper;
use Validator;
use App\Models\MainTheme;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use App\Models\PosWebsiteProperty;
use App\Models\PosTemplate;
use App\Models\PosTemplateType;
use DB;
use Gate;
use Auth;
use App\Helpers\ImagesHelper;
use App\Models\PosCateservice;
use App\Models\PosService;
use Session;
use App\Models\PosWebSeo;
use App\Models\PosMenu;
use App\Models\PosBanner;
use GuzzleHttp\Client;
use Carbon\Carbon;




class PlaceController extends Controller
{
    private $place;

    public function __construct(){
        $this->place = new PosPlace;
    }

    public function index(){

        if(Gate::allows('permission','place-admin')
            || Gate::allows('permission','place-staff')
        ){
            $data['templateType'] = PosTemplateType::getAll();
            return view('tools.place',$data);
        }else
            return doNotPermission();


    }
    public function cloneUpdateWebsite(Request $request)
    {
        $place_id = $request->place_id;

        //Check menu exit
        $menus = PosMenu::where([['menu_place_id',$place_id],['menu_status',1]])->count();
        $banners = PosBanner::where([['ba_place_id',$place_id],['ba_status',1]])->count();

        if($menus > 0 || $banners > 0){
            return response(['status'=>0,'msg'=>'Failed! Menu, Banner existed!']);
        }else{
            DB::beginTransaction();
            try{
                    //Check place_id in pos_place with pos_menu
                $menu_place = DB::table('pos_place')->join('pos_menu',function($join){
                    $join->on('pos_place.place_id','pos_menu.menu_place_id');
                })
                ->where('pos_place.place_ip_license',$request->theme_license)
                ->where('pos_menu.menu_status',1)
                ->select('pos_place.*','pos_menu.*')
                ->get();

                if($menu_place->count() > 0 ){
                    $menu_arr = [];
                    $stt = 1;
                    foreach($menu_place as $key => $menu){
                        $menu_arr[] = [
                            'menu_id' => $stt,
                            'menu_place_id' => $place_id,
                            'menu_parent_id' => $menu->menu_parent_id,
                            'menu_name' => $menu->menu_name,
                            'menu_index' => $menu->menu_index,
                            'menu_url' => $menu->menu_url,
                            'menu_descript' => $menu->menu_descript,
                            'menu_image' => $menu->menu_image,
                            'menu_list_image' => $menu->menu_list_image,
                            'menu_type' => $menu->menu_type,
                            'created_by' => Auth::user()->user_id,
                            'updated_by' => Auth::user()->user_id,
                            'menu_status' => 1,
                            'enable_status' => 1,
                        ];
                        $stt++;
                    }
                    PosMenu::insert($menu_arr);
                }
                //Check place_id in pos_place with pos_banner
                $banner_place = DB::table('pos_place')->join('pos_banner',function($join){
                    $join->on('pos_place.place_id','pos_banner.ba_place_id');
                })
                ->where('pos_place.place_ip_license',$request->theme_license)
                ->where('pos_banner.ba_status',1)
                ->get();

                if($banner_place->count() > 0){
                     $banner_arr = [];
                     $stt_ba = 1;
                    foreach($banner_place as $banner){

                        $banner_arr[] = [
                            'ba_id' => $stt_ba,
                            'ba_place_id' => $place_id,
                            'ba_name' => $banner->ba_name,
                            'ba_index' => $banner->ba_index,
                            'ba_descript' => $banner->ba_descript,
                            'ba_image' => $banner->ba_image,
                            'created_by' => Auth::user()->user_id,
                            'updated_by' => Auth::user()->user_id,
                            'ba_status' => 1,
                            'ba_style'  => $banner->ba_style,
                        ];
                        $stt_ba++;
                    }
                }
                $place = PosPlace::getPlaceIdByLicense($request->get_license);

                $place->place_theme_code = $request->get_code;
                $place->place_theme_property = $request->id_properties ?? NULL;
                $place->save();

                $placeId = $place->place_id;

                PosWebsiteProperty::cloneUpdate($request->id_properties,$placeId);
                DB::commit();
                return response(['status'=>1,'msg'=>"Clone website successfully!"]);

            }catch(\Exception $e){
                DB::rollBack();
                \Log::info($e);
                return response(['status'=>0,'msg'=>"Failed! Clone website failed!"]);
            }
        }
    }


    public function getPlacesDatatable(){

        $user_id = Auth::user()->user_id;

        $places = PosPlace::select('place_website','place_status','place_id','place_name','place_address','place_email','place_phone','place_ip_license','created_at','place_theme_code','place_theme_property');
        if(Gate::allows('permission','place-admin')){}
        elseif(Gate::allows('permission','place-staff')){
            $place_id_arr = MainTask::where(function($query) use ($user_id) {
                $query->where('assign_to',$user_id)
                    ->orWhere('created_by',$user_id)
                    ->orWhere('updated_by',$user_id);
            })
                ->where('status','!=',3)
                ->select('place_id')
                ->get()->toArray();

            $place_id_arr = array_values($place_id_arr);

            $places = $places->whereIn('place_id',$place_id_arr);
        }

        return DataTables::of($places)
        ->editColumn('action',function($places){
            return '<a class="btn btn-sm btn-secondary view" data-id="'.$places->place_id.'" href="#" data-toggle="tooltip" title="View users"><i class="fas fa-user-cog"></i></a>
            <a class="btn btn-sm btn-secondary detail" data-id="'.$places->place_id.'" href="#" data-toggle="tooltip" title="Detail"><i class="fas fa-eye"></i></a>
            <a class="btn btn-sm btn-secondary setting" data-theme-code="'.$places->place_theme_code.'" data-theme-property="'.$places->place_theme_property.'" data-id="'.$places->place_id.'" data-license="'.$places->place_ip_license.'" href="#" data-toggle="tooltip" title="Setting place theme"><i class="fas fa-cogs"></i></a>
            <a class="btn btn-sm btn-secondary btn-custom-properties" data-id="'.$places->place_id.'" href="#" data-toggle="tooltip" title="Custom properties"><i class="fas fa-project-diagram"></i></a>
            <a class="btn btn-sm btn-secondary btn-auto-coupon" data-id="'.$places->place_id.'" href="#" data-toggle="tooltip" title="Auto coupon"><i class="fas fa-images"></i></a>
            <a class="btn btn-sm btn-secondary extension-service" place_id="'.$places->place_id.'" href="javascript:void(0)" title="Extension for Place"><i class="fas fa-shopping-cart"></i></a>
            <a class="btn btn-sm btn-secondary" place_id="'.$places->place_id.'" href="'.route('place.webbuilder',$places->place_id).'" title="Webbuider"><i class="fas fa-edit"></i></a>';
        })
        ->editColumn('place_status',function($row){
            if($row->place_status == 1) $checked='checked';
            else $checked="";
                return '<input type="checkbox" place_id="'.$row->place_id.'" place_status="'.$row->place_status.'" class="js-switch"'.$checked.'/>';
        })
        ->editColumn('created_at',function($places){
            return format_datetime($places->created_at);
        })
        ->rawColumns(['action','place_status'])
        ->make(true);
    }
    /**
     * get customer datatable by placeId
     * @param  $request->placeId
     * @return
     */
    public function getUsersDatatable(Request $request){
        $customers = PosUser::select('user_id','user_nickname','user_phone','user_email','created_at','user_lock_status')
                        ->where('user_status',1)
                        ->where('enable_status',1)
                        ->where('user_place_id',$request->placeId)
                        ->get();

        return DataTables::of($customers)
        ->editColumn('created_at',function($customers){
            return format_datetime($customers->created_at);
        })
        ->editColumn('user_lock_status',function($row){
            if($row->user_lock_status == 1) $checked='checked';
            else $checked="";
                return '<input type="checkbox" user_id="'.$row->user_id.'" user_lock_status="'.$row->user_lock_status.'" class="js-switch-user"'.$checked.'/>';
        })
        ->rawColumns(['user_lock_status'])
        ->make(true);
    }
    /**
     * update new password a user of places
     * @param  $request->placeId
     * @param  $request->userId
     * @param  $request->newPassword
     * @param  $request->confirmPassword
     * @return json
     */
    public function changeNewPassword(Request $request){
        $validate = Validator::make($request->all(),[
            'newPassword' => 'required',
        ]);
        $errorArr = [];
        if($validate->fails()){
            foreach ($validate->messages()->getMessages() as $messages) {
                $errorArr[] = $messages;
            }
            return response()->json(['status'=>0,'msg'=>$errorArr]);
        }
        DB::beginTransaction();
        $user_info = PosUser::where('user_id',$request->userId)
                        ->where('user_place_id',$request->placeId)->first();

        $user = $user_info->update([
                            'user_password' => bcrypt($request->newPassword),
                        ]);
        //SEND SMS
        $receiver_total[] = [
            'name' => $user_info->user_nickname??"quy khach",
            'phone' => $user_info->user_phone,
        ];
        if(!empty($receiver_total)){
            $date = now()->format('Y_m_d_His');

            $file_name = "receiver_sms_list_".$date;

            \Excel::create($file_name,function($excel) use ($receiver_total){

                $excel ->sheet('receiver_list_send_birthday', function ($sheet) use ($receiver_total)
                {
                    $sheet->cell('A1', function($cell) {$cell->setValue('phone');   });
                    $sheet->cell('B1', function($cell) {$cell->setValue('{p2}');   });
                    // $sheet->cell('C1', function($cell) {$cell->setValue('{p3}');   });

                    if (!empty($receiver_total)) {
                        foreach ($receiver_total as $key => $value) {
                            $i= $key+2;
                            if($value['phone'] != ""){
                                $sheet->cell('A'.$i, $value['phone']);
                                $sheet->cell('B'.$i, $value['name']);
                                // $sheet->cell('C'.$i, Carbon::parse($value['birthday'])->format('d/m/Y'));
                            }
                        }
                    }
                });
            })->store('xlsx', false, true);

            $file_url = storage_path('exports/'.$file_name.".xlsx");

            $sms_content_template = "Gui tu Dataeglobal! Mat khau dang nhap moi cua quy khach: ".$request->newPassword;

            $url_event = 'pushsms';

            $url = env('SMS_API_URL').$url_event;

            $client = new Client([
            ]);

            $sms_content_template = str_replace("{phone}","{p1}",$sms_content_template);
            $sms_content_template = str_replace("{name}","{p2}",$sms_content_template);
            // $sms_content_template = str_replace("{birthday}","{p3}",$sms_content_template);

            $date_time_send = format_date_d_m_y(now())." 00:00:00";
            $date_time_end =  format_date_d_m_y(now())." 23:59:59";

            $response = $client->request('POST', $url ,[
                'multipart' => [
                    [
                        'name' => 'content',
                        'contents' => $sms_content_template,
                    ],
                    [
                        'name' => 'title',
                        'contents' => 'reset password',
                    ],
                    [
                        'name' => 'merchant_id',
                        'contents' => 1,
                    ],
                    [
                        'name' => 'start',
                        'contents' => $date_time_send,
                    ],
                    [
                        'name' => 'date_before',
                        'contents' => '0',
                    ],
                    [
                        'name' => 'repeat',
                        'contents' => '0',
                    ],
                    [
                        'name' => 'repeat_on',
                        'contents' => '0',
                    ],
                    [
                        'name' => 'timesend',
                        'contents' => Carbon::parse(now())->addMinute(2)->format('H:i'),
                    ],
                    [
                        'name' => 'type_event',
                        'contents' => 1,
                    ],
                    [
                        'name' => 'event_id',
                        'contents' => 1,
                    ],
                    [
                        'name' => 'end',
                        'contents' => $date_time_end,
                    ],
                    [
                        'name'     => 'upfile',
                        'contents' => fopen($file_url,'r'),
                    ],
                    [
                        'name' => 'status',
                        'contents' => 1,
                    ]

                ],
                'headers' => [
                    'Authorization' => 'Bearer ' .env("SMS_API_KEY"),
                ],
            ]);

        $resp =  (string)$response->getBody();
        $send_sms_status = json_decode($resp)->status;
        $message = 'Successfully'.' '.json_decode($resp)->messages;

        if(!$user || $send_sms_status = 0){
            DB::rollBack();
            return response(['status'=>0,'msg'=>'Failed']);
        }
        else{
            DB::commit();
            return response()->json(['status'=>1,'msg'=>"Changed successfully!"]);
        }
    }
}
    /**
     * get detail place by placeId
     * @param  $request->placeId
     * @return json
     */
    public function getDetailPlace(Request $request){
        $place = PosPlace::select(
                                    'place_name','place_address','place_taxcode',
                                    'place_email','place_amount','place_description',
                                    'place_actiondate','place_logo','place_favicon',
                                    'hide_service_price','place_worker_mark_bonus',
                                    'place_interest','place_website','place_phone','place_url_plugin',
                                    'place_map_direction',
                                    'place_latlng',
                                    'booking_v2'
                                )
                ->where('place_id',$request->placeId)
                ->where('place_status',1)
                ->first();
        $place_actiondate = json_decode($place->place_actiondate,true); //dd(format_time12h($place_actiondate['mon']['start']));

        $arrActiondate = [
            'mon' => [
                'start' => format_time12h($place_actiondate['mon']['start']),
                'end' => format_time12h($place_actiondate['mon']['end']),
                'closed' => $place_actiondate['mon']['closed'],
            ],
            'tue' => [
                'start' => format_time12h($place_actiondate['tue']['start']),
                'end' => format_time12h($place_actiondate['tue']['end']),
                'closed' => $place_actiondate['tue']['closed'],
            ],
            'wed' => [
                'start' => format_time12h($place_actiondate['wed']['start']),
                'end' => format_time12h($place_actiondate['wed']['end']),
                'closed' => $place_actiondate['wed']['closed'],
            ],
            'thur' => [
                'start' => format_time12h($place_actiondate['thur']['start']),
                'end' => format_time12h($place_actiondate['thur']['end']),
                'closed' => $place_actiondate['thur']['closed'],
            ],
            'fri' => [
                'start' => format_time12h($place_actiondate['fri']['start']),
                'end' => format_time12h($place_actiondate['fri']['end']),
                'closed' => $place_actiondate['fri']['closed'],
            ],
            'sat' => [
                'start' => format_time12h($place_actiondate['sat']['start']),
                'end' => format_time12h($place_actiondate['sat']['end']),
                'closed' => $place_actiondate['sat']['closed'],
            ],
            'sun' => [
                'start' => format_time12h($place_actiondate['sun']['start']),
                'end' => format_time12h($place_actiondate['sun']['end']),
                'closed' => $place_actiondate['sun']['closed'],
            ],
        ];


        $result = [
            'place' =>$place,
            'place_actiondate' => $arrActiondate,
        ];

        return response()->json(['status'=>1,'data'=>$result]);
    }

    public function getThemeDatatable(){
        return MainTheme::getDatatable();
    }

    public function getThemeProperties(Request $request){
        if($request->themeId){
            $properties = MainThemeProperties::getThemePropertiesByThemeId($request->themeId);

            return response()->json(['status'=>1,'data'=>$properties]);
        }
    }
    public function getServicePlace(Request $request){
        $place_id = $request->place_id;
        $service_combo_list = MainCustomerService::active()
            ->with('getComboService')
            ->where('cs_place_id',$place_id)
            ->get();
        if(!isset($service_combo_list))
            return response(['status'=>'error','message'=>'Get Service Error']);
        else
            return response(['status'=>'success','service_combo_list'=>$service_combo_list]);
    }
    public function saveExpireDate(Request $request){

        $cs_id = $request->cs_id;
        $expire_date = $request->expire_date;
        $count_1 = 0;
        $count_2 = 0;

        DB::beginTransaction();

        foreach ($expire_date as $key => $date){
            if($date == null){}
            else{
                $count_1++;
                $update_cs = MainCustomerService::where('cs_place_id',$request->place_id)
                    ->where('cs_service_id',$cs_id[$key])
                    ->update(['cs_date_expire'=>format_date_db($date)]);
                if(isset($update_cs))
                    $count_2++;
            }
        }
        if($count_1 != $count_2){
            DB::callback();
            return response(['status'=>'error','message'=>'Failed!']);
        }
        else{
            DB::commit();
            return response(['status'=>'success','message'=>'Successfully!']);
        }

    }

    public function getWpDatableByPlaceId(Request $request){
        return PosWebsiteProperty::getDatatableByPlaceId($request->placeId);
    }

    public function deleteValueProperty(Request $request){
        if($request->id){
            PosWebsiteProperty::deleteByIdAndPlaceId($request->id,$request->placeId);
            return response()->json(['status'=>1,'msg'=>"Deleted successfully!"]);
        }
    }

    public function saveCustomValueProperty(Request $request){
       PosWebsiteProperty::saveValue($request->variable,$request->name,$request->value,$request->image,$request->action,$request->placeId);
       return response()->json(['status'=> 1,"msg"=>"Saved successfully"]);
    }

    public function getAutoTemplateDatatableDatatable(Request $request){
        return PosTemplate::getDatatableByPlaceId($request->placeId,$request->type);
    }

    public function saveAutoTemplate(Request $request){
        $cateservices = null;
        foreach ($request->cateservices as $key => $value) {
            $cateservices .= $value.";";
        }

        $template = PosTemplate::saveAuto(
            $request->id,  
            $request->placeId, 
            $request->title, 
            $request->discount, 
            $request->discountType, 
            $request->image, 
            $cateservices, 
            $request->templateType,
            $request->templateTableType
        );

        return response()->json(['status'=>1,'msg'=>"saved successfully"]);
    }

    // public function deleteAutoCoupon(Request $request){
    //     if($request->id){
    //         PosTemplate::deleteByIdAndPlaceId($request->id, $request->placeId);

    //         return response()->json(['status'=>1,'msg'=>"deleted successfully"]);
    //     }
    // }

    public function getAutoTemplateById(Request $request){
        if($request->id){
            $template = PosTemplate::getByPlaceIdAndId($request->id, $request->placeId);
            // dd($template);
            return response()->json(['status'=>1,'data'=>$template]);
        }
    }
    public function changePlaceStatus(Request $request){

        $place_id = $request->place_id;
        $place_status = $request->place_status;

        if($place_status == 1)
            $status = 0;
        else
            $status = 1;

        $place_update = PosPlace::where('place_id',$place_id)->update(['place_status'=>$status]);

        if(!isset($place_update))
            return response(['status'=>'error','message'=>'Failed! Change Status Failed!']);

        return response(['status'=>'success','message'=>'Successfully! Change Status Successfully!']);
    }
    public function placeWebbuilder($place_id){

        $data['webSeo'] = PosWebSeo::select('web_seo_descript','web_seo_meta')
                            ->where('web_seo_place_id',$place_id)
                            ->first();

        $data['place_id'] = $place_id;
        $place_ip_license = PosPlace::where('place_id',$place_id)->first()->place_ip_license;
        Session::put('place_id',$place_id);
        Session::put('place_ip_license',$place_ip_license);
        $data['cateservices'] = DB::table('pos_cateservice')->where([['cateservice_place_id',$place_id],['cateservice_status',1]])->get();
        return view('tools.webbuilder',$data);
    }

    public function saveDetail(Request $request){
        //dd($request->all());
        $placeId = $request->placeId;

        $place = $this->place->getLicenseByPlaceId($placeId);

        $license = $place->place_ip_license;

        $pathUpload = $license.'/logo/photos';

        if($request->hasFile('logo')){
            $logo = ImagesHelper::uploadImageToAPI($request->logo,$pathUpload);
        }

        if($request->hasFile('favicon')){
            $favicon = ImagesHelper::uploadImageToAPI($request->favicon,$pathUpload);
        }

        $arrActiondate = [
            'mon' => [
                'start' => format_time24h($request->mon_start),
                'end' => format_time24h($request->mon_end),
                'closed' => $request->work_mon == 0 ? true : false,
            ],
            'tue' => [
                'start' => format_time24h($request->tue_start),
                'end' => format_time24h($request->tue_end),
                'closed' => $request->work_tue == 0 ? true : false,
            ],
            'wed' => [
                'start' => format_time24h($request->wed_start),
                'end' => format_time24h($request->wed_end),
                'closed' => $request->work_wed == 0 ? true : false,
            ],
            'thur' => [
                'start' => format_time24h($request->thu_start),
                'end' => format_time24h($request->thu_end),
                'closed' => $request->work_thu == 0 ? true : false,
            ],
            'fri' => [
                'start' => format_time24h($request->fri_start),
                'end' => format_time24h($request->fri_end),
                'closed' => $request->work_fri == 0 ? true : false,
            ],
            'sat' => [
                'start' => format_time24h($request->sat_start),
                'end' => format_time24h($request->sat_end),
                'closed' => $request->work_sat == 0 ? true : false,
            ],
            'sun' => [
                'start' => format_time24h($request->sun_start),
                'end' => format_time24h($request->sun_end),
                'closed' => $request->work_sun == 0 ? true : false,
            ],
        ];

        $arr = [
              'place_name' => $request->business_name,
              'place_phone' => $request->business_phone,
              'hide_service_price' => $request->hide_service_price,
              'booking_v2' => $request->bookingv2,
              'place_address' => $request->address,
              'place_email' => $request->email,
              'place_website' => $request->website,
              'place_interest' => $request->interest,
              'place_description' => $request->description,
              'place_logo' => $logo ?? null,
              'place_favicon' => $favicon ?? null,
              'place_taxcode' => $request->tax_code,
              'place_worker_mark_bonus' => $request->price_floor,
              'place_url_plugin' => $request->place_url_plugin,
              'place_map_direction' => $request->place_map_direction,
              'place_actiondate' => json_encode($arrActiondate),
              'place_latlng' => $request->latlng,
        ];

        if(!isset($logo)){
            unset($arr['place_logo']);
        }

        if(!isset($favicon)){
            unset($arr['place_favicon']);
        }

        $this->place->updateByPlaceIdAndArr($placeId, $arr);

        return response()->json(['status'=>1,'data'=>['msg'=>'saved successfully']]);
    }


    public function lockUser(Request $request){

        $user_id = $request->user_id;
        $user_status = $request->user_status;

        if($user_status == 1)
            $status = 0;
        else
            $status = 1;

        $update = PosUser::where('user_id',$user_id)->where('user_place_id',$request->placeId)->update([
            'user_lock_status' => $status,
            'user_wrong_password_number' => 0
        ]);

        if(!isset($update))
            return response(['status'=>'error','message'=>'Failed! Change Status Failed!']);

        return response(['status'=>'success','message'=>'Successfully! Change Status Successfully!']);
    }

}

