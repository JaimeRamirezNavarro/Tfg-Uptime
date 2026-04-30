<div class="space-y-12">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-4">
        <div>
            <h1 class="text-4xl font-display font-black text-text-primary tracking-tight">Gestión de Alertas</h1>
            <p class="text-base text-text-secondary font-medium mt-2">Configuración de umbrales y auditoría de incidencias críticas</p>
        </div>
    </div>

    <!-- Alert Rules Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 glass-card p-10 overflow-hidden relative">
            <h3 class="text-lg font-display font-black text-text-primary mb-8 flex items-center gap-3 relative z-10">
                <div class="h-2 w-2 bg-success rounded-full shadow-[0_0_8px_rgba(16,185,129,0.4)]"></div>
                Reglas de Umbral Activas
            </h3>
            
            <div class="space-y-5 relative z-10">
                @foreach([['metric' => 'CPU', 'threshold' => '90%', 'channels' => ['Telegram', 'Email'], 'color' => 'primary'], ['metric' => 'RAM', 'threshold' => '95%', 'channels' => ['Email'], 'color' => 'sky']] as $rule)
                <div class="flex items-center justify-between p-6 bg-bg-tertiary/50 rounded-xl border border-border-subtle hover:border-accent-primary hover:bg-bg-tertiary hover:shadow-lg hover:-translate-y-1 transition-all group cursor-pointer">
                    <div class="flex items-center gap-5">
                        <div class="h-14 w-14 bg-bg-tertiary text-text-primary rounded-xl flex items-center justify-center font-black text-sm border border-border-subtle">{{ $rule['metric'] }}</div>
                        <div>
                            <p class="text-base font-black text-text-primary tracking-tight">Carga {{ $rule['metric'] }} > {{ $rule['threshold'] }}</p>
                            <p class="text-[10px] text-text-tertiary font-black uppercase tracking-widest mt-1">Notificación vía {{ implode(' & ', $rule['channels']) }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="bg-success/10 text-success text-[10px] font-black px-4 py-1.5 rounded-lg uppercase tracking-widest border border-success/30 shadow-sm">Verified State</span>
                        <button class="text-text-tertiary hover:text-accent-primary transition-all p-2.5 hover:bg-bg-tertiary rounded-lg shadow-sm border border-transparent hover:border-border-subtle"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" /></svg></button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="bg-bg-tertiary rounded-xl border border-border-strong p-10 flex flex-col justify-between hover:shadow-accent-primary/20 hover:-translate-y-2 transition-all cursor-pointer group relative overflow-hidden">
            <div class="absolute top-0 right-0 w-48 h-48 bg-white/5 rounded-full -mr-20 -mt-20 group-hover:scale-125 transition-transform duration-700 blur-2xl"></div>
            <div class="relative z-10">
                <div class="h-16 w-16 bg-bg-secondary rounded-xl flex items-center justify-center text-text-primary mb-8 border border-border-subtle">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                </div>
                <h4 class="text-text-primary font-display font-black text-3xl tracking-tight mb-3 leading-none">Nueva Regla</h4>
                <p class="text-text-secondary text-sm font-bold leading-relaxed">Configura un nuevo umbral de monitorización avanzada</p>
            </div>
            <div class="relative z-10 pt-8 mt-auto border-t border-border-subtle flex items-center justify-between">
                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-text-tertiary">Add Threshold</span>
                <div class="h-10 w-10 bg-accent-primary text-white rounded-lg flex items-center justify-center shadow-xl group-hover:rotate-12 transition-transform">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- History Table -->
    <div class="glass-card flex flex-col overflow-hidden">
        <div class="px-10 py-8 border-b border-border-subtle flex justify-between items-center bg-bg-tertiary/50">
            <div>
                <h3 class="text-lg font-display font-black text-text-primary flex items-center gap-3">
                    <div class="h-2 w-2 bg-danger rounded-full animate-pulse ring-4 ring-danger/10"></div>
                    Stream de Incidentes Críticos
                </h3>
                <p class="text-xs text-text-secondary font-bold uppercase tracking-widest mt-1">Auditoría completa de alertas disparadas</p>
            </div>
        </div>

        <div class="overflow-x-auto flex-1 p-0">
            <table class="dark-table w-full">
                <thead>
                    <tr>
                        <th class="px-10 py-5">UTC Timestamp</th>
                        <th class="px-6 py-5">Managed Node</th>
                        <th class="px-6 py-5 text-center">Telemetry Source</th>
                        <th class="px-6 py-5 text-center">Peak Value</th>
                        <th class="px-6 py-5 text-center">Config Threshold</th>
                        <th class="px-10 py-5 text-right">Channel Delivery</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($history as $alert)
                        <tr>
                            <td>
                                <span class="text-xs font-bold text-text-secondary font-mono tracking-tighter">{{ $alert->created_at->format('Y-m-d H:i:s') }}</span>
                            </td>
                            <td>
                                <span class="text-sm font-black text-text-primary">{{ $alert->server->name ?? 'Unknown Node' }}</span>
                            </td>
                            <td class="text-center">
                                <span class="bg-danger/10 text-danger text-[10px] font-black px-4 py-1.5 rounded-lg uppercase tracking-widest border border-danger/30 shadow-sm leading-none">
                                    {{ $alert->cpu_load > 90 ? 'CPU Integrity' : 'RAM Capacity' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="text-sm font-black text-danger">{{ $alert->cpu_load > 90 ? $alert->cpu_load : $alert->ram_usage }}%</span>
                            </td>
                            <td class="text-center">
                                <span class="text-[10px] font-black text-text-secondary uppercase tracking-widest">{{ $alert->cpu_load > 90 ? 'Limit: 90%' : 'Limit: 95%' }}</span>
                            </td>
                            <td class="text-right">
                                <div class="flex justify-end gap-2">
                                    <div class="text-[9px] bg-bg-tertiary text-text-primary px-3 py-1.5 rounded-lg uppercase font-black tracking-widest border border-border-subtle shadow-sm">Telegram</div>
                                    <div class="text-[9px] bg-bg-tertiary text-text-primary px-3 py-1.5 rounded-lg uppercase font-black tracking-widest border border-border-subtle shadow-sm">Email</div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-10 py-32 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="h-20 w-20 bg-bg-tertiary text-text-tertiary border border-border-subtle rounded-2xl flex items-center justify-center mb-6">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    </div>
                                    <p class="font-black text-base text-text-secondary">Estado de Red Saludable</p>
                                    <p class="text-sm text-text-tertiary mt-2 uppercase tracking-widest font-black text-[10px]">Sin alertas registradas en el periodo actual</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
