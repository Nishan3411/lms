<x-app-layout>
    <x-slot name="header">
        <div class="module-header">
            <div>
                <h2 class="module-title">Fee Management</h2>
                <p class="module-copy">Create class fees, assign them to students, and record payments.</p>
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

            <div class="module-card">
                <form method="GET" action="{{ route('admin.fees.index') }}" class="grid gap-4 md:grid-cols-5">
                    <select name="class_room_id" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All classes</option>
                        @foreach ($classRooms as $classRoom)
                            <option value="{{ $classRoom->id }}" @selected((string) ($filters['class_room_id'] ?? '') === (string) $classRoom->id)>{{ $classRoom->name }}</option>
                        @endforeach
                    </select>
                    <select name="student_id" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All students</option>
                        @foreach ($students as $student)
                            <option value="{{ $student->id }}" @selected((string) ($filters['student_id'] ?? '') === (string) $student->id)>{{ $student->name }}</option>
                        @endforeach
                    </select>
                    <select name="status" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All statuses</option>
                        @foreach (['pending', 'partial', 'paid'] as $status)
                            <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                    <select name="due" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All dues</option>
                        <option value="overdue" @selected(($filters['due'] ?? '') === 'overdue')>Overdue only</option>
                    </select>
                    <button type="submit" class="inline-flex items-center justify-center rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Filter</button>
                </form>
                <div class="mt-4 flex flex-wrap gap-3">
                    <a href="{{ route('admin.fees.export', request()->query()) }}" class="secondary-action">Export CSV</a>
                    <a href="{{ route('admin.fees.index') }}" class="secondary-action">Clear Filters</a>
                </div>
            </div>

            <div class="grid gap-6 xl:grid-cols-3">
                <div class="module-card">
                    <h3 class="text-lg font-semibold text-slate-900">Create Fee</h3>
                    <p class="mt-1 text-sm text-slate-500">Use semester-based titles like Semester 1 Fee, Semester 2 Fee, and Semester 3 Fee.</p>
                    <form method="POST" action="{{ route('admin.fees.store') }}" class="mt-5 space-y-4">
                        @csrf
                        <select name="class_room_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="">Select class</option>
                            @foreach ($classRooms as $classRoom)
                                <option value="{{ $classRoom->id }}">{{ $classRoom->name }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="title" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Semester 1 Fee" required>
                        <input type="number" step="0.01" min="0.01" name="amount" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Amount" required>
                        <input type="date" name="due_date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Create Fee</button>
                    </form>
                </div>

                <div class="module-card">
                    <h3 class="text-lg font-semibold text-slate-900">Assign Existing Fee</h3>
                    <form method="POST" action="{{ route('admin.fees.assign') }}" class="mt-5 space-y-4">
                        @csrf
                        <select name="fee_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="">Select fee</option>
                            @foreach ($fees as $fee)
                                <option value="{{ $fee->id }}">{{ $fee->title }} · {{ $fee->classRoom->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Assign Fee to Class Students</button>
                    </form>
                </div>

                <div class="module-card">
                    <h3 class="text-lg font-semibold text-slate-900">Record Payment</h3>
                    <form method="POST" action="{{ route('admin.fees.payments.store') }}" class="mt-5 space-y-4">
                        @csrf
                        <select name="student_fee_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="">Select student fee</option>
                            @foreach ($studentFees as $studentFee)
                                <option value="{{ $studentFee->id }}">{{ $studentFee->student->name }} · {{ $studentFee->fee->title }} · Pending {{ number_format($studentFee->pendingAmount(), 2) }}</option>
                            @endforeach
                        </select>
                        <input type="number" step="0.01" min="0.01" name="amount" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Payment amount" required>
                        <input type="text" name="payment_method" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Cash / UPI / Bank Transfer" required>
                        <input type="text" name="transaction_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Transaction ID (optional)">
                        <input type="date" name="paid_at" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Record Payment</button>
                    </form>
                </div>
            </div>

            <div class="grid gap-6 xl:grid-cols-[1.1fr_1.4fr]">
                <div class="module-card">
                    <h3 class="text-lg font-semibold text-slate-900">Fees</h3>
                    <div class="mt-5 space-y-4">
                        @forelse ($fees as $fee)
                            <div class="module-subcard">
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <h4 class="font-semibold text-slate-900">{{ $fee->title }}</h4>
                                        <p class="text-sm text-slate-500">{{ $fee->classRoom->name }} · Due {{ $fee->due_date->format('d M Y') }}</p>
                                    </div>
                                    <span class="text-sm font-semibold text-slate-900">{{ number_format($fee->amount, 2) }}</span>
                                </div>
                                <p class="mt-2 text-sm text-slate-500">{{ $fee->student_fees_count }} student fee records</p>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">No fees created yet.</p>
                        @endforelse
                    </div>
                </div>

                <div class="module-card">
                    <h3 class="text-lg font-semibold text-slate-900">Student Fees and Payment History</h3>
                    <div class="mt-5 space-y-4">
                        @forelse ($studentFees as $studentFee)
                            <div class="module-subcard">
                                <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                                    <div>
                                        <h4 class="font-semibold text-slate-900">{{ $studentFee->student->name }}</h4>
                                        <p class="text-sm text-slate-500">{{ $studentFee->fee->title }} · {{ $studentFee->fee->classRoom->name }}</p>
                                    </div>
                                    <div class="text-sm text-slate-600">
                                        Total {{ number_format($studentFee->total_amount, 2) }} · Paid {{ number_format($studentFee->paid_amount, 2) }} · Pending {{ number_format($studentFee->pendingAmount(), 2) }}
                                    </div>
                                </div>
                                <div class="mt-2 flex flex-wrap items-center gap-3">
                                    <p class="text-sm font-medium uppercase tracking-wide text-slate-500">{{ $studentFee->status }}</p>
                                    @if ($studentFee->pendingAmount() > 0 && $studentFee->fee->due_date->isPast())
                                        <span class="rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-rose-700">Overdue</span>
                                    @endif
                                </div>
                                <div class="mt-2 flex flex-wrap gap-3">
                                    <a href="{{ route('admin.fees.students.show', $studentFee->student) }}" class="inline-flex text-sm font-medium text-slate-700 underline">View student fee history</a>
                                    <a href="{{ route('admin.fees.invoices.show', $studentFee) }}" class="inline-flex text-sm font-medium text-slate-700 underline">Invoice</a>
                                </div>
                                <div class="mt-3 space-y-2">
                                    @forelse ($studentFee->payments as $payment)
                                        <div class="rounded-lg bg-white px-3 py-2 text-sm text-slate-700 ring-1 ring-slate-200">
                                            {{ number_format($payment->amount, 2) }} via {{ $payment->payment_method }} on {{ $payment->paid_at->format('d M Y') }}
                                            <a href="{{ route('admin.fees.payments.receipt', $payment) }}" class="ms-2 font-medium underline">Receipt</a>
                                        </div>
                                    @empty
                                        <p class="text-sm text-slate-500">No payments recorded yet.</p>
                                    @endforelse
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">No student fee records yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
