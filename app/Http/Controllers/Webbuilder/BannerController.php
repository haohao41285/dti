<?php

namespace App\Http\Controllers\Webbuilder;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use yajra\Datatables\Datatables;
use App\Models\PosBanner;

class BannerController extends Controller
{
    public function index(Request $request)
    {
    	$ba_item = PosBanner::join('pos_user',function($join){
    		$join->on('pos_banner.created_by','=','pos_user.user_id')->on('pos_banner.ba_place_id','=','pos_user.user_place_id');
    	})
    	->where('pos_banner.ba_place_id',$request->place_id)
        ->where('ba_status',1)
        ->select('pos_banner.*',"pos_user.user_id","pos_user.user_place_id","pos_user.user_places_id","pos_user.user_default_place_id","pos_user.user_usergroup_id","pos_user.user_main_customer_id","pos_user.user_permission","pos_user.user_nickname","pos_user.user_phone","pos_user.user_email","pos_user.user_password","pos_user.user_fullname","pos_user.user_avatar","pos_user.user_status","pos_user.user_token","pos_user.remember_token","pos_user.created_at","pos_user.updated_at","pos_user.created_by","pos_user.updated_by","pos_user.updated_at","pos_user.user_login_time")
        ->get();
        // dd($ba_item);

    	return Datatables::of($ba_item)
			->editColumn('ba_name',function($row){
				return "<a href='".route('banner',$row->ba_id)."'>".$row->ba_name."</a>";
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
            return '<a href="'.route('banner',$row->ba_id).'"  class="btn btn-sm btn-secondary"><i class="fa fa-edit"></i></a>
                    <a href="#" class="delete-banner btn btn-sm btn-secondary" id="'.$row->ba_id.'"><i class="fa fa-trash-o"></i></a>';
        	})
			->rawColumns(['ba_name','ba_image','enable_status','action','ba_descript'])
			->make(true);
	}
}
