<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use App\Helpers\GeneralHelper;
use App\Helpers\MenuHelper;
use Validator;
use Session;
use DB;

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
         $credentials = ($request->only('user_phone', 'user_password'));
        if (Auth::attempt($credentials)){

            //GET PERMISSION
            $user_list = DB::table('main_user')->leftjoin('main_group_user',function($join){
                                    $join->on('main_user.user_group_id','main_group_user.gu_id');
                                    })
                                    ->where('user_phone',$request->user_phone)
                                    ->get();
            if($user_list[0]->user_group_id == 1) $permission = 1;
            else $permission = 0;

            //CHECK PERMISSION EXIST IN GU_ROLE_NEW
            if($user_list[0]->gu_role_new == null || $permission == 1){
                $permission_list_session = self::setPermissionList($permission);
                //INSERT PERMISSION ROLE TO DATABASE
                DB::table('main_group_user')->where('gu_id',$user_list[0]->gu_id)->update(['gu_role_new'=>$permission_list_session]);
                Session::put('permission_list_session',json_decode($permission_list_session,TRUE));
            }else{
                Session::put('permission_list_session',json_decode($user_list[0]->gu_role_new,TRUE));
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
        return 'user_phone';
    }

    protected function credentials(Request $request)
    {
        return $request->only($this->username(), 'user_password');
    }
    public static function setPermissionList($permission){

        $permission_arr = [];

        $menu_list = MenuHelper::getMenuList();


        foreach ($menu_list as $number => $menu) {

            $menu_arr = self::getChildrenMenu($menu);

            foreach ($menu_arr as $key => $value) {

                $permission_arr[$value] = [
                    'Create' => $permission,
                    'Read' => $permission,
                    'Update' => $permission,
                    'Delete' => $permission
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
}
