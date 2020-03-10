<?php

namespace App\Http\Controllers\Setting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MainUser;
use App\Models\MainTeamType;
use App\Models\MainTeam;
use DataTables;

class SetupSaleTeam extends Controller
{
    public function index(){
        return view('setting.setup-sale-team');
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
    public function calendar(Request $request){
        $today = today(); 
        $dates = []; 

        for($i=1; $i < $today->daysInMonth + 1; ++$i) {
            $dates[] = \Carbon\Carbon::createFromDate($today->year, $today->month, $i)->format('F-d-Y');
        }
        return $dates;
    }
}
