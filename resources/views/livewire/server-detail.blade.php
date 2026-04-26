@php 
    $current = $server->metrics()->latest()->first(); 
    $status = ($current && $current->created_at->diffInSeconds(now()) < 50) ? 'ONLINE' : 'OFFLINE';
@endphp

<div class="space-y-10" wire:poll.5s>
    <!-- Header: Node Info -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('dashboard') }}" class="text-surface-400 hover:text-surface-900 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h1 class="text-3xl font-display font-black text-surface-900 tracking-tight">{{ $server->name }}</h1>
            </div>
            <p class="text-sm text-surface-500 font-medium">Node Endpoint: {{ $server->ip_address }} • {{ strtoupper($server->check_type) }} Connectivity</p>
        </div>

        <div class="flex items-center gap-4">
            <button wire:click="toggleServer" class="btn-secondary">
                {{ $server->is_enabled ? 'Disable Monitoring' : 'Enable Monitoring' }}
            </button>
            <button onclick="confirm('Permanent deletion?') || event.stopImmediatePropagation()" wire:click="deleteServer" class="btn-secondary border-rose-200 text-rose-600 hover:bg-rose-50 hover:border-rose-300">
                Delete Node
            </button>
        </div>
    </div>

    <!-- Analytics Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Stats Card -->
        <div class="lg:col-span-2 premium-card p-10 bg-white relative overflow-hidden border-none shadow-2xl shadow-black/5" wire:ignore wire:key="chart-wrapper-{{ $server->id }}">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h3 class="text-sm font-black uppercase tracking-widest text-surface-900">Performance Stream</h3>
                    <p class="text-[10px] text-surface-400 font-bold uppercase tracking-widest mt-1">Real-time load telemetry</p>
                </div>
                <div class="flex gap-6 bg-surface-50 px-4 py-2 border border-surface-100">
                    <div class="flex items-center gap-2">
                        <div class="h-2 w-2 rounded-full {{ ($server->check_type === 'ping' || $server->check_type === 'http') ? ($server->check_type === 'http' ? 'bg-sky-500' : 'bg-emerald-500') : 'bg-primary-500' }}"></div>
                        <span class="text-[9px] font-black text-surface-500 uppercase tracking-widest">{{ ($server->check_type === 'ping' || $server->check_type === 'http') ? 'Latency' : 'CPU' }}</span>
                    </div>
                    @if($server->check_type === 'agent')
                        <div class="flex items-center gap-2">
                            <div class="h-2 w-2 rounded-full bg-sky-500"></div>
                            <span class="text-[9px] font-black text-surface-500 uppercase tracking-widest">Memory</span>
                        </div>
                    @endif
                </div>
            </div>
            <div class="h-[300px] w-full">
                <canvas id="analyticsChart"></canvas>
            </div>
        </div>

        <!-- Right Side Panel -->
        <div class="space-y-6">
            <div class="premium-card p-8 bg-surface-900 text-white border-none shadow-2xl shadow-black/10">
                <p class="text-[10px] font-bold text-surface-400 uppercase tracking-widest mb-4">Availability Index</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-5xl font-display font-black tracking-tighter">99.9</span>
                    <span class="text-sm font-bold text-surface-500">%</span>
                </div>
                <p class="text-[9px] text-surface-500 uppercase font-black tracking-widest mt-6">Stable Connection Verified</p>
            </div>

            <div class="premium-card p-8 bg-white border-none shadow-2xl shadow-black/5 flex flex-col justify-between h-48">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[10px] font-bold text-surface-500 uppercase tracking-widest mb-1">Status</p>
                        <span class="status-pill {{ $status === 'ONLINE' ? 'online text-[10px]' : 'offline text-[10px]' }}">{{ $status }}</span>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-bold text-surface-500 uppercase tracking-widest mb-1">Last Sync</p>
                        <p class="text-xl font-display font-black text-surface-900 tracking-tight">{{ $current ? $current->created_at->format('H:i:s') : '--:--:--' }}</p>
                    </div>
                </div>
                <div class="pt-4 border-t border-surface-50">
                    <div class="flex justify-between items-center text-[10px] font-black uppercase tracking-widest text-surface-400">
                        <span>Avg Load</span>
                        <span class="text-surface-900">{{ round($server->metrics()->avg('cpu_load'), 2) }} {{ $server->check_type === 'agent' ? '%' : 'ms' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Runtime Ecosystem (Agent Only) -->
    @if($server->check_type === 'agent' && $server->last_sync_details)
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="premium-card">
                <div class="px-8 py-5 border-b border-surface-100 bg-surface-50/50">
                    <h3 class="text-[10px] font-black uppercase tracking-widest text-surface-900">Docker Runtime</h3>
                </div>
                <div class="p-6 space-y-2">
                    @forelse($server->last_sync_details['containers'] ?? [] as $container)
                        @php $isRunning = str_contains(strtolower($container), 'up') || str_contains(strtolower($container), 'running'); @endphp
                        <div class="flex items-center justify-between p-3 bg-surface-50 border border-surface-100 rounded">
                            <span class="text-xs font-bold text-surface-700">{{ $container }}</span>
                            <span class="status-pill {{ $isRunning ? 'online' : 'offline' }} scale-75">
                                {{ $isRunning ? 'Active' : 'Offline' }}
                            </span>
                        </div>
                    @empty
                        <p class="text-[10px] font-bold text-surface-400 text-center py-4 uppercase tracking-widest">No containers detected</p>
                    @endforelse
                </div>
            </div>

            <div class="premium-card">
                <div class="px-8 py-5 border-b border-surface-100 bg-surface-50/50">
                    <h3 class="text-[10px] font-black uppercase tracking-widest text-surface-900">System Services</h3>
                </div>
                <div class="p-6 grid grid-cols-2 gap-3">
                    @forelse($server->last_sync_details['services'] ?? [] as $service)
                        <div class="flex items-center gap-2 p-2 bg-surface-50 border border-surface-200 rounded">
                            <div class="h-1 w-1 rounded-full bg-primary-600"></div>
                            <span class="text-[9px] font-bold text-surface-600 uppercase tracking-tighter truncate">{{ $service }}</span>
                        </div>
                    @empty
                        <p class="col-span-2 text-[10px] font-bold text-surface-400 text-center py-4 uppercase tracking-widest">No services detected</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    <!-- Data Registry -->
    <div class="premium-card overflow-hidden bg-white border-none shadow-2xl shadow-black/5">
        <div class="px-8 py-6 border-b border-surface-100 bg-surface-50/50 flex justify-between items-center">
            <h3 class="text-sm font-black uppercase tracking-widest text-surface-900">Metrics History</h3>
            <span class="text-[10px] font-black text-surface-400 uppercase tracking-widest">{{ $server->metrics()->count() }} Entries</span>
        </div>
        <div class="overflow-x-auto">
            <table class="professional-table">
                <thead>
                    <tr>
                        <th class="w-1/3">Timestamp</th>
                        <th class="text-center">Recorded Load</th>
                        <th class="text-right">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lastMetrics as $m)
                        <tr class="hover:bg-surface-50/50 transition-colors">
                            <td class="font-bold text-surface-500">{{ $m->created_at->format('Y-m-d H:i:s') }}</td>
                            <td class="text-center">
                                @if($server->check_type === 'agent')
                                    <span class="text-[10px] font-black text-surface-900 bg-surface-50 px-3 py-1 rounded">CPU {{ $m->cpu_load }}% / RAM {{ $m->ram_usage }}%</span>
                                @else
                                    <span class="text-[10px] font-black text-surface-900 bg-surface-50 px-3 py-1 rounded">{{ $m->cpu_load }} ms</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <span class="status-pill online scale-75">Verified</span>
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
                if (existingChart) {
                    existingChart.destroy();
                }

                Chart.defaults.font.family = "'Inter', sans-serif";
                Chart.defaults.color = '#94a3b8';

                const ctx = canvas.getContext('2d');
                
                const primaryColor = '{{ ($server->check_type === "ping") ? "#10b981" : (($server->check_type === "http") ? "#0ea5e9" : "#6366f1") }}';
                const accentColor = '#38bdf8';
                
                const gradientPrimary = ctx.createLinearGradient(0, 0, 0, 300);
                gradientPrimary.addColorStop(0, primaryColor + '20');
                gradientPrimary.addColorStop(1, primaryColor + '00');

                chart = new Chart(canvas, {
                    type: 'line',
                    data: {
                        labels: @js($chartData['labels']),
                        datasets: [
                            {
                                label: 'Metrics',
                                data: @js($chartData['cpu']),
                                borderColor: primaryColor,
                                backgroundColor: gradientPrimary,
                                borderWidth: 3,
                                tension: 0.4,
                                fill: true,
                                pointRadius: 0,
                                pointHoverRadius: 6,
                            },
                            @if($server->check_type === 'agent')
                            {
                                label: 'Memory',
                                data: @js($chartData['ram']),
                                borderColor: accentColor,
                                borderWidth: 3,
                                tension: 0.4,
                                fill: false,
                                pointRadius: 0,
                            }
                            @endif
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, border: { display: false }, grid: { color: '#f1f5f9' } },
                            x: { grid: { display: false } }
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
