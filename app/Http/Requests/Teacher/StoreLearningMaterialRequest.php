<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreLearningMaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isTeacher() ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'class_room_id' => ['required', 'integer', 'exists:class_rooms,id'],
            'subject_id' => [
                'nullable',
                'integer',
                Rule::exists('subjects', 'id')->where(
                    fn ($query) => $query->where('class_room_id', $this->integer('class_room_id'))
                ),
            ],
            'file' => ['required', 'file', 'mimes:pdf,ppt,pptx', 'max:20480'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if (! $this->user()?->teachingClasses()->whereKey($this->integer('class_room_id'))->exists()) {
                    $validator->errors()->add('class_room_id', 'You can only upload materials for classes assigned to you.');
                }
            },
        ];
    }
}
