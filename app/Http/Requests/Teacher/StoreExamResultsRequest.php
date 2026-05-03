<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreExamResultsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isTeacher() ?? false;
    }

    public function rules(): array
    {
        return [
            'results' => ['required', 'array', 'min:1'],
            'results.*.student_id' => ['required', 'integer', 'exists:users,id'],
            'results.*.marks_obtained' => ['nullable', 'numeric', 'min:0'],
            'results.*.remarks' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $exam = $this->route('exam');

                if (! $exam) {
                    return;
                }

                $validStudentIds = $exam->classRoom->students()->pluck('users.id')->all();

                foreach ((array) $this->input('results', []) as $index => $result) {
                    $studentId = (int) ($result['student_id'] ?? 0);
                    $marks = $result['marks_obtained'] ?? null;

                    if (! in_array($studentId, $validStudentIds, true)) {
                        $validator->errors()->add("results.$index.student_id", 'Student must belong to the exam class.');
                    }

                    if ($marks !== null && (float) $marks > (float) $exam->max_marks) {
                        $validator->errors()->add("results.$index.marks_obtained", 'Marks cannot exceed exam maximum marks.');
                    }
                }
            },
        ];
    }
}
