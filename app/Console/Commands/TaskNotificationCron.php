<?php

namespace App\Console\Commands;

use App\Models\MainNotification;
use App\Models\MainTask;
use Illuminate\Console\Command;
use App\Jobs\SendNotification;

class TaskNotificationCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:taskNotification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Notification when task expired!';

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
        $date_expired = today()->addDays(1);

        $task_list = MainTask::where([
            ['date_end','!=',null],
            ['assign_to','!=',null]
        ])
            ->whereDate('date_end',$date_expired)->get();

        foreach($task_list as $task){
            //SEND NOTIFICATION BY MAIL
            $input = [];
            $message = '';
            $message .= 'Service:' .$task->subject."<br>";
            $message .= 'Expired Date:' .format_date($task->date_end)."<br>";
            $message .= 'Click <a href="'.route('task-detail',$task->id).'">HERE</a> to go the Task';

            if($task->getAssignTo->user_email != ""){
                $input['subject'] = 'EXPIRED TASK NOTIFICATION';
                $input['email'] = $task->getAssignTo->user_email;
                $input['name'] = $task->getAssignTo->user_nickname;
                $input['message'] = $message;

                dispatch(new SendNotification($input))->delay(now()->addSecond(5));
            }
            //END SEND NOTIFICATION BY MAIL
            //ADD NOTIFICATION IN DATABASE
            $notification_arr = [
                'content' => $task->subject." be expired at ".format_date($task->date_end),
                'href_to' => route('task-detail',$task->id),
                'receiver_id' => $task->getAssignTo->user_id,
                'read_not' => 0,
                'created_by' => 0
            ];
            MainNotification::create($notification_arr);
            //END
        }

        $this->info('Done!');
    }
}
