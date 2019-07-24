<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Validator;

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
}
