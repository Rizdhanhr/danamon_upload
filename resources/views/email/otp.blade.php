@extends('emails.layouts.main')
@section('title', 'OTP Code')
@section('content')
    <div style="font-size:22px;font-weight:600;color:#111827;margin-bottom:8px;">
        Hi, {{ $name }}
    </div>

    <div style="font-size:15px;color:#6b7280;margin-bottom:28px;">
        There are only one step left to complete the process, please confirm by entering the OTP code below.
    </div>

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
@endsection
