<?php

namespace App\Console\Commands;

use App\Models\MainCustomerService;
use App\Models\MainNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Jobs\SendNotification;

class ServiceNotificationCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:servicenotification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send service notification for sale';

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
        $date_expired = Carbon::parse(now())->addDays(15);

        $customer_service_list = MainCustomerService::active()->whereDate('cs_date_expire',$date_expired)->get();
        foreach ($customer_service_list as $service){
            //SEND NOTIFICATION BY MAIL
            $input = [];
            if($service->getCreatedBy->user_email != ""){

                $message = "Service Name: ".$service->getComboService->cs_name."<br>";
                $message .= "Customer: ".$service->getCustomer->customer_firstname." ".$service->getCustomer->customer_lastname."<br>";
                $message .= "Expired Date: ".format_datetime($date_expired)."<br>";
                $message .= "Click <a href='".route('add-order',$service->getCustomer->customer_id)."'>HERE</a> to order";

                $input['subject'] = 'EXPIRED SERVICE NOTIFICATION!';
                $input['email'] = $service->getCreatedBy->user_email;
                $input['name'] = $service->getCreatedBy->user_nickname;
                $input['message'] = $message;

                dispatch(new SendNotification($input))->delay(now()->addSecond(10));
            }
            //END SEND NOTIFICATION BY MAIL
            //ADD NOTIFICATION TO DATABASE
            $notification_arr = [
                'content' => "Expired Service Notification!",
                'href_to' => route('add-order',$service->getCustomer->customer_id),
                'receiver_id' => $service->getCreatedBy->user_id,
                'read_not' => 0,
                'created_by' => 0
            ];
        }
        MainNotification::create($notification_arr);

        $this->info('Cron Successfully!');
    }
}
