<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class GuestLayout extends Component
{
    /**
     * @param bool $flush Skip the layout's default vertical padding around
     *                     the page content. The homepage builds its own
     *                     hero-to-footer rhythm and needs to sit flush
     *                     against the header; every other guest page (auth
     *                     forms, kiosk, display) keeps the default padding.
     * @param bool $hideFooter Skip the site footer entirely — for pages
     *                         that are a task or a passive display rather
     *                         than a place visitors browse from (kiosk,
     *                         the wall display, the login form).
     * @param bool $hideHeader Skip the public nav bar too — for the
     *                         dedicated per-department displays, which run
     *                         unattended on a mounted screen and have no
     *                         reason to invite navigation away from
     *                         themselves.
     */
    public function __construct(public bool $flush = false, public bool $hideFooter = false, public bool $hideHeader = false)
    {
    }

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.guest');
    }
}
