<div class="space-y-10">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-4">
        <div>
            <h1 class="text-4xl font-display font-black tracking-tighter transition-colors">Alert Management</h1>
            <p class="text-[10px] text-[var(--text-muted)] font-black uppercase tracking-[0.3em] mt-2">Threshold Configuration & Incident Audit</p>
        </div>
    </div>

    <!-- Alert Rules Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 bg-[var(--bg-card)] border border-[var(--border-color)] p-8 rounded-3xl shadow-xl transition-colors duration-500">
            <div class="flex items-center gap-3 mb-8">
                <div class="h-2 w-2 bg-success rounded-full shadow-[0_0_10px_rgba(34,197,94,0.5)]"></div>
                <h3 class="text-xs font-bold text-[var(--text-main)] uppercase tracking-widest">Active Threshold Rules</h3>
            </div>
            
            <div class="space-y-4">
                @foreach([['metric' => 'CPU', 'threshold' => '90%', 'channels' => ['Telegram', 'Email']], ['metric' => 'RAM', 'threshold' => '95%', 'channels' => ['Email']]] as $rule)
                <div class="flex items-center justify-between p-5 bg-[var(--bg-main)] rounded-2xl border border-[var(--border-color)] hover:border-[var(--accent-primary)]/30 transition-all group cursor-pointer">
                    <div class="flex items-center gap-5">
                        <div class="h-12 w-12 bg-[var(--bg-card)] text-[var(--text-main)] rounded-xl flex items-center justify-center font-black text-xs border border-[var(--border-color)]">{{ $rule['metric'] }}</div>
                        <div>
                            <p class="text-xs font-bold text-[var(--text-main)] tracking-tight">Load {{ $rule['metric'] }} > {{ $rule['threshold'] }}</p>
                            <p class="text-[8px] text-[var(--text-muted)] font-black uppercase tracking-widest mt-1">Notification via {{ implode(' & ', $rule['channels']) }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-[8px] font-black px-3 py-1.5 rounded-md border border-success/30 text-success bg-success/5 uppercase tracking-widest">Operational</span>
                        <x-lucide-more-vertical class="h-4 w-4 text-[var(--text-muted)]" />
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="bg-[var(--bg-card)] border border-[var(--border-color)] p-8 rounded-3xl shadow-xl flex flex-col justify-between hover:scale-[1.02] transition-all cursor-pointer group relative overflow-hidden">
            <div class="relative z-10">
                <div class="h-14 w-14 bg-[var(--accent-primary)] text-white rounded-2xl flex items-center justify-center mb-8 shadow-lg shadow-[var(--accent-primary)]/20">
                    <x-lucide-plus class="h-7 w-7" />
                </div>
                <h4 class="text-[var(--text-main)] font-black text-2xl tracking-tight mb-2">New Threshold</h4>
                <p class="text-[var(--text-muted)] text-[10px] font-bold uppercase tracking-widest leading-relaxed">Configura un nuevo punto de monitorización avanzada</p>
            </div>
            <div class="relative z-10 pt-8 mt-auto border-t border-[var(--border-color)] flex items-center justify-between">
                <span class="text-[9px] font-black uppercase tracking-[0.2em] text-[var(--text-muted)]">Establish Rule</span>
                <x-lucide-arrow-right class="h-5 w-5 text-[var(--accent-primary)] group-hover:translate-x-2 transition-transform" />
            </div>
        </div>
    </div>

    <!-- History Log Table -->
    <div class="bg-[var(--bg-card)] border border-[var(--border-color)] rounded-3xl shadow-xl overflow-hidden transition-colors duration-500">
        <div class="px-8 py-6 border-b border-[var(--border-color)] flex justify-between items-center bg-[var(--bg-sidebar)]/30">
            <div class="flex items-center gap-3">
                <div class="h-2 w-2 bg-danger rounded-full animate-pulse shadow-[0_0_10px_rgba(239,68,68,0.5)]"></div>
                <h3 class="text-xs font-bold text-[var(--text-main)] uppercase tracking-widest">Critical Incident Stream</h3>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-[var(--bg-sidebar)]/50">
                    <tr>
                        <th class="px-8 py-5 text-[9px] font-bold text-[var(--text-muted)] uppercase tracking-widest">UTC Timestamp</th>
                        <th class="px-6 py-5 text-[9px] font-bold text-[var(--text-muted)] uppercase tracking-widest">Servidor</th>
                        <th class="px-6 py-5 text-[9px] font-bold text-[var(--text-muted)] uppercase tracking-widest text-center">Origen</th>
                        <th class="px-6 py-5 text-[9px] font-bold text-[var(--text-muted)] uppercase tracking-widest text-center">Peak Value</th>
                        <th class="px-8 py-5 text-[9px] font-bold text-[var(--text-muted)] uppercase tracking-widest text-right">Channel Delivery</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border-color)]">
                    @forelse($history as $alert)
                        <tr class="hover:bg-[var(--accent-primary)]/[0.02] transition-colors">
                            <td class="px-8 py-6 text-[10px] font-mono font-bold text-[var(--text-muted)]">{{ $alert->created_at->format('Y-m-d H:i:s') }}</td>
                            <td class="px-6 py-6">
                                <span class="text-xs font-bold text-[var(--text-main)]">{{ $alert->server->name ?? 'Servidor Desconocido' }}</span>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span class="text-[8px] font-black px-3 py-1.5 rounded-md border border-danger/30 text-danger bg-danger/5 uppercase tracking-widest">
                                    {{ $alert->cpu_load > 90 ? 'CPU Integrity' : 'RAM Capacity' }}
                                </span>
                            </td>
                            <td class="px-6 py-6 text-center text-xs font-black text-danger">
                                {{ $alert->cpu_load > 90 ? $alert->cpu_load : $alert->ram_usage }}%
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex justify-end gap-2">
                                    <span class="text-[7px] font-black bg-[var(--bg-main)] text-[var(--text-muted)] px-3 py-1 rounded border border-[var(--border-color)] uppercase tracking-widest">Telegram</span>
                                    <span class="text-[7px] font-black bg-[var(--bg-main)] text-[var(--text-muted)] px-3 py-1 rounded border border-[var(--border-color)] uppercase tracking-widest">Email</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-32 text-center opacity-30">
                                <x-lucide-shield-check class="h-12 w-12 mx-auto mb-4 text-success" />
                                <p class="text-[10px] font-black uppercase tracking-widest">No Critical Incidents Detected</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
