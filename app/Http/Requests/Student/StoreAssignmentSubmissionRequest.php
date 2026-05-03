<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreAssignmentSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isStudent() ?? false;
    }

    public function rules(): array
    {
        return [
            'answer_text' => ['nullable', 'string', 'max:10000'],
            'submission_file' => ['nullable', 'file', 'mimes:pdf,doc,docx,zip,jpg,jpeg,png', 'max:20480'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if (! $this->filled('answer_text') && ! $this->hasFile('submission_file')) {
                    $validator->errors()->add('submission_file', 'Please provide answer text or upload a submission file.');
                }
            },
        ];
    }
}
