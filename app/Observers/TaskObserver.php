<?php

namespace App\Observers;

use App\Models\MainTask;
use App\Jobs\SendNotification;

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
        $content = '<b style="text-transform: capitalize">Hi '.$mainTask->getAssignTo->user_firstname." ".$mainTask->getAssignTo->user_lastname.'</b><br>
                    Have a new task to you<br>
                    <span style="text-transform: capitalize">Service: '.$mainTask->subject.'</span>
                    <div style="background-color: #c69500" >Notes:'.$mainTask->desription.'</div>
                    <a href="'.route('task-detail',$mainTask->id).'" style="color:#e83e8c">Click here to view ticket detail</a>
                    <hr>
                    WEB MASTER (DTI SYSTEM)
';
        $input['subject'] = 'New Task';
        $input['email'] = $mainTask->getAssignTo->user_email;
        $input['name'] = $mainTask->getAssignTo->user_firstname." ".$mainTask->getAssignTo->user_lastname;
        $input['message'] = $content;

        dispatch(new SendNotification($input));
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
