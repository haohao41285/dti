<?php

namespace App\Http\Controllers\Setting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MainUser;
use App\Models\MainTeamType;
use App\Models\MainTeam;
use DataTables;
use Gate;

class SetupSaleTeam extends Controller
{
    public function index(){
        if(Gate::allows('permission','setup-sale-team'))
            return view('setting.setup-sale-team');
        else
            return doNotPermission();
    }
    public function datatable(Request $request){
        //GET TEAM SALE
        $team_type_id = MainTeamType::where('team_type_name','Telesale MKT')->first()->id;
        $team_id = MainTeam::select('id')->where('team_type',$team_type_id)->get()->toArray();
        $team_id_array = array_values($team_id);
        //GET SELLERS
        $sellers = MainUser::whereIn('user_team',$team_id_array)->get();
        
        return DataTables::of($sellers)
        ->addColumn('fullname',function($row){
            return $row->getFullname();
        })
        ->make(true);
    }
    public function save(Request $request){
        if(!$request->user_id)
            return response(['status'=>'error','message'=>'Failed!']);
        $user_update = MainUser::where('user_id',$request->user_id)
        ->update(['user_phone_call'=>$request->user_phone_call,'user_target_sale'=>$request->user_target_sale]);

        if(!$user_update)
            return response(['status'=>'error','message'=>'Failed!']);

        return response(['status'=>'success','message'=>'Successfully!']);
    }
    public function datatableTeam(Request $request){
        $teams = MainTeam::active()->with('getTeamType');
        return DataTables::of($teams)
        ->addColumn('team_type',function($row){
            return $row->getTeamType->team_type_name;
        })
        ->make(true);
    }
    function saveTeam(Request $request){

        if( isset($request->other_date) && count(array_filter($request->other_date) ) > 0 ){
            $other_date = implode(';', array_filter(array_filter($request->other_date)) );
        }else
        $other_date = "";

        if( isset($request->sale_date) && count(array_filter($request->sale_date)) )
            $sale_date = implode(';',array_filter($request->sale_date) );
        else
            $sale_date = '';

        try{
            MainTeam::find($request->team_id)->update(['sale_date'=>$sale_date,'other_date'=>$other_date]);
            return response(['status'=>'success','message'=>'Successfully!']);
        }catch(\Exception $e){
            \Log::info($e);
            return response(['status'=>'error','message'=>'Failed!']);
        }
            
        return $request->all();
    }
}
