@extends('email.layouts')
@section('title', 'Notification')
@section('content')
    <div style="font-size:22px;font-weight:600;color:#111827;margin-bottom:8px;">
        Dear Finance
    </div>

    <div style="font-size:15px;color:#6b7280;margin-bottom:28px;">
        Please find attached the Danamon Cashout file. Total: Rp 157,039,900
    </div>

    <div style="font-size:13px;color:#6b7280;margin-bottom:6px;">
        This code is only valid for 5 minutes. Never share the OTP code with anyone!
    </div>

    <div style="font-size:13px;color:#9ca3af;">
        Please DO NOT REPLY to this email.
        If you have questions or need assistance, please contact our call center at +62
        882-1009-8000 or via email at CS@get-intouch.com
    </div>
@endsection
