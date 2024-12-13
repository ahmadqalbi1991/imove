<?php 
namespace App\Http\Controllers;

use Kreait\Firebase\Database;
use App\Http\Controllers\Controller;
use App\Services\UserFBNotificationService;
class TestingController extends Controller
{
    protected $database;
    protected $userFBNotifications;

    public function __construct(Database $database,UserFBNotificationService $userFBNotifications)
    {
        $this->database = $database;
        $this->userFBNotifications = $userFBNotifications;
    }
    public function testToAddNotificationToUser()
        {
            $user=\App\Models\User::find(16);
     
           return  $this->userFBNotifications->addUserNotification($user,[
                'title'=>'adsstatus',
                'message'=>'hi,adasd',
                'description'=>'asdasd',
                'imageURL'=>'https://myiamges.com/4355,jpg',
                'notificationType'=>'status_changed',
                'orderId'=>'898678',
                'status'=>'5',
            ],true);
        }

    public function readDataFromFirbase()
    {
        $snapshot = $this->database->getReference('users/1')->getSnapshot();
        $data = $snapshot->getValue();

        return response()->json($data);
    }


    public function writeDataToFirbase()
    {
        $this->database
            ->getReference('users/1')
            ->set([
                'name' => 'John Doe',
                'email' => 'johndoe@example.com',
                'age' => 30
            ]);

        return response()->json(['message' => 'Data written successfully']);
    }
}
