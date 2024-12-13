<?php
function headers() {
  return array(
      "Authorization: key= ".env('FIREBASE_AUTH_KEY'),
      "Content-Type: application/json",
      "project_id: ".env('FIREBASE_PROJECT_ID')

  );
}
function send_single_notification($fcm_token, $notification, $data, $priority = 'high') {
    $fields = array(
        'notification' => $notification,
        'data'=>$data,
        'content_available' => true,
        'priority' =>  $priority,
        'to' => $fcm_token
         );

     if ( $curl_response=send(json_encode($fields), "https://fcm.googleapis.com/fcm/send") ) {
        return json_decode($curl_response);
    }
    else
        return false;
}

 function send_multicast_notification($fcm_tokens, $notification, $data, $priority = 'high') {
    $fields = array(
        'notification' => $notification,
        'data'=>$data,
        'content_available' => true,
        'priority' =>  $priority,
        'registration_ids' => $fcm_tokens
    );

    if ( $curl_response=send(json_encode($fields), "https://fcm.googleapis.com/fcm/send") ) {
        return json_decode($curl_response);
    }
    else
        return false;
}

 function send_notification($notification_key, $notification, $data, $priority = 'high') {
    $fields = array(
        'notification' => $notification,
        'data'=>$data,
        'content_available' => true,
        'priority' =>  $priority ,
        'to' => $notification_key
    );

    if ( $curl_response=send(json_encode($fields), "https://fcm.googleapis.com/fcm/send") ) {
        return json_decode($curl_response);
   }
   else
        return false;

}

 function send($fields,  $url ="", $headers = array() ) {

    if(empty($url)) $url = FIREBASE_URL;

    $headers = array_merge(headers(), $headers);

    $ch = curl_init();

    if (!$ch)  {
        $curl_error = "Couldn't initialize a cURL handle";
        return false;
    }

    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

    $curl_response = curl_exec($ch);

    if(curl_errno($ch))
        $curl_error = curl_error($ch);

    if ($curl_response == FALSE) {
        return false;
    }
    else {
        $curl_info = curl_getinfo($ch);
        //printr($curl_info);
        curl_close($ch);
        return $curl_response;
    }

}


?>
