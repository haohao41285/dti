<?php

namespace App\Observers;

use App\Models\MainTask;
use App\Jobs\SendNotification;
use OneSignal;

class TaskObserver
{
    /**
     * Handle the main task "created" event.
     *
     * @param  \App\Models\MainTask  $mainTask
     * @return void
     */
    public function created(MainTask $mainTask)
    {
        //SEND MAIL NOTIFICATION
        $content = '<b style="text-transform: capitalize">Hi '.$mainTask->getAssignTo->user_firstname." ".$mainTask->getAssignTo->user_lastname.'</b><br>
                    Have a new task to you<br>
                    <span style="text-transform: capitalize">Service: '.$mainTask->subject.'</span>
                    <div style="background-color: #c69500" >Notes:'.$mainTask->desription.'</div>
                    <a href="'.route('task-detail',$mainTask->id).'" style="color:#e83e8c">Click here to view ticket detail</a>
                    <hr>
                    WEB MASTER (DTI SYSTEM)';

        $input['subject'] = 'New Task';
        $input['email'] = $mainTask->getAssignTo->user_email;
        $input['name'] = $mainTask->getAssignTo->user_firstname." ".$mainTask->getAssignTo->user_lastname;
        $input['message'] = $content;

//        dispatch(new SendNotification($input));
        //END SEND MAIL

        //SEND NOTIFICATION WITH ONESIGNAL
        $name_created = $mainTask->getCreatedBy->user_nickname;
        $receiver_id = $mainTask->getAssignTo->user_id;
        $task_id = $mainTask->id;

        if($receiver_id != "")
            OneSignal::sendNotificationUsingTags($name_created . " have just created a task to you" ,
                array(["field" => "tag", "key" => "user_id", "relation" => "=", "value" => $receiver_id]),
                $url = route('task-detail',$task_id)
            );
        //END SEND NOTIFICATION
    }

    /**
     * Handle the main task "updated" event.
     *
     * @param  \App\MainTask  $mainTask
     * @return void
     */
    public function updated(MainTask $mainTask)
    {
        //
    }

    /**
     * Handle the main task "deleted" event.
     *
     * @param  \App\MainTask  $mainTask
     * @return void
     */
    public function deleted(MainTask $mainTask)
    {
        //
    }

    /**
     * Handle the main task "restored" event.
     *
     * @param  \App\MainTask  $mainTask
     * @return void
     */
    public function restored(MainTask $mainTask)
    {
        //
    }

    /**
     * Handle the main task "force deleted" event.
     *
     * @param  \App\MainTask  $mainTask
     * @return void
     */
    public function forceDeleted(MainTask $mainTask)
    {
        //
    }
}
