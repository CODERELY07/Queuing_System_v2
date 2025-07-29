document.addEventListener("DOMContentLoaded", function () {
    function loadDashboardData() {
        fetch('/staff/dashboard-data')
            .then(response => response.json())
            .then(data => {
                document.getElementById('waiting-count').textContent = data.waiting;
                document.getElementById('waiting-patient-name').textContent = data.waitingPatient.name;
                document.getElementById('waiting-patient-number').textContent = data.waitingPatient.number;

                const recentList = document.getElementById('recent-activity');
                recentList.innerHTML = '';
                data.recent.forEach(item => {
                    const li = document.createElement('li');
                    li.textContent = `${item.number} - ${item.status}`;
                    recentList.appendChild(li);
                });
            });
    }

    loadDashboardData();

    document.getElementById('call-next-btn').addEventListener('click', function () {
        fetch('/staff/call-next', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
        })
        .then(res => res.json())
        .then(data => {
            console.log(data);
            loadDashboardData();
        });
    });
});

