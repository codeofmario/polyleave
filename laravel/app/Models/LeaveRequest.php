<?php

namespace App\Models;

use App\Enums\LeaveStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    protected $fillable = ['user_id', 'start_date', 'end_date', 'reason', 'status'];

    protected $casts = [
        'status' => LeaveStatus::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopePending(Builder $q): void
    {
        $q->where('status', LeaveStatus::Pending->value);
    }
}
