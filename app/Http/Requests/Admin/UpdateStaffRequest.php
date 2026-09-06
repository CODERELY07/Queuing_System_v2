<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->user_type === 'admin';
    }

    public function rules(): array
    {
        // The {id} route parameter, so the uniqueness checks ignore the
        // record being edited instead of rejecting its own current value.
        $staffId = $this->route('id');

        return [
            'name' => ['required', 'string', 'max:50', "unique:users,name,{$staffId}"],
            'email' => ['required', 'email', "unique:users,email,{$staffId}"],
            'service_id' => ['required', 'integer', 'exists:services,id'],
            // Optional on update — leaving it blank keeps the current password.
            'password' => ['nullable', 'string', 'confirmed'],
        ];
    }
}
