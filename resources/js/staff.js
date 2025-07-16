document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('staffForm');

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const actionUrl = form.dataset.action;
        const formData = new FormData(form);

        fetch(actionUrl, {
            method: 'POST',
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
            const successMessage = document.getElementById('success-message');
            let textStyle = "";
            data.status === 'success' ? textStyle = "text-green-500" : textStyle = "text-red-500";
            successMessage.textContent = data.message;
            successMessage.classList.add(textStyle);
            successMessage.classList.remove('hidden');
            

            setTimeout(() => {
                 successMessage.classList.add('hidden');
                 successMessage.classList.remove(textStyle);
            }, 3000);

            if(data.status === 'success'){
                form.reset();
            }
            
        })
        .catch(error => {
            alert(error.message);
        });
    });
});
