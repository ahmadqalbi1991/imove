<!DOCTYPE html>
<html>
<head>
    <title>Password Change Confirmation</title>
</head>

<body style="margin: 0; background-color: #009CCB;">
    <div marginwidth="0" marginheight="0">
        <div marginwidth="0" marginheight="0" style="background-color:#009CCB; margin:0; padding:20px 0; width:100%;">
            <table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
                <tbody>
                    <tr>
                        <td align="center" valign="top">
                            <table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color:#eeee; border:0px solid #dadada; border-radius:10px!important; overflow: hidden;">
                                <tbody>
                                    <tr>
                                        <td>
                                            <div style="padding: 15px 20px; background: #f9f9f9; padding-bottom: 15px;">
                                                <table style="background: #f9f9f9; font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif; font-size:14px; width: 100%;">
                                                    <tbody>
                                                        <tr>
                                                            <td style="width: 100%;" align="center">
                                                                <img src="{{ asset('') }}admin-assets/assets/img/logo.svg" alt="Logo" style="max-width: 120px; margin-bottom: 10px;">
                                                                <h1 style="color: #000; font-size: 24px; line-height: 28px; margin: 20px 0 10px 0;">
                                                                    Password Changed Successfully!
                                                                </h1>
                                                                <p style="color: #111941; font-size: 16px; margin: 0;">
                                                                    Your password has been changed successfully. If you didn't request this change, please contact us immediately.
                                                                </p>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="center" valign="top">
                                            <table border="0" cellpadding="0" cellspacing="0" width="600">
                                                <tbody>
                                                    <tr>
                                                        <td valign="top" style="background-color:#fff; padding:0px;">
                                                            <table border="0" cellpadding="20" cellspacing="0" width="100%" style="font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif;">
                                                                <tbody>
                                                                    <tr>
                                                                        <td valign="top" style="padding-bottom: 0px;">
                                                                            <div style="color:#000; font-family: Roboto, RobotoDraft, Helvetica, Arial, sans-serif; font-size:14px; line-height:150%; text-align:left; margin-top: 30px">
                                                                                <h4 style="font-size: 16px;">Hello {{$name}},</h4>
                                                                                <br>
                                                                                <p style="margin:0;padding:0;margin-bottom:5px;color:#000;font-weight:400;font-size:14px;line-height:1.6">
                                                                                    Your password for your <strong>{{ env("APP_NAME") }}</strong> account has been updated. If you did not make this change, please reset your password immediately or contact our support team.
                                                                                </p>
                                                                                <br>
                                                                                <p style="margin:0 0 10px; font-size: 14px; line-height: 26px; color: #111941; text-align: left;">
                                                                                    We're here to assist you with any questions or concerns.
                                                                                </p>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            <h4 style="color: #000; font-size: 14px; margin: 0px 0px 8px; text-align: left;">Best Regards,</h4>
                                                                            <p style="color: #000; font-size: 16px; margin: 0px 0px 10px; text-align: left;">The {{ config('app.name') }} Team</p>
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
                                        <td>
                                            <div style="padding: 20px; background:#eee;">
                                                <table style="background:#eee; font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif; font-size:14px; width: 100%;">
                                                    <tbody>
                                                        <tr>
                                                            <td style="text-align: center;">
                                                                <p style="color: #000;">&#169;{{date('Y')}} {{ config('app.name') }}. All Rights Reserved.</p>
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
