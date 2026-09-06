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
            //
            // 0, not false: Rule::exists()->where() doesn't stay a native
            // query callback — it serializes back into a string rule
            // ("exists:services,id,is_internal,\"...\"") via
            // DatabaseRule::formatWheres(), which builds that string with
            // str_replace('"', '""', $value). PHP silently casts a bool
            // argument there, so `false` becomes '' — an empty condition
            // that reaches Postgres as invalid boolean input, rather than
            // going through Connection::prepareBindings()'s normal
            // bool-to-int handling like an ordinary ->where() call would.
            // `0` round-trips through that string form intact.
            'service_id' => ['required', 'integer', Rule::exists('services', 'id')->where('is_internal', 0)],
            'priority' => ['sometimes', 'boolean'],
        ];
    }
}
