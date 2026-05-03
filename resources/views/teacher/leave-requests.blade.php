<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="module-title">Leave Requests</h2>
            <p class="module-copy">Review leave requests for your assigned classes.</p>
        </div>
    </x-slot>

    <div class="module-page">
        <div class="module-shell">
            @if (session('success'))
                <div class="notice-success">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="notice-error">
                    <ul class="list-disc ps-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="space-y-4">
                @forelse ($leaveRequests as $leaveRequest)
                    <div class="module-card">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <h3 class="font-semibold text-slate-900">{{ $leaveRequest->student->name }}</h3>
                                <p class="text-sm text-slate-500">{{ $leaveRequest->classRoom?->name ?? 'General leave' }} - {{ $leaveRequest->start_date->format('d M Y') }} to {{ $leaveRequest->end_date->format('d M Y') }} - Requested by {{ $leaveRequest->requester->name }}</p>
                            </div>
                            <span class="data-pill">{{ $leaveRequest->status }}</span>
                        </div>
                        <p class="mt-3 text-sm text-slate-600">{{ $leaveRequest->reason }}</p>

                        @if ($leaveRequest->status === 'pending')
                            <form method="POST" action="{{ route('teacher.leave-requests.update', $leaveRequest) }}" class="mt-5 grid gap-3 lg:grid-cols-[180px_1fr_auto]">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="approved">Approve</option>
                                    <option value="rejected">Reject</option>
                                </select>
                                <input type="text" name="review_note" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Optional review note">
                                <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Save</button>
                            </form>
                        @elseif ($leaveRequest->reviewer)
                            <p class="mt-3 text-sm text-slate-500">Reviewed by {{ $leaveRequest->reviewer->name }}{{ $leaveRequest->review_note ? ': '.$leaveRequest->review_note : '' }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No leave requests for your classes yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
