<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Mail;
use App\Models\MainTeam;

class SendNotificationInvoice implements ShouldQueue
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
    public function handle(){
        $input = $this->input;
        // $name = $input['name'];
        Mail::send('email_theme_2',['name'=>$input['name'] ], function ($message) use($input) {
            $message->from(env('MAIL_USERNAME_INVOICE'), 'DataeGlobal Web Master');
            $message->to($input['email']);
            if(isset($input['email_arr']) && !empty($input['email_arr'])) {
                foreach($input['email_arr'] as $email){
                    $message->cc($email);
                }
            }
            $message->subject($input['subject']);
            if(!empty($input['file_term_service'])){
                foreach ($input['file_term_service'] as $key => $file) {
                    $message->attach(public_path($file)); 
                }
            }
            // $message->attach(public_path('file/review_google.pdf'));
        });
    }
}
