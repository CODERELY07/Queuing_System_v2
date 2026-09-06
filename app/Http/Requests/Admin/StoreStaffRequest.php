<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreStaffRequest extends FormRequest
{
    /**
     * The route already sits behind `user_type:admin` middleware; this is
     * the FormRequest's own say on who may submit it, which is what
     * `authorize()` exists for.
     */
    public function authorize(): bool
    {
        return $this->user()?->user_type === 'admin';
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50', 'unique:users,name'],
            'email' => ['required', 'email', 'unique:users,email'],
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'password' => ['required', 'string', 'confirmed'],
        ];
    }
}
