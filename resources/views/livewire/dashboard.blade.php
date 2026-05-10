<div class="space-y-10" wire:poll.15s wire:loading.class="opacity-50 pointer-events-none transition-opacity">
    <!-- Header Dashboard -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h1 class="text-4xl font-display font-black tracking-tighter transition-colors text-[var(--text-main)]">Infrastructure Overview</h1>
            <p class="text-[10px] text-[var(--text-muted)] font-black uppercase tracking-[0.3em] mt-2 opacity-80">Real-time Node Matrix & Telemetry</p>
        </div>
        <button wire:click="$set('open', true)" class="bg-[var(--accent-primary)] text-white h-12 rounded-xl px-8 text-[10px] font-black uppercase tracking-widest shadow-lg shadow-[var(--accent-primary)]/20 hover:opacity-90 active:scale-95 transition-all border-none flex items-center gap-3 cursor-pointer">
            <x-lucide-plus class="h-4 w-4" />
            Provision Node
        </button>
    </div>

    <!-- Cards Matrix -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach(['Global Nodes' => count($servers), 'Operational' => $stats['active_nodes'] ?? 0, 'Active Alerts' => $stats['alerts'] ?? 0, 'Cluster Uptime' => $stats['uptime_percent'] ?? '100%'] as $label => $val)
            <div class="bg-[var(--bg-card)] border border-[var(--border-color)] p-6 shadow-sm transition-colors duration-500" style="border-radius: 24px;">
                <p class="text-[10px] font-black text-[var(--text-muted)] uppercase tracking-widest">{{ $label }}</p>
                <h4 class="text-3xl font-black text-[var(--text-main)] tracking-tighter mt-1">{{ $val }}</h4>
            </div>
        @endforeach
    </div>

    <!-- MODAL INFALIBLE (Conectado a tu Sistema de Temas) -->
    @if($open)
    <div class="fixed inset-0 z-[99999]">
        <!-- Backdrop Difuminado -->
        <div class="absolute inset-0 bg-black/60" style="backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px);" wire:click="$set('open', false)"></div>
        
        <!-- Tarjeta Centrada -->
        <div class="absolute bg-[var(--bg-card)] border border-[var(--border-color)] shadow-2xl overflow-hidden transition-colors duration-500"
             style="top: 50%; left: 50%; transform: translate(-50%, -50%); width: 440px; max-width: 95vw; border-radius: 24px;">
            
            <div class="p-8">
                <!-- Header -->
                <div class="flex justify-between items-start mb-8">
                    <div>
                        <h2 class="text-2xl font-black text-[var(--text-main)] tracking-tight">Node Configuration</h2>
                        <p class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest mt-1">PROVISIONING LAYER</p>
                    </div>
                    <button wire:click="$set('open', false)" class="text-[var(--text-muted)] hover:text-[var(--text-main)] cursor-pointer transition-colors mt-1">
                        <x-lucide-x class="h-5 w-5" />
                    </button>
                </div>

                <div class="space-y-6">
                    <!-- Selector de Metodo -->
                    <div class="space-y-2">
                        <label class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest">DETECTION METHOD</label>
                        <div class="flex bg-[var(--bg-main)] p-1 border border-[var(--border-color)] transition-colors duration-500" style="border-radius: 12px;">
                            @foreach(['agent' => 'AGENT', 'ping' => 'PING', 'http' => 'HTTP'] as $type => $label)
                                <button wire:click="$set('checkType', '{{ $type }}')" class="flex-1 py-2 text-[9px] font-black transition-all {{ $checkType === $type ? 'bg-[var(--accent-primary)] text-white shadow-md' : 'text-[var(--text-muted)]' }}" style="border-radius: 8px;">
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Campos de Texto -->
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest">DISPLAY NAME</label>
                            <input type="text" wire:model="newName" autocomplete="off" placeholder="ZimaBlade-Prod" class="w-full bg-[var(--bg-main)] border border-[var(--border-color)] px-4 py-3 text-xs font-bold text-[var(--text-main)] outline-none focus:border-[var(--accent-primary)] transition-colors duration-500" style="border-radius: 12px;">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest">TARGET ENDPOINT</label>
                            <input type="text" wire:model="ip" autocomplete="off" placeholder="10.0.0.X" class="w-full bg-[var(--bg-main)] border border-[var(--border-color)] px-4 py-3 text-xs font-bold text-[var(--text-main)] outline-none focus:border-[var(--accent-primary)] transition-colors duration-500" style="border-radius: 12px;">
                        </div>
                    </div>

                    <!-- Campos SSH -->
                    @if($checkType === 'agent')
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest">SSH USER</label>
                            <input type="text" wire:model="sshUser" autocomplete="off" class="w-full bg-[var(--bg-main)] border border-[var(--border-color)] px-4 py-3 text-xs font-bold text-[var(--text-main)] outline-none focus:border-[var(--accent-primary)] transition-colors duration-500" style="border-radius: 12px;">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest">SSH PASS</label>
                            <input type="password" wire:model="sshPassword" autocomplete="new-password" class="w-full bg-[var(--bg-main)] border border-[var(--border-color)] px-4 py-3 text-xs font-bold text-[var(--text-main)] outline-none focus:border-[var(--accent-primary)] transition-colors duration-500" style="border-radius: 12px;">
                        </div>
                    </div>

                    <label class="flex items-center gap-3 cursor-pointer group mt-2">
                        <div class="h-4 w-4 bg-[var(--bg-main)] border border-[var(--border-color)] flex items-center justify-center relative transition-colors duration-500" style="border-radius: 4px;">
                            <input type="checkbox" wire:model="autoDeploy" class="absolute opacity-0 w-0 h-0">
                            @if($autoDeploy) <div class="h-2 w-2 bg-[var(--accent-primary)]" style="border-radius: 2px;"></div> @endif
                        </div>
                        <span class="text-[8px] font-black text-[var(--text-main)] uppercase tracking-widest opacity-80">ENABLE REMOTE DEPLOYMENT</span>
                    </label>
                    @endif

                    <!-- Botón Principal -->
                    <div class="pt-4">
                        <button wire:click="addServer" class="w-full h-14 bg-[var(--accent-primary)] text-white text-[11px] font-black uppercase tracking-[0.2em] shadow-xl hover:opacity-90 active:scale-95 transition-all cursor-pointer border-none" style="border-radius: 9999px;">
                            INITIALIZE CONNECTION
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Tabla de Nodos -->
    <div class="bg-[var(--bg-card)] border border-[var(--border-color)] overflow-hidden shadow-xl mt-10 transition-colors duration-500" style="border-radius: 24px;">
        <table class="w-full text-left">
            <thead class="border-b border-[var(--border-color)] text-[9px] font-black text-[var(--text-muted)] uppercase tracking-[0.2em]">
                <tr>
                    <th class="px-6 py-5">Node Identity</th>
                    <th class="px-6 py-5 text-center">Status</th>
                    <th class="px-6 py-5 text-center">Telemetry</th>
                    <th class="px-6 py-5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[var(--border-color)]">
                @foreach($servers as $server)
                    @php
                        $metric = $lastMetrics->get($server->id);
                        $cpu = $metric ? round($metric->cpu_load) : 0;
                        $ram = $metric ? round($metric->ram_usage) : 0;
                        $latency = $metric ? ($metric->details['latency'] ?? 0) : 0;
                    @endphp
                    <tr class="hover:bg-[var(--bg-main)] transition-colors">
                        <td class="px-6 py-6">
                            <a href="{{ route('server.detail', $server->id) }}" class="flex items-center gap-4 group cursor-pointer">
                                <div class="h-8 w-8 bg-[var(--bg-main)] rounded-lg flex items-center justify-center shrink-0 border border-[var(--border-color)] group-hover:border-[var(--accent-primary)] transition-colors">
                                    <x-mary-icon name="{{ $server->check_type === 'agent' ? 'o-server' : ($server->check_type === 'ping' ? 'o-bolt' : 'o-globe-alt') }}" class="h-4 w-4 text-[var(--text-muted)] group-hover:text-[var(--accent-primary)] transition-colors" />
                                </div>
                                <span class="text-xs font-bold text-[var(--text-main)] group-hover:text-[var(--accent-primary)] transition-colors">{{ $server->name }}</span>
                            </a>
                        </td>
                        <td class="px-6 py-6 text-center"><span class="text-[8px] font-bold text-success border border-success/30 px-3 py-1 uppercase" style="border-radius: 9999px;">Online</span></td>
                        <td class="px-6 py-6 text-center">
                            @if($server->check_type === 'agent')
                                <div class="flex items-center justify-center gap-4">
                                    <div class="flex items-center gap-1.5"><x-lucide-cpu class="h-3 w-3 text-[var(--text-muted)]" /><span class="text-[10px] font-black text-[var(--text-main)]">{{ $cpu }}%</span></div>
                                    <div class="flex items-center gap-1.5"><x-lucide-memory-stick class="h-3 w-3 text-[var(--text-muted)]" /><span class="text-[10px] font-black text-[var(--text-main)]">{{ $ram }}%</span></div>
                                </div>
                            @else
                                <div class="flex items-center justify-center gap-1.5"><x-lucide-activity class="h-3 w-3 text-[var(--text-muted)]" /><span class="text-[10px] font-black text-[var(--text-main)]">{{ $latency }}ms</span></div>
                            @endif
                        </td>
                        <td class="px-6 py-6 text-right">
                            <x-mary-button icon="o-trash" wire:click="deleteServer({{ $server->id }})" class="btn-ghost btn-xs text-[var(--text-muted)] hover:text-danger" />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
