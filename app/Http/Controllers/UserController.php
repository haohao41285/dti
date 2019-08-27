<?php

namespace App\Http\Controllers;

// use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MainUser;
use Auth;

class UserController extends Controller 
{    
    public function index(){
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
}