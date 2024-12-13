<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Twilio\Rest\Client;
use Twilio\Exceptions\TwilioException;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
if (! function_exists('get_storage_path') ) {
    function get_storage_path( $filename='', $dir='' )
    {
        if ( !empty($filename) ) {

            $upload_dir = config('global.upload_path');
            if (! empty($dir) ) {
                $dir= config("global.{$dir}");
            }
            if ( \Storage::disk(config('global.upload_bucket'))->exists($dir.$filename) ) {
               return \Storage::url("{$dir}{$filename}");
           }
        }


        return '';
    }
}
if (! function_exists('get_uploaded_image_url') ) {
    function get_uploaded_image_url( $filename='', $dir='', $default_file='placeholder.png' )
    {

        if ( !empty($filename) ) {

            $upload_dir = config('global.upload_path');
            if (! empty($dir) ) {
                $dir= config("global.{$dir}");
               
            }

            if ( \Storage::disk(config('global.upload_bucket'))->exists($dir.$filename) ) {
                // return 'https://d3k2qvqsrjpakn.cloudfront.net/moda/public'.\Storage::url("{$dir}{$filename}");
                return \Storage::disk(config('global.upload_bucket'))->url($dir.$filename);
                //return asset(\Storage::url("{$dir}{$filename}"));
           }else{
                
            return asset(\Storage::url("{$dir}{$filename}"));
           }
        }
        if ( !empty($default_file) ) {
            if (! empty($dir) ) {
                $dir= config("global.{$dir}");
            }
            $default_file = asset(\Storage::url("{$dir}{$default_file}"));
        }
        if (! empty($default_file) ) {
            return $default_file;
        }


        return \Storage::url("logo.png");
    }
}

function return_response($status, $code, $message, $validation_errors = [], $oData = [])
{
    $validation_errors = is_array($validation_errors) ? (object)$validation_errors : $validation_errors;
    $oData = is_array($oData) ? (object)$oData : $oData;
    return response()->json([
        'status' => $status,
        'message' => $message,
        'validationErrors' => $validation_errors,
        'oData' => $oData
    ], $code);
}

function convertNumbersToStrings(array $array): array
{
    foreach ($array as $key => $value) {
        if (is_object($value)) {
            $value = (array) $value;
        }

        if ($value === 'send_empty_obj') {
            $array[$key] = (object)[];
        }

        if (is_array($value)) {
            $array[$key] = convertNumbersToStrings($value);
        } elseif (is_numeric($value)) {
            $array[$key] = (string)$value;
        } elseif ($value === null) {
            $array[$key] = '';
        }
    }

    return $array;
}

if (! function_exists('time_ago') ) {
    function time_ago( $datetime, $now=NULL, $timezone='Etc/GMT' )
    {
        if (! $now ) {
            $now = time();
        }
        $timezone_user  = new DateTimeZone($timezone);
        $date           = new DateTime($datetime, $timezone_user);
        $timestamp      = $date->getTimestamp();
        $timespan       = explode(', ', timespan($timestamp, $now));
        $timespan       = $timespan[0] ?? '';
        $timespan       = strtolower($timespan);

        if (! empty($timespan) ) {
            if ( stripos($timespan, 'second') !== FALSE ) {
                $timespan = 'few seconds ago';
            } else {
                $timespan .= " ago";
            }
        }

        return $timespan;
    }
}

function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}

if (! function_exists('get_date_in_timezone') ) {
    function get_date_in_timezone($date, $format="d-M-Y h:i a",$timezone='',$server_time_zone="Etc/GMT")
    {
        if($timezone == ''){
            $timezone = config('global.date_timezone');
        }
        try {
            $timezone_server    = new DateTimeZone($server_time_zone);
            $timezone_user      = new DateTimeZone($timezone);
        }
        catch (Exception $e) {
            $timezone_server    = new DateTimeZone($server_time_zone);
            $timezone_user      = new DateTimeZone($server_time_zone);
        }


        $dt = new DateTime($date, $timezone_server);

        $dt->setTimezone($timezone_user);

        return $dt->format($format);
    }
}
function public_url()
{
    if (config('app.url') == 'http://127.0.0.1:8000') {
        return str_replace('/public', '', config('app.url'));
    }
    return config('app.asset_url');
}

function image_upload($request,$model,$file_name, $mb_file_size = 25)
{

    if($request->file($file_name ))
    {
        $file = $request->file($file_name);
        return  file_save($file,$model, $mb_file_size);
    }
    return ['status' =>false,'link'=>null,'message' => 'Unable to upload file'];
}

if (! function_exists('array_combination') ) {
    function array_combination($arrays, $i = 0)
    {
        if (!isset($arrays[$i])) {
            return array();
        }
        if ($i == count($arrays) - 1) {
            return $arrays[$i];
        }

        // get combinations from subsequent arrays
        $tmp = array_combination($arrays, $i + 1);

        $result = array();

        // concat each array from tmp with each element from $arrays[$i]
        foreach ($arrays[$i] as $v) {
            foreach ($tmp as $t) {
                $result[] = is_array($t) ?
                    array_merge(array($v), $t) :
                    array($v, $t);
            }
        }

        return $result;
    }
}

function file_save($file,$model,$mb_file_size=25)
{
     try {
        $model = str_replace('/','',$model);
        //validateSize
        $precision = 2;
        $size = $file->getSize();
        $size = (int) $size;
        $base = log($size) / log(1024);
        $suffixes = array(' bytes', ' KB', ' MB', ' GB', ' TB');
        $dSize = round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];

        $aSizeArray = explode(' ', $dSize);
        if ($aSizeArray[0] > $mb_file_size && ($aSizeArray[1] == 'MB' || $aSizeArray[1] == 'GB' || $aSizeArray[1] == 'TB')) {
            return ['status' =>false,'link'=>null,'message' => 'Image size should be less than equal '.$mb_file_size.' MB'];
        }
        // rename & upload files to upload folder
                
        $fileName = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs($model,$fileName,config('global.upload_bucket'));
        $image_url = $fileName;
            
        return ['status' =>true,'link'=>$image_url,'message' => 'file uploaded'];

    } catch (\Exception $e) {
        return ['status' =>false,'link'=> null ,'message' => $e->getMessage()];
    }
}

function printr($data){
  echo '<pre>';
  var_dump($data);
  echo '</pre>';
}
function url_title($str, $separator = '-', $lowercase = FALSE)
{
    if ($separator == 'dash')
    {
        $separator = '-';
    }
    else if ($separator == 'underscore')
    {
        $separator = '_';
    }

    $q_separator = preg_quote($separator);

    $trans = array(
        '&.+?;'                 => '',
        '[^a-z0-9 _-]'          => '',
        '\s+'                   => $separator,
        '('.$q_separator.')+'   => $separator
    );

    $str = strip_tags($str);

    foreach ($trans as $key => $val)
    {
        $str = preg_replace("#".$key."#i", $val, $str);
    }

    if ($lowercase === TRUE)
    {
        $str = strtolower($str);
    }

    return trim($str, $separator);
}

function send_email($to, $subject, $mailbody)
{

    require base_path("vendor/autoload.php");
    $mail = new PHPMailer(true);
    try {
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPAuth = true;
        $mail->Username = "info@themoda.com";
        $mail->Password = "xinuoujtnampbkkr";
        $mail->SMTPSecure = "STARTTLS";
        $mail->Port = 587;
        $mail->setFrom("info@themoda.com");
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $mailbody;
        // $mail->SMTPOptions = array(
        //     'ssl' => array(
        //         'verify_peer' => false,
        //         'verify_peer_name' => false,
        //         'allow_self_signed' => true
        //     )
        // );
        if (!$mail->send()) {
            // dd($e->getMessage());
            return 0;
        } else {
            return 1;
        }
    } catch (Exception $e) {
         dd($e->getMessage());
        return 0;
    }
}
function send_normal_SMS($message, $mobile_numbers, $sender_id = "")
{
    return true;
    $username = "teyaar"; //username
    $password = "06046347"; //password
    $sender_id = "smscntry";
    $message_type = "N";
    $delivery_report = "Y";
    $url = "http://www.smscountry.com/SMSCwebservice_Bulk.aspx";
    $proxy_ip = "";
    $proxy_port = "";
    $message_type = "N";
    $message = urlencode($message);
    $sender_id = (!empty($sender_id)) ? $sender_id : $sender_id;
    $ch = curl_init();
    if (!$ch) {
        $curl_error = "Couldn't initialize a cURL handle";
        return false;
    }
    $ret = curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "User=" . $username . "&passwd=" . $password . "&mobilenumber=" . $mobile_numbers . "&message=" . $message . "&sid=" . $sender_id . "&mtype=" . $message_type . "&DR=" . $delivery_report);
    $ret = curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if (!empty($proxy_ip)) {
        $ret = curl_setopt($ch, CURLOPT_PROXY, $proxy_ip . ":" . $proxy_port);
    }
    $curl_response = curl_exec($ch);
    if (curl_errno($ch)) {
        $curl_error = curl_error($ch);
    }
    if (empty($ret)) {
        curl_close($ch);
        // dd('1');
        return false;
    } else {
        $curl_info = curl_getinfo($ch);
        curl_close($ch);
        return true;
    }
}

// function send_normal_SMS($message, $receiverNumber, $sender_id = "")
// {
//     try {
//         $receiverNumber = '+'.str_replace("+","",$receiverNumber);
//         $account_sid = getenv("TWILIO_SID");
//         $auth_token = getenv("TWILIO_TOKEN");
//         $twilio_number = getenv("TWILIO_FROM");
//         $client = new Client($account_sid, $auth_token);
//         $client->messages->create($receiverNumber, [
//             'from' => $twilio_number,
//             'body' => $message]
//         );
//         return 1;
//     } catch (TwilioException $e) {
//         return $e->getMessage();
//         // return 0;
//     }
// }

function convert_all_elements_to_string($data=null){
    if($data != null){
        array_walk_recursive($data, function (&$value, $key) {
            if (! is_object($value) ) {
                //echo $value."<br>";
                $value = (string) $value;
            } else {
                $json = json_encode($value);
                $array = json_decode($json, true);

                array_walk_recursive($array, function (&$obj_val, $obj_key) {
                    $obj_val = (string) $obj_val;
                });

                if (! empty($array) ) {
                    $json = json_encode($array);
                    $value = json_decode($json);
                } else {
                    $value = new stdClass();
                }
            }
        });
    }
    return $data;
}
function thousandsCurrencyFormat($num) {

    if( $num > 1000 ) {
        $x = round($num);
        $x_number_format = number_format($x);
        $x_array = explode(',', $x_number_format);
        $x_parts = array('k', 'm', 'b', 't');
        $x_count_parts = count($x_array) - 1;
        $x_display = $x;
        $x_display = $x_array[0] . ((int) $x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');
        $x_display .= $x_parts[$x_count_parts - 1];
        return $x_display;
    }

    return $num;
}
function order_status($id)
   {
        $status_string = "Pending";
        if($id == config('global.order_status_pending'))
                {
                   $status_string = "Pending";
                }
                if($id == config('global.order_status_accepted'))
                {
                   $status_string = "Order Placed";
                }
                if($id == config('global.order_status_ready_for_delivery'))
                {
                   $status_string = "Ready for Delivery";
                }
                if($id == config('global.order_status_dispatched'))
                {
                   $status_string = "Dispatched";
                }
                if($id == config('global.order_status_delivered'))
                {
                   $status_string = "Delivered";
                }
                if($id == config('global.order_status_cancelled'))
                {
                   $status_string = "Cancelled";
                }
                if($id == config('global.order_status_returned'))
                {
                   $status_string = "Returned";
                }
    return $status_string;
   }

   
   function encryptor($string) {
    $output = false;

    $encrypt_method = "AES-128-CBC";
    //pls set your unique hashing key
    $secret_key = 'muni';
    $secret_iv = 'muni123';

    // hash
    $key = hash('sha256', $secret_key);

    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
    $iv = substr(hash('sha256', $secret_iv), 0, 16);

    //do the encyption given text/string/number

        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);


    return $output;
}

function decryptor( $string) {
    $output = false;

    $encrypt_method = "AES-128-CBC";
    //pls set your unique hashing key
    $secret_key = 'muni';
    $secret_iv = 'muni123';

    // hash
    $key = hash('sha256', $secret_key);

    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
    $iv = substr(hash('sha256', $secret_iv), 0, 16);


        //decrypt the given text/string/number
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);


    return $output;
}


   

function generate_otp(){
  return 1111;
  //return rand(1111,9999);
}


function namespacedXMLToArray($xml)
{
    // One function to both clean the XML string and return an array
    return json_decode(json_encode(simplexml_load_string(removeNamespaceFromXML($xml))), true);
}
function check_permission($module,$permission){
    $userid = Auth::user()->id;
    $privilege = 0;
    if ($userid > 1) {
        $privileges = \App\Models\UserPrivileges::privilege();
        $privileges = json_decode($privileges, true);
        if (!empty($privileges[$module][$permission])) {
            if ($privileges[$module][$permission] == 1) {
                $privilege = 1;
            }
        }
    } else {
        $privilege = 1;
    }
    return $privilege;
}
function retrive_hash_tags($data=''){
    $d = explode(" ",$data);
    $words=[];
    foreach($d as $k){
        if(substr($k,0,1) == '#'){
          $words[]=ltrim($k,'#');
        }

    }
    return $words;
}
function GetDrivingDistance($lat1, $lat2, $long1, $long2)
    {
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$lat1.",".$long1."&destinations=".$lat2.",".$long2."&mode=driving&key=AIzaSyCtugJ9XvE2MvkXCBeynQDFKq-XN_5xsxM";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close($ch);
        $response_a = json_decode($response, true);
        $dist = '-';
        $time = '-';
        if(isset($response_a['rows'][0]['elements'][0]['distance']['text'])){
            $dist = $response_a['rows'][0]['elements'][0]['distance']['text'];
            $time = $response_a['rows'][0]['elements'][0]['duration']['text'];
        }
        return array('distance' => $dist, 'time' => $time);
    }
    function GetDrivingDistanceToMultipleLocations($from_latlong, $destinations)
    {
        $distance_data = file_get_contents('https://maps.googleapis.com/maps/api/distancematrix/json?origins='.$from_latlong.'&destinations='.$destinations.'&mode=driving&key=AIzaSyCtugJ9XvE2MvkXCBeynQDFKq-XN_5xsxM');
        return json_decode($distance_data, true);
    }

    function set_menu_active($menu_type='sub',$check=''){
        $route = \Request::route()->getName();
        $route_list = explode(".",$route);
        if($menu_type == 'main'){
            $result=array_intersect($route_list,$check);
            if(!empty($result)){
                return ' active open';
            }
        }else{
            if(in_array($check,$route_list)){
                return ' active';
            }
        }
     }
    
     function get_user_permission($model,$operation='r'){
        
        $return = false;
        if(Auth::user()->role_id == '1'){
            $return = true;
        }else if(Auth::user()->is_admin_access == 1)
        {   
            $user_permissions = \Session::get('user_permissions');
           
            if(isset($user_permissions[strtolower($model)])){
                $permissions = json_decode($user_permissions[strtolower($model)]??'');
                if(in_array($operation,$permissions)){
                    $return = true;
                }
            }
        }
        return $return;
     }
    
     function all_permission_check($needed_permissions=[],$operation='r'){
        $return  = false;
        foreach($needed_permissions as $permission){
            $resp = get_user_permission($permission,$operation);
            if($resp == true && $return == false){
                $return = true;
            }
        }
        return $return;
     }
     function create_plink($text) {
        $ptext = preg_replace('#[ -]+#', '-', trim(strtolower($text))); 
        $ptext = str_replace("&", "and", $ptext);
    
        $ptext = preg_replace('/[^A-Za-z0-9\-]/', '', $ptext);
    
        $ptext = preg_replace('/-+/', '-', $ptext); // Replaces multiple hyphens with single one.
        return $ptext;
    }

    function get_countries(){
        $countries = DB::table('countries')->where('country_status',1)->where('deleted_at',null)->pluck('country_name');
        if(count($countries) > 0){
            $countries = $countries->toArray();
        }else{
            $countries = [];
        }
        return $countries;
    }

    function dial_codes(){
        // data from https://gist.github.com/andyj/7108917

        return $array = [
            '44' => 'UK (+44)',
            '1' => 'USA (+1)',
            '213' => 'Algeria (+213)',
            '376' => 'Andorra (+376)',
            '244' => 'Angola (+244)',
            '1264' => 'Anguilla (+1264)',
            '1268' => 'Antigua & Barbuda (+1268)',
            '54' => 'Argentina (+54)',
            '374' => 'Armenia (+374)',
            '297' => 'Aruba (+297)',
            '61' => 'Australia (+61)',
            '43' => 'Austria (+43)',
            '994' => 'Azerbaijan (+994)',
            '1242' => 'Bahamas (+1242)',
            '973' => 'Bahrain (+973)',
            '880' => 'Bangladesh (+880)',
            '1246' => 'Barbados (+1246)',
            '375' => 'Belarus (+375)',
            '32' => 'Belgium (+32)',
            '501' => 'Belize (+501)',
            '229' => 'Benin (+229)',
            '1441' => 'Bermuda (+1441)',
            '975' => 'Bhutan (+975)',
            '591' => 'Bolivia (+591)',
            '387' => 'Bosnia Herzegovina (+387)',
            '267' => 'Botswana (+267)',
            '55' => 'Brazil (+55)',
            '673' => 'Brunei (+673)',
            '359' => 'Bulgaria (+359)',
            '226' => 'Burkina Faso (+226)',
            '257' => 'Burundi (+257)',
            '855' => 'Cambodia (+855)',
            '237' => 'Cameroon (+237)',
            '1' => 'Canada (+1)',
            '238' => 'Cape Verde Islands (+238)',
            '1345' => 'Cayman Islands (+1345)',
            '236' => 'Central African Republic (+236)',
            '56' => 'Chile (+56)',
            '86' => 'China (+86)',
            '57' => 'Colombia (+57)',
            '269' => 'Comoros (+269)',
            '242' => 'Congo (+242)',
            '682' => 'Cook Islands (+682)',
            '506' => 'Costa Rica (+506)',
            '385' => 'Croatia (+385)',
            '53' => 'Cuba (+53)',
            '90392' => 'Cyprus North (+90392)',
            '357' => 'Cyprus South (+357)',
            '42' => 'Czech Republic (+42)',
            '45' => 'Denmark (+45)',
            '253' => 'Djibouti (+253)',
            '1809' => 'Dominica (+1809)',
            '1809' => 'Dominican Republic (+1809)',
            '593' => 'Ecuador (+593)',
            '20' => 'Egypt (+20)',
            '503' => 'El Salvador (+503)',
            '240' => 'Equatorial Guinea (+240)',
            '291' => 'Eritrea (+291)',
            '372' => 'Estonia (+372)',
            '251' => 'Ethiopia (+251)',
            '500' => 'Falkland Islands (+500)',
            '298' => 'Faroe Islands (+298)',
            '679' => 'Fiji (+679)',
            '358' => 'Finland (+358)',
            '33' => 'France (+33)',
            '594' => 'French Guiana (+594)',
            '689' => 'French Polynesia (+689)',
            '241' => 'Gabon (+241)',
            '220' => 'Gambia (+220)',
            '7880' => 'Georgia (+7880)',
            '49' => 'Germany (+49)',
            '233' => 'Ghana (+233)',
            '350' => 'Gibraltar (+350)',
            '30' => 'Greece (+30)',
            '299' => 'Greenland (+299)',
            '1473' => 'Grenada (+1473)',
            '590' => 'Guadeloupe (+590)',
            '671' => 'Guam (+671)',
            '502' => 'Guatemala (+502)',
            '224' => 'Guinea (+224)',
            '245' => 'Guinea - Bissau (+245)',
            '592' => 'Guyana (+592)',
            '509' => 'Haiti (+509)',
            '504' => 'Honduras (+504)',
            '852' => 'Hong Kong (+852)',
            '36' => 'Hungary (+36)',
            '354' => 'Iceland (+354)',
            '91' => 'India (+91)',
            '62' => 'Indonesia (+62)',
            '98' => 'Iran (+98)',
            '964' => 'Iraq (+964)',
            '353' => 'Ireland (+353)',
            '972' => 'Israel (+972)',
            '39' => 'Italy (+39)',
            '1876' => 'Jamaica (+1876)',
            '81' => 'Japan (+81)',
            '962' => 'Jordan (+962)',
            '7' => 'Kazakhstan (+7)',
            '254' => 'Kenya (+254)',
            '686' => 'Kiribati (+686)',
            '850' => 'Korea North (+850)',
            '82' => 'Korea South (+82)',
            '965' => 'Kuwait (+965)',
            '996' => 'Kyrgyzstan (+996)',
            '856' => 'Laos (+856)',
            '371' => 'Latvia (+371)',
            '961' => 'Lebanon (+961)',
            '266' => 'Lesotho (+266)',
            '231' => 'Liberia (+231)',
            '218' => 'Libya (+218)',
            '417' => 'Liechtenstein (+417)',
            '370' => 'Lithuania (+370)',
            '352' => 'Luxembourg (+352)',
            '853' => 'Macao (+853)',
            '389' => 'Macedonia (+389)',
            '261' => 'Madagascar (+261)',
            '265' => 'Malawi (+265)',
            '60' => 'Malaysia (+60)',
            '960' => 'Maldives (+960)',
            '223' => 'Mali (+223)',
            '356' => 'Malta (+356)',
            '692' => 'Marshall Islands (+692)',
            '596' => 'Martinique (+596)',
            '222' => 'Mauritania (+222)',
            '269' => 'Mayotte (+269)',
            '52' => 'Mexico (+52)',
            '691' => 'Micronesia (+691)',
            '373' => 'Moldova (+373)',
            '377' => 'Monaco (+377)',
            '976' => 'Mongolia (+976)',
            '1664' => 'Montserrat (+1664)',
            '212' => 'Morocco (+212)',
            '258' => 'Mozambique (+258)',
            '95' => 'Myanmar (+95)',
            '264' => 'Namibia (+264)',
            '674' => 'Nauru (+674)',
            '977' => 'Nepal (+977)',
            '31' => 'Netherlands (+31)',
            '687' => 'New Caledonia (+687)',
            '64' => 'New Zealand (+64)',
            '505' => 'Nicaragua (+505)',
            '227' => 'Niger (+227)',
            '234' => 'Nigeria (+234)',
            '683' => 'Niue (+683)',
            '672' => 'Norfolk Islands (+672)',
            '670' => 'Northern Marianas (+670)',
            '47' => 'Norway (+47)',
            '968' => 'Oman (+968)',
            '680' => 'Palau (+680)',
            '507' => 'Panama (+507)',
            '675' => 'Papua New Guinea (+675)',
            '595' => 'Paraguay (+595)',
            '51' => 'Peru (+51)',
            '63' => 'Philippines (+63)',
            '48' => 'Poland (+48)',
            '351' => 'Portugal (+351)',
            '1787' => 'Puerto Rico (+1787)',
            '974' => 'Qatar (+974)',
            '262' => 'Reunion (+262)',
            '40' => 'Romania (+40)',
            '7' => 'Russia (+7)',
            '250' => 'Rwanda (+250)',
            '378' => 'San Marino (+378)',
            '239' => 'Sao Tome & Principe (+239)',
            '966' => 'Saudi Arabia (+966)',
            '221' => 'Senegal (+221)',
            '381' => 'Serbia (+381)',
            '248' => 'Seychelles (+248)',
            '232' => 'Sierra Leone (+232)',
            '65' => 'Singapore (+65)',
            '421' => 'Slovak Republic (+421)',
            '386' => 'Slovenia (+386)',
            '677' => 'Solomon Islands (+677)',
            '252' => 'Somalia (+252)',
            '27' => 'South Africa (+27)',
            '34' => 'Spain (+34)',
            '94' => 'Sri Lanka (+94)',
            '290' => 'St. Helena (+290)',
            '1869' => 'St. Kitts (+1869)',
            '1758' => 'St. Lucia (+1758)',
            '249' => 'Sudan (+249)',
            '597' => 'Suriname (+597)',
            '268' => 'Swaziland (+268)',
            '46' => 'Sweden (+46)',
            '41' => 'Switzerland (+41)',
            '963' => 'Syria (+963)',
            '886' => 'Taiwan (+886)',
            '7' => 'Tajikstan (+7)',
            '66' => 'Thailand (+66)',
            '228' => 'Togo (+228)',
            '676' => 'Tonga (+676)',
            '1868' => 'Trinidad & Tobago (+1868)',
            '216' => 'Tunisia (+216)',
            '90' => 'Turkey (+90)',
            '7' => 'Turkmenistan (+7)',
            '993' => 'Turkmenistan (+993)',
            '1649' => 'Turks & Caicos Islands (+1649)',
            '688' => 'Tuvalu (+688)',
            '256' => 'Uganda (+256)',
            '380' => 'Ukraine (+380)',
            '971' => 'United Arab Emirates (+971)',
            '598' => 'Uruguay (+598)',
            '7' => 'Uzbekistan (+7)',
            '678' => 'Vanuatu (+678)',
            '379' => 'Vatican City (+379)',
            '58' => 'Venezuela (+58)',
            '84' => 'Vietnam (+84)',
            '84' => 'Virgin Islands - British (+1284)',
            '84' => 'Virgin Islands - US (+1340)',
            '681' => 'Wallis & Futuna (+681)',
            '969' => 'Yemen (North)(+969)',
            '967' => 'Yemen (South)(+967)',
            '260' => 'Zambia (+260)',
            '263' => 'Zimbabwe (+263)',
        ];
    }

    function get_categories(){
        $categories = Category::where('status','active')->get();
        return $categories;
    }

    function get_account_types(){
        $account_types = DB::table('account_types')->get();
        return $account_types;
    }

    function deligate_attributes(){

       return [
            'truck' => [
                'name' => 'truck',
                'label' => 'Truck'
            ],
            'weight' => [
                 'input_type' => 'text',
                 'placeholder' => 'Weight   kg',
                 'label' => 'Weight   kg',
                 'name'       => 'weight',
                 'fields'     => '1',   
                ],

            'no_of_crtns' => [
                 'input_type' => 'text',
                 'placeholder' => 'No of Cartns',
                 'label' => 'No of Cartns',
                 'name'       => 'no_of_crts',
                 'fields'     => '1',   
                ],

            'crt_dimension' => [
                 'input_type' => 'text',
                 'placeholder' => 'Cartn Dimension',
                 'label' => 'Cartn Dimension',
                 'name'       => 'crt_dimension',
                 'fields'     => '1',   
                ],

            'no_of_pallets' => [
                 'input_type' => 'text',
                 'placeholder' => 'No of Pallets',
                 'label' => 'No of Pallets',
                 'name'       => 'no_of_pallets',
                 'fields'     => '1',   
                ],

            'item' => [
                 'input_type' => 'text',
                 'placeholder' => 'Item',
                 'label' => 'Item',
                 'name'       => 'item',
                 'fields'     => '1',   
                ],

            'weight_pallet' => [
                 'input_type' => 'text',
                 'placeholder' => 'Weight Pallet  kg',
                 'label' => 'Weight Pallet  kg',
                 'name'       => 'weight_pallet',
                 'fields'     => '1',   
                ],            

            'total_weight' => [
                 'input_type' => 'text',
                 'placeholder' => 'Total Weight   kg',
                 'label' => 'Total Weight   kg',
                 'name'       => 'total_weight',
                 'fields'     => '1',   
                ],

            'total_item_value' => [
                 'input_type' => 'text',
                 'placeholder' => 'Total Item Value',
                 'label' => 'Total Item Value',
                 'name'       => 'total_item_value',
                 'fields'     => '1',   
                ],                    

            'pallet_dimension' => [
                 'input_type' => 'text',
                 'placeholder' => 'Pallet Dimensions',
                 'label' => 'Pallet Dimensions',
                 'name'       => 'pallet_dimension',
                 'fields'     => '1',   
                ],

            'size' => [
                 'input_type' => 'text',
                 'placeholder' => '',
                 'label' => 'Size',
                 'name'       => 'size[]',
                 'fields'     => '3',   
                ],        

        ];
    }

    function deligate_attribute_values($attribute){

       $attributes = [
            'truck' => [
                'name' => 'truck',
                'label' => 'Truck'
            ],
            'weight' => [
                 'input_type' => 'text',
                 'placeholder' => 'Weight   kg',
                 'label' => 'Weight   kg',
                 'name'       => 'weight',
                 'fields'     => '1',   
                ],

            'no_of_crtns' => [
                 'input_type' => 'text',
                 'placeholder' => 'No of Cartns',
                 'label' => 'No of Cartns',
                 'name'       => 'no_of_crts',
                 'fields'     => '1',   
                ],

            'crt_dimension' => [
                 'input_type' => 'text',
                 'placeholder' => 'Cartn Dimension',
                 'label' => 'Cartn Dimension',
                 'name'       => 'crt_dimension',
                 'fields'     => '1',   
                ],

            'no_of_pallets' => [
                 'input_type' => 'text',
                 'placeholder' => 'No of Pallets',
                 'label' => 'No of Pallets',
                 'name'       => 'no_of_pallets',
                 'fields'     => '1',   
                ],

            'item' => [
                 'input_type' => 'text',
                 'placeholder' => 'Item',
                 'label' => 'Item',
                 'name'       => 'item',
                 'fields'     => '1',   
                ],

            'weight_pallet' => [
                 'input_type' => 'text',
                 'placeholder' => 'Weight Pallet  kg',
                 'label' => 'Weight Pallet  kg',
                 'name'       => 'weight_pallet',
                 'fields'     => '1',   
                ],            

            'total_weight' => [
                 'input_type' => 'text',
                 'placeholder' => 'Total Weight   kg',
                 'label' => 'Total Weight   kg',
                 'name'       => 'total_weight',
                 'fields'     => '1',   
                ],

            'total_item_value' => [
                 'input_type' => 'text',
                 'placeholder' => 'Total Item Value',
                 'label' => 'Total Item Value',
                 'name'       => 'total_item_value',
                 'fields'     => '1',   
                ],                    

            'pallet_dimension' => [
                 'input_type' => 'text',
                 'placeholder' => 'Pallet Dimensions',
                 'label' => 'Pallet Dimensions',
                 'name'       => 'pallet_dimension',
                 'fields'     => '1',   
                ],

            'size' => [
                 'input_type' => 'text',
                 'placeholder' => '',
                 'label' => 'Size',
                 'name'       => 'size[]',
                 'fields'     => '3',   
                ],        
        ];

        return $attributes[$attribute] ?? array();

    }

    function get_earned_amount($total_amount,$qouted_amount){
        $earned = ($total_amount - $qouted_amount);
        return $earned; 
    }

    function get_commission_amount($qouted_amount,$comission){
        $commission_amout = ($qouted_amount * $comission)/100;
        return $commission_amout; 
    }

    function get_total_calculate($qouted_amount,$comission){
        $commission_amout = ($qouted_amount * $comission)/100;
        $total = $commission_amout + $qouted_amount;
        return $total; 
    }

    function get_total_amount($qouted_amount,$comission){
        $commission_amout = ($qouted_amount * $comission)/100;
        $total = $commission_amout + $qouted_amount;

        return number_format($total,3);
    }    

    function get_booking_status($booking_status,$statusonly=false){
        $status = '';
        $status_color = '';
        $data['booking_status'] = $booking_status;
        if($data['booking_status'] == 'pending'){
            $status = 'PENDING';
            $status_color = 'secondary';
        }
        else if($data['booking_status'] == 'qouted'){
            $status = 'QUOTED';
            $status_color = 'warning';
        }
        else if($data['booking_status'] == 'accepted'){
            $status = 'ACCEPTED';
            $status_color = 'success';
        }
        else if($data['booking_status'] == 'journey_started'){
            $status = 'JOURNEY STARTED';
            $status_color = 'info';
        }
        else if($data['booking_status'] == 'item_collected'){
            $status = 'ITEM COLLECTED';
            $status_color = 'info';
        }
        else if($data['booking_status'] == 'on_the_way'){
            $status = 'On THE WAY';
            $status_color = 'info';
        }
        else if($data['booking_status'] == 'border_crossing'){
            $status = 'BORDER CLEARNACE';
            $status_color = 'info';
        }
        else if($data['booking_status'] == 'custom_clearance'){
            $status = 'CUSTOM CLEARANCE';
            $status_color = 'info';
        }
        else if($data['booking_status'] == 'delivered'){
            $status = 'DELIVERED';
            $status_color = 'primary';
        }

        $html = '';

        if($statusonly){
            return $status;
        }
        $html .=    '<span class="badge badge-'.$status_color.'">
                            '. $status.'</span>';
        return $html;
    }
    function get_booking_status_string($booking_status) {
        $statuses = [
            1 => 'PENDING',
            2 => 'QUOTED',
            3 => 'ACCEPTED',
            4 => 'JOURNEY STARTED',
            5 => 'ITEM COLLECTED',
            6 => 'ON THE WAY',
            7 => 'BORDER CLEARANCE',
            8 => 'CUSTOM CLEARANCE',
            9 => 'DELIVERED'
        ];
        
        return $statuses[$booking_status] ?? 'UNKNOWN STATUS';
    }
    
    function get_driver_types()
    {
        return [];
    }
    function booking_status($status_id)
    {
        $status = "Waiting for quote";

        if($status_id == 1)
        {
            $status = "Quote received";
        }
        
        if($status_id == 2)
        {
            $status = "Quote Accepted";
        }
        if($status_id == 3)
        {
            $status = "On the way pickup";
        }
        if($status_id == 4)
        {
            $status = "On the way drop off";
        }
        if($status_id == 5)
        {
            $status = "Completed";
        }
       
        return $status;
    }

    function do_status($id)
    {
        $status = "";
        if($id == 0)
        {
            $status = 4;
        }
        if($id == 1)
        {
            $status = 6;
        }
        if($id == 4)
        {
            $status = 8;
        }
        return $status;
    }
   
    function order_type($id)
    {
        $status = "";
        if($id == 0)
        {
            $status = "Pick Up";
        }
        if($id == 1)
        {
            $status = "Pick Up";
        }
        if($id == 4)
        {
            $status = "Pick Up";
        }
        if($id == 5)
        {
            $status = "Drop Off";
        }
        if($id == 6)
        {
            $status = "Drop Off";
        }
        if($id == 8)
        {
            $status = "Drop Off";
        }
       
        return $status;
    }
    if (!function_exists('get_otp')) {
        function get_otp()
        {
            return generate_otp();
        }
    }
    function login_message()
{
    return 'Current login session has been expired. Please login again.';
}
function replace_plus($phone_number)
{
     $new_number = str_replace("+", "", $phone_number);

      $new_number = "+" . $new_number;

      return $new_number;
}

function payment_type($id)
    {
        $status = "";
        if($id == 1)
        {
            $status = "Card";
        }
        if($id == 4)
        {
            $status = "Cash On Delivery";
        }
      
        return $status;
    }

    function distanceCalculation($point1_lat, $point1_long, $point2_lat, $point2_long, $unit = 'km', $decimals = 2) {
        // Convert degrees to radians
     
        
        if(empty($point1_lat) || empty($point1_long) || empty($point2_lat) || empty($point2_long))
        {
            return 0;
        }
        $earthRadius = 6371; // Earth radius in kilometers
      
        $lat1 = deg2rad($point1_lat);
        $lon1 = deg2rad($point1_long);
        $lat2 = deg2rad($point2_lat);
        $lon2 = deg2rad($point2_long);
      
        $deltaLat = $lat2 - $lat1;
        $deltaLon = $lon2 - $lon1;
      
        $a = sin($deltaLat / 2) * sin($deltaLat / 2) + cos($lat1) * cos($lat2) * sin($deltaLon / 2) * sin($deltaLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
      
        $distance = $earthRadius * $c;
      
        // Convert to desired unit if specified
        if ($unit == 'm') {
          $distance *= 1000;
        } else if ($unit != 'km') {
          throw new InvalidArgumentException("Invalid unit specified: $unit");
        }
      
        return round($distance, $decimals);
      }
      if (!function_exists('web_date_in_timezone')) {
        function web_date_in_timezone($date, $format = "d M Y h:i A", $server_time_zone = "Asia/Dubai")
        {
            $timezone = session('user_timezone');
            if (!$timezone) {
                $timezone = $server_time_zone;
            }
            $timezone_server    = new DateTimeZone($server_time_zone);
            $timezone_user      = new DateTimeZone($timezone);
            $dt = new DateTime($date, $timezone_server);
            $dt->setTimezone($timezone_user);
            return $dt->format($format);
        }
    }

    function get_countries_list(){
        $countryDialCodes = [
            'flag-icon-gb' => ['dial_code' => '44', 'country' => 'United Kingdom'],
            'flag-icon-us' => ['dial_code' => '1', 'country' => 'United States'],
            'flag-icon-dz' => ['dial_code' => '213', 'country' => 'Algeria'],
            'flag-icon-ad' => ['dial_code' => '376', 'country' => 'Andorra'],
            'flag-icon-ao' => ['dial_code' => '244', 'country' => 'Angola'],
            'flag-icon-ai' => ['dial_code' => '1264', 'country' => 'Anguilla'],
            'flag-icon-ag' => ['dial_code' => '1268', 'country' => 'Antigua and Barbuda'],
            'flag-icon-ar' => ['dial_code' => '54', 'country' => 'Argentina'],
            'flag-icon-am' => ['dial_code' => '374', 'country' => 'Armenia'],
            'flag-icon-aw' => ['dial_code' => '297', 'country' => 'Aruba'],
            'flag-icon-au' => ['dial_code' => '61', 'country' => 'Australia'],
            'flag-icon-at' => ['dial_code' => '43', 'country' => 'Austria'],
            'flag-icon-az' => ['dial_code' => '994', 'country' => 'Azerbaijan'],
            'flag-icon-bs' => ['dial_code' => '1242', 'country' => 'Bahamas'],
            'flag-icon-bh' => ['dial_code' => '973', 'country' => 'Bahrain'],
            'flag-icon-bd' => ['dial_code' => '880', 'country' => 'Bangladesh'],
            'flag-icon-bb' => ['dial_code' => '1246', 'country' => 'Barbados'],
            'flag-icon-by' => ['dial_code' => '375', 'country' => 'Belarus'],
            'flag-icon-be' => ['dial_code' => '32', 'country' => 'Belgium'],
            'flag-icon-bz' => ['dial_code' => '501', 'country' => 'Belize'],
            'flag-icon-bj' => ['dial_code' => '229', 'country' => 'Benin'],
            'flag-icon-bm' => ['dial_code' => '1441', 'country' => 'Bermuda'],
            'flag-icon-bt' => ['dial_code' => '975', 'country' => 'Bhutan'],
            'flag-icon-bo' => ['dial_code' => '591', 'country' => 'Bolivia'],
            'flag-icon-ba' => ['dial_code' => '387', 'country' => 'Bosnia and Herzegovina'],
            'flag-icon-bw' => ['dial_code' => '267', 'country' => 'Botswana'],
            'flag-icon-br' => ['dial_code' => '55', 'country' => 'Brazil'],
            'flag-icon-bn' => ['dial_code' => '673', 'country' => 'Brunei'],
            'flag-icon-bg' => ['dial_code' => '359', 'country' => 'Bulgaria'],
            'flag-icon-bf' => ['dial_code' => '226', 'country' => 'Burkina Faso'],
            'flag-icon-bi' => ['dial_code' => '257', 'country' => 'Burundi'],
            'flag-icon-kh' => ['dial_code' => '855', 'country' => 'Cambodia'],
            'flag-icon-cm' => ['dial_code' => '237', 'country' => 'Cameroon'],
            'flag-icon-cv' => ['dial_code' => '238', 'country' => 'Cape Verde'],
            'flag-icon-ky' => ['dial_code' => '1345', 'country' => 'Cayman Islands'],
            'flag-icon-cf' => ['dial_code' => '236', 'country' => 'Central African Republic'],
            'flag-icon-cl' => ['dial_code' => '56', 'country' => 'Chile'],
            'flag-icon-cn' => ['dial_code' => '86', 'country' => 'China'],
            'flag-icon-co' => ['dial_code' => '57', 'country' => 'Colombia'],
            'flag-icon-km' => ['dial_code' => '269', 'country' => 'Comoros'],
            'flag-icon-cg' => ['dial_code' => '242', 'country' => 'Congo'],
            'flag-icon-ck' => ['dial_code' => '682', 'country' => 'Cook Islands'],
            'flag-icon-cr' => ['dial_code' => '506', 'country' => 'Costa Rica'],
            'flag-icon-hr' => ['dial_code' => '385', 'country' => 'Croatia'],
            'flag-icon-cu' => ['dial_code' => '53', 'country' => 'Cuba'],
            'flag-icon-cy' => ['dial_code' => '357', 'country' => 'Cyprus'],
            'flag-icon-cz' => ['dial_code' => '42', 'country' => 'Czech Republic'],
            'flag-icon-dk' => ['dial_code' => '45', 'country' => 'Denmark'],
            'flag-icon-dj' => ['dial_code' => '253', 'country' => 'Djibouti'],
            'flag-icon-do' => ['dial_code' => '1809', 'country' => 'Dominican Republic'],
            'flag-icon-ec' => ['dial_code' => '593', 'country' => 'Ecuador'],
            'flag-icon-eg' => ['dial_code' => '20', 'country' => 'Egypt'],
            'flag-icon-sv' => ['dial_code' => '503', 'country' => 'El Salvador'],
            'flag-icon-gq' => ['dial_code' => '240', 'country' => 'Equatorial Guinea'],
            'flag-icon-er' => ['dial_code' => '291', 'country' => 'Eritrea'],
            'flag-icon-ee' => ['dial_code' => '372', 'country' => 'Estonia'],
            'flag-icon-et' => ['dial_code' => '251', 'country' => 'Ethiopia'],
            'flag-icon-fk' => ['dial_code' => '500', 'country' => 'Falkland Islands'],
            'flag-icon-fo' => ['dial_code' => '298', 'country' => 'Faroe Islands'],
            'flag-icon-fj' => ['dial_code' => '679', 'country' => 'Fiji'],
            'flag-icon-fi' => ['dial_code' => '358', 'country' => 'Finland'],
            'flag-icon-fr' => ['dial_code' => '33', 'country' => 'France'],
            'flag-icon-gf' => ['dial_code' => '594', 'country' => 'French Guiana'],
            'flag-icon-pf' => ['dial_code' => '689', 'country' => 'French Polynesia'],
            'flag-icon-ga' => ['dial_code' => '241', 'country' => 'Gabon'],
            'flag-icon-gm' => ['dial_code' => '220', 'country' => 'Gambia'],
            'flag-icon-de' => ['dial_code' => '49', 'country' => 'Germany'],
            'flag-icon-gh' => ['dial_code' => '233', 'country' => 'Ghana'],
            'flag-icon-gi' => ['dial_code' => '350', 'country' => 'Gibraltar'],
            'flag-icon-gr' => ['dial_code' => '30', 'country' => 'Greece'],
            'flag-icon-gl' => ['dial_code' => '299', 'country' => 'Greenland'],
            'flag-icon-gd' => ['dial_code' => '1473', 'country' => 'Grenada'],
            'flag-icon-gp' => ['dial_code' => '590', 'country' => 'Guadeloupe'],
            'flag-icon-gu' => ['dial_code' => '671', 'country' => 'Guam'],
            'flag-icon-gt' => ['dial_code' => '502', 'country' => 'Guatemala'],
            'flag-icon-gn' => ['dial_code' => '224', 'country' => 'Guinea'],
            'flag-icon-gw' => ['dial_code' => '245', 'country' => 'Guinea-Bissau'],
            'flag-icon-gy' => ['dial_code' => '592', 'country' => 'Guyana'],
            'flag-icon-ht' => ['dial_code' => '509', 'country' => 'Haiti'],
            'flag-icon-hn' => ['dial_code' => '504', 'country' => 'Honduras'],
            'flag-icon-hk' => ['dial_code' => '852', 'country' => 'Hong Kong'],
            'flag-icon-hu' => ['dial_code' => '36', 'country' => 'Hungary'],
            'flag-icon-is' => ['dial_code' => '354', 'country' => 'Iceland'],
            'flag-icon-in' => ['dial_code' => '91', 'country' => 'India'],
            'flag-icon-id' => ['dial_code' => '62', 'country' => 'Indonesia'],
            'flag-icon-ir' => ['dial_code' => '98', 'country' => 'Iran'],
            'flag-icon-iq' => ['dial_code' => '964', 'country' => 'Iraq'],
            'flag-icon-ie' => ['dial_code' => '353', 'country' => 'Ireland'],
            'flag-icon-il' => ['dial_code' => '972', 'country' => 'Israel'],
            'flag-icon-it' => ['dial_code' => '39', 'country' => 'Italy'],
            'flag-icon-jm' => ['dial_code' => '1876', 'country' => 'Jamaica'],
            'flag-icon-jp' => ['dial_code' => '81', 'country' => 'Japan'],
            'flag-icon-jo' => ['dial_code' => '962', 'country' => 'Jordan'],
            'flag-icon-kz' => ['dial_code' => '7', 'country' => 'Kazakhstan'],
            'flag-icon-ke' => ['dial_code' => '254', 'country' => 'Kenya'],
            'flag-icon-ki' => ['dial_code' => '686', 'country' => 'Kiribati'],
            'flag-icon-kp' => ['dial_code' => '850', 'country' => 'North Korea'],
            'flag-icon-kr' => ['dial_code' => '82', 'country' => 'South Korea'],
            'flag-icon-kw' => ['dial_code' => '965', 'country' => 'Kuwait'],
            'flag-icon-kg' => ['dial_code' => '996', 'country' => 'Kyrgyzstan'],
            'flag-icon-la' => ['dial_code' => '856', 'country' => 'Laos'],
            'flag-icon-lv' => ['dial_code' => '371', 'country' => 'Latvia'],
            'flag-icon-lb' => ['dial_code' => '961', 'country' => 'Lebanon'],
            'flag-icon-ls' => ['dial_code' => '266', 'country' => 'Lesotho'],
            'flag-icon-lr' => ['dial_code' => '231', 'country' => 'Liberia'],
            'flag-icon-ly' => ['dial_code' => '218', 'country' => 'Libya'],
            'flag-icon-li' => ['dial_code' => '423', 'country' => 'Liechtenstein'],
            'flag-icon-lt' => ['dial_code' => '370', 'country' => 'Lithuania'],
            'flag-icon-lu' => ['dial_code' => '352', 'country' => 'Luxembourg'],
            'flag-icon-mo' => ['dial_code' => '853', 'country' => 'Macao'],
            'flag-icon-mk' => ['dial_code' => '389', 'country' => 'North Macedonia'],
            'flag-icon-mg' => ['dial_code' => '261', 'country' => 'Madagascar'],
            'flag-icon-mw' => ['dial_code' => '265', 'country' => 'Malawi'],
            'flag-icon-my' => ['dial_code' => '60', 'country' => 'Malaysia'],
            'flag-icon-mv' => ['dial_code' => '960', 'country' => 'Maldives'],
            'flag-icon-ml' => ['dial_code' => '223', 'country' => 'Mali'],
            'flag-icon-mt' => ['dial_code' => '356', 'country' => 'Malta'],
            'flag-icon-mh' => ['dial_code' => '692', 'country' => 'Marshall Islands'],
            'flag-icon-mq' => ['dial_code' => '596', 'country' => 'Martinique'],
            'flag-icon-mr' => ['dial_code' => '222', 'country' => 'Mauritania'],
            'flag-icon-mx' => ['dial_code' => '52', 'country' => 'Mexico'],
            'flag-icon-fm' => ['dial_code' => '691', 'country' => 'Micronesia'],
            'flag-icon-md' => ['dial_code' => '373', 'country' => 'Moldova'],
            'flag-icon-mc' => ['dial_code' => '377', 'country' => 'Monaco'],
            'flag-icon-mn' => ['dial_code' => '976', 'country' => 'Mongolia'],
            'flag-icon-ms' => ['dial_code' => '1664', 'country' => 'Montserrat'],
            'flag-icon-ma' => ['dial_code' => '212', 'country' => 'Morocco'],
            'flag-icon-mz' => ['dial_code' => '258', 'country' => 'Mozambique'],
            'flag-icon-mm' => ['dial_code' => '95', 'country' => 'Myanmar'],
            'flag-icon-na' => ['dial_code' => '264', 'country' => 'Namibia'],
            'flag-icon-nr' => ['dial_code' => '674', 'country' => 'Nauru'],
            'flag-icon-np' => ['dial_code' => '977', 'country' => 'Nepal'],
            'flag-icon-nl' => ['dial_code' => '31', 'country' => 'Netherlands'],
            'flag-icon-nc' => ['dial_code' => '687', 'country' => 'New Caledonia'],
            'flag-icon-nz' => ['dial_code' => '64', 'country' => 'New Zealand'],
            'flag-icon-ni' => ['dial_code' => '505', 'country' => 'Nicaragua'],
            'flag-icon-ne' => ['dial_code' => '227', 'country' => 'Niger'],
            'flag-icon-ng' => ['dial_code' => '234', 'country' => 'Nigeria'],
            'flag-icon-nu' => ['dial_code' => '683', 'country' => 'Niue'],
            'flag-icon-nf' => ['dial_code' => '672', 'country' => 'Norfolk Island'],
            'flag-icon-no' => ['dial_code' => '47', 'country' => 'Norway'],
            'flag-icon-om' => ['dial_code' => '968', 'country' => 'Oman'],
            'flag-icon-pw' => ['dial_code' => '680', 'country' => 'Palau'],
            'flag-icon-pa' => ['dial_code' => '507', 'country' => 'Panama'],
            'flag-icon-pg' => ['dial_code' => '675', 'country' => 'Papua New Guinea'],
            'flag-icon-py' => ['dial_code' => '595', 'country' => 'Paraguay'],
            'flag-icon-pe' => ['dial_code' => '51', 'country' => 'Peru'],
            'flag-icon-ph' => ['dial_code' => '63', 'country' => 'Philippines'],
            'flag-icon-pl' => ['dial_code' => '48', 'country' => 'Poland'],
            'flag-icon-pt' => ['dial_code' => '351', 'country' => 'Portugal'],
            'flag-icon-pr' => ['dial_code' => '1787', 'country' => 'Puerto Rico'],
            'flag-icon-qa' => ['dial_code' => '974', 'country' => 'Qatar'],
            'flag-icon-re' => ['dial_code' => '262', 'country' => 'Réunion'],
            'flag-icon-ro' => ['dial_code' => '40', 'country' => 'Romania'],
            'flag-icon-rw' => ['dial_code' => '250', 'country' => 'Rwanda'],
            'flag-icon-sm' => ['dial_code' => '378', 'country' => 'San Marino'],
            'flag-icon-st' => ['dial_code' => '239', 'country' => 'São Tomé and Príncipe'],
            'flag-icon-sa' => ['dial_code' => '966', 'country' => 'Saudi Arabia'],
            'flag-icon-sn' => ['dial_code' => '221', 'country' => 'Senegal'],
            'flag-icon-rs' => ['dial_code' => '381', 'country' => 'Serbia'],
            'flag-icon-sc' => ['dial_code' => '248', 'country' => 'Seychelles'],
            'flag-icon-sl' => ['dial_code' => '232', 'country' => 'Sierra Leone'],
            'flag-icon-sg' => ['dial_code' => '65', 'country' => 'Singapore'],
            'flag-icon-sk' => ['dial_code' => '421', 'country' => 'Slovakia'],
            'flag-icon-si' => ['dial_code' => '386', 'country' => 'Slovenia'],
            'flag-icon-sb' => ['dial_code' => '677', 'country' => 'Solomon Islands'],
            'flag-icon-so' => ['dial_code' => '252', 'country' => 'Somalia'],
            'flag-icon-za' => ['dial_code' => '27', 'country' => 'South Africa'],
            'flag-icon-es' => ['dial_code' => '34', 'country' => 'Spain'],
            'flag-icon-lk' => ['dial_code' => '94', 'country' => 'Sri Lanka'],
            'flag-icon-sd' => ['dial_code' => '249', 'country' => 'Sudan'],
            'flag-icon-sr' => ['dial_code' => '597', 'country' => 'Suriname'],
            'flag-icon-sz' => ['dial_code' => '268', 'country' => 'Eswatini (Swaziland)'],
            'flag-icon-se' => ['dial_code' => '46', 'country' => 'Sweden'],
            'flag-icon-ch' => ['dial_code' => '41', 'country' => 'Switzerland'],
            'flag-icon-sy' => ['dial_code' => '963', 'country' => 'Syria'],
            'flag-icon-tw' => ['dial_code' => '886', 'country' => 'Taiwan'],
            'flag-icon-tj' => ['dial_code' => '992', 'country' => 'Tajikistan'],
            'flag-icon-tz' => ['dial_code' => '255', 'country' => 'Tanzania'],
            'flag-icon-th' => ['dial_code' => '66', 'country' => 'Thailand'],
            'flag-icon-tl' => ['dial_code' => '670', 'country' => 'Timor-Leste'],
            'flag-icon-tg' => ['dial_code' => '228', 'country' => 'Togo'],
            'flag-icon-to' => ['dial_code' => '676', 'country' => 'Tonga'],
            'flag-icon-tt' => ['dial_code' => '1868', 'country' => 'Trinidad and Tobago'],
            'flag-icon-tn' => ['dial_code' => '216', 'country' => 'Tunisia'],
            'flag-icon-tr' => ['dial_code' => '90', 'country' => 'Turkey'],
            'flag-icon-tm' => ['dial_code' => '993', 'country' => 'Turkmenistan'],
            'flag-icon-tv' => ['dial_code' => '688', 'country' => 'Tuvalu'],
            'flag-icon-ug' => ['dial_code' => '256', 'country' => 'Uganda'],
            'flag-icon-ua' => ['dial_code' => '380', 'country' => 'Ukraine'],
            'flag-icon-ae' => ['dial_code' => '971', 'country' => 'United Arab Emirates'],
            'flag-icon-uy' => ['dial_code' => '598', 'country' => 'Uruguay'],
            'flag-icon-uz' => ['dial_code' => '998', 'country' => 'Uzbekistan'],
            'flag-icon-vu' => ['dial_code' => '678', 'country' => 'Vanuatu'],
            'flag-icon-va' => ['dial_code' => '379', 'country' => 'Vatican City'],
            'flag-icon-ve' => ['dial_code' => '58', 'country' => 'Venezuela'],
            'flag-icon-vn' => ['dial_code' => '84', 'country' => 'Vietnam'],
            'flag-icon-wf' => ['dial_code' => '681', 'country' => 'Wallis and Futuna'],
            'flag-icon-ye' => ['dial_code' => '967', 'country' => 'Yemen'],
            'flag-icon-zm' => ['dial_code' => '260', 'country' => 'Zambia'],
            'flag-icon-zw' => ['dial_code' => '263', 'country' => 'Zimbabwe'],
        ];
        return $countryDialCodes;
        
    }
?>
