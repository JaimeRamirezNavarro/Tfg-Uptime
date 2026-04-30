<div class="space-y-12">
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-8 mb-4">
        <div>
            <h1 class="text-4xl font-display font-black text-text-primary tracking-tight">Registro de Eventos</h1>
            <p class="text-base text-text-secondary font-medium mt-2">Auditoría detallada e inmutable de la infraestructura distribuida</p>
        </div>
        
        <div class="flex bg-bg-tertiary p-2 rounded-xl border border-border-subtle shadow-inner">
            @foreach(['ALL' => 'Todos', 'INFO' => 'Info', 'WARN' => 'Warn', 'ALERT' => 'Alert'] as $key => $label)
                @php
                    $isActive = $filter === $key;
                    $bgClass = match($key) {
                        'INFO' => 'bg-success',
                        'WARN' => 'bg-orange-500',
                        'ALERT' => 'bg-danger',
                        default => 'bg-accent-primary',
                    };
                @endphp
                <button wire:click="setFilter('{{ $key }}')" 
                        class="px-8 py-3.5 text-[10px] uppercase font-black tracking-[0.2em] rounded-lg transition-all duration-300 {{ $isActive ? $bgClass . ' text-white shadow-xl shadow-current/20 border-none' : 'text-text-tertiary hover:text-text-primary hover:bg-bg-secondary' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    <div class="glass-card flex flex-col overflow-hidden">
        <div class="overflow-x-auto flex-1 p-0">
            <table class="dark-table w-full">
                <thead>
                    <tr>
                        <th class="px-10 py-6">UTC Sequence</th>
                        <th class="px-6 py-6 text-center">Protocol Level</th>
                        <th class="px-6 py-6">Origin Node</th>
                        <th class="px-10 py-6">Telemetry Payload / Event Data</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        @php
                            $isAlert = $log->cpu_load > 90 || $log->ram_usage > 95;
                            $isWarn = ($log->cpu_load > 70 && $log->cpu_load <= 90) || ($log->ram_usage > 80 && $log->ram_usage <= 95);
                            
                            $level = $isAlert ? 'ALERT' : ($isWarn ? 'WARN' : 'INFO');
                            $colorClass = $isAlert ? 'text-danger bg-danger/10 border-danger/30' : ($isWarn ? 'text-orange-400 bg-orange-400/10 border-orange-400/30' : 'text-accent-primary bg-accent-primary/10 border-accent-primary/30');
                        @endphp
                        <tr class="group">
                            <td>
                                <span class="text-xs font-bold text-text-secondary font-mono tracking-tighter">{{ $log->created_at->format('Y-m-d H:i:s.v') }}</span>
                            </td>
                            <td class="text-center">
                                <span class="px-4 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-[0.2em] border shadow-sm {{ $colorClass }}">
                                    {{ $level }}
                                </span>
                            </td>
                            <td>
                                <span class="text-sm font-black text-text-primary group-hover:text-accent-primary transition-colors">{{ $log->server->name ?? 'System_Kernel' }}</span>
                            </td>
                            <td>
                                <p class="text-[11px] text-text-secondary font-bold leading-relaxed tracking-tight">
                                    <span class="text-text-tertiary uppercase font-black text-[9px] mr-2">RX_METRIC:</span>
                                    CPU <span class="text-text-primary font-black">{{ $log->cpu_load }}%</span> · 
                                    RAM <span class="text-text-primary font-black">{{ $log->ram_usage }}%</span> · 
                                    FREE_DISK <span class="text-text-primary font-black">{{ $log->disk_free }}%</span>
                                </p>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-10 py-32 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="h-24 w-24 bg-bg-tertiary text-text-tertiary border border-border-subtle rounded-2xl flex items-center justify-center mb-8">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                                    </div>
                                    <p class="font-black text-base text-text-secondary">Canal de Auditoría Vacío</p>
                                    <p class="text-[10px] text-text-tertiary font-black uppercase tracking-[0.2em] mt-2">Esperando flujo de datos de los nodos vinculados</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
