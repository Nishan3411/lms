<x-app-layout>
    <x-slot name="header">
        <div class="module-header">
            <div>
                <h2 class="module-title">Leave Requests</h2>
                <p class="module-copy">Submit leave requests for linked children and monitor approval status.</p>
            </div>

            <a href="{{ route('parent.dashboard') }}" class="secondary-action">Back to Dashboard</a>
        </div>
    </x-slot>

    <div class="module-page">
        <div class="module-shell">
            @if (session('success'))
                <div class="notice-success">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="notice-error">
                    <p class="font-semibold">Please fix the following issues:</p>
                    <ul class="mt-2 list-disc ps-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid gap-6 xl:grid-cols-[minmax(0,0.9fr)_minmax(0,1.1fr)]">
                <div class="module-card">
                    <h3 class="text-lg font-semibold text-slate-900">New Leave Request</h3>
                    <p class="mt-1 text-sm text-slate-500">Select the child, class context, and leave period before submitting.</p>

                    <form method="POST" action="{{ route('parent.leave-requests.store') }}" class="mt-5 grid gap-4">
                        @csrf

                        <div>
                            <label for="student_id" class="block text-sm font-medium text-gray-700">Child</label>
                            <select id="student_id" name="student_id" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Select child</option>
                                @foreach ($children as $child)
                                    <option value="{{ $child->id }}" @selected((string) old('student_id') === (string) $child->id)>{{ $child->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="class_room_id" class="block text-sm font-medium text-gray-700">Class Context</label>
                            <select id="class_room_id" name="class_room_id" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">General leave</option>
                                @foreach ($children as $child)
                                    @foreach ($child->enrolledClasses as $classRoom)
                                        <option value="{{ $classRoom->id }}" @selected((string) old('class_room_id') === (string) $classRoom->id)>{{ $child->name }} - {{ $classRoom->name }}</option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                                <input id="start_date" type="date" name="start_date" value="{{ old('start_date') }}" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            </div>

                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                                <input id="end_date" type="date" name="end_date" value="{{ old('end_date') }}" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            </div>
                        </div>

                        <div>
                            <label for="reason" class="block text-sm font-medium text-gray-700">Reason</label>
                            <textarea id="reason" name="reason" rows="5" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Reason for leave" required>{{ old('reason') }}</textarea>
                        </div>

                        <button type="submit" class="inline-flex items-center justify-center rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                            Submit Request
                        </button>
                    </form>
                </div>

                <div class="module-card">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h3 class="section-heading">Recent Requests</h3>
                            <p class="section-copy">Stay updated on approvals and reviewer notes.</p>
                        </div>
                        <span class="data-pill">{{ $leaveRequests->count() }} requests</span>
                    </div>

                    <div class="mt-5 space-y-4">
                        @forelse ($leaveRequests as $leaveRequest)
                            <div class="module-subcard">
                                <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                                    <div>
                                        <h4 class="font-semibold text-slate-900">{{ $leaveRequest->student->name }}</h4>
                                        <p class="mt-1 text-sm text-slate-500">
                                            {{ $leaveRequest->classRoom?->name ?? 'General leave' }}
                                            -
                                            {{ $leaveRequest->start_date->format('d M Y') }}
                                            to
                                            {{ $leaveRequest->end_date->format('d M Y') }}
                                        </p>
                                    </div>
                                    <span class="data-pill">{{ $leaveRequest->status }}</span>
                                </div>

                                <p class="mt-3 text-sm leading-6 text-slate-600">{{ $leaveRequest->reason }}</p>

                                @if ($leaveRequest->reviewer)
                                    <div class="mt-3 text-sm text-slate-500">
                                        Reviewed by {{ $leaveRequest->reviewer->name }}
                                        @if ($leaveRequest->review_note)
                                            - {{ $leaveRequest->review_note }}
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="module-subcard text-sm text-slate-500">
                                No leave requests yet.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
