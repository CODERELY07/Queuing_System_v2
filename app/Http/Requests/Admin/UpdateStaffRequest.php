<?php

namespace App\Http\Requests\Admin;

class UpdateStaffRequest extends StaffRequest
{
    protected function passwordPresence(): string
    {
        return 'nullable';
    }
}
