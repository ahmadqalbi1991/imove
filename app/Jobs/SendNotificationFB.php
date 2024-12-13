<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Kreait\Firebase\Contract\Database;

class SendNotificationFB implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

     protected $data;
     protected $database;

  
    public function __construct($data)
    {
        $this->data = $data;
   
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
          $database = App::make(Database::class);
       dump($this->data['notification_data']);
       switch ($this->data['type']) {
        case 'with_token':
            $database->getReference()->update($this->data['notification_data']);
            break;
            case 'without_token':
                $res = send_single_notification(
                    ...$this->data['notification_data']
                );
                break;
            
        default:
            # code...
            break;
       }


     
    }
}
