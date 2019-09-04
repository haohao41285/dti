<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\GeneralHelper;
use App\Models\MainTeam;
use App\Models\MainTeamType;
use App\Models\MainUser;
use DataTables;


class SetupTeamController  extends Controller
{
	public function index(){

		return view('setting.setup-team');
	}
	public function getTemDatatable(Request $request)
	{
		$team_list = MainTeam::leftjoin('main_user',function($join){
								$join->on('main_team.team_leader','main_user.user_id');
							    })
								->leftjoin('main_team_type',function($join){
									$join->on('main_team.team_type','main_team_type.id');
								})
								->where('team_status',1)
								->select('main_team.*','main_user.user_firstname','main_user.user_lastname','main_team_type.team_type_name','main_user.user_id','main_team.id');

		return DataTables::of($team_list)
				
			->editColumn('team_leader',function($row){
				return $row->user_firstname." ".$row->user_lastname;
			})
			->addColumn('action',function($row){
			return '<a class="btn btn-sm btn-secondary add-team"  href="javascript:void(0)"><i class="fas fa-plus"></i></a> <a class="btn btn-sm btn-secondary edit-team" team_name="'.$row->team_name.'" team_type="'.$row->team_type.'" leader_id="'.$row->user_id.'" team_id="'.$row->id.'" href="javascript:void(0)"><i class="fas fa-edit"></i></a>
                <a class="btn btn-sm btn-secondary delete-team" team_id="'.$row->id.'" href="javascript:void(0)"><i class="fas fa-trash"></i></a>';
			})
			->rawColumns(['action'])
			->make(true);
	}
	public function editTeam(Request $request)
	{
		$team_id = $request->team_id;

		$data['user_list'] = MainUser::where('user_status',1)->select('user_firstname','user_lastname','user_id')->get();

		$data['team_type_list'] = MainTeamType::where('team_type_status',1)->get();

		return $data;
	}
	public function saveTeam(Request $request)
	{
		$team_id = $request->team_id;
		$team_name = $request->team_name;
		$team_leader = $request->team_leader;
		$team_type_id = $request->team_type_id;

		if($team_id != 0){
			//CHECK TEAM EXISTED
			$check = MainTeam::where('id','!=',$team_id)->where('team_name',$team_name)->where('team_status',1)->count();

			if($check > 0)
			{
				return response(['status'=>'error','message'=>'Team Name existed! Check again!']);
			}else
			{
				$team_update = MainTeam::where('id',$team_id)->update([
					'team_name'=>$team_name,
					'team_type'=>$team_type_id,
					'team_leader' => $team_leader
				]);
				if(!isset($team_update))
					return response(['status'=>'error','message'=>'Update Team Error!']);
				else
					return response(['status' => 'success','message'=>'Update Team Success!']);
			}
		}elseif ($team_id == 0) {
			//CHECK TEAM EXISTED
			$check = MainTeam::where('team_name',$team_name)->where('team_status',1)->count();
			if($check > 0)
			{
				return response(['status'=>'error','message'=>'Team Name existed! Check again!']);
			}else
			{
				$team_arr = [
					'team_name'=>$team_name,
					'team_type'=>$team_type_id,
					'team_leader' => $team_leader,
					'team_status' => 1
				];

				$team_insert = MainTeam::insert($team_arr);

				if(!isset($team_insert))
					return response(['status'=>'error','message'=>'Add Team Error!']);
				else
					return response(['status' => 'success','message'=>'Add Team Success!']);
			}
		}
			
	}
	public function deleteTeam(Request $request)
	{
		$team_id = $request->team_id;
		if(!isset($team_id))
			return response(['status'=>'error','message'=>'Error!']);

		$delete_team = MainTeam::where('id',$team_id)->update(['team_status'=>0]);

		if(!isset($delete_team))
			return response(['status'=>'error','message'=>'Deleting Error!']);
		else
			return response(['status'=>'success','message'=>'Deleting Error']);
	}
	public function getMemberList(Request $request)
	{
		$team_id = $request->team_id;

		$member_list = MainUser::where('user_team',$team_id)->where('user_status',1);

		return DataTables::of($member_list)
			->addColumn('user_fullname',function($row){
				return $row->getFullname();
			})
			->addColumn('action',function($row){
				return '<a class="btn btn-sm btn-secondary remove-member" user_id="'.$row->user_id.'" href="javascript:void(0)"><i class="fas fa-trash"></i></a>';
			})
			->rawColumns(['action'])
			->make(true);
	}
	public function getUserList(Request $request)
	{
		$user_list = MainUser::leftjoin('main_team',function($join){
								$join->on('main_user.user_team','main_team.id');
							})
							->where('user_status',1);

		return DataTables::of($user_list)
			->addColumn('user_fullname',function($row){
				return $row->getFullname();
			})
			->make(true);
	}
	public function removeMemberFromTeam(Request $request)
	{
		$user_id = $request->user_id;

		$user_update = MainUser::where('user_id',$user_id)->update(['user_team'=>NULL]);

		if(!isset($user_update))
			return response(['status'=>'error','message'=>'Remove Error. Check agaig!']);
		else
			return response(['status'=>'success','message'=>'Remove Success!']);
	}
	public function addMemberToTeam(Request $request){

		$user_id = $request->user_id;
		$team_id = $request->team_id;

		if(!isset($user_id))
			return response(['status'=>'error','message'=>'Adding Error! Check again!']);

		//CHECK USER EXIST IN TEAM
		$check = MainUser::where('user_id',$user_id)->first()->user_team;
		if($check != NULL)
			return response(['status'=>'error','message'=>'This User existed in a team! Check again!']);

		$user_update = MainUser::where('user_id',$user_id)->update(['user_team'=>$team_id]);

		if(!isset($user_update))
			return response(['status'=>'error','message'=>'Adding Error. Check Again!']);
		else
			return response(['status'=>'success','message'=>'Adding Success!']);
	}
}