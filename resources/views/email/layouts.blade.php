<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Email' }}</title>
</head>

<body
    style="margin:0;padding:0;background:#f3f4f6;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="padding:40px 0;background:#f3f4f6;">
        <tr>
            <td align="center">

                <!-- Logo -->
                <table width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;margin-bottom:18px;">
                    <tr>
                        <td align="center">
                            <img src="{{ asset('template/images/logo_more_voucher.png') }}" width="120">
                        </td>
                    </tr>
                </table>

                <!-- Card -->
                <table width="100%" cellpadding="0" cellspacing="0"
                    style="max-width:560px;background:#ffffff;border-radius:12px;padding:40px;box-shadow:0 10px 25px rgba(0,0,0,0.08);">
                    <tr>
                        <td align="center">


                            @yield('content')

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
