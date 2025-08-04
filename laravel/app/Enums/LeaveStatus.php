<?php
// app/Enums/LeaveStatus.php
namespace App\Enums;

enum LeaveStatus: string
{
    case Pending  = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
}
