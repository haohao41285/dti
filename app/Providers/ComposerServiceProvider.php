<?php

namespace App\Providers;

use App\Models\MainNotification;
use App\Models\MainUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
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

            if($event_holidays->count() > 0){
                foreach ($event_holidays as $event){

                    $event_info = [
                        'name' => $event->name,
                        'image' => $event->image
                    ];
                }
                $data['event_info'] = $event_info;
                //END GET EVENT HOLIDAY
            }

            //GET BIRTHDAY STAFF
            $user_info = [];
            $image_arr = ['birthday_1.gif','birthday_2.gif','birthday_3.gif','birthday_4.gif','birthday_5.gif','birthday_6.gif'];
            $month = Carbon::parse(now())->format('m');
            $date = Carbon::parse(now())->format('d');

            $user_list = MainUser::where('user_status',1)
                ->whereDay('user_birthdate',$date)
                ->whereMonth('user_birthdate',$month)
                ->get();

            if($user_list->count() >0 ){
                foreach ($user_list as $user){

                    $user_info[] = [
                        'id' => $user->user_id,
                        'nickname' => $user->user_nickname,
                        'fullname' => $user->getFullname()
                    ];
                }
                $data['image_birthday'] = $image_arr[rand(0,5)];
                $data['user_info'] = $user_info;
            }

            //GET NOTIFICATION
            if(isset(Auth::user()->user_id)){
                $notification_count = MainNotification::where('receiver_id',Auth::user()->user_id)->notRead()->latest()->count();
                $data['notification_count'] = $notification_count;
            }
            if(isset($data))
                $view->with('data',$data);
        });
    }
}
