<x-app-layout>
    <x-slot name="header">
        <div class="module-header">
            <div>
                <h2 class="module-title">Leave Requests</h2>
                <p class="module-copy">Review submitted leave requests, approve valid cases, and add decision notes.</p>
            </div>

            <a href="{{ route('admin.dashboard') }}" class="secondary-action">Back to Dashboard</a>
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

            <div class="space-y-5">
                @forelse ($leaveRequests as $leaveRequest)
                    <div class="module-card">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-slate-900">{{ $leaveRequest->student->name }}</h3>
                                <p class="mt-1 text-sm text-slate-500">
                                    {{ $leaveRequest->classRoom?->name ?? 'General leave' }}
                                    -
                                    {{ $leaveRequest->start_date->format('d M Y') }}
                                    to
                                    {{ $leaveRequest->end_date->format('d M Y') }}
                                    -
                                    Requested by {{ $leaveRequest->requester->name }}
                                </p>
                            </div>
                            <span class="data-pill">{{ $leaveRequest->status }}</span>
                        </div>

                        <div class="module-subcard mt-5">
                            <h4 class="font-semibold text-slate-900">Reason</h4>
                            <p class="mt-2 text-sm leading-6 text-slate-600">{{ $leaveRequest->reason }}</p>
                        </div>

                        @if ($leaveRequest->status === 'pending')
                            <form method="POST" action="{{ route('admin.leave-requests.update', $leaveRequest) }}" class="mt-5 grid gap-4 lg:grid-cols-[200px_1fr_auto]">
                                @csrf
                                @method('PATCH')

                                <div>
                                    <label for="status_{{ $leaveRequest->id }}" class="block text-sm font-medium text-gray-700">Decision</label>
                                    <select id="status_{{ $leaveRequest->id }}" name="status" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                        <option value="approved">Approve</option>
                                        <option value="rejected">Reject</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="review_note_{{ $leaveRequest->id }}" class="block text-sm font-medium text-gray-700">Review Note</label>
                                    <input
                                        id="review_note_{{ $leaveRequest->id }}"
                                        type="text"
                                        name="review_note"
                                        class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Optional review note"
                                    >
                                </div>

                                <div class="flex items-end">
                                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                                        Save Decision
                                    </button>
                                </div>
                            </form>
                        @elseif ($leaveRequest->reviewer)
                            <div class="module-subcard mt-5 text-sm text-slate-600">
                                <span class="font-semibold text-slate-900">Reviewed by {{ $leaveRequest->reviewer->name }}</span>
                                @if ($leaveRequest->review_note)
                                    <p class="mt-1">{{ $leaveRequest->review_note }}</p>
                                @endif
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="module-card text-sm text-slate-500">
                        No leave requests have been submitted yet.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
