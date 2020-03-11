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
        $time_usa = now()->subHours(14)->format('Y-m-d');

        $time_usa = '2020-02-24';

        $phone_call_arr = MainUser::whereNotNull('user_phone_call')->get();
        foreach($phone_call_arr as $phone){
            $InOutInternal = 'Out';
            $extension = $phone->user_phone_call;
            $signature = md5(env('CALL_LOG_KEY').$time_usa.$time_usa.$InOutInternal.$extension);
            //GET NUMBER CALL OF TODAY
            try{
                $client = new Client;
                $response = $client->request('GET', env('CALL_LOG_URL').'?Signature='.$signature.'&FromDate='.$time_usa.'%2000:00:00&ToDate='.$time_usa.'%2023:00:00&InOutInternal='.$InOutInternal.'&Extension='.$extension);
                $body = (string)$response->getBody();
                $dom = new \DomDocument();
                $dom->loadXml($body);
                $rowIndexs = $dom->getElementsByTagName('rowIndex');
                
                foreach($rowIndexs as $id){
                    $amount = $id->textContent;
                }
                if(!isset($amount))
                    $amount = 'empty';
                else{
                    //Update phone log
                    try{
                        $user_phone_log = json_decode($phone->user_phone_log,TRUE);
                        $user_phone_log_today[$time_usa] = $amount;

                        if( is_array($user_phone_log) ){
                            //Remove old log phone - from 2 months ago
                            $old_day = today()->subMonths(2);
                            foreach($user_phone_log as $key => $c){
                                if($key < $old_day){
                                    unset($user_phone_log[$key]);
                                }
                            }
                            $user_phone_log_new = array_merge($user_phone_log,$user_phone_log_today);
                        }
                            
                        else
                            $user_phone_log_new = $user_phone_log_today;
                            

                        $user_phone_log_new_list = json_encode($user_phone_log_new);
                        $phone->update(['user_phone_log'=>$user_phone_log_new_list]);
                    }
                    catch(\Exception $e){
                        \Log::info($e);
                        $amount = $e;
                    }
                }
            }
            catch(\Exception $e){
                \Log::info($e);
                $amount = 'error';
            }
        }
        $this->info($amount);
    }
}
