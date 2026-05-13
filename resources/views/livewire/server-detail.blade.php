@php
    $current = $server->metrics()->latest()->first();
    $status = ($current && $current->created_at->diffInSeconds(now()) < 50) ? 'ONLINE' : 'OFFLINE';
@endphp

<div class="space-y-8" wire:poll.15s>
    <!-- Analytics Cockpit -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Telemetry Chart -->
        <div class="lg:col-span-2 bg-[var(--bg-card)] border border-[var(--border-color)] p-8 rounded-3xl shadow-xl transition-colors duration-500" wire:ignore wire:key="chart-wrapper-{{ $server->id }}">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-[var(--text-muted)]">Performance Analytics</h3>
                    <p class="text-xl font-bold text-[var(--text-main)] mt-1">Real-time Stream Matrix</p>
                </div>
                <div class="flex gap-6 bg-[var(--bg-sidebar)]/50 px-5 py-2.5 border border-[var(--border-color)] rounded-2xl">
                    <div class="flex items-center gap-2">
                        <div class="h-1.5 w-1.5 rounded-full" style="background: var(--accent-primary)"></div>
                        <span class="text-[10px] font-black text-[var(--text-muted)] uppercase tracking-widest">{{ ($server->check_type === 'ping' || $server->check_type === 'http') ? 'Latency' : 'CPU' }}</span>
                    </div>
                    @if($server->check_type === 'agent')
                        <div class="flex items-center gap-2">
                            <div class="h-1.5 w-1.5 rounded-full bg-sky-500"></div>
                            <span class="text-[10px] font-black text-[var(--text-muted)] uppercase tracking-widest">RAM</span>
                        </div>
                    @endif
                </div>
            </div>
            <div class="h-[320px] w-full">
                <canvas id="analyticsChart"></canvas>
            </div>
        </div>

        <!-- Real-time Gauges -->
        <div class="space-y-6">
            <div class="bg-[var(--bg-card)] p-8 border border-[var(--border-color)] rounded-3xl shadow-xl transition-colors duration-500">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-[10px] font-black text-[var(--text-muted)] uppercase tracking-widest">CPU Utilization</p>
                    <div class="h-2 w-2 rounded-full bg-[var(--accent-primary)] animate-pulse shadow-[0_0_10px_var(--accent-primary)]"></div>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-5xl font-display font-black tracking-tighter text-[var(--text-main)]">{{ $current ? round($current->cpu_load, 1) : '0' }}</span>
                    <span class="text-sm font-bold text-[var(--text-muted)] uppercase tracking-widest">{{ $server->check_type === 'agent' ? '%' : 'MS' }}</span>
                </div>
                <div class="mt-6 h-1.5 w-full bg-[var(--bg-main)] rounded-full overflow-hidden border border-[var(--border-color)]">
                    <div class="h-full bg-[var(--accent-primary)] transition-all duration-1000" style="width: {{ $current ? min($current->cpu_load, 100) : 0 }}%"></div>
                </div>
            </div>

            @if($server->check_type === 'agent')
                <div class="bg-[var(--bg-card)] p-8 border border-[var(--border-color)] rounded-3xl shadow-xl transition-colors duration-500">
                    <div class="flex items-center justify-between mb-4">
                        <p class="text-[10px] font-black text-[var(--text-muted)] uppercase tracking-widest">Memory Allocation</p>
                        <div class="h-2 w-2 rounded-full bg-sky-500 animate-pulse shadow-[0_0_10px_rgba(14,165,233,0.5)]"></div>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-5xl font-display font-black tracking-tighter text-[var(--text-main)]">{{ $current ? round($current->ram_usage, 1) : '0' }}</span>
                        <span class="text-sm font-bold text-[var(--text-muted)] uppercase tracking-widest">%</span>
                    </div>
                    <div class="mt-6 h-1.5 w-full bg-[var(--bg-main)] rounded-full overflow-hidden border border-[var(--border-color)]">
                        <div class="h-full bg-sky-500 transition-all duration-1000" style="width: {{ $current ? min($current->ram_usage, 100) : 0 }}%"></div>
                    </div>
                </div>
            @endif

            <div class="bg-[var(--bg-card)] p-8 border border-[var(--border-color)] rounded-3xl shadow-xl flex flex-col justify-between min-h-[160px] transition-colors duration-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] font-black text-[var(--text-muted)] uppercase tracking-widest mb-3">Sync Status</p>
                        <span class="px-3 py-1 text-[9px] font-black rounded-md border {{ $status === 'ONLINE' ? 'bg-success/5 border-success/30 text-success' : 'bg-danger/5 border-danger/30 text-danger' }} uppercase tracking-[0.2em]">
                            {{ $status }}
                        </span>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-black text-[var(--text-muted)] uppercase tracking-widest mb-3">Last Heartbeat</p>
                        <p class="text-2xl font-display font-black text-[var(--text-main)] tracking-tight">{{ $current ? $current->created_at->format('H:i:s') : '--:--:--' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Infrastructure Services (Agent Only) -->
    @if($server->check_type === 'agent')
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Container Layer -->
            <div x-data="{ open: false }" class="bg-[var(--bg-card)] border border-[var(--border-color)] rounded-3xl shadow-xl overflow-hidden transition-all duration-500">
                <button @click="open = !open" class="w-full p-6 flex items-center justify-between bg-[var(--bg-sidebar)]/30 hover:bg-[var(--bg-sidebar)]/50 transition-colors focus:outline-none">
                    <div class="flex items-center gap-3">
                        <x-lucide-box class="h-5 w-5 text-[var(--accent-primary)]" />
                        <h3 class="text-xs font-bold text-[var(--text-main)] uppercase tracking-widest">Contenedores Docker</h3>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[var(--text-muted)] transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-show="open" x-transition.opacity.duration.300ms class="p-6 space-y-3 max-h-[400px] overflow-y-auto custom-scrollbar border-t border-[var(--border-color)] bg-[var(--bg-card)]">
                    @forelse($server->last_sync_details['containers'] ?? [] as $container)
                        @php $isRunning = str_contains(strtolower($container), 'up') || str_contains(strtolower($container), 'running'); @endphp
                        <div class="flex items-center justify-between p-4 bg-[var(--bg-main)] border border-[var(--border-color)] rounded-2xl group hover:border-[var(--accent-primary)]/30 transition-all">
                            <span class="text-xs font-bold text-[var(--text-main)]">{{ $container }}</span>
                            <span class="text-[8px] font-black px-2 py-0.5 rounded border {{ $isRunning ? 'border-success/30 text-success bg-success/5' : 'border-[var(--border-color)] text-[var(--text-muted)]' }} uppercase">
                                {{ $isRunning ? 'Up' : 'Stop' }}
                            </span>
                        </div>
                    @empty
                        <div class="py-12 text-center opacity-40">
                            <x-lucide-layers-2 class="h-10 w-10 mx-auto mb-4 text-[var(--text-muted)]" />
                            <p class="text-[10px] font-bold text-[var(--text-muted)] uppercase tracking-widest">Sin contenedores</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Core Services -->
            <div x-data="{ open: false }" class="bg-[var(--bg-card)] border border-[var(--border-color)] rounded-3xl shadow-xl overflow-hidden transition-all duration-500">
                <button @click="open = !open" class="w-full p-6 flex items-center justify-between bg-[var(--bg-sidebar)]/30 hover:bg-[var(--bg-sidebar)]/50 transition-colors focus:outline-none">
                    <div class="flex items-center gap-3">
                        <x-lucide-activity class="h-5 w-5 text-[var(--accent-primary)]" />
                        <h3 class="text-xs font-bold text-[var(--text-main)] uppercase tracking-widest">Servicios del Sistema</h3>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[var(--text-muted)] transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-show="open" x-transition.opacity.duration.300ms class="p-6 grid grid-cols-2 gap-4 border-t border-[var(--border-color)] bg-[var(--bg-card)]">
                    @forelse($server->last_sync_details['services'] ?? [] as $service)
                        <div class="flex items-center gap-3 p-4 bg-[var(--bg-main)] border border-[var(--border-color)] rounded-2xl group hover:border-[var(--accent-primary)]/30 transition-all">
                            <div class="h-2 w-2 rounded-full bg-success shadow-[0_0_10px_rgba(34,197,94,0.5)]"></div>
                            <span class="text-[10px] font-black text-[var(--text-main)] uppercase tracking-tight truncate" title="{{ $service }}">{{ $service }}</span>
                        </div>
                    @empty
                        <div class="col-span-2 py-12 text-center opacity-40">
                            <x-lucide-cpu class="h-10 w-10 mx-auto mb-4 text-[var(--text-muted)]" />
                            <p class="text-[10px] font-bold text-[var(--text-muted)] uppercase tracking-widest">Sin servicios activos</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    <!-- Registry History -->
    <div class="bg-[var(--bg-card)] border border-[var(--border-color)] rounded-3xl shadow-xl overflow-hidden transition-colors duration-500">
        <div class="p-6 border-b border-[var(--border-color)] bg-[var(--bg-sidebar)]/30 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <x-lucide-history class="h-5 w-5 text-[var(--text-muted)]" />
                <h3 class="text-xs font-bold text-[var(--text-main)] uppercase tracking-widest">Telemetry History</h3>
            </div>
            <span class="text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest border border-[var(--border-color)] px-3 py-1 rounded-full">{{ $server->metrics()->count() }} Logged Entries</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-[var(--bg-sidebar)]/50">
                    <tr>
                        <th class="px-6 py-4 text-[9px] font-bold text-[var(--text-muted)] uppercase tracking-widest">Registry Timestamp</th>
                        <th class="px-6 py-4 text-[9px] font-bold text-[var(--text-muted)] uppercase tracking-widest text-center">Metric Data</th>
                        <th class="px-6 py-4 text-[9px] font-bold text-[var(--text-muted)] uppercase tracking-widest text-right">Integrity</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border-color)]">
                    @foreach($lastMetrics as $m)
                        <tr class="hover:bg-[var(--accent-primary)]/[0.02] transition-colors">
                            <td class="px-6 py-5 text-xs font-medium text-[var(--text-muted)]">{{ $m->created_at->format('Y-m-d H:i:s') }}</td>
                            <td class="px-6 py-5 text-center">
                                @if($server->check_type === 'agent')
                                    <span class="text-[10px] font-mono font-bold text-[var(--text-main)] bg-[var(--bg-main)] px-4 py-1.5 rounded-lg border border-[var(--border-color)] shadow-inner">
                                        CPU: {{ $m->cpu_load }}% / RAM: {{ $m->ram_usage }}%
                                    </span>
                                @else
                                    <span class="text-[10px] font-mono font-bold text-[var(--text-main)] bg-[var(--bg-main)] px-4 py-1.5 rounded-lg border border-[var(--border-color)] shadow-inner">
                                        Response: {{ $m->cpu_load }} ms
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-5 text-right">
                                <span class="text-[8px] font-black text-success uppercase tracking-widest">Verified</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (function() {
            let chart = null;

            function init() {
                const canvas = document.getElementById('analyticsChart');
                if (!canvas) return;

                const existingChart = Chart.getChart(canvas);
                if (existingChart) existingChart.destroy();

                const rootStyles = getComputedStyle(document.documentElement);
                const textColor = rootStyles.getPropertyValue('--text-muted').trim();
                const accentColor = rootStyles.getPropertyValue('--accent-primary').trim();
                const borderColor = rootStyles.getPropertyValue('--border-color').trim();

                Chart.defaults.font.family = "'Inter', sans-serif";
                Chart.defaults.color = textColor;

                const ctx = canvas.getContext('2d');
                const primaryColor = accentColor;
                const ramColor = '#0ea5e9';

                const gradientPrimary = ctx.createLinearGradient(0, 0, 0, 320);
                gradientPrimary.addColorStop(0, primaryColor + '25');
                gradientPrimary.addColorStop(1, primaryColor + '00');

                chart = new Chart(canvas, {
                    type: 'line',
                    data: {
                        labels: @js($chartData['labels']),
                        datasets: [
                            {
                                label: 'Main Load',
                                data: @js($chartData['cpu']),
                                borderColor: primaryColor,
                                backgroundColor: gradientPrimary,
                                borderWidth: 3,
                                tension: 0.4,
                                fill: true,
                                pointRadius: 0,
                                pointHoverRadius: 6,
                                pointHoverBackgroundColor: primaryColor,
                                pointHoverBorderWidth: 3,
                                pointHoverBorderColor: '#fff'
                            },
                            @if($server->check_type === 'agent')
                            {
                                label: 'RAM Usage',
                                data: @js($chartData['ram']),
                                borderColor: ramColor,
                                borderWidth: 2,
                                borderDash: [5, 5],
                                tension: 0.4,
                                fill: false,
                                pointRadius: 0,
                                pointHoverRadius: 6
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
                                backgroundColor: '#000',
                                titleFont: { size: 10, weight: 'bold' },
                                bodyFont: { size: 10 },
                                padding: 12,
                                borderRadius: 10,
                                displayColors: false,
                                animation: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: borderColor },
                                border: { display: false },
                                ticks: { font: { size: 9 } }
                            },
                            x: {
                                grid: { display: false },
                                border: { display: false },
                                ticks: { font: { size: 9 } }
                            }
                        }
                    }
                });
            }

            document.addEventListener('livewire:navigated', init);
            init();

            window.addEventListener('update-chart', (event) => {
                const data = event.detail.data;
                if (chart) {
                    chart.data.labels = data.labels;
                    chart.data.datasets[0].data = data.cpu;
                    if (chart.data.datasets.length > 1) chart.data.datasets[1].data = data.ram;
                    chart.update('none');
                }
            });
        })();
    </script>
    @endpush
</div>
