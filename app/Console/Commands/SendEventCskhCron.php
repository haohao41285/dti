<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendNotification;
use App\Models\MainEventHoliday;
use App\Models\MainTeamType;
use App\Models\MainTeam;
use Carbon\Carbon;

class SendEventCskhCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:SendEventCskhCron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $today = today();
        $date_after_a_month = today()->addMonth(1)->format('d');
        $month_after_a_month = today()->addMonth(1)->format('m');

        $events = MainEventHoliday::active()->whereDay('date',$date_after_a_month)->whereMonth('date',$month_after_a_month)->get();

        $message = "";
        foreach ($events as $key => $event) {
            $message .= "<span style='color:red;text-transform:uppercase'>".$event->name."</span> at" .$event->date;

        }
        if($message != ""){
            //GET TEAM'S CSKH TEAM
            $cskh_team_type = MainTeamType::where('team_type_name','CSKH')->first();
            $user_team_list = MainTeam::where('team_type',$cskh_team_type->id)->with('getUserOfTeam')->get();

            foreach ($user_team_list as $key => $user_list) {
                // return $user;
                foreach ($user_list->getUserOfTeam as $user) {

                    if($user->user_email != ""){
                        $input = [];
                        $input['subject'] = 'EVENT HOLIDAY NOTIFICATION!';
                        $input['email'] = $user->user_email;
                        $input['name'] = $user->user_nickname;
                        $input['message'] = $message;
                        dispatch(new SendNotification($input))->delay(now()->addSecond(10));
                    }
                }
            }
        }
        $this->info('cron sucessfully!');
    }
}
