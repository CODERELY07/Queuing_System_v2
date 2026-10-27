<?php

namespace App\Http\Requests\Admin;

class StoreStaffRequest extends StaffRequest
{
    protected function passwordPresence(): string
    {
        return 'required';
    }
}
