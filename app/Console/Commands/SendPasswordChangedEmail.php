<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\User; 
use Illuminate\Support\Facades\Mail;

class SendPasswordChangedEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:send_changepassword_mail {user_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Change Password Email';

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
        $user_id =  $this->argument('user_id');
        $user=User::find($user_id);
        $name=$user->first_name.' '.$user->last_name;
        Mail::send('email_templates.password_changed_mail', compact('name',), function($message) use ($user) {
            $message->to($user->email);
            //$message->to('sabeeh.hashmi2@gmail.com');
            $message->subject('Password Change Successfully');
            
            
        });
    }
}
