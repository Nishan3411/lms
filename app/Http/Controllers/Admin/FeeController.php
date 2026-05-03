<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssignFeeToClassRequest;
use App\Http\Requests\Admin\CreateFeeRequest;
use App\Http\Requests\Admin\RecordPaymentRequest;
use App\Models\ClassRoom;
use App\Models\Fee;
use App\Models\Payment;
use App\Models\StudentFee;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;

class FeeController extends Controller
{
    public function index(Request $request): View
    {
        $classRooms = ClassRoom::query()
            ->withCount('students')
            ->orderBy('name')
            ->get();

        $students = User::query()
            ->where('role', 'student')
            ->orderBy('name')
            ->get();

        $fees = Fee::query()
            ->with(['classRoom', 'studentFees.student'])
            ->withCount('studentFees')
            ->latest('due_date')
            ->get();

        $studentFees = $this->studentFeeQuery($request)->latest()->get();

        return view('admin.fee-management', [
            'classRooms' => $classRooms,
            'students' => $students,
            'fees' => $fees,
            'studentFees' => $studentFees,
            'filters' => $request->only(['class_room_id', 'student_id', 'status', 'due']),
        ]);
    }

    public function createFee(CreateFeeRequest $request): RedirectResponse
    {
        $fee = Fee::create($request->validated());
        $fee->load('classRoom');
        $fee->assignToStudents();

        return redirect()
            ->route('admin.fees.index')
            ->with('success', 'Fee created and assigned successfully.');
    }

    public function assignFeeToClass(AssignFeeToClassRequest $request): RedirectResponse
    {
        $fee = Fee::with('classRoom')->findOrFail($request->integer('fee_id'));
        $existingAssignments = $fee->studentFees()->count();

        $fee->assignToStudents();

        $newAssignments = $fee->studentFees()->count() - $existingAssignments;

        return redirect()
            ->route('admin.fees.index')
            ->with('success', $newAssignments > 0
                ? "Assigned fee to {$newAssignments} additional students."
                : 'All students in this class already have this fee assigned.');
    }

    public function recordPayment(RecordPaymentRequest $request): RedirectResponse
    {
        $studentFee = StudentFee::with(['student', 'fee'])->findOrFail($request->integer('student_fee_id'));
        $amount = (float) $request->input('amount');

        if ($amount > $studentFee->pendingAmount()) {
            return redirect()
                ->route('admin.fees.index')
                ->withErrors(['amount' => 'Payment amount cannot exceed pending dues.'])
                ->withInput();
        }

        DB::transaction(function () use ($request, $studentFee, $amount): void {
            Payment::create([
                'student_fee_id' => $studentFee->id,
                'amount' => $amount,
                'payment_method' => $request->input('payment_method'),
                'transaction_id' => $request->input('transaction_id'),
                'paid_at' => $request->date('paid_at'),
            ]);

            $studentFee->paid_amount = (float) $studentFee->paid_amount + $amount;
            $studentFee->syncStatus();
            $studentFee->save();
        });

        return redirect()
            ->route('admin.fees.index')
            ->with('success', 'Payment recorded successfully.');
    }

    public function showStudentFees(User $user): View
    {
        abort_unless($user->isStudent(), 404);

        $studentFees = $user->fees()
            ->with(['fee.classRoom', 'payments'])
            ->latest()
            ->get();

        return view('admin.student-fees', [
            'student' => $user,
            'studentFees' => $studentFees,
        ]);
    }

    public function invoice(StudentFee $studentFee): View
    {
        $studentFee->load(['student', 'fee.classRoom', 'payments']);

        return view('admin.fee-invoice', [
            'studentFee' => $studentFee,
        ]);
    }

    public function receipt(Payment $payment): View
    {
        $payment->load(['studentFee.student', 'studentFee.fee.classRoom']);

        return view('admin.payment-receipt', [
            'payment' => $payment,
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $studentFees = $this->studentFeeQuery($request)->oldest('id')->get();

        return response()->streamDownload(function () use ($studentFees): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Student', 'Class', 'Fee', 'Total', 'Paid', 'Pending', 'Status', 'Due Date']);

            foreach ($studentFees as $studentFee) {
                fputcsv($handle, [
                    $studentFee->student->name,
                    $studentFee->fee->classRoom->name,
                    $studentFee->fee->title,
                    $studentFee->total_amount,
                    $studentFee->paid_amount,
                    $studentFee->pendingAmount(),
                    $studentFee->status,
                    $studentFee->fee->due_date->format('Y-m-d'),
                ]);
            }

            fclose($handle);
        }, 'fee-report.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function studentFeeQuery(Request $request)
    {
        return StudentFee::query()
            ->with(['student', 'fee.classRoom', 'payments'])
            ->when($request->filled('student_id'), fn ($query) => $query->where('user_id', $request->integer('student_id')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->when($request->filled('class_room_id'), fn ($query) => $query->whereHas('fee', fn ($feeQuery) => $feeQuery->where('class_room_id', $request->integer('class_room_id'))))
            ->when($request->input('due') === 'overdue', fn ($query) => $query
                ->whereIn('status', ['pending', 'partial'])
                ->whereHas('fee', fn ($feeQuery) => $feeQuery->whereDate('due_date', '<', now()->toDateString())));
    }
}
