/**
 * AJAX submission for the admin create / edit / delete forms (staff and
 * services). Any <form data-ajax-form data-action="..."> opts in.
 *
 * Replaces a copy that was wired up by id prefix (`form[id^=staffForm]`)
 * and reported every outcome through a blocking alert(). Now: the button
 * disables while the request is in flight (so a double-click can't create
 * a staff member twice), validation errors appear inline in the modal, and
 * success is a toast that survives the reload that follows.
 */
import { onReady } from './lib/dom';
import { request } from './lib/http';
import { toastAfterReload } from './lib/toast';

function showErrors(box, error) {
    if (!box) return;

    const messages = Object.values(error.errors ?? {}).flat();
    const lines = messages.length ? messages : [error.message];

    box.replaceChildren(
        ...lines.map((line) => {
            const item = document.createElement('li');
            item.textContent = line;
            return item;
        }),
    );
    box.hidden = false;
}

onReady(() => {
    document.querySelectorAll('form[data-ajax-form]').forEach((form) => {
        const submit = form.querySelector('[type="submit"]');
        const errorBox = form.querySelector('[data-form-errors]');
        let pending = false;

        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            if (pending) return;

            pending = true;
            if (submit) submit.disabled = true;
            if (errorBox) errorBox.hidden = true;

            try {
                // The form carries its own _method (PUT/DELETE) and _token,
                // so this is always a real POST and Laravel does the
                // method spoofing the usual way.
                const data = await request(form.dataset.action, { method: 'POST', body: new FormData(form) });

                toastAfterReload(data.message, 'success');
                window.location.reload();
            } catch (error) {
                showErrors(errorBox, error);
                pending = false;
                if (submit) submit.disabled = false;
            }
        });
    });
});
