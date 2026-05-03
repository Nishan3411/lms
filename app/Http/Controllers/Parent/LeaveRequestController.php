<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Http\Requests\Parent\StoreLeaveRequest;
use App\Models\LeaveRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaveRequestController extends Controller
{
    public function index(Request $request): View
    {
        $parent = $request->user()->load(['children.enrolledClasses']);
        $childIds = $parent->children->pluck('id');

        return view('parent.leave-requests', [
            'children' => $parent->children,
            'leaveRequests' => LeaveRequest::with(['student', 'classRoom', 'reviewer'])
                ->whereIn('student_id', $childIds)
                ->latest()
                ->get(),
        ]);
    }

    public function store(StoreLeaveRequest $request): RedirectResponse
    {
        LeaveRequest::create([
            'requester_id' => $request->user()->id,
            'student_id' => $request->integer('student_id'),
            'class_room_id' => $request->input('class_room_id'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'reason' => $request->input('reason'),
        ]);

        return redirect()
            ->route('parent.leave-requests.index')
            ->with('success', 'Leave request submitted successfully.');
    }
}
