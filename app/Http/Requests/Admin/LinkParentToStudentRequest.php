<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LinkParentToStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'parent_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', 'parent')),
            ],
            'student_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', 'student')),
            ],
        ];
    }
}
