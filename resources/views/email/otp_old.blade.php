<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Code</title>
</head>

<body
    style="margin:0;padding:0;background:#f3f4f6;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="padding:40px 0;background:#f3f4f6;">
        <tr>
            <td align="center">

                <!-- Logo outside card -->
                <table width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;margin-bottom:18px;">
                    <tr>
                        <td align="center">
                            <img src="{{ asset('template/images/logo_more_voucher.png') }}" width="120"
                                style="display:block;">
                        </td>
                    </tr>
                </table>

                <!-- Card -->
                <table width="100%" cellpadding="0" cellspacing="0"
                    style="max-width:560px;background:#ffffff;border-radius:12px;padding:40px 40px;box-shadow:0 10px 25px rgba(0,0,0,0.08);">
                    <tr>
                        <td align="center">
                            <div style="font-size:22px;font-weight:600;color:#111827;margin-bottom:8px;">
                                Hi, {{ $name }}
                            </div>

                            <div style="font-size:15px;color:#6b7280;margin-bottom:28px;">
                                There are only one step left to complete the process, please confirm by entering the OTP
                                code below.
                            </div>

                            <!-- OTP -->
                            <div style="margin-bottom:28px;">
                                <span
                                    style="display:inline-block;background:#FFFFFF;color:#030303;border-radius:10px;padding:16px 28px;font-size:34px;font-weight:700;letter-spacing:10px;">
                                    {{ $otp }}
                                </span>
                            </div>

                            <div style="font-size:13px;color:#6b7280;margin-bottom:6px;">
                                This code is only valid for 5 minutes. Never share the OTP code with anyone!
                            </div>

                            <div style="font-size:13px;color:#9ca3af;">
                                Please DO NOT REPLY to this email.
                                If you have questions or need assistance, please contact our call center at +62
                                882-1009-8000 or via email at CS@get-intouch.com
                            </div>

                        </td>
                    </tr>

                </table>

                <!-- Footer -->
                <table width="560" cellpadding="0" cellspacing="0" style="margin-top:18px;">
                    <tr>
                        <td style="text-align:center;font-size:12px;color:#9ca3af;">
                            © {{ date('Y') }} Intouch Innovate. All rights reserved.
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>

</body>

</html>
