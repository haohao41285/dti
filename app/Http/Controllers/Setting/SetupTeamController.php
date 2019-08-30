<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SetupTeamController  extends Controller
{
	public function index(){
		return view('setting.setup-team');
	}
}