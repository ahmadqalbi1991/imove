<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml"
    xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Beljwar</title>
</head>

<body style="margin: 0; color: #fff; background: #1BD1EA;">

    <div marginwidth="0" marginheight="0">
    <div marginwidth="0" marginheight="0" id="" dir="ltr" style="background-color: #1BD1EA;  margin:0;padding:20px 0 20px 0;width:100%; margin: 0;">

    <table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" style="background-color: #1BD1EA;">
        <tbody>
            <tr>
                <td align="center" valign="top">
                    <table border="0" cellpadding="0" cellspacing="0" width="600" style="background:#1BD1EA;border-radius:10px!important;overflow: hidden;">
                        <tbody>
                            <tr>
                                <td style="background: #0D4752;">
                                    <div style="padding: 15px 20px; background:#0D4752; padding-bottom: 15px;">
                                        <table style="background:#0D4752; font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;font-size:14px;width: 100%;">
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        
                                                        <img src="{{ asset('') }}admin-assets/assets/img/logo.png" alt="" style="max-width: 120px; margin-bottom: 0px; ">
                                                    </td>

                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        <tr>
                            <td align="center" valign="top" style="background: #fff;">
                                <table border="0" cellpadding="0" cellspacing="0" width="600" style="background: #fff;">
                                    <tbody>
                                    <tr>
                                        <td valign="top" style="background-color: #fff;   padding:0;">
                                            <table border="0" cellpadding="20" cellspacing="0" width="100%" style="font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;">
                                                <tbody>
                                                <tr>
                                                    <td valign="top" style="padding-bottom: 0px;">

                                                        <div  style="color:#000;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;font-size:14px;line-height:150%;text-align:left;margin-top: 0px">
                                                            <h4 style="font-weight: 600; font-size: 18px; color: #000;margin-top: 0;">Dear,  {{$user->name}}</h4>
                                                            <p style="margin:0 0 14px; font-size: 16px; line-height: 26px; color: #000; text-align: left;"> Thank you for booking with <b>I Move It</b>! We’re excited to confirm your reservation.</p>
                                                            
                                                            <h4 style="font-weight: 600; font-size: 18px; color: #0D4752;margin-top: 0; margin-bottom: 10px;">Booking Details</h4>
                                                            
                                                            <table width="100%" style="line-height: 28px; font-size: 14px;" cellpadding="0" cellspacing="0" role="presentation">
                                                                <tbody>
                                                                    
                                                                    <tr>
                                                                        <td style="width: 40%;border: 1px solid #eee; padding: 5px;">Order Number:</td>
                                                                        <th style="border: 1px solid #eee; padding: 5px; text-align: right">{{ $datamain->order_number ?? '' }}</th>
                                                                    </tr>
                                                                    
                                                                    <tr>
                                                                        <td style="width: 40%;border: 1px solid #eee; padding: 5px;">Category:</td>
                                                                        <th style="border: 1px solid #eee; padding: 5px; text-align: right">{{ $datamain->category_details->name ?? '' }}</th>
                                                                    </tr>
                                                                    <tr>
                                                                        <td style="width: 40%;border: 1px solid #eee; padding: 5px;">Pick Up Location:</td>
                                                                        <th style="border: 1px solid #eee; padding: 5px; text-align: right">{{ $datamain->location ?? '' }}</th>
                                                                    </tr>
                                                                    <tr>
                                                                      
                                                                        <td style="width: 40%;border: 1px solid #eee; padding: 5px;">Pick Up Landmark:</td>
                                             
                                                                        <th style="border: 1px solid #eee; padding: 5px; text-align: right">{{ $datamain->landmark ?? '' }}</th>
                                                                    </tr>
                                                                    <tr>
                                                                       
                                                                        <td style="width: 40%;border: 1px solid #eee; padding: 5px;">Pickup Contact Person:</td>
                                                                        <th style="border: 1px solid #eee; padding: 5px; text-align: right">{{ $datamain->contact_person ?? '' }}</th>
                                                                    </tr>
                                                                    <tr>
                                                                       
                                                                        <td style="width: 40%;border: 1px solid #eee; padding: 5px;">Pick Up Mobile Number:</td>
                                                                        <th style="border: 1px solid #eee; padding: 5px; text-align: right">{{replace_plus($datamain->dail_code.' '.$datamain->mobile_no)}}</th>
                                                                    </tr>
                                                                   
                                                                    <tr>
                                                                        <td style="width: 40%;border: 1px solid #eee; padding: 5px;">Drop Off Location:</td>
                                                                        <th style="border: 1px solid #eee; padding: 5px; text-align: right">{{ $datamain->dropoff->location ?? '' }}</th>
                                                                    </tr>
                                                                    <tr>
                                                                       
                                                                        <td style="width: 40%;border: 1px solid #eee; padding: 5px;">Drop Off Landmark:</td>
                                                                        <th style="border: 1px solid #eee; padding: 5px; text-align: right">{{ $datamain->dropoff->landmark  ?? '' }}</th>
                                                                    </tr>
                                                                    <tr>
                                                                       
                                                                        <td style="width: 40%;border: 1px solid #eee; padding: 5px;">Drop Off Contact Person:</td>
                                                                        <th style="border: 1px solid #eee; padding: 5px; text-align: right">{{ $datamain->dropoff->contact_person ?? '' }}</th>
                                                                    </tr>
                                                                    
                                                                    <tr>
                                                                       
                                                                        <td style="width: 40%;border: 1px solid #eee; padding: 5px;">Crop Off Mobile Number:</td>
                                                                        <th style="border: 1px solid #eee; padding: 5px; text-align: right">{{replace_plus($datamain->dropoff->dail_code.' '.$datamain->dropoff->mobile_no)}}</th>
                                                                    </tr>
                                                                    <tr>
                                                                        
                                                                        <td style="width: 40%;border: 1px solid #eee; padding: 5px;">Description:</td>
                                                                        <th style="border: 1px solid #eee; padding: 5px; text-align: right">{{ $datamain->description ?? '-' }}</th>
                                                                    </tr>
                                                                    <tr>
                                                                        
                                                                        <td style="width: 40%;border: 1px solid #eee; padding: 5px;">Delivery Type:</td>
                                                                        <th style="border: 1px solid #eee; padding: 5px; text-align: right">{{ $datamain->delivery_type?? '-' }}</th>
                                                                    </tr>
                                                                    <tr>
                                                                    
                                                                        <td style="width: 40%;border: 1px solid #eee; padding: 5px;">Size:</td>
                                                                        <th style="border: 1px solid #eee; padding: 5px; text-align: right">{{ $datamain->size_details->name ?? '-' }}</th>
                                                                    </tr>
                                                                    <tr>
                                                                       
                                                                        <td style="width: 40%;border: 1px solid #eee; padding: 5px;">Care of Pack:</td>
                                                                        <th style="border: 1px solid #eee; padding: 5px; text-align: right">{{ $datamain->care_details->name ?? '-' }}</th>
                                                                    </tr>
                                                                    <tr>
                                                                    
                                                                        <td style="width: 40%;border: 1px solid #eee; padding: 5px;">Instruction:</td>
                                                                        <th style="border: 1px solid #eee; padding: 5px; text-align: right">{{ $datamain->instruction ?? '-' }}</th>
                                                                    </tr>
                                                                    <tr>
                                                                        
                                                                        <td style="width: 40%;border: 1px solid #eee; padding: 5px;">Date:</td>
                                                                        <th style="border: 1px solid #eee; padding: 5px; text-align: right">{{ $datamain->date ?? '-' }}</th>
                                                                    </tr>
                                                                    <tr>
                                                                        
                                                                        <td style="width: 40%;border: 1px solid #eee; padding: 5px;">Time:</td>
                                                                        <th style="border: 1px solid #eee; padding: 5px; text-align: right">{{ $datamain->time ?? '-' }}</th>
                                                                    </tr>
                                                                    <tr>
                                                                        <td style="width: 40%;border: 1px solid #eee; padding: 5px;">Payment Type:</td>
                                                                        <th style="border: 1px solid #eee; padding: 5px; text-align: right">{{payment_type($datamain->payment_type)}}</th>
                                                                    </tr>
                                                                    <tr>
                                                                        <td style="width: 40%;border: 1px solid #eee; padding: 5px;">Subtotal:</td>
                                                                        <th style="border: 1px solid #eee; padding: 5px; text-align: right">AED {{number_format($datamain->cost??0, 2, '.', '')}}</th>
                                                                    </tr>
                                                                    <tr>
                                                                        <td style="width: 40%;border: 1px solid #eee; padding: 5px;">Service Price:</td>
                                                                        <th style="border: 1px solid #eee; padding: 5px; text-align: right">AED {{number_format($datamain->service_price??0, 2, '.', '')}}</th>
                                                                    </tr>
                                                                    <tr>
                                                                        <td style="width: 40%;border: 1px solid #eee; padding: 5px;">TAX:</td>
                                                                        <th style="border: 1px solid #eee; padding: 5px; text-align: right">AED {{number_format($datamain->tax??0, 2, '.', '')}}</th>
                                                                    </tr>
                                                                    
                                                                    <tr>
                                                                        <th style="width: 40%;border: 1px solid #eee; padding: 5px;">Grand Total:</th>
                                                                        <th style="border: 1px solid #eee; padding: 5px; text-align: right">AED {{number_format($datamain->grand_total??0, 2, '.', '')}}</th>
                                                                    </tr>
                                                                </tbody>
                                                            </table>


                                                            <h4 style="font-weight: 600; font-size: 18px; color: #0D4752;margin-top: 15px; margin-bottom: 10px;">Driver Details</h4>


                                                            <table width="100%" style="line-height: 28px; font-size: 14px;" cellpadding="0" cellspacing="0" role="presentation">
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="width: 40%;border: 1px solid #eee; padding: 5px;">Pickup driver:</td>
                                                                        <th style="border: 1px solid #eee; padding: 5px; text-align: right"><select name="pickup_driver" id="pickup_driver" disabled required {{!empty($datamain->booking_status) && $datamain->booking_status >= 4 ? 'disabled' : '';}}>
                                                                            <option value="">Not Assigned Yet</option>
                                                                            @foreach($drivers as $value)
                                                                            <option value="{{$value->id}}"  {{!empty($datamain->pickup_driver) && $datamain->pickup_driver == $value->id ? 'selected' : null;}}>{{$value->name}}</option>
                                                                            @endforeach
                                                                           </select></th>
                                                                    </tr>
                                                                    <tr>
                                                                       
                                                                        <td style="width: 40%;border: 1px solid #eee; padding: 5px;">Driver Comment:</td>
                                                                        <th style="border: 1px solid #eee; padding: 5px; text-align: right">{{$datamain->comment}}</th>
                                                                    </tr>
                                                                </tbody>
                                                            </table>

                                                            
                                                            <p style="margin:20px 0 14px; font-size: 16px; line-height: 26px; color: #000; text-align: left;">
                                                                Best regards,
                                                                <br>
                                                                I Move It Team
                                                            </p>



                                                        </div>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td style="background: #0D4752;">
                                <div style="padding: 20px; background: #0D4752;">
                                    <table style="background: #0D4752; font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;font-size:14px;width: 100%;">
                                        <tbody>

                                            <tr>
                                                <td style="width: 100%;" colspan="2">
                                                    <table style="font-size: 16px; width: 100%;">
                                                        <tbody>


                                                            <tr>
                                                                <td colspan="2" valign="middle"
                                                                    style="padding:0;border:0;color:#fff;font-family:Arial;font-size:12px;line-height:125%;text-align:center; background: #0D4752;">
                                                                    <p style="color: #fff; padding-top: 20px; font-style: 16px; margin-top: 0px">
                                                                        © 2024 Beljwar. All Rights Reserved.</p>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>

        </tbody>
    </table>

</div>
</div>

</body>

</html>
