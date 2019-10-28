<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Models\MainComboService;
use App\Models\MainGroupUser;
use App\Models\MainTeam;
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

class UserController extends Controller
{
    private $validator;

    public function index(){
        return view('user.list');
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
                    ->where('main_group_user.gu_status',1)
                    ->select('main_user.user_birthdate','main_group_user.gu_name','main_user.user_firstname','main_user.user_lastname','main_user.user_nickname','main_user.user_phone','main_user.user_status','main_user.user_email','main_user.user_id');

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
        return view('user.roles');
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
    				return '<a class="btn btn-sm btn-secondary role-edit" href="'.route('permission',$row->gu_id).'"><i class="fas fa-edit"></i></a>';
    	        })
    	        ->rawColumns(['gu_status','action'])
    	        ->make(true);

    }
    public function changeStatusRole(Request $request){

        $gu_id = $request->gu_id;
        $gu_status = $request->gu_status;

    	if($gu_status == 1){
            $gu_status = 0;
            //CHECK USER USE THIS ROLE
            $count_user = DB::table('main_user')->where('user_group_id',$gu_id)->where('user_status',1)->count();
            if($count_user > 0){
                return response()->json(['message'=>'Can delete this role. Cause users are using it!']);
            }
        }
    	else
    		$gu_status = 1;


        DB::table('main_group_user')->where('gu_id',$gu_id)->update(['gu_status'=>$gu_status]);
    }
    public function addRole(Request $request){

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
    	if(!isset($gu_insert)){
			return 0;
		}
		else{
			return 1;
		}
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
    public function permission($role_id){

        if(Gate::forUser("Roles")->denies('permission',"Update")){

            return back()->with('error',"You don't have permission!");

        }else{

            $permission_arr = ['Read','Create','Update','Delete'];

            $permission_check = DB::table('main_group_user')
                                ->where('gu_id',$role_id)
                                ->where('gu_status',1)
                                ->first();

            if(!isset($permission_check)){
                return back()->with(['error'=>'Turn On This Status Role!']);
            }else
                $permission_list = $permission_check->gu_role_new;

            $role_name = $permission_check->gu_name;

            $role_permission_arr = json_decode($permission_list,TRUE);

            $menu_list = MenuHelper::getMenuList();

            return view('user.role-permission',compact('menu_list','role_permission_arr','permission_arr','role_id','role_name'));
        }
    }
    public function changePermission(Request $request){

        $permission_name = $request->permission_name;
        $permission_status = $request->permission_status;
        $menu = $request->menu;
        $role_id = $request->role_id;

        if($permission_status == 1)
            $permission_status = 0;
        else
            $permission_status = 1;

        $permission_list =  DB::table('main_group_user')->where('gu_id',$role_id)->first()->gu_role_new;

        $permission_list = json_decode($permission_list,TRUE);

        $permission_list[$menu][$permission_name] = $permission_status;

        $permission_list = json_encode($permission_list);

        $permission_update = DB::table('main_group_user')->where('gu_id',$role_id)->update(['gu_role_new'=>$permission_list]);

        if(!isset($permission_update))
            return 0;
        else
            return 1;
    }
    public function userAdd($id = 0){

        $data['user'] =MainUser::where('user_id',$id)->first();
        $data['roles'] = MainGroupUser::active()->get();
        $data['teams'] = MainTeam::active()->get();

        return view('user.user-add',$data);
    }
    public function userSave(Request $request){

        $user_id = $request->user_id;

        $rule = [
            'user_firstname' => 'required',
            'user_lastname' => 'required',
            'user_nickname' => 'required',
            'user_phone' => 'required|min:10|max:15',
            'user_email' =>'required|email'
        ];
        $message = [
            'user_firstname.required' => 'Enter Firstname',
            'user_lastname.required' => 'Enter Lastname',
            'user_nickname.required' => 'Enter Nickname',
            'user_phone.required' => 'Enter Phone',
            'user_phone.min' => 'Phone not True',
            'user_phone.max' => 'Phone not True',
            'user_email.required' => 'Enter Mail',
            'user_email.email' => 'Enter Enable Mail',

        ];
        if($user_id == 0){
            $rule['new_password'] = 'required|min:6';
            $message['new_password.required'] = 'Enter Pasword';
            $message['new_password.min'] = 'Password at least 6 characters';
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

        $data['role_list'] = MainGroupUser::active()->get();
        $data['service_list'] = MainComboService::where('cs_status',1)->get();

        return view('user.service-permission',$data);
    }
    public function changeServicePermission(Request $request){
        $role_id = $request->role_id;
        $service_id = $request->service_id;

        $service_permission = MainGroupUser::where('gu_id',$role_id)->first();
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
        $service_update = MainGroupUser::where('gu_id',$role_id)->update(['service_permission'=> $service_permission_list]);

        if(!isset($service_update))
            return response(['status'=>'error','message'=>'Failed!']);
        else
            return response(['status'=>'success','message'=>'Successfully!']);
    }
}
