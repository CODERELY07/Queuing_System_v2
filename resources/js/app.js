import './bootstrap';
import './counter';
import './display';
import './admin-forms';
import './print';

import Alpine from 'alpinejs';

import liveStatus from './live-status';
import { onReady } from './lib/dom';
import { flushQueuedToast } from './lib/toast';

window.Alpine = Alpine;
Alpine.data('liveStatus', liveStatus);
Alpine.start();

// Shows the confirmation for an action that finished by reloading the page.
onReady(flushQueuedToast);
