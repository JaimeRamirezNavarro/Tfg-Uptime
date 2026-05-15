<div class="bg-gray-900 rounded-3xl p-6 shadow-xl border border-gray-800 font-mono text-sm">
    <div class="flex justify-between items-center mb-4 border-b border-gray-800 pb-4">
        <h3 class="text-green-400 font-bold flex items-center gap-2">
            <x-lucide-terminal class="h-5 w-5" />
            Terminal Remota SSH
        </h3>
        
        <div class="flex items-center gap-4">
            <span class="text-gray-500 text-xs">{{ $server->ip_address }}</span>
            @if($isConnected)
                <button wire:click="disconnect" class="bg-red-500/20 hover:bg-red-500/30 text-red-400 border border-red-500/30 px-3 py-1 rounded text-xs font-bold transition-colors">
                    Apagar Terminal
                </button>
            @endif
        </div>
    </div>

    @if(!$isConnected)
        <div class="py-12 flex flex-col items-center justify-center bg-gray-800/50 rounded-2xl border border-gray-800">
            <x-lucide-power class="h-12 w-12 text-gray-500 mb-4" />
            <p class="text-gray-400 mb-6 text-center max-w-md">La terminal está desconectada para ahorrar recursos. Conéctate para enviar comandos por SSH al servidor.</p>
            <button wire:click="connect" class="bg-green-600 hover:bg-green-500 text-white font-bold py-2 px-6 rounded-xl transition-all shadow-lg shadow-green-900/50 flex items-center gap-2">
                <div wire:loading wire:target="connect" class="h-4 w-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                <span wire:loading.remove wire:target="connect">💻 Encender Terminal</span>
                <span wire:loading wire:target="connect">Conectando...</span>
            </button>
            
            @foreach($history as $line)
                @if($line['type'] === 'error')
                    <p class="text-red-400 mt-4 text-xs">{{ $line['text'] }}</p>
                @endif
            @endforeach
        </div>
    @else
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
            <span class="text-green-400 self-center font-bold">{{ $server->ssh_user }}@{{ $server->name }}:{{ $currentPath }}$</span>
            <input 
                type="text" 
                wire:model="command" 
                class="flex-1 bg-transparent border-none text-white focus:ring-0 outline-none placeholder-gray-600"
                placeholder="Escribe un comando..."
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
                    if (historyDiv) {
                        historyDiv.scrollTop = historyDiv.scrollHeight;
                    }
                });
            });
        </script>
    @endif
</div>
