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
            // Letters (any script), digits, spaces and the punctuation real
            // names use. Never markup: this value is shown on the staff
            // console and the waiting-room display and spoken aloud.
            //
            // Unique only among tickets still *in the queue* for the same
            // service. It used to be globally unique forever — so once
            // anyone named "Maria Santos" had taken a ticket, no other
            // Maria Santos (or the same one, on a later visit) could ever
            // take one again.
            'name' => [
                'bail',
                'required',
                'string',
                'min:2',
                'max:100',
                "regex:/^[\\pL\\pM\\pN][\\pL\\pM\\pN\\s.,'’\\-]*$/u",
                Rule::unique('client_queues', 'name')->where(fn ($query) => $query
                    ->where('service_id', $this->integer('service_id'))
                    ->whereIn('status', ['waiting', 'serving'])
                    ->whereNull('deleted_at')),
            ],
            // `bail`: without it a non-numeric id still reached the exists
            // query below, which Postgres answers with a type error (a 500)
            // instead of a validation message.
            //
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
            'service_id' => ['bail', 'required', 'integer', Rule::exists('services', 'id')->where('is_internal', 0)],
            'priority' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex' => 'Please use letters, numbers, spaces and . , \' - only.',
            'name.unique' => 'There is already an active ticket under this name for that service.',
        ];
    }
}
