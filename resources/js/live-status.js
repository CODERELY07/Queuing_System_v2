/**
 * Alpine component behind the "Live / Offline" indicator and clock in the
 * page header. Reflects the Echo websocket this page actually listens on —
 * not decoration — plus a ticking clock, so "as of when" is never
 * ambiguous on a screen that never gets manually refreshed.
 *
 * Usage: <span x-data="liveStatus">…</span>
 */
import { onConnectionChange } from './lib/realtime';

const formatTime = () => new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });

export default () => ({
    time: formatTime(),
    connected: false,

    init() {
        setInterval(() => {
            this.time = formatTime();
        }, 1000);

        onConnectionChange((connected) => {
            this.connected = connected;
        });
    },
});
