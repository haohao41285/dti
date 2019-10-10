<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Response;


class DashboardController extends Controller {

    public function __construct()
    {

    }

    public function index()
    {
        return view('dashboard');
    }
    public function confirmEvent(){
        try{
            //subHours(11) to get time American
            $now = Carbon::parse(now())->subHours(11);
            $end_time = Carbon::parse(now())->subHours(11)->endOfDay();

            $minutes = $end_time->diffInMinutes($now);

            $response = new Response;
            $response->withCookie( 'event', 'confirm', $minutes);
            return $response;

        }catch(\Exception $e){
            \Log::info($e);
            return 'Confirm Failed!';
        }
    }
}
