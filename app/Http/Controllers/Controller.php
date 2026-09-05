<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * {status: 'success', message, ...} JSON response used by the admin AJAX endpoints.
     * Kept at HTTP 200 to match the frontend, which reads the `status` field rather than the HTTP code.
     */
    protected function jsonSuccess(string $message, array $extra = [])
    {
        return response()->json(array_merge(['status' => 'success', 'message' => $message], $extra));
    }

    /**
     * {status: 'error', message} JSON response used by the admin AJAX endpoints.
     */
    protected function jsonError(string $message)
    {
        return response()->json(['status' => 'error', 'message' => $message]);
    }
}
