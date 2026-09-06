document.addEventListener("DOMContentLoaded", function () {
    // The registration form and the ticket confirmation are both served
    // from "/kiosk" (the POST back to the same URL renders the ticket
    // view directly, no redirect) — so the pathname alone can't tell them
    // apart. Only the ticket page actually has a #print button.
    const printButton = document.getElementById('print');
    if (!printButton) {
        return;
    }

    // Reuses the "Back to Kiosk" link's own href — rendered server-side via
    // route('kiosk') — as the single source of truth for where "back"
    // means, rather than hardcoding the path again here.
    const kioskUrl = document.getElementById('back-to-kiosk')?.href || '/kiosk';

    printButton.addEventListener('click', function () {
        const printContents = document.getElementById('print-body').innerHTML;
        const printWindow = window.open('', '', 'width=800,height=600');
        printWindow.document.writeln(`
            <html>
                <head>
                    <title>Print Queue Number</title>
                    <style>
                        body { font-family: sans-serif; text-align: center; padding: 20px; }
                        .text-5xl { font-size: 3rem; }
                        .font-bold { font-weight: bold; }
                        .mb-4 { margin-bottom: 1rem; }
                        .py-6 { padding-top: 1.5rem; padding-bottom: 1.5rem; }
                    </style>
                </head>
                <body onload="window.print(); window.close();">
                    ${printContents}
                </body>
            </html>
        `);
        printWindow.document.close();

        // The print popup above is independent of this page and keeps
        // printing (then closes itself) even after this one navigates away
        // — the short delay just gives the print dialog a moment to
        // actually appear before the kiosk resets for the next visitor.
        setTimeout(function () {
            window.location.href = kioskUrl;
        }, 1500);
    });

    // Kiosk self-reset: nobody's meant to linger on the ticket screen —
    // whether they printed or just walked off without touching anything,
    // the machine needs to be ready for the next visitor within a bounded
    // time either way.
    let secondsLeft = 15;
    const secondsEl = document.getElementById('auto-redirect-seconds');

    const countdown = setInterval(function () {
        secondsLeft -= 1;

        if (secondsEl) {
            secondsEl.textContent = secondsLeft;
        }

        if (secondsLeft <= 0) {
            clearInterval(countdown);
            window.location.href = kioskUrl;
        }
    }, 1000);
});
