<?php 
namespace App\Helpers;

use Nexmo\Client\Credentials\Basic;
use Nexmo\Client as ClientNexmo;

class SendSmsHelper 
{
	public function send($phone, $content){
		try {
			$basic  = new Basic(env("NEXMO_API_KEY") ,env("NEXMO_API_SECRET"));
			$client = new ClientNexmo($basic);		


			$message = $client->message()->send([
				    'to' => $phone,
				    'from' => '19285823028',
				    'text' => $content
			]);
		} catch (\Exception $e) {
			\Log::info($e);
		}
		

		
	}
}