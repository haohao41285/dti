<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Models\MainComboService;
use App\Models\MainGroupUser;
use App\Models\MainMenuDti;
use App\Models\MainPermissionDti;
use App\Models\MainTeam;
use App\Models\MainTeamType;
use Illuminate\Http\Request;
use App\Helpers\MenuHelper;
use App\Helpers\GeneralHelper;
use App\Models\MainUser;
use Auth;
use DataTables;
use DB;
use App\Helpers\ImagesHelper;
use Gate;
use Validator;
use Hash;
use App\Models\MainComboServiceType;
use Session;

class UserController extends Controller
{
    private $validator;

    public function index(){
        if(Gate::allows('permission','users-read'))
             return view('user.list');
        else
            return back()->with('error',"You don't have permission");
    }

    public function editProfile(){
    	$data['user'] = Auth::user();
        return view('auth.editprofile',$data);
    }


    public function edit(Request $request){
    	$id = Auth::id();

    	$this->validate($request,[
            'confirm_password' =>'same:new_password',
    	],[

    	]);
        //CHECK USER NICKNAME
        $check_user = MainUser::where([['user_id','!=',$id],['user_nickname',$request->user_nickname]])->count();
        if($check_user > 0)
            return back()->with(['error'=>'User Nickname has taken, Choose Another']);

    	$user = MainUser::where('user_id',$id)->first();
        $user->user_nickname = $request->user_nickname;
    	$user->user_firstname = $request->user_firstname;
    	$user->user_lastname = $request->user_lastname;

        if($request->hasFile('avatar')){
            $user->user_avatar = ImagesHelper::uploadImage($request->hasFile('avatar'),$request->avatar,$user->user_avatar);
            // dd($user->user_avatar);
        }

        if($request->password && $request->new_password && $request->confirm_password){
            if(\Hash::check($request->password,$user->user_password)){
                $user->user_password = bcrypt($request->new_password);
            }else {
                return back()->with('error',"Password don't match");
            }
        }

    	$user->save();

    	return back()->with('success','Profile have been updated successfully!');
    }

    public function userDataTable(Request $request){
        $user_list = MainUser::join('main_group_user',function($join){
                    $join->on('main_user.user_group_id','main_group_user.gu_id');
                    })
                    ->where('main_group_user.gu_status',1);

        if(Gate::allows('permission','user-admin')){}
        elseif(Gate::allows('permission','user-leader'))
            $user_list =  $user_list->where('main_user.user_team',Auth::user()->user_team);

        $user_list = $user_list->select('main_user.user_birthdate','main_group_user.gu_name','main_user.user_firstname','main_user.user_lastname','main_user.user_nickname','main_user.user_phone','main_user.user_status','main_user.user_email','main_user.user_id');

        return DataTables::of($user_list)

               ->editColumn('user_fullname',function($row){
                    return $row->user_lastname." ".$row->user_firstname;
               })
               ->editColumn('user_status',function($row){

                if($row->user_status == 1) $checked='checked';
                else $checked="";
                    return '<input type="checkbox" user_id="'.$row->user_id.'" user_status="'.$row->user_status.'" class="js-switch"'.$checked.'/>';
               })
                ->addColumn('user_birthdate',function($row){
                    if($row->user_birthdate != "")
                        return format_date($row->user_birthdate);
                    else
                        return "";
                })
               ->addColumn('action',function($row){
                    return '<a class="btn btn-sm btn-secondary edit-user" href="'.route('user-add',$row->user_id).'"><i class="fas fa-edit"></i></a>
                    <a class="btn btn-sm btn-secondary delete-user" user_id="'.$row->user_id.'" href="javascript:void(0)"><i class="fas fa-trash"></i></a>';
               })
               ->rawColumns(['user_status','action'])
               ->make(true);
    }
    public function changeStatusUser(Request $request){
        if(Gate::denies('permission','users-update'))
            return response(['status'=>'error','message'=>'You do Not have permission']);
        $user_status = $request->user_status;
        $user_id = $request->user_id;

        if($user_status == 1)
            $user_status = 0;
        else
            $user_status = 1;

        $update_user = MainUser::where('user_id',$user_id)->update(['user_status'=>$user_status]);
        if(!isset($update_user))
            return response(['status'=>'error','message'=>'Change Failed!']);
        else
            return response(['status'=>'success','message'=>'Change Successfully!']);
    }
    //ROLES
    public function roleList(){
        if(Gate::allows('permission','roles-read'))
            return view('user.roles');
        else
            return doNotPermission();
    }
    public function roleDatatable(Request $request){

    	$role_list = DB::table('main_group_user')->select('gu_id','gu_name','gu_descript','gu_status');

    	return DataTables::of($role_list)
    			->editColumn('gu_status',function($row){
    				if($row->gu_status == 1) $checked='checked';
    	       		else $checked="";
    				return '<input type="checkbox" gu_id="'.$row->gu_id.'" gu_status="'.$row->gu_status.'" class="js-switch"'.$checked.'/>';
    			})
    			->addColumn('action',function($row){
    				return '<a class="btn btn-sm btn-secondary role-edit" href="javascript:void(0)"><i class="fas fa-edit"></i></a>';
    	        })
    	        ->rawColumns(['gu_status','action'])
    	        ->make(true);

    }
    public function changeStatusRole(Request $request){

        if(Gate::denies('permission','roles-update'))
            return doNotPermissionAjax();

        $gu_id = $request->gu_id;
        $gu_status = $request->gu_status;

    	if($gu_status == 1){
            $gu_status = 0;
            //CHECK USER USE THIS ROLE
            $count_user = DB::table('main_user')->where('user_group_id',$gu_id)->where('user_status',1)->count();
            if($count_user > 0){
                return response()->json(['status'=>'error','message'=>'Can delete this role. Cause users are using it!']);
            }
        }
    	else
    		$gu_status = 1;
        $role_update = DB::table('main_group_user')->where('gu_id',$gu_id)->update(['gu_status'=>$gu_status]);
        if(!isset($role_update))
            return response(['status'=>'error','message'=>'Change Status Failed!']);
        else
            return response(['status'=>'success','message'=>'Change Status Successfully!']);
    }
    public function addRole(Request $request){

        if(Gate::denies('permission','roles-create'))
            return doNotPermissionAjax();

    	$gu_id = $request->gu_id;
    	$gu_name = $request->gu_name;
    	$gu_descript = $request->gu_descript;

    	if($gu_id > 0){

    		$gu_insert = DB::table('main_group_user')->where('gu_id',$gu_id)->update(['gu_name'=>$gu_name,'gu_descript'=>$gu_descript]);
    	}else{
    		$gu_id_max = DB::table('main_group_user')->max('gu_id')+1;

    		$gu_arr = [
    			'gu_id' => $gu_id_max,
    			'gu_name' => $gu_name,
    			'gu_descript' => $gu_descript,
    			'gu_role' => 'empty',
                'gu_role_new' => self::setPermissionList()
    		];
    		$gu_insert = DB::table('main_group_user')->insert($gu_arr);
    	}
    	if(!isset($gu_insert))
    	    return response(['status'=>'error','message'=>'Save Role Failed!']);
		else
            return response(['status'=>'success','message'=>'Save Role Successfully!']);
    }
    public static function setPermissionList(){

        $permission_arr = [];

        $menu_list = MenuHelper::getMenuList();


        foreach ($menu_list as $number => $menu) {

            $menu_arr = self::getChildrenMenu($menu);

            foreach ($menu_arr as $key => $value) {

                $permission_arr[$value] = [
                    'Create' => 0,
                    'Read' => 0,
                    'Update' => 0,
                    'Delete' => 0
                ];
            }
        }
        return json_encode($permission_arr);
    }
    public static function getChildrenMenu($menu){

        $permission_arr = ['Create','Read','Update','Delete'];
        $menu_arr = [];

        if(isset($menu['childrens'])){

            foreach ($menu['childrens'] as $key => $value) {

                $menu_list = self::getChildrenMenu($value);

                foreach ($menu_list as $key => $value) {

                    $menu_arr[] = $menu_list[0];
                }
            }
        }else{
             $menu_arr[] = $menu['text'];
        }
        return $menu_arr;
    }
    public function userAdd($id = 0){

        if(Gate::denies('permission','users-create'))
            return doNotPermission();

        $data['user'] =MainUser::where('user_id',$id)->first();
        $data['roles'] = MainGroupUser::active()->get();
        $data['teams'] = MainTeam::active()->get();

        return view('user.user-add',$data);
    }
    public function userSave(Request $request){

        if(Gate::denies('permission','users-create'))
            return doNotPermission();

        $user_id = $request->user_id;

        $rule = [
            'user_firstname' => 'required',
            'user_lastname' => 'required',
            'user_nickname' => 'required',
            'user_phone' => 'required|min:10|max:15',
            'user_email' =>'required|email'
        ];
        $message = [
            'user_phone.min' => 'Phone not True',
            'user_phone.max' => 'Phone not True',
        ];
        if($user_id == 0){
            $rule['new_password'] = 'required|min:6';
            $rule['user_phone'] = 'required|min:10|max:15|unique:main_user,user_phone';
            $rule['user_email'] = 'required|email|unique:main_user,user_email';
            $message['new_password.required'] = 'Enter Pasword';
            $message['new_password.min'] = 'Password at least 6 characters';
        }else{
            //CHECK USER PHONE OR USER EMAIL EXISTED
            $count = MainUser::where('user_id','!=',$user_id)->where(function($query) use ($request){
                $query->where('user_phone',$request->user_phone)->orWhere('user_email',$request->user_email);
            })->count();
            if($count != 0)
                return back()->with(['error'=>'User phone or user email has already been taken. !']);
        }

        $validator = Validator::make($request->all(),$rule,$message);
        if($validator->fails()){
            return back()->withErrors($validator)->withInput();
        }
        $input = $request->all();

        if($request->hasFile('avatar')){
            $input['user_avatar'] = ImagesHelper::uploadImage($request->hasFile('avatar'),$request->avatar,"");
            // dd($user->user_avatar);
        }
        if($request->new_password != ""){
            $input['user_password'] = Hash::make($request->new_password);
        }
        if($request->user_birthdate != ""){
            $input['user_birthdate'] = format_date_db($request->user_birthdate);
        }
        if($user_id == 0){
            $max_user_id = MainUser::max('user_id')+1;
            $input['user_id'] = $max_user_id;
            $input['user_country_code'] = '84';
            $save_user = MainUser::create($input);
        }else{
            unset($input['_token']);
            unset($input['new_password']);
            unset($input['confirm_password']);
            unset($input['avatar']);

            $save_user = MainUser::where('user_id',$user_id)->update($input);
        }

        if(!isset($save_user)){
            return back()->with(['error'=>'Save User Failed']);
        }else
            return redirect()->route('userList')->with(['success'=>'Save User Successfully!']);
    }
    public function userDelete(Request $request){

        if(Gate::denies('permission','users-delete'))
            return doNotPermissionAjax();

        $user_id = $request->user_id;
        $delete_user = MainUser::where('user_id',$user_id)->delete();
        if(!isset($delete_user)){
            return response(['status'=>'error','message'=>'Delete Failed!']);
        }else{
            return response(['status'=>'success','message'=>'Delete Successfully!']);
        }
    }
    public function userExport(Request $request)
    {
        $user_list = MainUser::orderBy('user_firstname','asc')->get();

        $date = \Carbon\Carbon::now()->format('Y_m_d_His');
        // dd($data);
        return \Excel::create('user_list_'.$date,function($excel) use ($user_list){

            $excel ->sheet('User List', function ($sheet) use ($user_list)
            {
                $sheet->cell('A1', function($cell) {$cell->setValue('Stt');$cell->setFontWeight('bold'); });
                $sheet->cell('B1', function($cell) {$cell->setValue('Last Name');$cell->setFontWeight('bold'); });
                $sheet->cell('C1', function($cell) {$cell->setValue('First Name');$cell->setFontWeight('bold'); });
                $sheet->cell('D1', function($cell) {$cell->setValue('Birthdate');$cell->setFontWeight('bold'); });
                $sheet->cell('E1', function($cell) {$cell->setValue('Phone');$cell->setFontWeight('bold'); });
                $sheet->cell('F1', function($cell) {$cell->setValue('Mail');$cell->setFontWeight('bold'); });
                $sheet->cell('G1', function($cell) {$cell->setValue('Role');$cell->setFontWeight('bold'); });
                $sheet->cell('H1', function($cell) {$cell->setValue('Team');$cell->setFontWeight('bold'); });
                $sheet->cell('I1', function($cell) {$cell->setValue('Status');$cell->setFontWeight('bold'); });

                if (!empty($user_list)) {
                    $stt = 1;
                    foreach ($user_list as $key => $value) {
                        $i=$key+2;
                        $sheet->cell('A'.$i,function ($cell) use($stt) {$cell->setValue($stt);$cell->setValignment('center'); });
                        $sheet->cell('B'.$i, ucwords($value->user_lastname));
                        $sheet->cell('C'.$i, ucwords($value->user_firstname));
                        $sheet->cell('D'.$i, $value->user_birthdate);
                        $sheet->cell('E'.$i, $value->user_phone);
                        $sheet->cell('F'.$i, $value->user_email);
                        $sheet->cell('G'.$i, $value->getUserGroup->gu_name);
                        $sheet->cell('H'.$i, $value->getTeam->team_name);
                        $sheet->cell('I'.$i, $value->user_status==1?"Enable":"Disable");

                        $stt++;
                    }
                }
            });
        })->download("xlsx");
    }
    public function servicePermission(){
        if(Gate::denies('permission','service-permission-read'))
            return doNotPermission();

        $data['role_list'] = MainGroupUser::active()->get();
        $data['team_list'] = MainTeamType::active()->get();
        // $data['service_type_list'] = MainComboServiceType::active()->get();
        $data['service_type_list'] = DB::table('main_combo_service_type')->where('status',1)->get();
        $combo_service_list = DB::table('main_combo_service')->where('cs_status',1)->get();
        $data['combo_service_list'] = collect($combo_service_list);

        return view('user.service-permission',$data);
    }
    public function changeServicePermission(Request $request){

        if(Gate::denies('permission','service-permission-update'))
            return doNotPermissionAjax();

        $team_id = $request->team_id;
        $service_id = $request->service_id;

        $service_permission = MainTeamType::find($team_id);
        $service_permission_list = $service_permission->service_permission;

        if($service_permission_list == ""){
            $service_permission_list = $service_id;

        }else{
            $service_permission_arr = explode(';',$service_permission_list);

            if (($key = array_search($service_id, $service_permission_arr)) !== false) {
                unset($service_permission_arr[$key]);
                $service_permission_list = implode(';',$service_permission_arr);
            }
            else
                $service_permission_list = $service_permission_list.";".$service_id;
        }
        $service_update = MainTeamType::find($team_id)->update(['service_permission'=> $service_permission_list]);

        if(!isset($service_update))
            return response(['status'=>'error','message'=>'Failed!']);
        else
            return response(['status'=>'success','message'=>'Successfully!']);
    }
    public function userPermission(){

        if(Gate::denies('permission','user-permission-read'))
            return doNotPermission();

        $data['role_list'] = MainGroupUser::active()->get();

        $data['permission_list'] = Session::get('permission_list');

        $data['menu_list_all'] = Session::get('menu_list_all');

        $data['permission_other'] = $data['permission_list']->where('menu_id',null);

        $data['menu_parent'] = $data['menu_list_all']->where('parent_id',0);


        return view('user.user-permission',$data);

    }
    public function changePermissionRole(Request $request){

        if(Gate::denies('permission','user-permission-update'))
            return doNotPermissionAjax();

        $permission_id = $request->permission_id;
        $role_id = $request->role_id;
//        $check = $request->check;

        $permission_role = MainGroupUser::where('gu_id',$role_id)->first()->gu_role_new;
        if($permission_role == "")
            $permission_list = $permission_id;
        else{
            $permission_role_arr = explode(';',$permission_role);

            if (($key = array_search($permission_id, $permission_role_arr)) !== false) {
                unset($permission_role_arr[$key]);
            }else{
                $permission_role_arr[] = $permission_id;
            }

            $permission_list = implode(';',$permission_role_arr);
        }
        $role_permission_update = MainGroupUser::where('gu_id',$role_id)->update(['gu_role_new'=>$permission_list]);

        if(!isset($role_permission_update))
            return response(['status'=>'error','message'=>'Set Permission Failed!']);
        else
            return response(['status'=>'success','message'=>'Set Successfully!']);
    }
}
