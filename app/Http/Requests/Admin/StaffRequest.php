<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * The validation shared by creating and editing a staff account; the
 * concrete requests only differ in whether a password is mandatory.
 */
abstract class StaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->user_type === 'admin';
    }

    /**
     * 'required' when creating, 'nullable' when editing (blank keeps the
     * current password).
     */
    abstract protected function passwordPresence(): string;

    public function rules(): array
    {
        $ignoreId = $this->route('id');

        return [
            'name' => ['required', 'string', 'max:50', Rule::unique('users', 'name')->ignore($ignoreId)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($ignoreId)],
            // Real departments only: the internal "Admin" bucket is for
            // admin accounts and was selectable here before. A closure,
            // not ->where('is_internal', false), so the boolean goes
            // through the query builder's own binding (see
            // StoreClientQueueRequest for why the string form is avoided).
            'service_id' => [
                'bail',
                'required',
                'integer',
                Rule::exists('services', 'id')->where(fn ($query) => $query->where('is_internal', false)),
            ],
            // There was no minimum before — a one-character password was
            // accepted. 72 is bcrypt's limit; anything longer is silently
            // truncated, so it's rejected instead.
            'password' => [$this->passwordPresence(), Password::min(8)->max(72), 'confirmed'],
        ];
    }
}
