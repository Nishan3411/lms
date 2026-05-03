<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\StudentFee;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeeController extends Controller
{
    public function index(Request $request): View
    {
        $student = $request->user()->load([
            'fees.fee.classRoom',
            'fees.payments',
        ]);

        return view('student.fees', [
            'student' => $student,
            'studentFees' => $student->fees,
        ]);
    }

    public function invoice(Request $request, StudentFee $studentFee): View
    {
        abort_unless($studentFee->user_id === $request->user()->id, 403);

        $studentFee->load(['student', 'fee.classRoom', 'payments']);

        return view('admin.fee-invoice', [
            'studentFee' => $studentFee,
        ]);
    }

    public function receipt(Request $request, Payment $payment): View
    {
        $payment->load(['studentFee.student', 'studentFee.fee.classRoom']);

        abort_unless($payment->studentFee->user_id === $request->user()->id, 403);

        return view('admin.payment-receipt', [
            'payment' => $payment,
        ]);
    }
}
