<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->user_type === 'admin';
    }

    public function rules(): array
    {
        // The {id} route parameter, so the uniqueness checks ignore the
        // record being edited instead of rejecting its own current value.
        $serviceId = $this->route('id');

        return [
            'name' => ['required', 'string', 'max:100', "unique:services,name,{$serviceId}"],
            'prefix' => ['required', 'string', 'max:5', "unique:services,prefix,{$serviceId}"],
        ];
    }
}
