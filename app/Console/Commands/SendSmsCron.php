<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\SendSmsHelper;
use App\Models\MainSmsSendDetail;

class SendSmsCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sendSmsCron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'send sms';

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
    public function handle(SendSmsHelper $sendSmsHelper,MainSmsSendDetail $mainSmsSendDetail)
    {   
    	$dateNow = get_nowDate('Y-m-d H:i');
    	// \Log::info($dateNow);
    	$data = $mainSmsSendDetail->getByDateTime($dateNow);  

    	foreach ($data as $key => $value) {
    		$sendSmsHelper->send($value->phone, $value->content);		
    	}
        
        $this->info('Execed Successfully');
    }
}
