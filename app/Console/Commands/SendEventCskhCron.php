<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendNotification;
use App\Models\MainEventHoliday;
use App\Models\MainTeamType;
use App\Models\MainTeam;
use Carbon\Carbon;
use App\Models\MainCustomer;

class SendEventCskhCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:SendEventCskhCron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        //SEND BEFORE A MONTH
        $date_after_a_month = today()->addMonth(1)->format('d');
        $month_after_a_month = today()->addMonth(1)->format('m');

        $events = MainEventHoliday::active()->whereDay('date',$date_after_a_month)->whereMonth('date',$month_after_a_month)->get();

        $message = "";
        foreach ($events as $key => $event) {
            $message .= "<span style='color:red;text-transform:uppercase'>".$event->name."</span> at" .$event->date;

        }
        if($message != ""){
            //GET TEAM'S CSKH TEAM
            $cskh_team_type = MainTeamType::where('team_type_name','CSKH')->first();
            $user_team_list = MainTeam::where('team_type',$cskh_team_type->id)->with('getUserOfTeam')->get();

            foreach ($user_team_list as $key => $user_list) {
                // return $user;
                foreach ($user_list->getUserOfTeam as $user) {

                    if($user->user_email != ""){
                        $input = [];
                        $input['subject'] = 'EVENT HOLIDAY NOTIFICATION!';
                        $input['email'] = $user->user_email;
                        $input['name'] = $user->user_nickname;
                        $input['message'] = $message;
                        dispatch(new SendNotification($input))->delay(now()->addSecond(10));
                    }
                }
            }

             //SEND SMS EVENT FOR CUSTOMER
            $customers = MainCustomer::where([['customer_status',1],['customer_phone','!=','']])->get();

            foreach ($customers as $key => $customer) {
                $receiver_total[] = [
                    'name' =>$customer->customer_fullname,
                    'phone'=>$customer->customer_phone,
                    // 'birthday'=>$customer->customer_birthdate,
                ];
            }
             if(!empty($receiver_total)){
            $date = now()->format('Y_m_d_His');

            $file_name = "receiver_sms_list_".$date;

            \Excel::create($file_name,function($excel) use ($receiver_total){

                $excel ->sheet('receiver_list_send_birthday', function ($sheet) use ($receiver_total)
                {
                    $sheet->cell('A1', function($cell) {$cell->setValue('phone');   });
                    $sheet->cell('B1', function($cell) {$cell->setValue('{p2}');   });
                    // $sheet->cell('C1', function($cell) {$cell->setValue('{p3}');   });

                    if (!empty($receiver_total)) {
                        foreach ($receiver_total as $key => $value) {
                            $i= $key+2;
                            if($value['phone'] != ""){
                                $sheet->cell('A'.$i, $value['phone']);
                                $sheet->cell('B'.$i, $value['name']);
                                // $sheet->cell('C'.$i, Carbon::parse($value['birthday'])->format('d/m/Y'));
                            }
                        }
                    }
                });
            })->store('xlsx', false, true);

            $file_url = storage_path('exports/'.$file_name.".xlsx");

            $sms_content_template = "Dear {name}! Cảm ơn quý vị vì vẫn là khách hàng của chúng tôi. Chúng tôi cảm thấy hãnh diện vì điều đó, và chúc quý vị ".$message." vui vẻ và an lành.";

            $url_event = 'pushsms';

            $url = env('SMS_API_URL').$url_event;

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
                        'contents' => Carbon::parse(now())->addMinute(2)->format('H:i'),
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
        }
           

        $this->info('cron sucessfully!');
    }
}
