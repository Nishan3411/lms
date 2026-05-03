<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreLeaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isStudent() ?? false;
    }

    public function rules(): array
    {
        return [
            'class_room_id' => ['nullable', 'integer', 'exists:class_rooms,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['required', 'string', 'max:5000'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($this->filled('class_room_id') && ! $this->user()->enrolledClasses()->whereKey($this->integer('class_room_id'))->exists()) {
                    $validator->errors()->add('class_room_id', 'Selected class must be one of your enrolled classes.');
                }
            },
        ];
    }
}
