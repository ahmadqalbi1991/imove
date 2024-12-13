<?php

namespace App\Services;

use Kreait\Firebase\Database;
use App\Models\User;

class UserFBNotificationService
{
    protected $database;
    protected $fcm;

    public function __construct(Database $database, FcmNotificationService $fcm)
    {
        $this->database = $database;
        $this->fcm = $fcm;
    }


    public function addUserNotification($users, $data, $sendNotification = false, $stored = false): \Illuminate\Http\JsonResponse
    {
        $batchData = [];

        foreach ($users as $user) {
            if ($user->firebase_user_key && $user->user_device_token) {
                if ($stored) {
                    $batchData['notifications/' . $user->firebase_user_key . "/" . time()] = $data + [
                            'read' => '0',
                            'seen' => '0',
                            'createdAt' => \Carbon\Carbon::now()->setTimezone('UTC')->toDateTimeString()
                        ];
                }

                $this->fcm->sendNotification($user->user_device_token, $data['title'], $data['message'], convert_all_elements_to_string($data));
            }
        }

        if (!empty($batchData)) {
            $this->database->getReference()->update($batchData);
        }

        return response()->json(['message' => 'Data written successfully']);
    }
}
