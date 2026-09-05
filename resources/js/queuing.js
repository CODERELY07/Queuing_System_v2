document.addEventListener("DOMContentLoaded", function () {
        // Each row's "Call" button fetches directly on click — it used to
        // call the generic `call()` helper below (which attaches a fetch
        // listener) and then immediately re-click itself to fire it. Since
        // `disabled` wasn't set until after that synthetic click, the
        // re-entrant click ran the same handler again — attaching another
        // listener and clicking again — recursing until the call stack
        // overflowed. Same POST every time, so it just fetches inline.
        document.querySelectorAll('.selected-call').forEach(button => {
            button.addEventListener('click', function () {
                if (this.disabled) return;

                const queueId = this.getAttribute('data-id');
                this.disabled = true;

                fetch(`/staff/call/${queueId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                })
                .then(res => res.json())
                .then(data => {
                    console.log(data);
                    location.reload();
                })
                .catch(err => {
                    console.error('Error:', err);
                    this.disabled = false;
                });
            });
        });


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
        } else if(id === 'skip-btn'){
            endpoint = '/staff/skip';
        }
        else {
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
                console.log(data);
                loadDashboardData();
                return;
            })
            .catch(err => console.error('Error:', err));
        });
    }
    displayQeueue();
    displaySingleQueue();
    call('call-next-btn');
    call('call-prev-btn');
    call('call');
    call('skip-btn');

    // Space calls the next patient without reaching for the mouse — the
    // one shortcut a counter actually repeats all day. Ignored while a
    // form field has focus so it doesn't fire mid-search or mid-typing.
    document.addEventListener('keydown', function (e) {
        if (window.location.pathname !== '/dashboard/staff') return;
        if (e.code !== 'Space' && e.key !== ' ') return;

        const tag = document.activeElement?.tagName;
        if (tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT') return;

        e.preventDefault();
        document.getElementById('call-next-btn')?.click();
    });

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
        displaySingleQueue();
    });

    // Speak the queue call — on the all-services board, or on a
    // department's own dedicated display when the call is for that
    // department (never announces a different department's call there).
    function VoiceQueue(ticket) {
        const singleDisplay = document.getElementById('single-display');
        const onAllDisplay = window.location.pathname === '/display';
        const onMatchingSingleDisplay = singleDisplay && String(singleDisplay.dataset.serviceId) === String(ticket.service_id);

        if (!onAllDisplay && !onMatchingSingleDisplay) {
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
        displaySingleQueue();
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
                        ? `<p class="font-mono tabular-nums font-bold text-6xl sm:text-7xl text-center text-brand-600 tracking-tight animate-flash-once">${service.serving.number}</p>
                        <p class="text-gray-500 text-center text-xl mt-2 font-medium">${service.serving.name}</p>`
                        : `<span class="italic text-gray-400 text-center block py-8">No patient being served</span>`;

                    const nextInfo = service.next && service.next.length
                        ? `<div class="px-6 pb-5 pt-4 border-t border-gray-100">
                            <p class="text-[11px] font-semibold uppercase tracking-widest text-gray-400 text-center mb-2">Up next</p>
                            <p class="font-mono tabular-nums text-gray-500 text-center text-sm">${service.next.join(' &middot; ')}</p>
                          </div>`
                        : '';

                    container.innerHTML += `
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-md overflow-hidden transition-shadow hover:shadow-lg animate-fade-up">
                            <div class="bg-brand-600 px-4 py-3">
                                <h4 class="text-sm font-bold text-white text-center uppercase tracking-wide">${service.service_name}</h4>
                            </div>
                            <div class="min-h-32 flex flex-col justify-center px-6 py-6">
                                ${servingInfo}
                            </div>
                            ${nextInfo}
                        </div>
                    `;
                });
            })
            .catch(error => console.error('Error fetching serving patients:', error));
            }

    }

    // The dedicated one-department display — gated on the page actually
    // having the #single-display element rather than on the URL, so it
    // doesn't care what the route looks like, only what's on the page.
    function displaySingleQueue() {
        const el = document.getElementById('single-display');
        if (!el) return;

        const serviceSlug = el.dataset.serviceSlug;

        fetch(`/display/${serviceSlug}/serving-patient`)
            .then(response => response.json())
            .then(data => {
                const numberEl = document.getElementById('single-serving-number');
                const nameEl = document.getElementById('single-serving-name');
                const nextEl = document.getElementById('single-next');

                if (data.serving) {
                    numberEl.textContent = data.serving.number;
                    nameEl.textContent = data.serving.name;
                } else {
                    numberEl.textContent = '—';
                    nameEl.textContent = 'No patient being served';
                }

                nextEl.textContent = data.next && data.next.length ? data.next.join(' · ') : '—';
            })
            .catch(error => console.error('Error fetching single display:', error));
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
                document.getElementById('serving-patient-priority').classList.toggle('hidden', !data.servingPatient.priority);

                const recentList = document.getElementById('recent-activity');
                recentList.innerHTML = '';
                if (data.recent.length === 0) {
                    recentList.innerHTML = '<li class="text-gray-400 dark:text-gray-500">Nothing waiting.</li>';
                }
                data.recent.forEach(item => {
                    const li = document.createElement('li');
                    li.className = 'flex items-center justify-between gap-2';
                    li.innerHTML = `
                        <span class="font-mono tabular-nums">${item.number}</span>
                        ${item.priority ? '<span class="text-[11px] font-semibold uppercase tracking-wide text-violet-600 dark:text-violet-400">Priority</span>' : ''}
                    `;
                    recentList.appendChild(li);
                });
            });
        }
     
    }

 

    // Next 
    
});

