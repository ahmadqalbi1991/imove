<?php

$config['server_mode']                                  = 'local'; //live
$config['site_name']                                    = env("APP_NAME",'I Move It');
$config['date_timezone']								= 'Asia/Dubai';
$config['datetime_format']								= 'M d, Y h:i A';
$config['date_format']									= 'M d, Y';
$config['date_format_excel']							= 'd/m/Y';
$config['default_currency_code']						= 'AED';

$config['upload_bucket']						        = 'public';//s3
$config['upload_path']              					= 'storage/';
$config['care_image_upload_dir']    					= 'cares/';
$config['user_image_upload_dir']    					= 'users/';
$config['category_image_upload_dir']    				= 'category/';
$config['company_image_upload_dir']    				    = 'comapny/';
$config['booking_pictures_upload_dir']    				= 'booking_pictures/';
$config['deligates_upload_dir']                         = 'deligates/';
$config['shipping_methods_upload_dir']                  = 'shipping_methods/';
$config['product_image_upload_dir']    				    = 'products/';
$config['post_image_upload_dir']    				    = 'posts/';
$config['banner_image_upload_dir']                      = 'banner_images/';
$config['request_images_upload_dir']                      = 'banner_images/';


//user status
$config['user_status_inactive']                         = 'inactive';
$config['user_status_active']                           = 'active';

//order status
$config['order_status_pending']                         = 0;
$config['order_status_accepted']                        = 1;
$config['order_status_ready_for_delivery']              = 2;
$config['order_status_dispatched']                      = 3;
$config['order_status_delivered']                       = 4;
$config['order_status_cancelled']                       = 10;
$config['order_status_returned']                        = 11;

$config['order_prefix']                                 = 'NF-';
$config['product_image_width']              			= '1024';
$config['product_image_height']              			= '1024';

$config['wowza_key']                              = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI5NTU3NzEwZS0yYzhlLTQ1MDgtOTEwOS1hZWMxNTEwODAxY2UiLCJqdGkiOiI1N2M3ZDk5MDRhMDYwY2ZlNGQ0NjBjYmI3ZTI2NGE3NTAwYzU1Y2FjMzdkNWI0MDI0YTg0Njk5NzQxYzAyMjZjYjY1ODlmZDc4YmJhZTczYyIsImlhdCI6MTY2MzA0MDA3MywibmJmIjoxNjYzMDQwMDczLCJleHAiOjIyOTQxOTIwNzMsInN1YiI6Ik9VLTRmMjQzYTM2LThjZTItNDcyYS04MDhlLWE3Njg2NDE4MzViOCJ9.s4TXFbAO1J-MqfxxT7Bw3x8Ohjm6tmPvcZemcs6whQIP1LHPb4BPcDVlqt8HnsGnpWgI0DMARmxpOHR1d43nOYAxgBekIgPZn59BHB8gb-ovKvdOkqXYu7u1olvxPfs0tpJ1w_ey-3oxaeVdLIbYtSiyvB8KALN90Xpy1ueSyhcAdtulfRlcwUj5cXZkaeMJleCujpU7X_NSvAHG1xjAKk0yd3Tt9bt4a71VpP7B8wpkaSsf1vQ_PQphfFgEG0xqPOeTxPPIUUIHLfC46vVDySh8Kgo0Hxm1ZXRB0futXf8h6bCvB3HPIOzmdmUUtrmK_XRfkARPYRF5yserjX7vJ8674fqMyusroIBRfErlw5aDHnh4VKlLuZAIlizYlnoTWdF1cFCntTnsTo_tso0LjAFP-eAShitrSAzsAnJvymsXjslIBQdPixtNY32f8srowxnFqXY52UHEfae1jmZk-6F5TjxU7n6dCjaIukVJ_uOmpIq9crhE2wB5jQVkgQHJWEQpSsQ2q1Mob4OWhTPHT6xCsce3R0vS4dnHfreLMF5jRFnugH9vUurwNul3miDMFjzSVhU788xudLAmCcIFnfbozms2KjeijstpiH77BCD8-NNZzXAlcJLAfpYZxyacQaEAseEPnCCxiZPTrB7ccxStVh6DXLMo8ewnXjEWWp8';
$config['wowza_token_name']                       ='api_token_v1';

$config['message_privacy']                        = [
    '1'     =>  '24 Hours',
    '7'     =>  '7 Days',
    '90'    =>  '90 Days',
    '999999' =>  'Off'
];

$config['report_user_problems']                        = [
    '1'    =>  'Nudity or sexual activity',
    '2'    =>  'Hate speach or symbols',
    '3'    =>  'Scam or fraud',
    '4'    =>  'Violence or dangerous organisations',
    '5'    =>  'Sale of illegal or regulated goods',
    '6'    =>  'Bullying or harassment',
    '7'    =>  'Pretending to be someone else',
];

$config['limit_distance'] = 60; //km
$config['limit_distance_crossby'] = 20; //km

$config['tax'] = 5;
$config['commission'] = 20;
return $config;
?>
