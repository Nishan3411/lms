<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewLeaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() || $this->user()?->isTeacher();
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:approved,rejected'],
            'review_note' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
