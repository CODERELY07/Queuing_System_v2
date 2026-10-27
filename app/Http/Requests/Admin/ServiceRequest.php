<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * The validation shared by creating and editing a service. The two
 * concrete requests below it differ in name only — they used to be
 * near-identical copies, one with the uniqueness checks hand-built as
 * "unique:services,name,{$id}" strings.
 */
abstract class ServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->user_type === 'admin';
    }

    /**
     * Uppercase the prefix *before* validating. It used to be uppercased
     * afterwards in the controller, so "r" passed the uniqueness check
     * against an existing "R" and then saved as a second "R" — two
     * departments issuing indistinguishable R-001 tickets.
     */
    protected function prepareForValidation(): void
    {
        if (is_string($this->input('prefix'))) {
            $this->merge(['prefix' => strtoupper(trim($this->input('prefix')))]);
        }
    }

    public function rules(): array
    {
        // The {id} route parameter when editing (the uniqueness checks
        // must ignore the record's own current value), null when creating.
        $ignoreId = $this->route('id');

        return [
            'name' => ['required', 'string', 'max:100', Rule::unique('services', 'name')->ignore($ignoreId)],
            'prefix' => ['required', 'string', 'alpha_num', 'max:5', Rule::unique('services', 'prefix')->ignore($ignoreId)],
        ];
    }
}
