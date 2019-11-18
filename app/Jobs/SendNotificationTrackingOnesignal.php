<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use OneSignal;

class SendNotificationTrackingOnesignal implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $input_onesignal;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($input_onesignal)
    {
        $this->input_onesignal = $input_onesignal;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        OneSignal::sendNotificationUsingTags($this->input_onesignal['name_created'] . " have just created a comment on task#" . $this->input_onesignal['task_id'],
            array(["field" => "tag", "key" => "user_id", "relation" => "=", "value" => $this->input_onesignal['receiver_id']]),
            $url = route('task-detail',$this->input_onesignal['task_id'])
        );
    }
}
