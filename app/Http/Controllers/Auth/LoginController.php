<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\MainPermissionDti;
use App\Models\MainUser;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use App\Helpers\GeneralHelper;
use App\Helpers\MenuHelper;
use Validator;
use Session;
use DB;
use App\Models\MainMenuDti;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function postLogin(Request $request){

        $validator = $this->validator($request->input());

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->messages());

        }
        //CHECK USER STATUS ENABLE
        $user_info = MainUser::where('user_nickname',$request->user_nickname)->first();
        if($user_info['user_status'] == 0){
            return back()->with(['error'=>'You Do Not ave Permission!']);
        }

         $credentials = ($request->only('user_nickname', 'user_password'));
        if (Auth::attempt($credentials)){

            //GET PERMISSION
            $user_list = DB::table('main_user')->leftjoin('main_group_user',function($join){
                $join->on('main_user.user_group_id','main_group_user.gu_id');
            })
                ->where('user_nickname',$request->user_nickname)
                ->get();
            if($user_list[0]->user_group_id == 1) $role = 1;
            else $role = 0;

            //SET PERMISSION LIST TO SESSION
            $permission_list = MainPermissionDti::all();
            $permission_list = collect($permission_list);
            Session::put('permission_list',$permission_list);
            //SET MENU LIST ALL FOR SESSION
            $menu_list_all = MainMenuDti::active()->get();
            $menu_list_all = collect($menu_list_all);
            Session::put('menu_list_all',$menu_list_all);

            //CHECK PERMISSION EXIST IN GU_ROLE_NEW
            if($user_list[0]->gu_role_new == null || $role == 1){
                $permission_list_session = self::setPermissionList($role);
                //INSERT PERMISSION ROLE TO DATABASE
                DB::table('main_group_user')->where('gu_id',$user_list[0]->gu_id)->update(['gu_role_new'=>$permission_list_session]);
                Session::put('permission_list_session',explode(';',$permission_list_session));
            }else{
                $permission_list_session = DB::table('main_group_user')->where('gu_id',$user_list[0]->gu_id)->first()->gu_role_new;
                Session::put('permission_list_session',explode(';',$permission_list_session));
            }
            return redirect()->intended('/');
        } else {
            $errors = new MessageBag(['errorLogin' => 'User Phone or Password is incorrect']);
            return redirect()->back()->withInput()->withErrors($errors);
        }

    }
    public function logout() {
        Auth::logout();
        return redirect('/login');
    }
    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $rules = [
            $this->username() => 'required|max:255',
            'user_password' => 'required|max:255',
        ];
        if(env('GOOGLE_RECAPTCHA')){
           $rules['g-recaptcha-response'] =  ['required', new \App\Rules\ValidRecaptcha];
        }
        return Validator::make($data, $rules);
    }

    public function username()
    {
        return 'user_nickname';
    }

    protected function credentials(Request $request)
    {
        return $request->only($this->username(), 'user_password');
    }
    public static function setPermissionList($role){

        $permission_list = '';

        if($role == 1){

            $permission_arr = [];

            $permission_list = MainPermissionDti::select('id')->active()->get();

            foreach($permission_list as $permission){

                $permission_arr[] = $permission->id;
            }
            $permission_list = implode(';',$permission_arr);
        }
        return $permission_list;

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
}
