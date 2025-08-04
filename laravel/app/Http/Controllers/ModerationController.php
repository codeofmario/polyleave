<?php

namespace App\Http\Controllers;

use App\Enums\LeaveStatus;
use App\Enums\RoleType;
use App\Mail\LeaveRequestStatusMail;
use App\Models\LeaveRequest;
use App\Notifications\LeaveRequestStatusChanged;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ModerationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        abort_unless(
            $user->hasRole(RoleType::Moderator),
            403,
            'Forbidden'
        );

        $leaves = LeaveRequest::with('user')
            ->pending()
            ->latest()
            ->get();

        return response()->json($leaves);
    }

    public function decide(Request $request, LeaveRequest $leave): JsonResponse
    {
        $user = $request->user();

        abort_unless($user->hasRole(RoleType::Moderator), 403, 'Forbidden');
        abort_if($leave->status !== LeaveStatus::Pending, 422, 'This leave-request has already been processed.');

        $data = $request->validate([
            'status' => [
                'required',
                Rule::enum(LeaveStatus::class)
                    ->only([LeaveStatus::Approved, LeaveStatus::Rejected]),
            ],
        ]);

        DB::transaction(function () use ($leave, $data) {
            $leave->update(['status' => LeaveStatus::from($data['status'])]);

            Mail::to($leave->user->email)
                ->send(new LeaveRequestStatusMail($leave));
        });

        return response()->json($leave->refresh());
    }

}
