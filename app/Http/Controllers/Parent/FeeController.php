<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\StudentFee;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeeController extends Controller
{
    public function index(Request $request): View
    {
        $parent = $request->user()->load([
            'children.fees.fee.classRoom',
            'children.fees.payments',
        ]);

        return view('parent.fees', [
            'parent' => $parent,
            'children' => $parent->children,
        ]);
    }

    public function invoice(Request $request, StudentFee $studentFee): View
    {
        $studentFee->load(['student', 'fee.classRoom', 'payments']);

        abort_unless($request->user()->children()->whereKey($studentFee->user_id)->exists(), 403);

        return view('admin.fee-invoice', [
            'studentFee' => $studentFee,
        ]);
    }

    public function receipt(Request $request, Payment $payment): View
    {
        $payment->load(['studentFee.student', 'studentFee.fee.classRoom']);

        abort_unless($request->user()->children()->whereKey($payment->studentFee->user_id)->exists(), 403);

        return view('admin.payment-receipt', [
            'payment' => $payment,
        ]);
    }
}
