<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\User;
use App\Models\TempUser;
use Illuminate\Support\Facades\Mail;

class SendRegistrationEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:send_nregistration_email {temp_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Verification Email';

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
     * @return int
     */
    public function handle()
    {
        $user_id =  $this->argument('temp_id');
        $user=TempUser::find($user_id);
        $name=$user->first_name.' '.$user->last_name;
        Mail::send('email_templates.registration_mail', compact('name',), function($message) use ($user) {
            $message->to($user->email);
            //$message->to('sabeeh.hashmi2@gmail.com');
            $message->subject('Registration Confirmation');
            
            
        });
    }
}
