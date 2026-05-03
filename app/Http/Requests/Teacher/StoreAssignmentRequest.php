<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isTeacher() ?? false;
    }

    public function rules(): array
    {
        return [
            'class_room_id' => ['required', 'integer', 'exists:class_rooms,id'],
            'subject_id' => [
                'nullable',
                'integer',
                Rule::exists('subjects', 'id')->where(
                    fn ($query) => $query->where('class_room_id', $this->integer('class_room_id'))
                ),
            ],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'max_marks' => ['required', 'integer', 'min:1', 'max:1000'],
            'due_at' => ['required', 'date'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,doc,docx,ppt,pptx,zip', 'max:20480'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if (! $this->user()?->teachingClasses()->whereKey($this->integer('class_room_id'))->exists()) {
                    $validator->errors()->add('class_room_id', 'You can only create assignments for classes assigned to you.');
                }
            },
        ];
    }
}
