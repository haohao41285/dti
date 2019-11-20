<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use OneSignal;

class SendNotificationOrderOnesignal implements ShouldQueue
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
        OneSignal::sendNotificationUsingTags('New Order',
            array(["field" => "tag", "key" => "user_id", "relation" => "=", "value" =>$this->input['user_id']]),
            $url = route('payment-order',$this->input['order_id'])
        );
    }
}
