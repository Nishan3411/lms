<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RecordPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'student_fee_id' => ['required', 'integer', 'exists:student_fees,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'string', 'max:100'],
            'transaction_id' => ['nullable', 'string', 'max:255'],
            'paid_at' => ['required', 'date'],
        ];
    }
}
