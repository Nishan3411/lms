<?php

namespace App\Http\Requests\Parent;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreLeaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isParent() ?? false;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'integer', 'exists:users,id'],
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
                $studentIsChild = $this->user()->children()->whereKey($this->integer('student_id'))->exists();

                if (! $studentIsChild) {
                    $validator->errors()->add('student_id', 'Selected student must be linked to this parent.');
                    return;
                }

                if ($this->filled('class_room_id')) {
                    $student = \App\Models\User::find($this->integer('student_id'));

                    if (! $student?->enrolledClasses()->whereKey($this->integer('class_room_id'))->exists()) {
                        $validator->errors()->add('class_room_id', 'Selected class must belong to the selected student.');
                    }
                }
            },
        ];
    }
}
