document.addEventListener("DOMContentLoaded", function () {
    // NOTE:: THis need to update it should no console error message in every page
    displayQeueue();
    call('call-next-btn');
    call('call-prev-btn');
    call('call');
    loadDashboardData();

    let selectedVoice = null;
    const synth = window.speechSynthesis;

    function initVoice() {
        const voices = synth.getVoices();

        if (voices.length > 0 && !selectedVoice) {
            selectedVoice = voices.find(v =>
                v.lang === 'en-US' && v.name === "Google US English"
            ) || voices[0];

            // console.log("Voice:", selectedVoice);
        }
    }

    initVoice();

    if (typeof speechSynthesis !== 'undefined' && speechSynthesis.onvoiceschanged !== undefined) {
        speechSynthesis.onvoiceschanged = initVoice;
    }


    window.Echo.channel('queue').listen('.queue.call', (event) => {
        // console.log('Event received:', event);
        VoiceQueue(event);
        displayQeueue(event);
    });

    // Speak the queue call
    function VoiceQueue(ticket) {
        if (window.location.pathname !== '/display') {
            return; 
        }
        if (!selectedVoice) {
            console.warn("Voice not ready.");
            return;
        }

        const utterance = new SpeechSynthesisUtterance(
            `${ticket.name}, ticket number ${ticket.queue_number} Please Come In`
        );
        utterance.voice = selectedVoice;

        synth.speak(utterance);
    }

    window.Echo.channel('queue').listen('.queue.next', (event) => {
    //    displayQeueue(event);
    });

    window.Echo.channel('queue').listen('.queue.prev', (event) => {
        displayQeueue(event);
    });

 


     function displayQeueue(){
        // Display
        if(window.location.pathname === "/display"){
             fetch('/display/serving-patients')
            .then(response => response.json())
            .then(data => {
            
                const container = document.getElementById('serving-list');
                container.innerHTML = ''; 

                data.forEach(service => {
                    const servingInfo = service.serving
                        ? `<p class="font-bold text-7xl text-center text-blue-600 animate-pulse">${service.serving.number}</p>
                        <p class="text-gray-700 dark:text-gray-300 text-center text-xl mt-2 font-medium">${service.serving.name}</p>`
                        : `<span class="italic text-gray-500 dark:text-gray-400 text-center block py-8">No patient being served</span>`;

                    container.innerHTML += `
                        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-6 border border-gray-200 dark:border-gray-700 transition-all hover:shadow-xl">
                            <h4 class="text-2xl font-bold text-gray-800 dark:text-white mb-4 text-center">${service.service_name}</h4>
                            <div class="min-h-32 flex flex-col justify-center">
                                ${servingInfo}
                            </div>
                        </div>
                    `;
                });
            })
            .catch(error => console.error('Error fetching serving patients:', error));
            }
        
    }


    // Staff Dashboard 
    function loadDashboardData() {
        if(window.location.pathname === "/dashboard/staff"){
            fetch('/staff/dashboard-data')
            .then(response => response.json())
            .then(data => {
              
                document.getElementById('waiting-count').textContent = data.waiting;
                document.getElementById('serving-patient-name').textContent = data.servingPatient.name;
                document.getElementById('serving-patient-number').textContent = data.servingPatient.number;

                const recentList = document.getElementById('recent-activity');
                recentList.innerHTML = '';
                data.recent.forEach(item => {
                    const li = document.createElement('li');
                    li.textContent = `${item.number} - ${item.status}`;
                    recentList.appendChild(li);
                });
            });
        }
     
    }

 

    // Next 
    function call(id) {
        const button = document.getElementById(id);
        if (!button) return;

        let endpoint = '';

        if (id === 'call-next-btn') {
            endpoint = '/staff/call-next';
        } else if (id === 'call-prev-btn') {
            endpoint = '/staff/call-previous';
        } else if(id === 'call'){
            endpoint = '/staff/call';
        }else {
            console.warn('Unknown button ID:', id);
            return;
        }

        button.addEventListener('click', function () {
            fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
            })
            .then(res => res.json())
            .then(data => {
                // console.log(data);
                loadDashboardData(); 
            })
            .catch(err => console.error('Error:', err));
        });
    }
});

