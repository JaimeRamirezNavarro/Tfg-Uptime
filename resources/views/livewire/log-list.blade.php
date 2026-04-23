<div class="space-y-12">
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-8 mb-4">
        <div>
            <h1 class="text-4xl font-display font-black text-surface-900 tracking-tight">Registro de Eventos</h1>
            <p class="text-base text-surface-500 font-medium mt-2">Auditoría detallada e inmutable de la infraestructura distribuida</p>
        </div>
        
        <div class="flex bg-surface-100/50 p-2 rounded-none border border-surface-200/50 shadow-inner backdrop-blur-sm">
            @foreach(['ALL' => 'Todos', 'INFO' => 'Info', 'WARN' => 'Warn', 'ALERT' => 'Alert'] as $key => $label)
                @php
                    $isActive = $filter === $key;
                    $bgClass = match($key) {
                        'INFO' => 'bg-emerald-500',
                        'WARN' => 'bg-amber-500',
                        'ALERT' => 'bg-rose-500',
                        default => 'bg-primary-600',
                    };
                @endphp
                <button wire:click="setFilter('{{ $key }}')" 
                        class="px-8 py-3.5 text-[10px] uppercase font-black tracking-[0.2em] rounded-none transition-all duration-300 {{ $isActive ? $bgClass . ' text-white shadow-xl shadow-current/20 border-none' : 'text-surface-400 hover:text-surface-800 hover:bg-white' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    <div class="premium-card flex flex-col overflow-hidden bg-white border-none shadow-2xl shadow-black/5">
        <div class="overflow-x-auto flex-1">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-[10px] font-black uppercase tracking-[0.2em] text-surface-400 border-b border-surface-100 bg-surface-50/80">
                        <th class="px-10 py-6">UTC Sequence</th>
                        <th class="px-6 py-6 text-center">Protocol Level</th>
                        <th class="px-6 py-6">Origin Node</th>
                        <th class="px-10 py-6">Telemetry Payload / Event Data</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-100">
                    @forelse($logs as $log)
                        @php
                            $isAlert = $log->cpu_load > 90 || $log->ram_usage > 95;
                            $isWarn = ($log->cpu_load > 70 && $log->cpu_load <= 90) || ($log->ram_usage > 80 && $log->ram_usage <= 95);
                            
                            $level = $isAlert ? 'ALERT' : ($isWarn ? 'WARN' : 'INFO');
                            $colorClass = $isAlert ? 'text-rose-600 bg-rose-50 border-rose-100' : ($isWarn ? 'text-amber-600 bg-amber-50 border-amber-100' : 'text-primary-600 bg-primary-50 border-primary-100');
                        @endphp
                        <tr class="hover:bg-surface-50/50 transition-all duration-200 group">
                            <td class="px-10 py-6 text-xs font-bold text-surface-400 font-mono tracking-tighter">{{ $log->created_at->format('Y-m-d H:i:s.v') }}</td>
                            <td class="px-6 py-6 text-center">
                                <span class="px-4 py-1.5 rounded-none text-[10px] font-black uppercase tracking-[0.2em] border shadow-sm {{ $colorClass }}">
                                    {{ $level }}
                                </span>
                            </td>
                            <td class="px-6 py-6">
                                <span class="text-sm font-black text-surface-900 group-hover:text-primary-600 transition-colors">{{ $log->server->name ?? 'System_Kernel' }}</span>
                            </td>
                            <td class="px-10 py-6">
                                <p class="text-[11px] text-surface-600 font-bold leading-relaxed tracking-tight">
                                    <span class="text-surface-400 uppercase font-black text-[9px] mr-2">RX_METRIC:</span>
                                    CPU <span class="text-surface-900 font-black">{{ $log->cpu_load }}%</span> · 
                                    RAM <span class="text-surface-900 font-black">{{ $log->ram_usage }}%</span> · 
                                    FREE_DISK <span class="text-surface-900 font-black">{{ $log->disk_free }}%</span>
                                </p>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-10 py-32 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="h-24 w-24 bg-surface-50 text-surface-200 rounded-none flex items-center justify-center mb-8 shadow-inner">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                                    </div>
                                    <p class="font-black text-base text-surface-400">Canal de Auditoría Vacío</p>
                                    <p class="text-[10px] text-surface-300 font-black uppercase tracking-[0.2em] mt-2">Esperando flujo de datos de los nodos vinculados</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
