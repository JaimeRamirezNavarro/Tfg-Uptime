@php
    $current = $server->metrics()->latest()->first();
    $status = ($current && $current->created_at->diffInSeconds(now()) < 50) ? 'ONLINE' : 'OFFLINE';
@endphp

<div class="space-y-6" wire:poll.5s>
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('dashboard') }}" class="p-2 hover:bg-bg-tertiary rounded-xl transition-colors">
                    <svg class="w-5 h-5 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h1 class="text-3xl font-display font-black text-gradient">{{ $server->name }}</h1>
            </div>
            <p class="text-sm text-text-secondary mt-1">Endpoint: {{ $server->ip_address }} • <span class="text-accent-secondary">{{ strtoupper($server->check_type) }}</span></p>
        </div>

        <div class="flex items-center gap-3">
            <button wire:click="toggleServer" class="btn-secondary">
                {{ $server->is_enabled ? 'Disable' : 'Enable' }}
            </button>
            <button onclick="confirm('Delete this node?') || event.stopImmediatePropagation()" wire:click="deleteServer" class="btn-secondary border-danger/30 text-danger hover:bg-danger/10">
                Delete
            </button>
        </div>
    </div>

    <!-- Analytics -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Chart -->
        <div class="lg:col-span-2 glass-card p-8" wire:ignore wire:key="chart-wrapper-{{ $server->id }}">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-sm font-black uppercase tracking-widest text-text-primary">Performance Stream</h3>
                    <p class="text-[9px] text-text-tertiary font-bold uppercase tracking-widest mt-1">Real-time telemetry</p>
                </div>
                <div class="flex gap-4 bg-bg-tertiary px-4 py-2 border border-border-subtle rounded-xl">
                    <div class="flex items-center gap-2">
                        <div class="h-2 w-2 rounded-full" style="background: {{ ($server->check_type === 'ping' || $server->check_type === 'http') ? ($server->check_type === 'http' ? '#0ea5e9' : '#10b981') : '#6366f1' }}"></div>
                        <span class="text-[9px] font-black text-text-secondary uppercase tracking-widest">{{ ($server->check_type === 'ping' || $server->check_type === 'http') ? 'Latency' : 'CPU' }}</span>
                    </div>
                    @if($server->check_type === 'agent')
                        <div class="flex items-center gap-2">
                            <div class="h-2 w-2 rounded-full bg-sky-500"></div>
                            <span class="text-[9px] font-black text-text-secondary uppercase tracking-widest">RAM</span>
                        </div>
                    @endif
                </div>
            </div>
            <div class="h-[280px] w-full">
                <canvas id="analyticsChart"></canvas>
            </div>
        </div>

        <!-- Side Stats -->
        <div class="space-y-4">
            <div class="glass-card p-6 bg-white/5 border-white/10">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-[10px] font-bold text-text-tertiary uppercase tracking-widest">Real-time CPU</p>
                    <div class="h-1.5 w-1.5 rounded-full bg-indigo-500 animate-pulse"></div>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-4xl font-display font-black tracking-tighter text-text-primary">{{ $current ? round($current->cpu_load, 1) : '0' }}</span>
                    <span class="text-xs font-bold text-text-secondary">%</span>
                </div>
                <div class="mt-4 h-1 w-full bg-bg-tertiary rounded-full overflow-hidden">
                    <div class="h-full bg-indigo-500 transition-all duration-500" style="width: {{ $current ? $current->cpu_load : 0 }}%"></div>
                </div>
            </div>

            <div class="glass-card p-6 bg-white/5 border-white/10">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-[10px] font-bold text-text-tertiary uppercase tracking-widest">Real-time RAM</p>
                    <div class="h-1.5 w-1.5 rounded-full bg-sky-500 animate-pulse"></div>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-4xl font-display font-black tracking-tighter text-text-primary">{{ $current ? round($current->ram_usage, 1) : '0' }}</span>
                    <span class="text-xs font-bold text-text-secondary">%</span>
                </div>
                <div class="mt-4 h-1 w-full bg-bg-tertiary rounded-full overflow-hidden">
                    <div class="h-full bg-sky-500 transition-all duration-500" style="width: {{ $current ? $current->ram_usage : 0 }}%"></div>
                </div>
            </div>

            <div class="glass-card p-8 flex flex-col justify-between h-40">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] font-bold text-text-tertiary uppercase tracking-widest mb-1">Status</p>
                        <span class="status-pill {{ $status === 'ONLINE' ? 'online' : 'offline' }}">{{ $status }}</span>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-bold text-text-tertiary uppercase tracking-widest mb-1">Last Sync</p>
                        <p class="text-xl font-display font-black text-text-primary tracking-tight">{{ $current ? $current->created_at->format('H:i:s') : '--:--:--' }}</p>
                    </div>
                </div>
                <div class="pt-3 border-t border-border-subtle">
                    <div class="flex justify-between items-center text-[9px] font-black uppercase tracking-widest text-text-tertiary">
                        <span>Avg Load</span>
                        <span class="text-text-primary">{{ round($server->metrics()->avg('cpu_load'), 2) }} {{ $server->check_type === 'agent' ? '%' : 'ms' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Docker & Services (Agent Only) -->
    @if($server->check_type === 'agent' && $server->last_sync_details)
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Docker -->
            <div class="glass-card">
                <div class="px-6 py-4 border-b border-border-subtle bg-bg-tertiary/50">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-accent-secondary" fill="currentColor" viewBox="0 0 24 24"><path d="M13.983 11.078h2.119a.186.186 0 00.186-.185V9.006a.186.186 0 00-.186-.186h-2.119a.185.185 0 00-.185.185v1.888c0 .102.083.185.185.185m-8.69-4.63l2.033-.931.917 2.33 2.088.126-.295-2.087 1.706-1.476-2.032-.373-.932-2.027-.932 2.027-2.032.372 1.705 1.477-.294 2.087 2.088-.126.916-2.33 2.033.932-.561 1.969 1.768 1.082-1.768 1.082.56 1.97-2.032.93-.917-2.331-2.088.127.295 2.087-1.706 1.475 2.032.373.932 2.028.932-2.027 2.033-.373-1.706-1.476.295-2.087-2.088.126-.917 2.33-2.033-.93.562-1.97-1.768-1.081 1.767-1.082-.56-1.969zM7.548 8.205l1.551-.672.69 1.76 1.59.095-.224-1.59 1.302-1.128-1.55-.285-.71-1.543-.709 1.543-1.55.285 1.3 1.128-.223 1.59 1.59-.095.69-1.76 1.55.672-.429 1.502 1.347.823-1.347.824.43 1.501-1.55.672-.69-1.76-1.59-.095.224 1.59-1.302 1.128 1.55.285.709 1.543.71-1.543 1.55-.285-1.3-1.128.224-1.59-1.59.095-.69 1.76-1.55-.672.428-1.502-1.347-.823 1.347-.824-.429-1.501z"/></svg>
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-text-primary">Docker Containers</h3>
                    </div>
                </div>
                <div class="p-6 space-y-2">
                    @forelse($server->last_sync_details['containers'] ?? [] as $container)
                        @php $isRunning = str_contains(strtolower($container), 'up') || str_contains(strtolower($container), 'running'); @endphp
                        <div class="flex items-center justify-between p-3 bg-bg-tertiary border border-border-subtle rounded-xl">
                            <span class="text-xs font-bold text-text-primary">{{ $container }}</span>
                            <span class="status-pill {{ $isRunning ? 'online' : 'offline' }}">
                                {{ $isRunning ? 'Active' : 'Stopped' }}
                            </span>
                        </div>
                    @empty
                        <p class="text-[10px] font-bold text-text-tertiary text-center py-4 uppercase tracking-widest">No containers</p>
                    @endforelse
                </div>
            </div>

            <!-- Services -->
            <div class="glass-card">
                <div class="px-6 py-4 border-b border-border-subtle bg-bg-tertiary/50">
                    <h3 class="text-[10px] font-black uppercase tracking-widest text-text-primary">System Services</h3>
                </div>
                <div class="p-6 grid grid-cols-2 gap-3">
                    @forelse($server->last_sync_details['services'] ?? [] as $service)
                        <div class="flex items-center gap-2 p-2.5 bg-bg-tertiary border border-border-subtle rounded-xl">
                            <div class="h-1.5 w-1.5 rounded-full bg-success glow-success"></div>
                            <span class="text-[9px] font-bold text-text-secondary uppercase tracking-tight truncate">{{ $service }}</span>
                        </div>
                    @empty
                        <p class="col-span-2 text-[10px] font-bold text-text-tertiary text-center py-4 uppercase tracking-widest">No services</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    <!-- Metrics History -->
    <div class="glass-card overflow-hidden">
        <div class="px-6 py-4 border-b border-border-subtle bg-bg-tertiary/50 flex justify-between items-center">
            <h3 class="text-sm font-black uppercase tracking-widest text-text-primary">Metrics History</h3>
            <span class="text-[9px] font-black text-text-tertiary uppercase tracking-widest">{{ $server->metrics()->count() }} entries</span>
        </div>
        <div class="overflow-x-auto">
            <table class="dark-table">
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th class="text-center">Metrics</th>
                        <th class="text-right">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lastMetrics as $m)
                        <tr>
                            <td class="font-medium text-text-secondary">{{ $m->created_at->format('Y-m-d H:i:s') }}</td>
                            <td class="text-center">
                                @if($server->check_type === 'agent')
                                    <span class="text-[10px] font-black text-text-primary bg-bg-tertiary px-3 py-1.5 rounded-lg">CPU {{ $m->cpu_load }}% / RAM {{ $m->ram_usage }}%</span>
                                @else
                                    <span class="text-[10px] font-black text-text-primary bg-bg-tertiary px-3 py-1.5 rounded-lg">{{ $m->cpu_load }} ms</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <span class="status-pill online">Verified</span>
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

                Chart.defaults.font.family = "'Outfit', sans-serif";
                Chart.defaults.color = 'rgba(255, 255, 255, 0.45)';

                const ctx = canvas.getContext('2d');
                const primaryColor = '{{ ($server->check_type === "ping") ? "#10b981" : (($server->check_type === "http") ? "#0ea5e9" : "#6366f1") }}';
                const accentColor = '#38bdf8';

                const gradientPrimary = ctx.createLinearGradient(0, 0, 0, 280);
                gradientPrimary.addColorStop(0, primaryColor + '30');
                gradientPrimary.addColorStop(1, primaryColor + '00');

                chart = new Chart(canvas, {
                    type: 'line',
                    data: {
                        labels: @js($chartData['labels']),
                        datasets: [
                            {
                                label: 'Load',
                                data: @js($chartData['cpu']),
                                borderColor: primaryColor,
                                backgroundColor: gradientPrimary,
                                borderWidth: 2,
                                tension: 0.4,
                                fill: true,
                                pointRadius: 0,
                                pointHoverRadius: 5,
                            },
                            @if($server->check_type === 'agent')
                            {
                                label: 'RAM',
                                data: @js($chartData['ram']),
                                borderColor: accentColor,
                                borderWidth: 2,
                                tension: 0.4,
                                fill: false,
                                pointRadius: 0,
                                pointHoverRadius: 5,
                            }
                            @endif
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            intersect: false,
                            mode: 'index',
                        },
                        animation: {
                            duration: 0
                        },
                        hover: {
                            mode: 'index',
                            intersect: false,
                            animationDuration: 0
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                enabled: true,
                                position: 'nearest',
                                external: null,
                                animation: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: 'rgba(255, 255, 255, 0.04)' },
                                border: { color: 'rgba(255, 255, 255, 0.1)' }
                            },
                            x: {
                                grid: { display: false },
                                border: { color: 'rgba(255, 255, 255, 0.1)' }
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
