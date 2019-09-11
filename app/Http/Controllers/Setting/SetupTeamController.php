<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\GeneralHelper;
use App\Models\MainTeam;
use App\Models\MainTeamType;
use App\Models\MainUser;
use App\Models\MainComboService;
use Carbon\Carbon;
use DataTables;
use DB;
use Auth;

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
			return '<a class="btn btn-sm btn-secondary edit-team" team_name="'.$row->team_name.'" team_type="'.$row->team_type.'" leader_id="'.$row->user_id.'" team_id="'.$row->id.'" href="javascript:void(0)"><i class="fas fa-edit"></i></a>
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
				DB::beginTransaction();
				$team_update = MainTeam::where('id',$team_id)->update([
					'team_name'=>$team_name,
					'team_type'=>$team_type_id,
					'team_leader' => $team_leader
				]);

				//INSERT TEAM LEADER INSIDE TEAM
				$user_update = MainUser::where('user_id',$team_leader)->update(['user_team'=>$team_id]);

				if(!isset($team_update) || !isset($user_update)){
					DB::calllback();
					return response(['status'=>'error','message'=>'Update Team Error!']);
				}
				else{
					DB::commit();
					return response(['status' => 'success','message'=>'Update Team Success!']);
				}
			}
		}elseif ($team_id == 0) {
			//CHECK TEAM EXISTED
			$check = MainTeam::where('team_name',$team_name)->where('team_status',1)->count();
			if($check > 0)
			{
				return response(['status'=>'error','message'=>'Team Name existed! Check again!']);
			}else
			{
			    DB::beginTransaction();
				$team_arr = [
					'team_name'=>$team_name,
					'team_type'=>$team_type_id,
					'team_leader' => $team_leader,
					'team_status' => 1
				];

				$team_insert = MainTeam::insert($team_arr);
				//INSERT TEAM LEADER INSIDE TEAM
				$max_id = MainTeam::max('id');
				$user_update = MainUser::where('user_id',$team_leader)->update(['user_team'=>$max_id]);

				if(!isset($team_insert) || !isset($user_update)){
					DB::callback();
					return response(['status'=>'error','message'=>'Add Team Error!']);
				}
				else{
					DB::commit();
					return response(['status' => 'success','message'=>'Add Team Success!']);
				}
			}
		}else
		    return response(['status'=>'error','message'=>'Error! Check again!']);
			
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
			return response(['status'=>'success','message'=>'Deleting Success!']);
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

		//CHECK TEAM LEADER

		$user_info = MainUser::join('main_team',function($join){
								$join->on('main_user.user_team','main_team.id');
							})
								->where('user_id',$user_id)
								->select('main_team.id')
								->get();

		if($user_info[0]->id == $user_id)
			return response(['status'=>'error','message'=>'Error! This User is Leader. Change leader first']);

		$user_update = MainUser::where('user_id',$user_id)->update(['user_team'=>NULL]);

		if(!isset($user_update))
			return response(['status'=>'error','message'=>'Remove Error. Check again!']);
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
	public function setupTeamType()
	{
		return view('setting.setup-team-type');
	}
	public function teamTypeDatatable(Request $request)
	{
		$team_type_list = MainTeamType::leftjoin('main_user',function($join){
			$join->on('main_team_type.created_by','main_user.user_id');
		})
		->select('main_team_type.*','main_user.user_nickname');

		return DataTables::of($team_type_list)
		    ->editColumn('team_type_status',function($row){
		    	if($row->team_type_status == 1) $checked='checked';
	       		else $checked="";
				return '<input type="checkbox" id="'.$row->id.'" team_type_status="'.$row->team_type_status.'" class="js-switch"'.$checked.'/>';
		    })
		    ->editColumn('created_at',function($row){
		    	return Carbon::parse($row->created_at)->format('m/d/Y') ." by ".$row->user_nickname;
		    })
		    ->addColumn('action',function($row){
				return '<a class="btn btn-sm btn-secondary edit-cs" title="Edit" href="javascript:void(0)"><i class="fas fa-edit"></i></a> <a class="btn btn-sm btn-secondary delete-tt" title="Delete" tt_id="'.$row->id.'"  href="javascript:void(0)"><i class="fas fa-trash"></i></a>';
			})
			->rawColumns(['action','team_type_status'])
			->make(true);
	}
	public function changeStatusTeamtype(Request $request)
	{
		$id = $request->id;
		$team_type_status = $request->team_type_status;

		if(!isset($id))
			return response(['status'=>'error','message'=>'Error. Check again!']);

		if($team_type_status == 1)
			$status = 0;
		else
			$status = 1;
		$update_tt = MainTeamType::where('id',$id)->update(['team_type_status'=>$status]);

		if(!isset($update_tt))
			return response(['status'=>'error','message'=>'Error. Check again!']);
		else
			return response(['status'=>'success','message'=>'Success!']);
	}
	public function addTeamType(Request $request)
	{
		$id = $request->id;
		$team_type_description = $request->team_type_description;
		$team_type_name = $request->team_type_name;

		if($id != 0){
			$tt_update = MainTeamType::where('id',$id)->update(['team_type_description'=>$team_type_description,'team_type_name'=>$team_type_name]);
		}else
		    $tt_update = MainTeamType::insert([
		    	'team_type_description'=>$team_type_description,
		    	'team_type_name'=>$team_type_name,
		    	'team_type_status'=>1,
		    	'created_by' => Auth::user()->user_id,
		    ]);
		if(!isset($tt_update))
			return response(['status'=>'error','message'=>'Error. Check again!']);
		else
			return response(['status'=>'success','message'=>'Success!']);
	}
	public function deleteTeamType(Request $request)
	{
		$tt_id = $request->tt_id;

		if(!isset($tt_id))
			return response(['status'=>'error','message'=>'Error!']);
		//CHECK TEAM USE TYPE
		$check = MainTeam::where('team_type',$tt_id)->count();

		if($check > 0)
			return response(['status'=>'error','message'=>'Error! This Type includes Team']);

		$team_type_delete = MainTeamType::find($tt_id)->delete();

		if(!isset($team_type_delete))
			return response(['status'=>'error','message'=>'Error!']);
		else
			return response(['status'=>'success','message'=>'Success!']);
	}
}