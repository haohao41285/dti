<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use App\Models\MainEventHoliday;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        \View::composer('*',function ($view){
            $event_info = [];
            //subHours(11) to get time American
            //addDays(3) to notice before 3 days
            $month = Carbon::parse(now())->subHours(11)->addDays(3)->format('m');
            $date = Carbon::parse(now())->subHours(11)->addDays(3)->format('d');

            $event_holidays = MainEventHoliday::where('status',1)
                ->whereDay('date',$date)
                ->whereMonth('date',$month)
                ->get();

            foreach ($event_holidays as $event){

                $event_info = [
                    'name' => $event->name,
                    'image' => $event->image
                ];
            }
            $data['event_info'] = $event_info;
            $view->with('data',$data);
        });
    }
}
