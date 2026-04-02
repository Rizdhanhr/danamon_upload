@extends('email.layouts')
@section('title', 'Approval')
@section('content')
    <div style="font-size:15px;color:#6b7280;margin-bottom:28px;">
        You have received a recipient upload request that requires your approval. Kindly review the details below and
        proceed accordingly.
    </div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
        style="border-collapse:collapse;font-size:14px;background:#ffffff;">
        <tr>
            <td style="border:1px solid #e5e7eb;padding:10px;font-weight:600;width:40%;">Batch Name</td>
            <td style="border:1px solid #e5e7eb;padding:10px;">{{ $name }}</td>
        </tr>
        <tr>
            <td style="border:1px solid #e5e7eb;padding:10px;font-weight:600;width:40%;">Original Filename</td>
            <td style="border:1px solid #e5e7eb;padding:10px;">{{ $original_filename }}</td>
        </tr>
        <tr>
            <td style="border:1px solid #e5e7eb;padding:10px;font-weight:600;">Total Recipient</td>
            <td style="border:1px solid #e5e7eb;padding:10px;">{{ $total_recipient }}</td>
        </tr>
        <tr>
            <td style="border:1px solid #e5e7eb;padding:10px;font-weight:600;">Total Amount</td>
            <td style="border:1px solid #e5e7eb;padding:10px;">Rp {{ $total_amount }}</td>
        </tr>
        <tr>
            <td style="border:1px solid #e5e7eb;padding:10px;font-weight:600;">Schedule Date</td>
            <td style="border:1px solid #e5e7eb;padding:10px;">{{ $scheduled_at }}</td>
        </tr>
        <tr>
            <td style="border:1px solid #e5e7eb;padding:10px;font-weight:600;">Created At</td>
            <td style="border:1px solid #e5e7eb;padding:10px;">{{ $created_at }}</td>
        </tr>
        <tr>
            <td style="border:1px solid #e5e7eb;padding:10px;font-weight:600;">Created By</td>
            <td style="border:1px solid #e5e7eb;padding:10px;">{{ $created_by }}</td>
        </tr>
    </table>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:30px;text-align:center;">
        <tr>
            <td>
                <a href="{{ $approval_url }}" target="_blank"
                    style="
                        display:inline-block;
                        padding:12px 24px;
                        font-size:14px;
                        color:#ffffff;
                        background-color:#2563eb;
                        text-decoration:none;
                        border-radius:6px;
                        font-weight:600;
                    ">
                    Review & Approve
                </a>
            </td>
        </tr>
    </table>

@endsection
