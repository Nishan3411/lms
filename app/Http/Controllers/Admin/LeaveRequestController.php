<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewLeaveRequest;
use App\Models\LeaveRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaveRequestController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.leave-requests', [
            'leaveRequests' => LeaveRequest::with(['student', 'requester', 'classRoom', 'reviewer'])
                ->latest()
                ->get(),
        ]);
    }

    public function update(ReviewLeaveRequest $request, LeaveRequest $leaveRequest): RedirectResponse
    {
        $leaveRequest->update([
            'status' => $request->input('status'),
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'review_note' => $request->input('review_note'),
        ]);

        return redirect()
            ->route('admin.leave-requests.index')
            ->with('success', 'Leave request reviewed successfully.');
    }
}
