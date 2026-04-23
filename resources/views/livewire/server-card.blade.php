<div class="bg-white border-none p-10 rounded-none relative hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 shadow-xl shadow-black/5 {{ $server->is_enabled ? '' : 'grayscale opacity-60 pointer-events-none' }}">
    @if(!$server->is_enabled)
        <div class="absolute inset-0 flex items-center justify-center z-10 pointer-events-auto bg-surface-50/40 backdrop-blur-[2px] rounded-none">
            <button wire:click="toggleServer" class="bg-primary-600 hover:bg-primary-500 text-white px-10 py-4 rounded-none text-xs font-black uppercase tracking-[0.2em] shadow-xl shadow-primary-500/20 transition-all active:scale-95">
                Reactivar Nodo
            </button>
        </div>
    @endif
    
    <div class="flex justify-between items-start mb-10">
        <div>
            <h2 class="text-3xl font-display font-black text-surface-900 tracking-tight leading-tight">{{ $server->name }}</h2>
            <div class="flex flex-wrap items-center gap-4 mt-3">
                <span class="text-[10px] font-bold text-surface-400 font-mono bg-surface-50 px-3 py-1.5 rounded-none border border-surface-100 shadow-inner">{{ $server->ip_address }}</span>
                <span class="text-[9px] font-black text-primary-600/60 uppercase tracking-widest">{{ $server->check_type === 'agent' ? 'Enterprise Agent' : 'Ping Protocol' }}</span>
            </div>
        </div>
        
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 flex items-center justify-center rounded-none {{ $server->is_online && $server->is_enabled ? 'text-emerald-600 bg-emerald-50 border-emerald-100' : 'text-rose-600 bg-rose-50 border-rose-100' }} border shadow-sm">
                <div class="h-2 w-2 rounded-full bg-current {{ $server->is_online && $server->is_enabled ? 'animate-pulse ring-4 ring-current/20' : '' }}"></div>
            </div>
            
            <div class="flex items-center gap-2">
                <button wire:click="toggleDetails" class="h-10 w-10 flex items-center justify-center rounded-none bg-surface-50 text-surface-400 hover:text-primary-600 hover:bg-white hover:shadow-sm border border-transparent hover:border-surface-100 transition-all" title="Ver Detalles">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </button>
                <button wire:click="toggleServer" class="h-10 w-10 flex items-center justify-center rounded-none bg-surface-50 text-surface-400 hover:text-amber-600 hover:bg-white hover:shadow-sm border border-transparent hover:border-surface-100 transition-all" title="Suspender Nodo">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </button>
            </div>
        </div>
    </div>

    @php $lastMetric = $server->metrics->first(); @endphp

    @if($lastMetric && $server->is_online)
        <div class="grid grid-cols-1 gap-10 mt-4">
            <div class="space-y-8">
                <div>
                    <div class="flex justify-between text-[10px] font-black uppercase tracking-[0.2em] mb-3 text-surface-400">
                        CPU Performance <span class="text-primary-600">{{ $lastMetric->cpu_load }}%</span>
                    </div>
                    <div class="w-full bg-surface-100 h-2.5 rounded-none overflow-hidden shadow-inner">
                        <div class="h-full bg-gradient-to-r from-primary-600 to-primary-400 transition-all duration-1000 rounded-none shadow-lg" style="width: {{ $lastMetric->cpu_load }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-[10px] font-black uppercase tracking-[0.2em] mb-3 text-surface-400">
                        Memory Allocation <span class="text-sky-600">{{ $lastMetric->ram_usage }}%</span>
                    </div>
                    <div class="w-full bg-surface-100 h-2.5 rounded-none overflow-hidden shadow-inner">
                        <div class="h-full bg-gradient-to-r from-sky-600 to-sky-400 transition-all duration-1000 rounded-none shadow-lg" style="width: {{ $lastMetric->ram_usage }}%"></div>
                    </div>
                </div>
            </div>
            
            <div class="h-44 bg-surface-50/50 rounded-none p-6 border border-surface-100 shadow-inner relative group/chart overflow-hidden" wire:ignore>
                <div class="absolute inset-0 bg-gradient-to-t from-white/10 to-transparent pointer-events-none"></div>
                <canvas id="chart-{{ $server->id }}" class="relative z-10"></canvas>
            </div>
        </div>

        @if($activeDetailsId === $server->id)
            <div class="mt-10 pt-10 border-t border-surface-100 grid grid-cols-1 md:grid-cols-2 gap-8 animate-in fade-in slide-in-from-top-4 duration-500">
                @php $details = json_decode($server->last_sync_details, true); @endphp
                
                <div>
                    <h4 class="text-[10px] font-black text-emerald-600 mb-5 flex items-center gap-3 uppercase tracking-widest">
                        <div class="h-1.5 w-1.5 bg-emerald-500 rounded-full shadow-[0_0_8px_rgba(16,185,129,0.5)] animate-pulse"></div>
                        Runtime Services
                    </h4>
                    <ul class="space-y-2.5">
                        @forelse($details['services'] ?? [] as $service)
                            <li class="text-[11px] font-bold text-surface-600 flex items-center gap-3 bg-surface-50 px-4 py-2.5 rounded-none border border-surface-100 transition-all hover:bg-white hover:shadow-sm">
                                <span class="text-emerald-400 font-black">·</span> {{ $service }}
                            </li>
                        @empty
                            <li class="text-[11px] text-surface-300 font-bold uppercase tracking-widest italic py-4">No services detected</li>
                        @endforelse
                    </ul>
                </div>

                <div>
                    <h4 class="text-[10px] font-black text-sky-600 mb-5 flex items-center gap-3 uppercase tracking-widest">
                        <div class="h-1.5 w-1.5 bg-sky-500 rounded-full shadow-[0_0_8px_rgba(14,165,233,0.5)] animate-pulse"></div>
                        Active Containers
                    </h4>
                    <ul class="space-y-2.5">
                        @forelse($details['containers'] ?? [] as $container)
                            <li class="text-[11px] font-bold text-surface-600 flex items-center gap-3 bg-surface-50 px-4 py-2.5 rounded-none border border-surface-100 transition-all hover:bg-white hover:shadow-sm">
                                <span class="text-sky-400 font-black">·</span> {{ $container }}
                            </li>
                        @empty
                            <li class="text-[11px] text-surface-300 font-bold uppercase tracking-widest italic py-4">No containers active</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        @endif
    @else
        <div class="py-20 mt-8 border-2 border-dashed border-surface-100 rounded-none text-center bg-surface-50/50 flex flex-col items-center">
            <div class="h-12 w-12 rounded-none border-2 border-surface-200 border-t-primary-500 animate-spin mb-4"></div>
            <p class="text-[10px] text-surface-400 font-black uppercase tracking-[0.2em] animate-pulse">Awaiting Telemetry Flow...</p>
        </div>
    @endif

    <script>
        document.addEventListener('livewire:initialized', () => {
            let ctx = document.getElementById('chart-{{ $server->id }}');
            if (!ctx) return;

            const labels = {!! json_encode($server->metrics->pluck('created_at')->map(fn($t) => $t->format('H:i:s'))->reverse()->values()) !!};
            const cpuData = {!! json_encode($server->metrics->pluck('cpu_load')->reverse()->values()) !!};
            const ramData = {!! json_encode($server->metrics->pluck('ram_usage')->reverse()->values()) !!};

            const chartCtx = ctx.getContext('2d');
            const gradientCpu = chartCtx.createLinearGradient(0, 0, 0, 150);
            gradientCpu.addColorStop(0, 'rgba(99, 102, 241, 0.15)');
            gradientCpu.addColorStop(1, 'rgba(99, 102, 241, 0.0)');

            const gradientRam = chartCtx.createLinearGradient(0, 0, 0, 150);
            gradientRam.addColorStop(0, 'rgba(14, 165, 233, 0.15)');
            gradientRam.addColorStop(1, 'rgba(14, 165, 233, 0.0)');

            const chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        { label: 'CPU', data: cpuData, borderColor: '#6366f1', backgroundColor: gradientCpu, borderWidth: 3.5, fill: true, tension: 0.4, pointRadius: 0 },
                        { label: 'RAM', data: ramData, borderColor: '#0ea5e9', backgroundColor: gradientRam, borderWidth: 3.5, fill: true, tension: 0.4, pointRadius: 0 }
                    ]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false }, tooltip: { 
                        mode: 'index', intersect: false,
                        backgroundColor: 'rgba(255, 255, 255, 0.98)',
                        titleColor: '#0f172a',
                        bodyColor: '#475569',
                        borderColor: 'rgba(226, 232, 240, 0.8)',
                        borderWidth: 1,
                        padding: 12,
                        titleFont: { family: "'Outfit', sans-serif", weight: '900' },
                        bodyFont: { family: "'Inter', sans-serif", weight: '600' }
                    } },
                    scales: { 
                        x: { display: false }, 
                        y: { 
                            min: 0, 
                            max: 100, 
                            border: { display: false },
                            ticks: { display: false },
                            grid: { color: 'rgba(241, 245, 249, 1)', drawBorder: false } 
                        } 
                    }
                }
            });

            window.Echo.channel('server.{{ $server->id }}')
                .listen('MetricUpdated', (e) => {
                    chart.data.labels.push(e.time);
                    chart.data.datasets[0].data.push(e.cpu);
                    chart.data.datasets[1].data.push(e.ram);
                    if (chart.data.labels.length > 20) {
                        chart.data.labels.shift();
                        chart.data.datasets[0].data.shift();
                        chart.data.datasets[1].data.shift();
                    }
                    chart.update('none');
                });
        });
    </script>
</div>
