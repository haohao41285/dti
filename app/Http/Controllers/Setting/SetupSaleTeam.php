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
}
