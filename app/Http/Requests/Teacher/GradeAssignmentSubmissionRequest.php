<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class GradeAssignmentSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isTeacher() ?? false;
    }

    public function rules(): array
    {
        return [
            'marks_obtained' => ['required', 'numeric', 'min:0'],
            'teacher_feedback' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $submission = $this->route('assignmentSubmission');

                if (! $submission) {
                    return;
                }

                if ((float) $this->input('marks_obtained') > (float) $submission->assignment->max_marks) {
                    $validator->errors()->add('marks_obtained', 'Marks cannot exceed assignment maximum marks.');
                }
            },
        ];
    }
}
