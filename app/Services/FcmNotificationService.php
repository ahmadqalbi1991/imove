<?php

namespace App\Services;

use Google\Auth\Credentials\ServiceAccountCredentials;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class FcmNotificationService
{
    protected $client;
    protected $projectId;

    public function __construct()
    {
        $this->client = new Client();
        $this->projectId = env('FIREBASE_PROJECT_ID'); // Your Firebase project ID
    }

    public function sendNotification($deviceToken, $title, $body,$dataParams=null)
    {
        $accessToken = $this->getAccessToken();
        // dd($accessToken);

        $data = [
            'message' => [
                'token' => $deviceToken,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => $dataParams ,
            ],
        ];

        try{
            $response = $this->client->post("https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $data,
            ]);
            if(request('fcm_debug')){
               print_r($response->getBody()->getContents()); 
            }
    
        }catch(ClientException  $e){
               // Handle client error (4xx status codes)
    $statusCode = $e->getResponse()->getStatusCode();
    $errorMessage = $e->getMessage();
    $errorBody = $e->getResponse()->getBody()->getContents();


    if(request('fcm_debug')){
        dd( [  $statusCode, $errorMessage,   $errorBody ]); 

    }

            return [  $statusCode, $errorMessage,   $errorBody ];

             

        }
        return json_decode($response->getBody()->getContents());
    }

    private function getAccessToken()
    {
        // dd(storage_path('../'.config('firebase.FIREBASE_CREDENTIALS')));
        // dd(base_path(config('firebase.FIREBASE_CREDENTIALS')));
        $credentials = new ServiceAccountCredentials(
            ['https://www.googleapis.com/auth/firebase.messaging'],
            base_path(config('firebase.FIREBASE_CREDENTIALS'))
        );

        return $credentials->fetchAuthToken()['access_token'];
    }
}
