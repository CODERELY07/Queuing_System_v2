<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientQueueRequest;
use App\Models\ClientQueues;
use App\Models\Service;
use App\Services\ClientQueueService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientQueueController extends Controller
{
    /** Session key holding the id of the ticket this kiosk just issued. */
    private const TICKET_SESSION_KEY = 'kiosk.ticket_id';

    public function __construct(private readonly ClientQueueService $clientQueues)
    {
    }

    public function index(): View
    {
        // publicFacing(): excludes the internal "Admin" service — a visitor
        // reaching the kiosk directly (rather than via the home page, which
        // already curates its own list the same way) should never be able
        // to pull a ticket for it.
        $services = Service::publicFacing()->get();

        return view('kiosk.index', compact('services'));
    }

    /**
     * Issue the ticket, then redirect to its confirmation page.
     *
     * Post/Redirect/Get: this used to render the ticket view straight from
     * the POST, so reloading the confirmation re-submitted the form and
     * silently issued a second ticket. Now the POST only ever creates one;
     * the page the visitor lands on is a plain GET they can reload freely.
     */
    public function store(StoreClientQueueRequest $request): RedirectResponse
    {
        $queue = $this->clientQueues->createTicket($request->validated());

        $request->session()->put(self::TICKET_SESSION_KEY, $queue->id);

        return redirect()->route('kiosk.ticket');
    }

    /**
     * The confirmation for the ticket this kiosk just issued. With nothing
     * in the session (a bookmark, an expired session) there's no ticket to
     * show, so it's back to the registration form.
     */
    public function show(Request $request): View|RedirectResponse
    {
        $ticketId = $request->session()->get(self::TICKET_SESSION_KEY);

        $queue = $ticketId ? ClientQueues::with('service')->find($ticketId) : null;

        if (!$queue) {
            return redirect()->route('kiosk');
        }

        return view('kiosk.ticket', [
            'queue' => $queue,
            'estimatedWaitTime' => $this->clientQueues->estimatedWaitMinutes($queue),
        ]);
    }
}
