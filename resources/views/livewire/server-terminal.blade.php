<div class="bg-gray-900 rounded-3xl p-6 shadow-xl border border-gray-800 font-mono text-sm">
    <div class="flex justify-between items-center mb-4 border-b border-gray-800 pb-4">
        <h3 class="text-green-400 font-bold flex items-center gap-2">
            <x-lucide-terminal class="h-5 w-5" />
            Terminal Remota SSH
        </h3>
        <span class="text-gray-500 text-xs">{{ $server->ip_address }}</span>
    </div>

    <!-- History Container -->
    <div class="h-96 overflow-y-auto mb-4 space-y-2 text-gray-300" id="terminal-history">
        @foreach($history as $line)
            @if($line['type'] === 'system')
                <div class="text-blue-400 font-bold">-- {{ $line['text'] }}</div>
            @elseif($line['type'] === 'input')
                <div class="text-green-300 font-bold">{{ $line['text'] }}</div>
            @elseif($line['type'] === 'error')
                <div class="text-red-400">{{ $line['text'] }}</div>
            @else
                <pre class="whitespace-pre-wrap">{{ $line['text'] }}</pre>
            @endif
        @endforeach
    </div>

    <!-- Input Form -->
    <form wire:submit="executeCommand" class="flex gap-2">
        <span class="text-green-400 self-center font-bold">~ $</span>
        <input 
            type="text" 
            wire:model="command" 
            class="flex-1 bg-transparent border-none text-white focus:ring-0 outline-none placeholder-gray-600"
            placeholder="Escribe un comando de linux..."
            autocomplete="off"
            autofocus
        >
        <button type="submit" class="hidden">Run</button>
    </form>

    <script>
        // Scroll to bottom whenever a command is executed
        document.addEventListener('livewire:initialized', () => {
            Livewire.hook('morph.updated', (el, component) => {
                const historyDiv = document.getElementById('terminal-history');
                historyDiv.scrollTop = historyDiv.scrollHeight;
            });
        });
    </script>
</div>
