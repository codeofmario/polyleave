<?php

namespace App\Http\Controllers;

use App\Enums\LeaveStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaveController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $today  = now()->toDateString();
        $leaves = $request->user()->leaves()
            ->where('end_date', '>=', $today)
            ->orderBy('start_date')
            ->get();

        return response()->json($leaves);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date'   => ['required', 'date', 'after_or_equal:start_date'],
            'reason'     => ['nullable', 'string', 'max:500'],
        ]);

        $overlap = $user->leaves()
            ->whereIn('status', [LeaveStatus::Pending, LeaveStatus::Approved])
            ->where(function ($q) use ($data) {
                $q->whereBetween('start_date', [$data['start_date'], $data['end_date']])
                    ->orWhereBetween('end_date',   [$data['start_date'], $data['end_date']])
                    ->orWhere(function ($q2) use ($data) {
                        $q2->where('start_date', '<=', $data['start_date'])
                            ->where('end_date',   '>=', $data['end_date']);
                    });
            })
            ->exists();

        if ($overlap) {
            return response()->json(
                ['msg' => 'The requested dates overlap a leave already filed.'],
                422
            );
        }

        $leave = DB::transaction(fn () => $user->leaves()->create([
            'start_date' => $data['start_date'],
            'end_date'   => $data['end_date'],
            'reason'     => $data['reason'] ?? null,
            'status'     => LeaveStatus::Pending,
        ]));

        return response()->json($leave, 201);
    }
}
