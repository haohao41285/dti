<?php

namespace App\Http\Controllers\WebBuilder;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use yajra\Datatables\Datatables;
use App\Models\PosBanner;
use App\Models\PosPlace;
use App\Models\MainThemeProperties;
use Validator;
use Session;
use Auth;

class BannerController extends Controller
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
    public function index(Request $request)
    {
        $ba_item = PosBanner::join('main_user',function($join){
            $join->on('pos_banner.created_by','main_user.user_id');
        })
        ->where('pos_banner.ba_place_id',Session::get('place_id'))
        ->where('ba_status',1)
        ->select('pos_banner.*','main_user.*');

        return Datatables::of($ba_item)
            ->editColumn('ba_name',function($row){
                return "<a href='".route('places.banners.edit',[Session::get('place_id'),$row->ba_id])."'>".$row->ba_name."</a>";
            })
            ->editColumn('ba_image',function($row){
                if(!empty($row->ba_image))
                    return "<img src=".config('app.url_file_view').$row->ba_image." width =100px alt=''>  ";
                else
                    return "";
            })
            ->addColumn('enable_status',function($row){
                $checked= "";
                if ($row->enable_status==1) {
                    $checked = 'checked';
                }
                return "<input type='checkbox' id='".$row->ba_id."' class='js-switch' ".$checked." />";
            })
            ->editColumn('updated_at',function($row){
                return format_datetime($row->updated_at)." by ".$row->user_nickname;
            })
            ->addColumn('action', function($row){
            return '<a href="'.route('places.banners.edit',[Session::get('place_id'),$row->ba_id]).'"  class="btn btn-sm btn-secondary"><i class="fa fa-edit"></i></a>
                    <a href="#" class="delete-banner btn btn-sm btn-secondary" id="'.$row->ba_id.'"><i class="fa fa-trash"></i></a>';
            })
            ->rawColumns(['ba_name','ba_image','enable_status','action','ba_descript'])
            ->make(true);
    }

    public function changeBannerStatus(Request $request)
    {
        $checked = $request->checked;

        $id = $request->id;
        // return $ba_id;
        $enable_status = 0;
        if($checked == "checked"){
            $enable_status = 1;
        }
        // return $enable_status;
        PosBanner::where('ba_place_id',Session::get('place_id'))
                ->where('ba_id',$id)
                ->update(['enable_status'=>$enable_status]);

        return "Update Status Success!";
    }
    
     public function edit($place_id, $id=0) {
        $list_banner = PosBanner::where('ba_place_id',Session::get('place_id'))->get();

        $theme = PosPlace::select("main_theme.theme_id","place_theme_property")
                    ->join('main_theme', function ($join) {
                        $join->on('main_theme.theme_name_temp', '=', 'pos_place.place_theme_code');
                    })
                    ->where('pos_place.place_id',Session::get('place_id'))
                    ->first();

        $themeId = $theme->theme_id ?? null;

        $properties = MainThemeProperties::getThemePropertiesByThemeCode($themeId);


            if ($id>0) {
                $ba_item = PosBanner::where('ba_place_id',Session::get('place_id'))
                                    ->where('ba_id',$id)
                                    ->first();
        // $banner_date = format_date($ba_item->banner_date);
                return view('tools.partials.banner_edit',compact('list_banner','ba_item','id','properties'));
            }else{
                return view('tools.partials.banner_edit',compact('list_banner','id','properties'));
            }
    }

    public function save(Request $request)
    {
        $ba_id = $request->ba_id;
        $ba_name = $request->ba_name;
        $rules = [
            'ba_name'       => 'required',
            'ba_image'      => 'mimes:jpeg,jpg,png,gif|max:1024'
        ];
        $messages = [
            'ba_name.required'      => 'Please enter Banner Name',
            // 'ba_descript.required'   => 'Please enter Description',
            'ba_image.mimes'        => 'Image must be .jpeg,.jpg,.png,.gif',
            'ba_image.max'          => 'The Maximun image 1M' 
        ];
        $validator = Validator::make($request->all(),$rules,$messages);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }else{
            if ($request->hasFile('ba_image')) {
                $ipPlaceLicense = Session::get('place_ip_license');
                $images = \App\Helpers\ImagesHelper::uploadImageWebbuilder($request->file('ba_image'),"banner",$ipPlaceLicense);
            }else{
                $images = $request->ba_image_old;
            }
            $list_banner = PosBanner::where('ba_place_id',Session::get('place_id'))->get();

            if ($ba_id>0) {//UPDATE BANNER
                
                 $PosBanner = PosBanner::where('ba_place_id',Session::get('place_id'))
                                        ->where('ba_id',$ba_id)
                                        ->update([
                                            'ba_name'       => $request->ba_name,
                                            'ba_index'      => $request->ba_index,
                                            'ba_descript'   => $request->ba_descript,
                                            'ba_style'      => $request->ba_style,
                                            'ba_image'      => $images,
                                            'enable_status' => 1,   
                                        ]);

                if ($PosBanner) {
                    Session::put('banners',1);
                    $request->session()->flash('success','Edit Banner Success');
                }else{
                    $request->session()->flash('error','Edit Banner Error');
                }
                return redirect()->route('place.webbuilder',Session::get('place_id'));
            }else{ // ADD BANNER
                $idPosBanner = PosBanner::where('ba_place_id','=',Session::get('place_id'))->max('ba_id')+1;
                $ipPlaceLicense = PosPlace::where('place_ip_license',Session::get('place_ip_license'))->get();
                //dd($ipPlaceLicense);
                $PosBanner = new PosBanner;
                        $PosBanner->ba_id           = $idPosBanner;
                        $PosBanner->ba_place_id     = Session::get('place_id');
                        $PosBanner->ba_name         = $request->ba_name;
                        $PosBanner->ba_index        = $request->ba_index;
                        $PosBanner->ba_descript     = $request->ba_descript;
                        $PosBanner->ba_image        = $images;
                        $PosBanner->ba_style        = $request->ba_style;
                        $PosBanner->enable_status   = 1;
                        $PosBanner->ba_status       = 1;
                        $PosBanner->created_by      = Auth::user()->user_id;
                        $PosBanner->save();
                
                if ($PosBanner) {
                    Session::put('banners',1);
                    $request->session()->flash('success','Insert Banner Success');
                }else{
                    $request->session()->flash('error','Insert Banner Error');
                }
                return redirect()->route('place.webbuilder',Session::get('place_id'));
            }
        }
    }

    public function delete(Request $request)
    {
        $banner = PosBanner::where('ba_place_id',Session::get('place_id'))
                            ->where('ba_id',$request->param_id)
                            ->update(['ba_status'=>0]);

        if(!isset($banner))
            return response(['status'=>'error','message'=>'Failed! Delete Banner Failed!']);

        return response(['status'=>'success','message'=>'Successfully! Delete Banner Successfully!']);
    }
}

