<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Leave Request {{ ucfirst($leave->status->value) }}</title>
</head>
<body style="font-family: sans-serif; line-height: 1.5;">
<h1 style="margin-top: 0;">Leave Request {{ ucfirst($leave->status->value) }}</h1>

<p>Hi {{ $leave->user->name }},</p>

<p>Your leave request from <strong>{{ \Carbon\Carbon::parse($leave->start_date)->toFormattedDateString() }}</strong> to
    <strong>{{ \Carbon\Carbon::parse($leave->end_date)->toFormattedDateString() }}</strong> has been
    <strong>{{ $leave->status->value }}</strong>.</p>

<p><strong>Reason:</strong> {{ $leave->reason }}</p>

@if($leave->status->value === 'approved')
    <p>We look forward to seeing you back on
        {{ \Carbon\Carbon::parse($leave->end_date)->addDay()->toFormattedDateString() }}.</p>
@else
    <p>If you have any questions, please contact HR.</p>
@endif

<p>Thanks,<br>
    {{ config('app.name') }}</p>
</body>
</html>
