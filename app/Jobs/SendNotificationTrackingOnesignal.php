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
    protected $input;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($input)
    {
        $this->input = $input;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        OneSignal::sendNotificationUsingTags($input['name_created'] . " have just created a comment on task#" . $input['task_id'],
            array(["field" => "tag", "key" => "user_id", "relation" => "=", "value" => $input['receiver_id']]),
            $url = route('task-detail',$input['task_id'])
        );
    }
}
