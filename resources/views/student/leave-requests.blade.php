<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="module-title">Leave Requests</h2>
            <p class="module-copy">Request leave and track approvals.</p>
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

            <div class="module-card">
                <h3 class="text-lg font-semibold text-slate-900">New Leave Request</h3>
                <form method="POST" action="{{ route('student.leave-requests.store') }}" class="mt-5 grid gap-4 lg:grid-cols-2">
                    @csrf
                    <select name="class_room_id" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">General leave</option>
                        @foreach ($classRooms as $classRoom)
                            <option value="{{ $classRoom->id }}">{{ $classRoom->name }}</option>
                        @endforeach
                    </select>
                    <div class="grid grid-cols-2 gap-4">
                        <input type="date" name="start_date" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <input type="date" name="end_date" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>
                    <textarea name="reason" rows="4" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 lg:col-span-2" placeholder="Reason for leave" required></textarea>
                    <button type="submit" class="inline-flex items-center justify-center rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800 lg:col-span-2">Submit Request</button>
                </form>
            </div>

            <div class="space-y-4">
                @forelse ($leaveRequests as $leaveRequest)
                    <div class="module-card">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <h3 class="font-semibold text-slate-900">{{ $leaveRequest->start_date->format('d M Y') }} to {{ $leaveRequest->end_date->format('d M Y') }}</h3>
                                <p class="text-sm text-slate-500">{{ $leaveRequest->classRoom?->name ?? 'General leave' }}</p>
                            </div>
                            <span class="data-pill">{{ $leaveRequest->status }}</span>
                        </div>
                        <p class="mt-3 text-sm text-slate-600">{{ $leaveRequest->reason }}</p>
                        @if ($leaveRequest->reviewer)
                            <p class="mt-3 text-sm text-slate-500">Reviewed by {{ $leaveRequest->reviewer->name }}{{ $leaveRequest->review_note ? ': '.$leaveRequest->review_note : '' }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No leave requests yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
