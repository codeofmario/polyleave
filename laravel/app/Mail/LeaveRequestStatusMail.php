<?php
namespace App\Mail;

use App\Models\LeaveRequest;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LeaveRequestStatusMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public LeaveRequest $leave
    ) {}

    public function build(): self
    {
        return $this->subject("Your leave request was {$this->leave->status->value}")
            ->markdown('emails.leave_request_status');
    }
}
