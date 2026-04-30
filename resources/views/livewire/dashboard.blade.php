<div class="space-y-8" wire:poll.5s="checkServers">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h1 class="text-3xl font-display font-bold text-gradient">Infrastructure Overview</h1>
            <p class="text-sm text-text-secondary mt-2">Real-time status and performance matrix for connected nodes</p>
        </div>

        <div x-data="{ open: false }">
            <button @click="open = true" class="btn-primary">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" /></svg>
                Register Node
            </button>

            <!-- Modal -->
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-cloak
                 class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-slate-900/20 backdrop-blur-md">

                <div @click.away="open = false"
                     class="glass-card w-full max-w-lg overflow-hidden">

                    <div class="px-8 py-6 border-b border-border-subtle flex justify-between items-center bg-bg-tertiary">
                        <div>
                            <h2 class="text-lg font-display font-bold text-text-primary leading-none">Node Configuration</h2>
                            <p class="text-[10px] font-bold text-text-tertiary uppercase tracking-widest mt-2">Provisioning Layer</p>
                        </div>
                        <button @click="open = false" class="text-text-tertiary hover:text-text-primary transition-colors">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="addServer" class="p-8 space-y-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-text-tertiary uppercase tracking-widest">Detection Method</label>
                            <div class="grid grid-cols-3 gap-2 p-1 bg-bg-tertiary border border-border-subtle rounded-xl">
                                @foreach(['agent' => 'Agent', 'ping' => 'Ping', 'http' => 'HTTP'] as $type => $label)
                                    <button type="button"
                                            wire:click="$set('checkType', '{{ $type }}')"
                                            class="py-2.5 text-[10px] font-bold uppercase tracking-widest transition-all rounded-lg {{ $checkType === $type ? 'bg-accent-primary text-white shadow-md' : 'text-text-tertiary hover:text-text-secondary' }}">
                                        {{ $label }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-[10px] font-black text-text-tertiary uppercase tracking-widest mb-2">Display Name</label>
                                <input type="text" wire:model="newName" class="input-dark" placeholder="ZimaBlade-Prod">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-text-tertiary uppercase tracking-widest mb-2">Target Endpoint</label>
                                <input type="text" wire:model="ip" class="input-dark" placeholder="e.g. 192.168.1.1 or example.com">
                            </div>

                            @if($checkType === 'agent')
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[10px] font-black text-text-tertiary uppercase tracking-widest mb-2">SSH User</label>
                                        <input type="text" wire:model="sshUser" class="input-dark" placeholder="root">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-text-tertiary uppercase tracking-widest mb-2">SSH Password</label>
                                        <input type="password" wire:model="sshPassword" class="input-dark" placeholder="••••••••">
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 p-3 bg-bg-tertiary border border-border-subtle rounded-xl">
                                    <input type="checkbox" wire:model="autoDeploy" class="rounded border-border-medium bg-bg-secondary text-accent-primary focus:ring-accent-primary focus:ring-2">
                                    <span class="text-[9px] font-bold text-text-secondary uppercase tracking-widest">Enable Remote Deployment</span>
                                </div>
                            @endif
                        </div>

                        <div class="pt-2">
                            <button type="submit" @click="open = false" class="w-full btn-primary py-4">
                                Initialize Connection
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $kpis = [
                ['label' => 'Global Nodes', 'value' => count($servers), 'unit' => 'Total', 'icon' => 'M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01'],
                ['label' => 'Operational', 'value' => $stats['activeServers'] ?? 0, 'unit' => 'Active', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['label' => 'Active Alerts', 'value' => $stats['activeAlerts'] ?? 0, 'unit' => 'Incidents', 'icon' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9'],
                ['label' => 'Cluster Uptime', 'value' => ($stats['averageUptime'] ?? 0) . '%', 'unit' => 'Last 30d', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'],
            ];
        @endphp

        @foreach($kpis as $kpi)
            <div class="stat-card group">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="text-[10px] font-bold text-text-tertiary uppercase tracking-[0.2em]">{{ $kpi['label'] }}</p>
                        <div class="flex items-baseline gap-2 mt-2">
                            <span class="text-3xl font-display font-bold text-text-primary tracking-tight">{{ $kpi['value'] }}</span>
                            <span class="text-[10px] font-semibold text-text-tertiary uppercase">{{ $kpi['unit'] }}</span>
                        </div>
                    </div>
                    <div class="h-10 w-10 bg-bg-tertiary rounded-xl flex items-center justify-center group-hover:bg-accent-primary/20 transition-colors">
                        <svg class="h-5 w-5 text-text-tertiary group-hover:text-accent-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $kpi['icon'] }}" />
                        </svg>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Servers Table -->
    <div class="glass-card overflow-hidden">
        <div class="px-8 py-5 border-b border-border-subtle flex justify-between items-center bg-bg-tertiary">
            <h3 class="text-[11px] font-bold uppercase tracking-widest text-text-primary">Infrastructure Matrix</h3>
            <div class="flex items-center gap-2">
                <div class="h-2 w-2 bg-success rounded-full animate-pulse glow-success"></div>
                <span class="text-[10px] font-bold text-text-tertiary uppercase tracking-widest">Real-time Stream</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="dark-table">
                <thead>
                    <tr>
                        <th class="w-1/3">Node Identity</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Load Profile</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($servers as $server)
                        <tr>
                            <td>
                                <a href="{{ route('server.detail', $server->id) }}" class="flex items-center gap-4 group/node">
                                    <div class="h-11 w-11 bg-bg-tertiary border border-border-subtle rounded-xl flex items-center justify-center transition-all group-hover/node:border-accent-primary/50 group-hover/node:bg-accent-primary/10">
                                        <svg class="h-5 w-5 text-text-tertiary group-hover/node:text-accent-secondary transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            @if($server->check_type === 'agent')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                            @elseif($server->check_type === 'ping')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0" />
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                            @endif
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-text-primary leading-none group-hover/node:text-accent-secondary transition-colors">{{ $server->name }}</p>
                                        <p class="text-[9px] text-text-tertiary font-black uppercase tracking-widest mt-1">{{ $server->ip_address }}</p>
                                    </div>
                                </a>
                            </td>

                            <td class="text-center">
                                @php
                                    $lastMetric = $server->metrics()->latest()->first();
                                    $isOnline = $lastMetric && $lastMetric->created_at->diffInSeconds(now()) < 50;
                                    if ($server->check_type !== 'agent' && $lastMetric) {
                                        $details = json_decode($lastMetric->details, true) ?? [];
                                        $isOnline = $isOnline && ($details['online'] ?? false);
                                    }
                                @endphp
                                <div class="flex justify-center">
                                    <span class="status-pill {{ $isOnline ? 'online' : 'offline' }}">
                                        {{ $isOnline ? 'Operational' : 'Offline' }}
                                    </span>
                                </div>
                            </td>

                            <td class="text-center">
                                @if($isOnline && $server->check_type === 'agent')
                                    <div class="flex items-center justify-center gap-6">
                                        <div class="flex flex-col items-center">
                                            <span class="text-[8px] font-black text-text-tertiary uppercase tracking-widest">CPU</span>
                                            <span class="text-sm font-bold text-text-primary mt-0.5">{{ $lastMetric->cpu_load }}%</span>
                                        </div>
                                        <div class="flex flex-col items-center">
                                            <span class="text-[8px] font-black text-text-tertiary uppercase tracking-widest">RAM</span>
                                            <span class="text-sm font-bold text-text-primary mt-0.5">{{ $lastMetric->ram_usage }}%</span>
                                        </div>
                                    </div>
                                @elseif($isOnline)
                                    <div class="flex flex-col items-center">
                                        <span class="text-[8px] font-black text-text-tertiary uppercase tracking-widest">Latency</span>
                                        <span class="text-sm font-bold text-text-primary mt-0.5">{{ round($lastMetric->cpu_load, 1) }} ms</span>
                                    </div>
                                @else
                                    <span class="text-[9px] font-bold text-text-tertiary uppercase tracking-widest italic">Signal lost</span>
                                @endif
                            </td>

                            <td class="text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button wire:click="toggleServer({{ $server->id }})" title="Toggle" class="p-2 text-text-tertiary hover:text-text-primary hover:bg-bg-tertiary rounded-lg transition-all">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </button>
                                    <button onclick="confirm('Delete this node?') || event.stopImmediatePropagation()"
                                            wire:click="deleteServer({{ $server->id }})"
                                            class="p-2 text-text-tertiary hover:text-danger hover:bg-danger/10 rounded-lg transition-all">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-16 text-center text-text-tertiary text-xs font-bold uppercase tracking-widest">No nodes detected in active cluster</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
