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

    function get_booking_status($booking_status){
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

        $html .=    '<span class="badge badge-'.$status_color.'">
                            '. $status.'</span>';
        return $html;
    }
    function get_driver_types()
    {
        return [];
    }
    function booking_status($id)
    {
        $status = "";
        if($id == 0)
        {
            $status = "Pending";
        }
        if($id == 6)
        {
            $status = "On the way pick Up";
        }
        if($id == 4)
        {
            $status = "Package Collected";
        }
        if($id == 5)
        {
            $status = "On Drop Off";
        }
        if($id == 8)
        {
            $status = "Drop Off-Delivered";
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
            $status = 5;
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
            $status = "Pickup";
        }
        if($id == 1)
        {
            $status = "Pickup";
        }
        if($id == 4)
        {
            $status = "Pickup";
        }
        if($id == 5)
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
      
?>
