<?php

namespace App\Http\Controllers\Webbuilder;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PosCateservice;
use yajra\Datatables\Datatables;
use App\Models\PosPlace;
use Validator;

class CateServiceController extends Controller
{
    public function index(Request $request){

        $place_id = $request->place_id;

    	$cateservice_item = PosCateservice::leftJoin('main_user',function($join){
            $join->on('pos_cateservice.created_by','=','main_user.user_id');
        })
        ->where('pos_cateservice.cateservice_place_id', $place_id)
        ->where('pos_cateservice.cateservice_status',1)
        ->get();

        return Datatables::of($cateservice_item)
            ->editColumn('cateservice_name',function($row) use ($place_id){
                return  "<a href='".route('places.cateservice.edit',[$place_id,$row->cateservice_id])."'>".$row->cateservice_name." </a>";
            })
            ->editColumn('cateservice_description',function($row){
                $result=substr($row->cateservice_description,0,20);
                $dot="";
                if(strlen($row->cateservice_description)>20)
                {
                    $dot="...";
                }
                return $result."".$dot;
            })
            ->editColumn('cateservice_image',function($row){
                if(!empty($row->cateservice_image))
                return  "<img src=".config('app.url_file_view').$row->cateservice_image." width='100px' alt=''>  ";
                else
                    return "";
            })
            ->editColumn('updated_at',function($row){
                return  format_datetime($row->updated_at)." by ".$row->user_nickname; 
            })
             ->addColumn('action', function($row) use ($place_id){
                return '<a href="'.route('places.cateservice.edit',[$place_id,$row->cateservice_id]).'"  class="btn btn-sm btn-secondary"><i class="fa fa-edit"></i></a>
                        <a href="#" class="delete-cateservice btn btn-sm btn-secondary" id="'.$row->cateservice_id.'"><i class="fa fa-trash"></i></a>';
            })
            ->rawColumns(['cateservice_name','cateservice_image' ,'action','cateservice_description'])
            ->make(true);
    }
    public function edit(Request $request,$place_id,$id=0) {
        if($id>0){
            $cateservice_item = PosCateservice::where('cateservice_place_id',$place_id)
                                ->where('cateservice_id',$id)
                                ->first();
            $cateservice_date = format_date($cateservice_item->cateservice_date);
            return view('tools.partials.cateservice_edit',compact('cateservice_item','id','cateservice_date','place_id'));
        } else {
            return view('tools.partials.cateservice_edit',compact('id','place_id'));
        }
    }
    public function save(Request $request)
    {
        // return $request->all();
        $cateservice_id = $request->cateservice_id;
        $cateservice_name = $request->cateservice_name;
        $description = $request->cateservice_description;

        $image_path ="";
        $place_id = $request->place_id;
        
          $rules = [
                'cateservice_name' => 'required',
                'cateservice_image' => 'mimes:jpeg,jpg,png,gif|max:1024', // max 3000kb
                'cateservice_icon_image' => 'mimes:jpeg,svg,jpg,png,gif|max:1024', // max 3000kb
                'cateservice_description' => 'required'
          ];
          $messages = [
            'cateservice_name.required' => "Please enter Full name",
            'cateservice_image.mimes' => 'Uploaded image is not in image format',
            'cateservice_image.max' => 'max size image 1Mb',
            'cateservice_description.required' => 'Please enter Description'
          ];
            $validator = Validator::make($request->all(), $rules, $messages);

        if($description == '<p><br></p>' )
            $description = "";
            
        //GET LICENSE PLACE
        $place_ip_license = PosPlace::where('place_id',$place_id)->first()->place_ip_license;
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }else{
            if($request->hasFile('cateservice_image')){
                //insert image 
                $image_path= \App\Helpers\ImagesHelper::uploadImageWebbuilder($request->file('cateservice_image'),"cateservice",$place_ip_license);

            }else{$image_path = $request->cateservice_image_old; }

            if($request->hasFile('cateservice_icon_image')){
                //insert image 
                $icon_image_path= \App\Helpers\ImagesHelper::uploadImageWebbuilder($request->file('cateservice_icon_image'),"cateservice",$place_ip_license);

            }else{$icon_image_path = $request->cateservice_image_old; }

            $list_cateservice = PosCateservice::where('cateservice_place_id', $place_id)->get();
            if($cateservice_id >0){
                //UPDATE CATESERVICE
                $PosCateservice = PosCateservice::where('cateservice_place_id','=',$place_id)
                            ->where('cateservice_id',$cateservice_id)
                            ->update(['cateservice_name'=>$request->cateservice_name ,
                                    'cateservice_index'=>$request->cateservice_index?$request->cateservice_index:0,
                                    'cateservice_image'=>$image_path,
                                    'cateservice_icon_image'=>$icon_image_path,
                                    'cateservice_description'=>$description,
                                ]);
                if($PosCateservice){
                    $request->session()->flash('message', 'Edit CateService Success!');
                }else{
                    $request->session()->flash('error', 'Edit CateService Error!');
                }   
                // return view('webbuilder.cateservices',compact('list_cateservice'));
                return redirect()->route('place.webbuilder',$place_id);
            }else{
                //CREATE CATESERVICE
                $idCateService = PosCateservice::where('cateservice_place_id','=',$place_id)->max('cateservice_id') +1;
                $PosCateservice = new PosCateservice ;
                                $PosCateservice->cateservice_id = $idCateService;
                                $PosCateservice->cateservice_place_id = $place_id;
                                $PosCateservice->cateservice_name = $request->cateservice_name;
                                $PosCateservice->cateservice_index = $request->cateservice_index?$request->cateservice_index:0;
                                $PosCateservice->cateservice_image = $image_path;
                                $PosCateservice->cateservice_icon_image = $icon_image_path;
                                $PosCateservice->cateservice_description = $description;
                                $PosCateservice->cateservice_status = 1;
                                $PosCateservice->save();
                    if($PosCateservice){
                                $request->session()->flash('message', 'Insert CateService Success!');
                    } else {
                                $request->session()->flash('error', 'Insert CateService Error!');
                    }
                    
                    // return view('webbuilder.cateservices',compact('list_cateservice'));
                    return redirect()->route('place.webbuilder',$place_id);
            }
        }          
    }
    public function delete(Request $request)
    {
        $cateservice = PosCateservice::where('cateservice_place_id',$request->place_id)
                                        ->where('cateservice_id',$request->id)
                                        ->update([ 'cateservice_status'=> 0 ]);

        if(!isset($cateservice))
            return response(['status'=>'error','message'=>"Failed! Delete cateservice Failed!"]);

        return response(['status'=>'success','message'=>"Successfully! Delete cateservice Successfully!"]);
            
    }  
}
