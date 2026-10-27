{{-- Connection status + clock — see resources/js/live-status.js. "Polling"
     (not "Offline") when the websocket isn't connected: the page isn't
     dead, it's refreshing itself over HTTP every few seconds instead. --}}
<span x-data="liveStatus" {{ $attributes->merge(['class' => 'inline-flex items-center gap-3 text-sm tabular-nums text-gray-500 dark:text-gray-400']) }}
      :title="connected ? 'Connected — updates arrive instantly' : 'Real-time connection unavailable — refreshing every few seconds'">
    <span class="inline-flex items-center gap-1.5">
        <span class="h-1.5 w-1.5 rounded-full transition-colors" :class="connected ? 'bg-emerald-500' : 'bg-amber-500'"></span>
        <span class="font-medium" x-text="connected ? 'Live' : 'Polling'">Polling</span>
    </span>
    <span class="font-mono" x-text="time"></span>
</span>
