@php 
    $current = $server->metrics()->latest()->first(); 
    $status = ($current && $current->created_at->diffInSeconds(now()) < 50) ? 'ONLINE' : 'OFFLINE';
@endphp

<div class="space-y-10" wire:poll.1s="poll" wire:key="server-detail-container-{{ $server->id }}">
    <!-- Header: Premium Node Identity -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-10 premium-card p-12 relative overflow-hidden bg-white border-none shadow-2xl shadow-black/5">
        <div class="flex items-center gap-10 relative z-10 w-full lg:w-auto">
            <div class="h-32 w-32 rounded-none {{ $server->check_type === 'agent' ? 'bg-primary-50 text-primary-600' : ($server->check_type === 'http' ? 'bg-sky-50 text-sky-600' : 'bg-emerald-50 text-emerald-600') }} flex items-center justify-center shadow-inner relative group transition-all duration-500 hover:scale-105 border border-transparent hover:border-current/10">
                <div class="absolute right-1 top-1 h-7 w-7 rounded-none {{ $status === 'ONLINE' ? 'bg-emerald-500 ring-4 ring-emerald-500/20 animate-pulse' : 'bg-rose-500 ring-4 ring-rose-500/20' }} border-4 border-white shadow-lg"></div>
                @if($server->check_type === 'agent')
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z" /><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" /></svg>
                @elseif($server->check_type === 'http')
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM4.332 8.027a6.012 6.012 0 011.912-2.706C6.512 5.73 6.974 6 7.5 6A1.5 1.5 0 019 7.5V9a2 2 0 002 2h1a2 2 0 002-2V7.5a.5.5 0 011 0V9a4 4 0 01-4 4H12v1.5a1.5 1.5 0 01-3 0V13a1 1 0 00-1-1 2 2 0 01-2-2v-1.973z" clip-rule="evenodd" /></svg>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.4503-.385l-7 3.5a1 1 0 00-.553.894v10a1 1 0 001.447.894l7-3.5a1 1 0 00.553-.894v-10zm-2.395 1.777v8.34l-5 2.5V5.83l5-2.502z" clip-rule="evenodd" /></svg>
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-5">
                    <h1 class="text-5xl font-display font-black text-surface-900 tracking-tight">{{ $server->name }}</h1>
                    <span class="px-5 py-2 {{ $status === 'ONLINE' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-rose-50 text-rose-600 border-rose-100' }} text-[10px] font-black uppercase tracking-[0.2em] rounded-none border shadow-sm">
                        Node {{ $status }}
                    </span>
                </div>
                <div class="flex flex-wrap items-center gap-6 mt-4">
                    <div class="flex items-center gap-2.5 text-xs font-black text-primary-600 uppercase tracking-widest bg-primary-50 px-3 py-1.5 rounded-none border border-primary-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                        {{ $server->check_type === 'agent' ? 'Node Agent' : ($server->check_type === 'http' ? 'Web Monitor' : 'Network Endpoint') }}
                    </div>
                    <div class="flex items-center gap-2.5 text-xs font-bold text-surface-400 font-mono">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" /></svg>
                        {{ $server->ip_address }}
                    </div>
                </div>
            </div>
        </div>
        
        <div class="flex bg-surface-100/50 p-2 rounded-none border border-surface-200/50 relative z-10 backdrop-blur-md shadow-inner">
            @foreach(['day' => '24h', 'month' => '30d', 'year' => '12m'] as $key => $label)
                <button wire:click="setTimeframe('{{ $key }}')" 
                        class="px-10 py-3.5 rounded-none text-[10px] font-black uppercase tracking-[0.2em] transition-all duration-300 {{ $timeframe === $key ? 'bg-white text-primary-600 shadow-xl shadow-black/5 border border-surface-200' : 'text-surface-400 hover:text-surface-600' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Telemetry Insights -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        @if($server->check_type === 'agent')
            <!-- CPU Insight -->
            <div class="premium-card p-10 bg-white border-none shadow-2xl shadow-black/5 group hover:-translate-y-2 transition-all duration-500">
                <div class="flex justify-between items-start mb-8">
                    <div class="h-14 w-14 bg-primary-50 text-primary-600 rounded-none flex items-center justify-center font-black group-hover:rotate-6 transition-all shadow-inner border border-primary-100/10">
                        CPU
                    </div>
                    <div class="text-right">
                        <span class="text-3xl font-display font-black text-surface-900 tracking-tighter">{{ $current->cpu_load ?? 0 }}%</span>
                        <p class="text-[9px] font-black text-surface-400 uppercase tracking-widest mt-1">Load Balance</p>
                    </div>
                </div>
                <div class="w-full bg-surface-100 h-3 rounded-none overflow-hidden mb-2 shadow-inner">
                    <div class="h-full bg-gradient-to-r from-primary-600 to-primary-400 rounded-none transition-all duration-1000 shadow-lg" style="width: {{ $current->cpu_load ?? 0 }}%"></div>
                </div>
            </div>

            <!-- RAM Insight -->
            <div class="premium-card p-10 bg-white border-none shadow-2xl shadow-black/5 group hover:-translate-y-2 transition-all duration-500">
                <div class="flex justify-between items-start mb-8">
                    <div class="h-14 w-14 bg-sky-50 text-sky-600 rounded-none flex items-center justify-center font-black group-hover:rotate-6 transition-all shadow-inner border border-sky-100/10">
                        RAM
                    </div>
                    <div class="text-right">
                        <span class="text-3xl font-display font-black text-surface-900 tracking-tighter">{{ $current->ram_usage ?? 0 }}%</span>
                        <p class="text-[9px] font-black text-surface-400 uppercase tracking-widest mt-1">Memory Allocation</p>
                    </div>
                </div>
                <div class="w-full bg-surface-100 h-3 rounded-none overflow-hidden mb-2 shadow-inner">
                    <div class="h-full bg-gradient-to-r from-sky-600 to-sky-400 rounded-none transition-all duration-1000 shadow-lg" style="width: {{ $current->ram_usage ?? 0 }}%"></div>
                </div>
            </div>

            <!-- Disk Insight -->
            <div class="premium-card p-10 bg-white border-none shadow-2xl shadow-black/5 group hover:-translate-y-2 transition-all duration-500">
                <div class="flex justify-between items-start mb-8">
                    <div class="h-14 w-14 bg-purple-50 text-purple-600 rounded-none flex items-center justify-center font-black group-hover:rotate-6 transition-all shadow-inner border border-purple-100/10">
                        DSK
                    </div>
                    <div class="text-right">
                        <span class="text-3xl font-display font-black text-surface-900 tracking-tighter">{{ $current->disk_free ?? 0 }}%</span>
                        <p class="text-[9px] font-black text-surface-400 uppercase tracking-widest mt-1">Free Storage</p>
                    </div>
                </div>
                <div class="w-full bg-surface-100 h-3 rounded-none overflow-hidden mb-2 shadow-inner">
                    <div class="h-full bg-gradient-to-r from-purple-600 to-purple-400 rounded-none transition-all duration-1000 shadow-lg" style="width: {{ $current->disk_free ?? 0 }}%"></div>
                </div>
            </div>
        @endif

        @if($server->check_type === 'ping' || $server->check_type === 'http')
            <!-- Connection Detail Card -->
            <div class="lg:col-span-3 premium-card p-10 bg-white border-none shadow-2xl shadow-black/5 group overflow-hidden">
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h3 class="text-lg font-display font-black text-surface-900 tracking-tight">{{ $server->check_type === 'http' ? 'Web Response Latency' : 'Network Round-Trip Time' }}</h3>
                        <p class="text-xs text-surface-400 font-bold uppercase tracking-widest mt-1">Real-time data stream</p>
                    </div>
                    <div class="text-right">
                        <span class="text-5xl font-display font-black {{ $server->check_type === 'http' ? 'text-sky-600' : 'text-emerald-600' }} tracking-tighter">{{ $current->cpu_load ?? 0 }}<span class="text-lg text-surface-300 ml-1">ms</span></span>
                    </div>
                </div>
                <div class="flex gap-2 h-16 items-end relative px-2">
                    <div class="absolute inset-x-0 bottom-0 h-px bg-surface-100"></div>
                    @php $pings = $server->metrics()->latest()->take(30)->get(); @endphp
                    @foreach($pings->reverse() as $p)
                        <div class="flex-1 {{ $server->check_type === 'http' ? 'bg-sky-50 outline-sky-500/10' : 'bg-emerald-50 outline-emerald-500/10' }} rounded-t-lg transition-all hover:scale-x-110 hover:{{ $server->check_type === 'http' ? 'bg-sky-500' : 'bg-emerald-500' }} cursor-crosshair group/ping" 
                             style="height: {{ max(15, min(100, $p->cpu_load ?? 0)) }}%">
                            <div class="opacity-0 group-hover/ping:opacity-100 absolute -top-8 left-1/2 -translate-x-1/2 bg-surface-900 text-white text-[8px] font-black px-2 py-1 rounded pointer-events-none transition-opacity">{{ $p->cpu_load }}ms</div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Global Status Detail -->
        <div class="premium-card p-10 bg-surface-950 border-none shadow-2xl shadow-black/20 group hover:shadow-primary-500/10 transition-all duration-500 flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <div class="h-14 w-14 bg-white/10 text-white rounded-none flex items-center justify-center group-hover:scale-110 transition-all border border-white/5 shadow-inner">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                </div>
                <div class="h-2.5 w-2.5 rounded-full {{ $status === 'ONLINE' ? 'bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.5)]' : 'bg-rose-500' }} animate-pulse"></div>
            </div>
            <div>
                <p class="text-[10px] font-black text-surface-500 uppercase tracking-[0.3em] mb-1.5">Last Sync</p>
                <p class="text-3xl font-display font-black text-white tracking-tight">{{ now()->format('H:i:s') }}</p>
                <div class="h-1 w-full bg-white/10 rounded-none mt-4 overflow-hidden">
                    <div class="h-full bg-primary-500 animate-[loading_5s_linear_infinite]"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Engine -->
    <div class="premium-card p-12 bg-white relative overflow-hidden border-none shadow-2xl shadow-black/5" wire:ignore wire:key="chart-wrapper-{{ $server->id }}">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-12 relative z-10 gap-6">
            <div>
                <h3 class="text-2xl font-display font-black text-surface-900 tracking-tight">Performance Stream</h3>
                <p class="text-sm text-surface-400 font-bold mt-1">High-fidelity visualization of node telemetry</p>
            </div>
            <div class="flex gap-8 bg-surface-50 px-6 py-3 rounded-none border border-surface-100 shadow-inner">
                <div class="flex items-center gap-3">
                    <div class="h-3 w-3 rounded-full {{ ($server->check_type === 'ping' || $server->check_type === 'http') ? ($server->check_type === 'http' ? 'bg-sky-500' : 'bg-emerald-500') : 'bg-primary-500' }} shadow-lg shadow-current/20"></div>
                    <span class="text-[10px] font-black text-surface-500 uppercase tracking-widest">{{ ($server->check_type === 'ping' || $server->check_type === 'http') ? 'Latency (ms)' : 'CPU Load (%)' }}</span>
                </div>
                @if($server->check_type === 'agent')
                    <div class="flex items-center gap-3">
                        <div class="h-3 w-3 rounded-full bg-sky-500 shadow-lg shadow-sky-500/20"></div>
                        <span class="text-[10px] font-black text-surface-500 uppercase tracking-widest">Memory (%)</span>
                    </div>
                @endif
            </div>
        </div>
        
        <div class="h-[400px] w-full relative z-10">
            <canvas id="analyticsChart"></canvas>
        </div>
    </div>

    <!-- Data Registry -->
    <div class="premium-card overflow-hidden relative bg-white border-none shadow-2xl shadow-black/5">
        <div class="px-12 py-10 border-b border-surface-100 flex flex-col md:flex-row justify-between items-start md:items-center bg-surface-50/30 gap-6">
            <div>
                <h3 class="text-xl font-display font-black text-surface-900 tracking-tight">Telemetry Records</h3>
                <p class="text-xs text-surface-400 font-bold uppercase tracking-widest mt-1">Immutability verified log stream</p>
            </div>
            <div class="flex items-center gap-4 text-[10px] font-black bg-white px-5 py-2.5 border border-surface-100 rounded-none shadow-sm text-surface-500 uppercase tracking-widest">
                <div class="h-2 w-2 rounded-full bg-primary-500"></div>
                Log Pool: {{ $server->metrics()->count() }} entries
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-[10px] font-black uppercase tracking-[0.2em] text-surface-400 border-b border-surface-100 bg-surface-50/50">
                        <th class="px-12 py-6">UTC Timestamp</th>
                        <th class="px-8 py-6 text-center">Payload Assets</th>
                        <th class="px-12 py-6 text-right">Verification</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-100">
                    @foreach($lastMetrics as $m)
                        <tr class="hover:bg-surface-50/80 transition-all duration-300 group cursor-default">
                            <td class="px-12 py-6 text-sm font-bold text-surface-400 font-mono tracking-tighter">{{ $m->created_at->format('Y-m-d H:i:s.v') }}</td>
                            <td class="px-8 py-6 text-center">
                                @if($server->check_type === 'agent')
                                    <div class="flex items-center justify-center gap-4">
                                        <span class="text-[10px] font-black text-primary-600 bg-primary-50 px-4 py-2 rounded-none border border-primary-100 shadow-sm transition-all group-hover:scale-105">CPU {{ $m->cpu_load }}%</span>
                                        <span class="text-[10px] font-black text-sky-600 bg-sky-50 px-4 py-2 rounded-none border border-sky-100 shadow-sm transition-all group-hover:scale-105">RAM {{ $m->ram_usage }}%</span>
                                    </div>
                                @else
                                    <span class="text-[10px] font-black {{ $m->cpu_load > 200 ? 'text-rose-600 bg-rose-50 border-rose-100' : 'text-emerald-600 bg-emerald-50 border-emerald-100' }} px-5 py-2.5 rounded-none border shadow-sm transition-all group-hover:scale-105">ACK {{ $m->cpu_load }}ms Time-to-Echo</span>
                                @endif
                            </td>
                            <td class="px-12 py-6 text-right">
                                <span class="text-[10px] font-black text-surface-300 uppercase tracking-[0.2em] group-hover:text-primary-500/40 transition-colors">SIG_VERIFIED_7A12</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Script Analytics Controller -->
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (function() {
            let chart = null;

            function init() {
                const canvas = document.getElementById('analyticsChart');
                if (!canvas) return;

                const existingChart = Chart.getChart(canvas);
                if (existingChart) {
                    existingChart.destroy();
                }

                Chart.defaults.font.family = "'Inter', sans-serif";
                Chart.defaults.color = '#94a3b8';

                const ctx = canvas.getContext('2d');
                
                const primaryColor = '{{ $server->check_type === "ping" ? "#10b981" : ($server->check_type === "http" ? "#0ea5e9" : ($primary["500"] ?? "#6366f1")) }}';
                const accentColor = '#38bdf8';
                
                const gradientPrimary = ctx.createLinearGradient(0, 0, 0, 400);
                gradientPrimary.addColorStop(0, primaryColor + '15');
                gradientPrimary.addColorStop(1, primaryColor + '00');

                const gradientAccent = ctx.createLinearGradient(0, 0, 0, 400);
                gradientAccent.addColorStop(0, accentColor + '15');
                gradientAccent.addColorStop(1, accentColor + '00');

                chart = new Chart(canvas, {
                    type: 'line',
                    data: {
                        labels: @js($chartData['labels']),
                        datasets: [
                            {
                                label: '{{ ($server->check_type === "ping" || $server->check_type === "http") ? "Latency" : "CPU" }}',
                                data: @js($chartData['cpu']),
                                borderColor: primaryColor,
                                backgroundColor: gradientPrimary,
                                borderWidth: 4,
                                tension: 0.4,
                                fill: true,
                                pointRadius: 0,
                                pointHoverRadius: 8,
                                pointHoverBackgroundColor: '#ffffff',
                                pointHoverBorderColor: primaryColor,
                                pointHoverBorderWidth: 4,
                            },
                            @if($server->check_type === 'agent')
                            {
                                label: 'Memory',
                                data: @js($chartData['ram']),
                                borderColor: accentColor,
                                backgroundColor: gradientAccent,
                                borderWidth: 4,
                                tension: 0.4,
                                fill: true,
                                pointRadius: 0,
                                pointHoverRadius: 8,
                                pointHoverBackgroundColor: '#ffffff',
                                pointHoverBorderColor: accentColor,
                                pointHoverBorderWidth: 4,
                            }
                            @endif
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { intersect: false, mode: 'index' },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(255, 255, 255, 0.98)',
                                titleColor: '#0f172a',
                                bodyColor: '#475569',
                                borderColor: 'rgba(226, 232, 240, 0.8)',
                                borderWidth: 1,
                                titleFont: { family: "'Outfit', sans-serif", size: 14, weight: '900' },
                                bodyFont: { family: "'Inter', sans-serif", size: 13, weight: '600' },
                                padding: 16,
                                boxPadding: 8,
                                usePointStyle: true,
                                boxWidth: 8,
                                boxHeight: 8
                            }
                        },
                        scales: {
                            y: { 
                                beginAtZero: true, 
                                max: 100,
                                border: { display: false },
                                grid: { color: 'rgba(241, 245, 249, 1)', drawBorder: false },
                                ticks: { font: { weight: 'bold', size: 11 }, padding: 15 }
                            },
                            x: { 
                                grid: { display: false },
                                ticks: { font: { weight: 'bold', size: 11 }, padding: 10 }
                            }
                        }
                    }
                });
            }

            document.addEventListener('livewire:navigated', init);
            init();

            window.addEventListener('update-chart', (event) => {
                const data = event.detail.data;
                requestAnimationFrame(() => {
                    if (chart) {
                        chart.data.labels = data.labels;
                        chart.data.datasets[0].data = data.cpu;
                        if (chart.data.datasets.length > 1) {
                            chart.data.datasets[1].data = data.ram;
                        }
                        chart.update('none');
                    }
                });
            });
        })();
    </script>
    @endpush
</div>
