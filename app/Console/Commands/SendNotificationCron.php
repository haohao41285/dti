<?php

namespace App\Console\Commands;

use App\Jobs\SendNotification;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use App\Models\PosCustomer;

class SendNotificationCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:sendnotification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notification for customer on birthday';

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
        $customers = PosCustomer::where('customer_status',1)->whereMonth('customer_birthdate',date('m'))->whereDay('customer_birthdate',date('d'))->get();

        foreach($customers as $customer){

            //SEND NOTIFICATION BY MAIL
//            $input = [];
//            if($customer->customer_email != ""){
//                $input['subject'] = 'HAPPY BIRTHDAY';
//                $input['email'] = $customer->customer_email;
//                $input['name'] = $customer->customer_fullname;
//                $input['message'] = 'Happy Birthday';
//
//                dispatch(new SendNotification($input))->delay(now()->addSecond(5));
//            }
            //END SEND NOTIFICATION BY MAIL

            //GET ARRAY INFORMATION TO SEND SMS
            if($customer->customer_phone != ""){
                $receiver_total[] = [
                    'name' =>$customer->customer_fullname,
                    'phone'=>$customer->customer_phone,
                    'birthday'=>$customer->customer_birthdate,
                ];
            }

        }
        //SEND NOTIFICATION BY SMS
        if(!empty($receiver_total)){
            $date = now()->format('Y_m_d_His');

            $file_name = "receiver_sms_list_".$date;

            \Excel::create($file_name,function($excel) use ($receiver_total){

                $excel ->sheet('receiver_list_send_birthday', function ($sheet) use ($receiver_total)
                {
                    $sheet->cell('A1', function($cell) {$cell->setValue('phone');   });
                    $sheet->cell('B1', function($cell) {$cell->setValue('{p2}');   });
                    $sheet->cell('C1', function($cell) {$cell->setValue('{p3}');   });

                    if (!empty($receiver_total)) {
                        foreach ($receiver_total as $key => $value) {
                            $i= $key+2;
                            if($value['phone'] != ""){
                                $sheet->cell('A'.$i, $value['phone']);
                                $sheet->cell('B'.$i, $value['name']);
                                $sheet->cell('C'.$i, Carbon::parse($value['birthday'])->format('d/m/Y'));
                            }
                        }
                    }
                });
            })->store('xlsx', false, true);

            $file_url = storage_path('exports/'.$file_name.".xlsx");

            $sms_content_template = "HAPPY BIRTHDAY";

            $url_event = 'pushsms';

            $url = env('SMS_API_URL').$url_event;

            $header = array('Authorization'=>'Bearer ' .env("SMS_API_KEY"));
            $client = new Client([
            ]);

            $sms_content_template = str_replace("{phone}","{p1}",$sms_content_template);
            $sms_content_template = str_replace("{name}","{p2}",$sms_content_template);
            $sms_content_template = str_replace("{birthday}","{p3}",$sms_content_template);

            $date_time_send = format_date_d_m_y(now())." 00:00:00";
            $date_time_end =  format_date_d_m_y(now())." 23:59:59";

            $response = $client->request('POST', $url ,[
                'multipart' => [
                    [
                        'name' => 'content',
                        'contents' => $sms_content_template,
                    ],
                    [
                        'name' => 'title',
                        'contents' => 'notification happy birthday',
                    ],
                    [
                        'name' => 'merchant_id',
                        'contents' => 1,
                    ],
                    [
                        'name' => 'start',
                        'contents' => $date_time_send,
                    ],
                    [
                        'name' => 'date_before',
                        'contents' => '0',
                    ],
                    [
                        'name' => 'repeat',
                        'contents' => '0',
                    ],
                    [
                        'name' => 'repeat_on',
                        'contents' => '0',
                    ],
                    [
                        'name' => 'timesend',
                        'contents' => Carbon::parse(now())->addMinute(1)->format('H:i'),
                    ],
                    [
                        'name' => 'type_event',
                        'contents' => 1,
                    ],
                    [
                        'name' => 'event_id',
                        'contents' => 1,
                    ],
                    [
                        'name' => 'end',
                        'contents' => $date_time_end,
                    ],
                    [
                        'name'     => 'upfile',
                        'contents' => fopen($file_url,'r'),
                    ],
                    [
                        'name' => 'status',
                        'contents' => 1,
                    ]

                ],
                'headers' => [
                    'Authorization' => 'Bearer ' .env("SMS_API_KEY"),
                ],
            ]);

            $resp =  (string)$response->getBody();

            $this->info($resp);

        }


        //END SEND NOTIFICATION BY SMS
        $this->info('Cron Successfully!');
    }
}
