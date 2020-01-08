<?php

namespace App\Http\Controllers\WebBuilder;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PosCateservice;
use App\Models\PosService;
use App\Models\PosServiceWebsite;
use yajra\Datatables\Datatables;
use App\Models\PosPlace;
use App\Helpers\ImagesHelper;
use Validator;
use Session;
use App\Models\PosMenu;

class ServiceController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
     public function index(Request $request){

        $place_id  = Session::get('place_id');
        $service_cate = $request->search_service_cate;
        $service_status = $request->search_service_status;
        $service_booking = $request->search_service_booking;

        $servicelist = PosService::join("pos_cateservice",function($join){

            $join->on("pos_service.service_cate_id","=","pos_cateservice.cateservice_id")
                ->on("pos_service.service_place_id","=","pos_cateservice.cateservice_place_id");
            })
        ->leftjoin("main_user",function($join1){

            $join1->on("pos_service.updated_by","=","main_user.user_id");
            })
            ->where('pos_service.service_place_id',$place_id)
            ->where('pos_service.service_status',1);

        if($service_cate>0){

           $servicelist->where('pos_service.service_cate_id',$service_cate);
        }
        if($service_status!=""){

            $servicelist->where('pos_service.enable_status',$service_status);  
        }
        if($service_booking!=""){

            $servicelist->where('pos_service.booking_online_status',$service_booking);
        }

        $servicelist->select('pos_service.*' ,'pos_cateservice.cateservice_name','main_user.user_nickname')
            ->get();

        return Datatables::of($servicelist)

            ->editColumn('service_id', function ($row) 
            {
                return '<div class="custom-control custom-checkbox mb-3">
                    <input type="checkbox" class="custom-control-input delete" value="'.$row->service_id.'" id="a'.$row->service_id.'" name="delete[]">
                    <label class="custom-control-label" for="a'.$row->service_id.'">'.$row->service_id.'</label>
                  </div>';
            })
            ->editColumn('service_name', function ($row) use ($place_id)
            {
                return '<a href="'.route('places.service.edit',[$place_id,$row->service_id]).'" >'.$row->service_name.'</a>';
            })
            ->addColumn('action1', function($row){
                $checked="";
                if($row->enable_status == 1){
                    $checked= "checked";
                }
                return '<div class="custom-control custom-switch">
                          <input type="checkbox" class="custom-control-input" id="enable_status_'.$row->service_id.'"  name="service_enable_status" value="'.$row->service_id.'" status="'.$row->enable_status.'" '.$checked.'>
                          <label class="custom-control-label" for="enable_status_'.$row->service_id.'"></label>
                        </div>';
            })
            ->addColumn('action2', function($row){
                $checked1="";
                if($row->booking_online_status==1){
                    $checked1= "checked";
                }
                return '<div class="custom-control custom-switch">
                          <input type="checkbox" class="custom-control-input" id="booking_online_status'.$row->service_id.'"  name="booking_online_status" value="'.$row->service_id.'" status="'.$row->booking_online_status.'" '.$checked1.'>
                          <label class="custom-control-label" for="booking_online_status'.$row->service_id.'"></label>
                        </div>';
            })
            ->editColumn('updated_at', function ($row) 
            {
                return format_datetime($row->updated_at)." by ".$row->user_nickname;
            })
            ->addColumn('action', function($row) use ($place_id){
                return " <a href='".route('places.service.edit',[$place_id,$row->service_id])."' class='edit-service btn btn-sm btn-secondary delete' ><i class='fa fa-edit'></i> </a>
                    <a href='javascript:void(0)' class='btn btn-sm btn-secondary delete-service' id='".$row->service_id."' data-type='user'><i class='fa fa-trash'></i></a>" ;
            })
            ->rawColumns(['service_id' , 'service_name', 'action1','action','action2','delete'])
            ->make(true);
    }
    //DELETE SERVICE
    public function delete(Request $request)
    {
        $service_update = PosService::where('service_place_id',Session::get('place_id'))
                      ->whereIn('service_id',$request->param_id)
                      ->update(['service_status' => 0]);

        if(!isset($service_update))
            return response(['status'=>'error','message'=>'Failed! Delete Service Failed!']);

        return response(['status'=>'success','message'=>'Successfully! Delete Service Successfully!']);
    }

    public function edit($place_id,$id = 0) {
        $list_services = PosCateservice::where('cateservice_place_id',$place_id)->where('cateservice_status',1)->get();
        if($id > 0)
            {
                $service_item = PosService::where('service_place_id',$place_id)
                                            ->where('service_id',$id)
                                            ->first();
                return view('tools.partials.service_edit',compact('list_services','service_item','id','place_id'));
            }else
            {
                return view('tools.partials.service_edit',compact('list_services','id','place_id'));
            }
    }
    //END GET EDIT SERVICE
    public function save(Request $request)
    {
        // return $request->all();
        $place_id = Session::get('place_id');

        $list_service_cates = PosCateservice::where('cateservice_place_id',$place_id)->get();

        //dd($list_service_cates);

        $service_id = $request->id;

        if($request->booking_online_status=="on")

            $booking_online_status = 1;

        else $booking_online_status = 0;

        $check_exist = PosService::where('service_place_id',$place_id)
                                       ->where('service_id',$service_id)
                                       // ->where('service_name',$request->service_name)
                                       ->count();
        //dd($check_exist);
        $rule = [
            'service_cate_id' =>'required',
            'service_name' =>'required',
            'service_price' =>'required|numeric',
            /*'service_price_repair' =>'required|numeric',*/
            'service_duration' =>'required|numeric',
            'service_price_extra' =>'required|numeric',
        ];

        $message = [
            'service_cate_id.required' => 'Please enter Cate Service',
            'service_name.required' => 'Please enter Name Service',
            'service_price.required' => 'Please enter Price Service',
            'service_price.numeric' => 'Please enter number',
            /*'service_price_repair.numeric' => 'Please enter number',*/
            'service_duration.numeric' => 'Please enter number',
            'service_price_extra.numeric' => 'Please enter number',
            /*'service_price_repair.required' => 'Please enter Price Repair Service',*/
            'service_duration.required' => 'Please enter Cate Duration Service',
            'service_price_extra.required' => 'Please enter Cate Price Extra',
        ];

        $validator = Validator::make($request->all(),$rule,$message);

        if($validator->fails()){
            return back()->withErrors($validator)->withInput();
        }
        else
        {

            if($check_exist == 0 )
            {
                $idService = PosService::where('service_place_id',$place_id)->max('service_id')+1;
            }else
            {
                $idService = $service_id;
            }

            // $place_ip_license = PosPlace::where('place_id',$place_id)->first()->place_ip_license;

            if(isset($request->service_image))
            {
                $service_image = ImagesHelper::uploadImageWebbuilder($request->service_image,'service',Session::get('place_ip_license'));
            }else
            {
                $service_image = $request->service_image_hidden;
            }
            $image_list = "";
                    if($request->multi_image && !$request->multi_image_add)
                    {
                        $image_list = $request->multi_image;
                    }
                    if($request->multi_image_add && !$request->multi_image)
                    {
                        $image_list = implode(";",$request->multi_image_add);
                    }
                    if($request->multi_image_add  && $request->multi_image)
                    {
                        $image_list = $request->multi_image.";".implode(";",$request->multi_image_add);
                    }


            $arr = [
                    'service_id' => $idService,
                    'service_place_id' => $place_id,
                    'service_cate_id'=>$request->service_cate_id,
                    /*'service_tag'=>$request->service_tag,*/
                    'service_name'=>$request->service_name,
                    'service_index'=>$request->service_index,
                    /*'service_short_name'=>$request->service_short_name,*/
                    'service_duration'=>$request->service_duration,
                    'service_price'=>$request->service_price?$request->service_price:0,
                    'service_price_extra'=>$request->service_price_extra,
                    /*'service_price_repair'=>$request->service_price_repair,
                    'service_price_hold'=>$request->service_price_hold,*/
                    'service_updown'=>$request->service_updown,
                    'service_image'=>$service_image,
                    // 'service_description'=>$request->service_description,
                    'service_descript_website'=>$request->service_description,
                    'service_status'=>1,
                    'booking_online_status'=>$booking_online_status,
                    'service_turn'=>0,
                    'service_tax'=>$request->service_tax,
                    'service_list_image'=>$image_list,
                ];
                //dd($arr['service_list_image']);

            if($check_exist == 0)
            {
                
                $service_list = PosService::create($arr);
                if($service_list)

                    $request->session()->flash('message','Insert Service Success');
                else
                    $request->session()->flash('error','Insert Insert Error');
            }elseif($check_exist ==1)
            {

                $service_list = PosService::where('service_place_id',$place_id)
                                            ->where('service_id',$service_id)
                                            ->update($arr);
                if($service_list)

                    $request->session()->flash('message','Edit Service Success');
                else
                    $request->session()->flash('error','Edit Insert Error');
            }
            // return view('webbuilder.services',compact('list_service_cates'));
            return redirect()->route("place.webbuilder",$place_id);
        }
    }

    public function changeStatus(Request $request)
    {
        $param_id = $request->param_id;        
        $status = $request->status;
        $type = $request->type;

        $status == 1 ? $status_update = 0 : $status_update = 1;

        if($type == 'service_enable_status')   
        
            $update = PosService::where('service_place_id',Session::get('place_id'))
                    ->where('service_id',$param_id)
                    ->update(['enable_status'=>$status_update]);

        elseif($type == 'booking_online_status')
            $update = PosService::where([
                        ['service_place_id',Session::get('place_id')],
                        ['service_id',$param_id]
                    ])
                    ->update(['booking_online_status'=>$status_update]);
        else
            $update = PosMenu::where([
                    ['menu_place_id',Session::get('place_id')],
                    ['menu_id',$param_id]
                ])
                ->update(['enable_status'=>$status_update]);
                
        if(!isset($update))
            return response(['status'=>'error','message'=>'Failed! Change Status Failed!']);

        return response(['status'=>'success','message'=>'Successfully! Change Status Successfully!']);
    }

    public function import() {
        return view('tools.partials.import_service');
    }

    public function importServices(Request $request)
    {
        // dd($request->all());
        $multi_image_cates=$request->multi_image_cate[0];
        $temp=explode('/',$request->multi_image_cate[0]);
        $pop=array_pop($temp);
        $multi_image_cate=implode('/', $temp)."/";


        
        if($request->hasFile('fileImport')){
            $path = $request->file('fileImport')->getRealPath();
            $begin_row = $request->begin_row;
            $end_row = $request->end_row;
            $update_exist = $request->check_update_exist;
            $update_count = 0;
            $insert_count = 0;

            \DB::beginTransaction();

            try{
                $data = \Excel::load($path)->toArray();
                if(!empty($data)){

                    foreach($data as $key => $value){

                        if( $key >= $begin_row && $key <= $end_row){

                            $check_cateservice = PosCateservice::where('cateservice_place_id',Session::get('place_id'))->where('cateservice_name',$value['cateservice_name'])->count();
                            if($check_cateservice == 0)
                            {
                                $idCateservice = PosCateservice::where('cateservice_place_id',Session::get('place_id'))->max('cateservice_id')+1;

                                $arr = [
                                    'cateservice_id' => $idCateservice,
                                    'cateservice_place_id'=> Session::get('place_id'),
                                    'cateservice_name' =>$value['cateservice_name'],
                                    'cateservice_image'=>$multi_image_cate.$value['cateservice_image'],
                                    'cateservice_index'=>1,
                                    'cateservice_status'=>1
                                ];
                                PosCateservice::create($arr);
                            }
                             else
                            {
                                $cateservice_id=PosCateservice::where('cateservice_place_id',Session::get('place_id'))
                                                                ->where("cateservice_name",$value['cateservice_name'])->first()->cateservice_id;
                                $arr = [
                                    'cateservice_id' => $cateservice_id,
                                    'cateservice_place_id'=> Session::get('place_id'),
                                    'cateservice_name' =>$value['cateservice_name'],
                                    'cateservice_image'=>$multi_image_cate.$value['cateservice_image'],
                                    'cateservice_index'=>1,
                                    'cateservice_status'=>1
                                ];
                                PosCateservice::where('cateservice_place_id',Session::get('place_id'))
                                                                ->where('cateservice_id',$cateservice_id)
                                                                ->update($arr);
                            }
                            // CHECK EXIST SERVICE
                            $check_exist=PosService::join('pos_cateservice',function($join){
                                $join->on('pos_cateservice.cateservice_id','=',"pos_service.service_cate_id")
                                ->where('cateservice_place_id',Session::get('place_id'));
                            })
                            ->where('cateservice_name',$value['cateservice_name'])
                            ->where('service_place_id',Session::get('place_id'))
                            ->where('service_name',$value['service_name'])->count();

                            $cate_service = PosCateservice::where('cateservice_place_id',Session::get('place_id'))
                                                            ->where('cateservice_name',$value['cateservice_name'])->first();
                                                    
                            $cate_services = PosCateservice::where('cateservice_place_id',Session::get('place_id'))
                                                            ->select('cateservice_name')->get();
                            
                            //Nếu chưa tồn tại service thì SERVICE_ID sẽ bằng max(SERVICE_ID)+1
                            if($check_exist ==  0){

                                $idService = PosService::where('service_place_id',Session::get('place_id'))->max('service_id')+1;

                            } else 
                            {
                                $service_id = PosService::where('service_place_id',Session::get('place_id'))
                                                       ->where('service_name',$value['service_name'])->first();
                                                       $idService = $service_id->service_id;
                                                       
                            }
                            if($value['service_image'] != ""){

                                $place_ip_license = PosPlace::where('place_id',Session::get('place_id'))->first()->place_ip_license;

                                $pathImage = '/images/'.$place_ip_license.'/website/service/';

                                $service_image = $pathImage.$value['service_image'];
                            }
                            else $service_image = "";

                            $arr_service = [
                                'service_id' => $idService,
                                'service_place_id' => Session::get('place_id'),
                                'service_cate_id'=>$cate_service->cateservice_id,
                                'service_tag'=>$value['service_tag'],
                                'service_name'=>$value['service_name'],
                                'service_short_name'=>$value['service_short_name'],
                                'service_duration'=>$value['service_duration']?$value['service_duration']:0,
                                'service_price'=>$value['service_price']?$value['service_price']:0,
                                'service_price_extra'=>$value['service_price_extra']?$value['service_price_extra']:0,
                                'service_price_repair'=>$value['service_price_repair']?$value['service_price_repair']:0,
                                'service_updown'=>$value['service_updown']?$value['service_updown']:0,
                                'service_image'=>$service_image,
                                'service_description'=>$value['service_description'],
                                'service_descript_website'=>$value['service_description_website']?$value['service_description_website']:"",
                                'booking_online_status'=>$value['booking_online_status']?$value['booking_online_status']:1,
                                'service_turn'=>$value['service_turn']?$value['service_turn']:0,
                                'service_tax'=>$value['service_tax'],
                                'service_status'=>1


                            ];
                            // dd($check_exist);
                            //Nếu chưa tồn tại thì create, không thì update

                            if($check_exist == 0 ){
                                
                                $a = PosService::create($arr_service);
                                
                                $insert_count++;
                            }else
                            {
                                if($update_exist == "on")
                                {   $service_id = PosService::where('service_place_id',Session::get('place_id'))
                                                       ->where('service_name',$value['service_name'])->first()->service_id;

                                    PosService::where('service_place_id',Session::get('place_id'))
                                                ->where('service_id',$service_id)
                                                ->update($arr_service);
                                    $update_count++;
                                }
                            }
                        }
                    }
                    \DB::commit();
                    $request->session()->flash('success','Import File Success , update:'.$update_count.'row, inserted:'.$insert_count.'row');
                }
                else{
                    $request->session()->flash('error','Import File Not Data');
                }
            } catch(\Exception $e){

                return $e->getMessage();
                $request->session()->flash('error','Import File Error is Error! Please  check import again!');
            }
        }
        else
        {
            $request->session()->flash('error','Please choose file import.');
            
        }
        return back();
    }
    public function export()
    {
        $data = PosService::join('pos_cateservice',function($join){
                           $join->on('pos_service.service_cate_id','pos_cateservice.cateservice_id')
                                 ->on('pos_service.service_place_id','pos_cateservice.cateservice_place_id');
                           })
                           ->join('main_user',function($join_user){
                           $join_user->on('pos_service.updated_by','main_user.user_id')
                                 ->on('pos_service.service_place_id','main_user.user_place_id');
                           })
                           ->where('service_place_id',Session::get('place_id'))
                           ->where('service_status',1)
                           ->select('cateservice_image','cateservice_name','service_tag','service_name','service_short_name','service_duration','service_price','service_price_extra','service_price_repair','service_updown','service_image','service_description','service_descript_website','booking_online_status','service_turn','service_tax','user_nickname')
                           ->orderBy('cateservice_name','asc')
                           ->get()->toArray();
        $date = format_date(now());
        // dd($data);
        return \Excel::create('service_table_'.$date,function($excel) use ($data){

            $excel ->sheet('Service Table', function ($sheet) use ($data)
            {
                $sheet->cell('A1', function($cell) {$cell->setValue('Cateservice Image');   });
                $sheet->cell('B1', function($cell) {$cell->setValue('Cateservice Name');   });
                $sheet->cell('C1', function($cell) {$cell->setValue('Service Tag');   });
                $sheet->cell('D1', function($cell) {$cell->setValue('Service Name');   });
                $sheet->cell('E1', function($cell) {$cell->setValue('Service Short Name');   });
                $sheet->cell('F1', function($cell) {$cell->setValue('Service Duration');   });
                $sheet->cell('G1', function($cell) {$cell->setValue('Service Price');   });
                $sheet->cell('H1', function($cell) {$cell->setValue('Service Price Extra');   });
                $sheet->cell('I1', function($cell) {$cell->setValue('Service Price Repair');   });
                $sheet->cell('J1', function($cell) {$cell->setValue('Service Updown');   });
                $sheet->cell('K1', function($cell) {$cell->setValue('Service Image');   });
                $sheet->cell('L1', function($cell) {$cell->setValue('Service Description');   });
                $sheet->cell('M1', function($cell) {$cell->setValue('Service Description Website');   });
                $sheet->cell('N1', function($cell) {$cell->setValue('Booking Online Status');   });
                $sheet->cell('O1', function($cell) {$cell->setValue('Service Turn');   });
                $sheet->cell('P1', function($cell) {$cell->setValue('Service Tax');   });
                // $sheet->cell('Q1', function($cell) {$cell->setValue('By');   });

                if (!empty($data)) {
                    foreach ($data as $key => $value) {
                        $i=$key+2;
                        $sheet->cell('A'.$i, $value['cateservice_image']); 
                        $sheet->cell('B'.$i, $value['cateservice_name']); 
                        $sheet->cell('C'.$i, $value['service_tag']);
                        $sheet->cell('D'.$i, $value['service_name']);
                        $sheet->cell('E'.$i, $value['service_short_name']);
                        $sheet->cell('F'.$i, $value['service_duration']);
                        $sheet->cell('G'.$i, $value['service_price']);
                        $sheet->cell('H'.$i, $value['service_price_extra']);
                        $sheet->cell('I'.$i, $value['service_price_repair']);
                        $sheet->cell('J'.$i, $value['service_updown']);
                        $sheet->cell('K'.$i, $value['service_image']);
                        $sheet->cell('L'.$i, $value['service_description']);
                        $sheet->cell('M'.$i, $value['service_descript_website']);
                        $sheet->cell('N'.$i, $value['booking_online_status']);
                        $sheet->cell('O'.$i, $value['service_turn']);
                        $sheet->cell('P'.$i, $value['service_tax']); 
                        // $sheet->cell('Q'.$i, $value['user_nickname']); 
                    }
                }
            });
        })->download("xlsx");
    }

    public function uploadImageService(Request $request)
    {
        if ($request->hasFile('file')) {

                $imageFiles = $request->file('file');

                $image_name = [];

                foreach ($request->file('file') as $fileKey => $fileObject ) {

                    if ($fileObject->isValid()) {

                        $image_name[] = ImagesHelper::uploadImageDropZone_get_path($fileObject,'service',Session::get('place_ip_license'));
                    }
                }
                return $image_name;
        }
                return "upload error";
    }
    public function uploadMultiImages(Request $request)
    {
        if ($request->hasFile('file')) {

            $imageFiles = $request->file('file');

            $image_name = [];

            foreach ($request->file('file') as $fileKey => $fileObject ) {

                if ($fileObject->isValid()) {

                    $image_name[] = ImagesHelper::uploadImageDropZone_get_path($fileObject,'service',Session::get('place_ip_license'));
                }
            }
            return $image_name;
        }
            return "upload error";
    }

     public function removeMultiImage(Request $request)
    {
        $service_list_image = PosService::where('service_place_id',Session::get('place_id'))
                 ->where('service_id',$request->service_id)
                 ->first()->service_list_image;

        $service_list_image = str_replace(";",",",$service_list_image);

        $service_list_image = explode(",",$service_list_image);

        foreach (array_keys($service_list_image, $request->src_image) as $key) {
                            unset($service_list_image[$key]);
                        }
        $service_list_image = implode(";", $service_list_image);

        PosService::where('service_place_id',Session::get('place_id'))
                 ->where('service_id',$request->service_id)
                 ->update(['service_list_image'=>$service_list_image]);

        return $service_list_image;
    }

    public function changeOnlineBooking(Request $request){
        $service_id = $request->id;        
        $status = $request->status;        
        
        PosService::where('service_place_id',Session::get('place_id'))
                    ->where('service_id',$service_id)
                    ->update(['booking_online_status'=>$status]);

        return "Change Online Booking Succsess!";
    }
    

}

