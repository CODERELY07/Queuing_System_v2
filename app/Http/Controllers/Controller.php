<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

abstract class Controller
{
    protected function jsonSuccess(string $message, array $extra = []): JsonResponse
    {
        return response()->json(array_merge(['status' => 'success', 'message' => $message], $extra));
    }

    /**
     * A failure the admin can act on ("reassign this service's staff
     * first"). Sent with a real error status — it used to be a 200 with
     * `status: error` in the body, which every client then had to remember
     * to check separately.
     *
     * Unexpected failures deliberately aren't caught anywhere in the admin
     * controllers: Laravel logs them and, for these JSON requests, answers
     * with a generic message. The old catch-all handlers returned
     * `$e->getMessage()` instead — and a QueryException's message is the
     * whole SQL statement with its bound values (table and column names, a
     * hashed password), shown straight in the admin's alert box.
     */
    protected function jsonError(string $message, int $status = 422): JsonResponse
    {
        return response()->json(['status' => 'error', 'message' => $message], $status);
    }
}
