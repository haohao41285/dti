<?php

namespace App\Observers;

use App\Models\MainTask;
use App\Jobs\SendNotification;
use App\Jobs\SendNotificationTaskOnesignal;
use App\Models\MainUser;

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
        $receiver_id = $mainTask->assign_to;
        $receiver_id = explode(';',$receiver_id);
        $receiver_list = MainUser::whereIn('user_id',$receiver_id)->get();
        foreach ($receiver_list as $key => $receiver){
            if($key == 0){
                $input['email'] = $receiver->user_email;
                $input['name'] = $receiver->getFullname();
            }else{
                $input['email_arr'][] = $receiver->user_email;
            }
        }
        if(!empty($receiver_id)){
            //SEND MAIL NOTIFICATION
            $content = '<b style="text-transform: capitalize">Hi '.$mainTask->getAssignTo->user_firstname." ".$mainTask->getAssignTo->user_lastname.'</b><br>
                    Have a new task to you<br>
                    <span style="text-transform: capitalize">Service: '.$mainTask->subject.'</span>
                    <div style="background-color: #c69500" >Notes:'.$mainTask->desription.'</div>
                    <a href="'.route('task-detail',$mainTask->id).'" style="color:#e83e8c">Click here to view ticket detail</a>
                    <hr>
                    WEB MASTER (DTI SYSTEM)';

            $input['subject'] = 'New Task';
            $input['message'] = $content;

            dispatch(new SendNotification($input));
            //END SEND MAIL
        }

        //SEND NOTIFICATION WITH ONESIGNAL
        $name_created = $mainTask->getCreatedBy->user_nickname;
        $task_id = $mainTask->id;

        $input_onesignal['task_id'] = $task_id;
        $input_onesignal['name_created'] = $name_created;

        foreach ($receiver_id as $receiver){
            $input_onesignal['receiver_id'] = $receiver;
            dispatch(new SendNotificationTaskOnesignal($input_onesignal))->delay(now()->addSecond(5));
        }

//            OneSignal::sendNotificationUsingTags($name_created . " have just created a task to you" ,
//                array(["field" => "tag", "key" => "user_id", "relation" => "=", "value" => $receiver_id]),
//                $url = route('task-detail',$task_id)
//            );
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
