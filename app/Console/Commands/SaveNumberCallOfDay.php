<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MainUser;
use GuzzleHttp\Client;

class SaveNumberCallOfDay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SaveNumberCallOfDay';

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
        $time_usa = now()->subHours(14);

        $phone_call_arr = MainUser::whereNotNull('user_phone_call')->select('user_phone_call')->get()->toArray();
        foreach($phone_call_arr as $phone){
            $signature = '5b77ba6d004b711f461ffaf598d432b1';
            $from_date = '2020-02-25';
            $to_date = '2020-02-29';
            $InOutInternal = 'In,Out';
            $extension = 908;
            //GET NUMBER CALL OF TODAY
            try{
                $client = new Client;
                $response = $client->request('GET', env('CALL_LOG_URL').'?Signature='.$signature.'&FromDate='.$from_date.'%2000:00:00&ToDate='.$to_date.'%2023:00:00&InOutInternal='.$InOutInternal.'&Extension='.$extension);
                $body = (string)$response->getBody();
                // return response(['status'=>'success','data'=>$body]);
            }
            catch(\Exception $e){
                \Log::info($e);
                $body = 'error';
            }
        }
            

        $this->info($body);
    }
}
