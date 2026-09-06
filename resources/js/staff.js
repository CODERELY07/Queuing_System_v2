document.addEventListener('DOMContentLoaded', function () {
    function formRequest(id){
        document.querySelectorAll(`form[id^=${id}]`).forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                // Confirmation already happened in the modal this form lives
                // in — the Delete button there IS the confirmation, so by
                // the time this fires the user has already agreed.
                const method = form.querySelector('input[name="_method"]')?.value || 'POST';

                const actionUrl = form.dataset.action;
                const formData = new FormData(form);

                fetch(actionUrl, {
                    method: method,
                    body: new URLSearchParams(formData),
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(async response => {
                    if (response.ok) {
                        return response.json();
                    } else if (response.status === 422) {
                        const errorData = await response.json();
                        const errors = Object.values(errorData.errors).flat().join("\n");
                        throw new Error(errors);
                    } else {
                        throw new Error('Something went wrong.');
                    }
                })
                .then(data => {
                    alert(data.message);
                    if (data.status === 'success') {
                        form.reset();
                        location.reload();
                    }
                })
                .catch(error => {
                    alert(error.message);
                });
            });
        });
    }
    
    formRequest("staffForm");
    formRequest("staff-delete");
    formRequest("serviceForm");
    formRequest("service-delete");
});
