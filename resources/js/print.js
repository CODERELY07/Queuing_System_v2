document.addEventListener("DOMContentLoaded", function () {
if(window.location.pathname === "/kiosk"){
    document.getElementById('print').addEventListener('click', function () {
    
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
  }
});
















































