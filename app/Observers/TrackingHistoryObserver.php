<?php

namespace App\Observers;

use App\Jobs\SendNotification;
use App\Models\MainTrackingHistory;
use OneSignal;

class TrackingHistoryObserver
{
    /**
     * Handle the main tracking history "created" event.
     *
     * @param  \App\Models\MainTrackingHistory  $mainTrackingHistory
     * @return void
     */
    public function created(MainTrackingHistory $mainTrackingHistory)
    {
        //SEND MAIL
        $name_created = $mainTrackingHistory->getUserCreated->user_nickname;
        $email_list = $mainTrackingHistory->email_list;

        $email_arr = [];
        if($email_list != ""){
            $email_arr = explode(";",$email_list);
        }

        $task_id = "";
        if($mainTrackingHistory->task_id != "")
            $task_id = $mainTrackingHistory->task_id;
        if($mainTrackingHistory->subtask_id != "")
            $task_id = $mainTrackingHistory->subtask_id;

        if($mainTrackingHistory->getUserCreated->user_email != ""){
            if($task_id != "") {
                $content = "Dear Sir/Madam,<br>";
                $content .= $name_created . " have just created a comment on task#" . $task_id . "<hr>" . $mainTrackingHistory->content;
                $content .= "<hr>";
                $content .= "<a href='" . route('task-detail', $task_id) . "'  style='color:#e83e8c'>Click here to view ticket detail</a><br>";
                $content .= "WEB MASTER (DTI SYSTEM)";
            }
            else{
                $content = "Dear Sir/Madam,<br>";
                $content .= $name_created . " have just created a comment on customer#" . $mainTrackingHistory->customer_id . "<hr>" . $mainTrackingHistory->content;
                $content .= "<hr>";
                $content .= "<a href='" . route('customer-detail',$mainTrackingHistory->customer_id) . "'  style='color:#e83e8c'>Click here to view customer detail</a><br>";
                $content .= "WEB MASTER (DTI SYSTEM)";
            }

            $input['subject'] = 'New Comment';
            $input['email'] = $mainTrackingHistory->getUserCreated->user_email;
            $input['email_arr'] = $email_arr;
            $input['name'] = $mainTrackingHistory->getUserCreated->user_firstname." ".$mainTrackingHistory->getUserCreated->user_lastname;
            $input['message'] = $content;

            dispatch(new SendNotification($input));
        }
        //END SEND MAIL

        //SEND NOTIFICATION WITH ONESIGNAL
        OneSignal::sendNotificationUsingTags($name_created . " have just created a comment on task#" . $task_id,
            array(["field" => "tag", "key" => "user_id", "relation" => "=", "value" => $$mainTrackingHistory->receiver_id]),
            $url = route('task-detail',$task_id)
        );
        //END SEND NOTIFICATION
    }

    /**
     * Handle the main tracking history "updated" event.
     *
     * @param  \App\Models\MainTrackingHistory  $mainTrackingHistory
     * @return void
     */
    public function updated(MainTrackingHistory $mainTrackingHistory)
    {
        //
    }

    /**
     * Handle the main tracking history "deleted" event.
     *
     * @param  \App\Models\MainTrackingHistory  $mainTrackingHistory
     * @return void
     */
    public function deleted(MainTrackingHistory $mainTrackingHistory)
    {
        //
    }

    /**
     * Handle the main tracking history "restored" event.
     *
     * @param  \App\Models\MainTrackingHistory  $mainTrackingHistory
     * @return void
     */
    public function restored(MainTrackingHistory $mainTrackingHistory)
    {
        //
    }

    /**
     * Handle the main tracking history "force deleted" event.
     *
     * @param  \App\Models\MainTrackingHistory  $mainTrackingHistory
     * @return void
     */
    public function forceDeleted(MainTrackingHistory $mainTrackingHistory)
    {
        //
    }
}
