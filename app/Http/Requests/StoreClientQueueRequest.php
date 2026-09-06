<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClientQueueRequest extends FormRequest
{
    /**
     * The public kiosk — anyone walking up to the machine can pull a
     * ticket, no login involved.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:client_queues,name'],
            // Rejects the internal "Admin" service, not just any missing
            // id — the picker already hides it, but this is a public,
            // unauthenticated endpoint, so a crafted request naming its id
            // directly needs to be rejected server-side too.
            'service_id' => ['required', 'integer', Rule::exists('services', 'id')->where('is_internal', false)],
            'priority' => ['sometimes', 'boolean'],
        ];
    }
}
