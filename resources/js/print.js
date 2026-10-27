/**
 * The kiosk ticket screen: print the ticket, then reset the kiosk for the
 * next visitor — immediately after printing, or after 15 seconds of nobody
 * touching it.
 *
 * Active only on the page that has a #print button.
 */
import { byId, onReady } from './lib/dom';

const RESET_AFTER_SECONDS = 15;

onReady(() => {
    const printButton = byId('print');
    if (!printButton) return;

    // The "Back to Kiosk" link's own href — rendered server-side from
    // route('kiosk') — is the single source of truth for where "back"
    // means, rather than hardcoding the path a second time here.
    const kioskUrl = byId('back-to-kiosk')?.href || '/kiosk';
    const goBack = () => {
        window.location.href = kioskUrl;
    };

    // Print the page itself: app.css's @media print rules show only the
    // ticket (#print-body) and hide everything else. This replaces a
    // popup window whose hand-written stylesheet never matched the
    // ticket's classes — so the printed number came out at body-text size
    // — and which any popup blocker could kill.
    printButton.addEventListener('click', () => window.print());

    // Fires when the print dialog closes, whether the visitor printed or
    // cancelled — either way they're done with this screen. The short
    // delay lets the browser finish handing the job to the printer.
    window.addEventListener('afterprint', () => setTimeout(goBack, 800));

    // Kiosk self-reset: nobody is meant to linger on the ticket screen,
    // so whether they printed or just walked off, the machine is ready
    // for the next visitor within a bounded time.
    const secondsEl = byId('auto-redirect-seconds');
    let secondsLeft = RESET_AFTER_SECONDS;

    const countdown = setInterval(() => {
        secondsLeft -= 1;

        if (secondsEl) secondsEl.textContent = secondsLeft;

        if (secondsLeft <= 0) {
            clearInterval(countdown);
            goBack();
        }
    }, 1000);
});
