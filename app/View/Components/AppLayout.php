<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    /**
     * @param string|null $title  Browser-tab title ("Queues · MedQueue").
     * @param bool        $search Show the topbar search box. Off by
     *                            default: `?q=` only means something on the
     *                            pages that read it (the queue tables and
     *                            the staff list) — everywhere else the box
     *                            used to accept input and silently do
     *                            nothing.
     */
    public function __construct(public ?string $title = null, public bool $search = false)
    {
    }

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.app');
    }
}
