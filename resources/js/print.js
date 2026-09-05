document.addEventListener("DOMContentLoaded", function () {
    // The registration form and the ticket confirmation are both served
    // from "/kiosk" (the POST back to the same URL renders the ticket
    // view directly, no redirect) — so the pathname alone can't tell them
    // apart. Only the ticket page actually has a #print button.
    const printButton = document.getElementById('print');
    if (!printButton) {
        return;
    }

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
    });
});
