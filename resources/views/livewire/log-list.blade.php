<div class="space-y-10">
    <!-- Header & Filters -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-end gap-8 mb-4">
        <div>
            <h1 class="text-4xl font-display font-black tracking-tighter transition-colors">Event Audit Log</h1>
            <p class="text-[10px] text-[var(--text-muted)] font-black uppercase tracking-[0.3em] mt-2">Immutable Distributed Infrastructure Registry</p>
        </div>
        
        <div class="flex bg-[var(--bg-card)] p-1.5 rounded-2xl border border-[var(--border-color)] shadow-sm">
            @foreach(['ALL' => 'All', 'INFO' => 'Info', 'WARN' => 'Warn', 'ALERT' => 'Alert'] as $key => $label)
                @php
                    $isActive = $filter === $key;
                @endphp
                <button wire:click="setFilter('{{ $key }}')" 
                        class="px-8 py-3 rounded-xl text-[9px] uppercase font-black tracking-[0.2em] transition-all duration-300 {{ $isActive ? 'bg-[var(--accent-primary)] text-white shadow-lg shadow-[var(--accent-primary)]/20 border-none' : 'text-[var(--text-muted)] hover:text-[var(--text-main)] hover:bg-[var(--bg-main)]' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Event Table -->
    <div class="bg-[var(--bg-card)] border border-[var(--border-color)] rounded-3xl shadow-xl overflow-hidden transition-colors duration-500">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-[var(--bg-sidebar)]/50">
                    <tr>
                        <th class="px-8 py-5 text-[9px] font-bold text-[var(--text-muted)] uppercase tracking-widest">Sequence Timestamp</th>
                        <th class="px-6 py-5 text-[9px] font-bold text-[var(--text-muted)] uppercase tracking-widest text-center">Protocol Level</th>
                        <th class="px-6 py-5 text-[9px] font-bold text-[var(--text-muted)] uppercase tracking-widest">Servidor</th>
                        <th class="px-8 py-5 text-[9px] font-bold text-[var(--text-muted)] uppercase tracking-widest">Datos Recibidos</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border-color)]">
                    @forelse($logs as $log)
                        @php
                            $isAlert = $log->cpu_load > 90 || $log->ram_usage > 95;
                            $isWarn = ($log->cpu_load > 70 && $log->cpu_load <= 90) || ($log->ram_usage > 80 && $log->ram_usage <= 95);
                            
                            $level = $isAlert ? 'ALERT' : ($isWarn ? 'WARN' : 'INFO');
                            $colorClass = $isAlert ? 'text-danger border-danger/30 bg-danger/5' : ($isWarn ? 'text-orange-500 border-orange-500/30 bg-orange-500/5' : 'text-[var(--accent-primary)] border-[var(--accent-primary)]/30 bg-[var(--accent-primary)]/5');
                        @endphp
                        <tr class="hover:bg-[var(--accent-primary)]/[0.01] group">
                            <td class="px-8 py-6 text-[10px] font-mono font-bold text-[var(--text-muted)] group-hover:text-[var(--text-main)] transition-colors">{{ $log->created_at->format('Y-m-d H:i:s.v') }}</td>
                            <td class="px-6 py-6 text-center">
                                <span class="px-3 py-1 rounded-md text-[8px] font-black uppercase tracking-[0.2em] border shadow-sm {{ $colorClass }}">
                                    {{ $level }}
                                </span>
                            </td>
                            <td class="px-6 py-6 text-xs font-bold text-[var(--text-main)]">{{ $log->server->name ?? 'Kernel_System' }}</td>
                            <td class="px-8 py-6">
                                <div class="text-[10px] text-[var(--text-muted)] font-bold tracking-tight">
                                    <span class="text-[var(--text-main)]">CPU: {{ $log->cpu_load }}%</span> · 
                                    <span class="text-[var(--text-main)]">RAM: {{ $log->ram_usage }}%</span> · 
                                    <span class="text-[var(--text-main)]">DSK: {{ $log->disk_free }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-8 py-32 text-center opacity-30">
                                <x-lucide-scroll-text class="h-12 w-12 mx-auto mb-4" />
                                <p class="text-[10px] font-black uppercase tracking-widest">Audit stream is currently empty</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
