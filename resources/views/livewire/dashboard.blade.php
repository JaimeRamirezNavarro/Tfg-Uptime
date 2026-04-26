<div class="space-y-12">
    <!-- Sophisticated Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h1 class="text-3xl font-display font-black text-slate-900 tracking-tight">Infrastructure Overview</h1>
            <p class="text-sm text-slate-500 font-medium mt-1">Status and performance matrix for connected nodes</p>
        </div>
        
        <div x-data="{ open: false }">
            <button @click="open = true" class="btn-primary">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" /></svg>
                Register Node
            </button>
            
            <!-- Elite Modal Overlay -->
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-cloak 
                 class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-slate-950/20 backdrop-blur-md">
                
                <div @click.away="open = false" 
                     class="bg-white w-full max-w-lg shadow-2xl border border-slate-200 overflow-hidden">
                    
                    <div class="px-10 py-8 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                        <div>
                            <h2 class="text-xl font-display font-bold text-slate-900 leading-none">Node Configuration</h2>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-2">Provisioning Layer</p>
                        </div>
                        <button @click="open = false" class="text-slate-300 hover:text-slate-900 transition-colors">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    
                    <form wire:submit.prevent="addServer" class="p-10 space-y-8">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Detection Method</label>
                            <div class="grid grid-cols-3 gap-2 p-1 bg-slate-100 border border-slate-200">
                                @foreach(['agent' => 'Agent', 'ping' => 'Ping', 'http' => 'HTTP'] as $type => $label)
                                    <button type="button" 
                                            wire:click="$set('checkType', '{{ $type }}')" 
                                            class="py-2.5 text-[9px] font-black uppercase tracking-widest transition-all {{ $checkType === $type ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-400' }}">
                                        {{ $label }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Display Name</label>
                                <input type="text" wire:model="newName" class="premium-input" placeholder="Core-Prod-01">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Target Endpoint</label>
                                <input type="text" wire:model="ip" class="premium-input" placeholder="e.g. 192.168.1.1 or example.com">
                            </div>
                        </div>

                        <div class="pt-4">
                            <button type="submit" @click="open = false" class="w-full btn-primary py-4">
                                Initialize Connection
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Cluster Performance Tiles -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @php
            $kpis = [
                ['label' => 'Global Nodes', 'value' => count($servers), 'unit' => 'Nodes Sync'],
                ['label' => 'Operational', 'value' => $stats['activeServers'] ?? 0, 'unit' => 'Available'],
                ['label' => 'Alerts active', 'value' => $stats['activeAlerts'] ?? 0, 'unit' => 'Incidents'],
                ['label' => 'Cluster Uptime', 'value' => ($stats['averageUptime'] ?? 0) . '%', 'unit' => 'Last 30d'],
            ];
        @endphp

        @foreach($kpis as $kpi)
            <div class="premium-card p-10 group overflow-hidden relative">
                <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                    <svg class="h-10 w-10 text-slate-900" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                </div>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">{{ $kpi['label'] }}</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-display font-black text-slate-900 tracking-tighter">{{ $kpi['value'] }}</span>
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ $kpi['unit'] }}</span>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Inventory Matrix -->
    <div class="premium-card">
        <div class="px-10 py-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/30">
            <h3 class="text-[10px] font-black uppercase tracking-widest text-slate-900">Infrastructure Matrix</h3>
            <div class="flex items-center gap-2">
                <div class="h-1.5 w-1.5 bg-emerald-500 rounded-full"></div>
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Real-time Stream</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="professional-table">
                <thead>
                    <tr>
                        <th class="w-1/3">Node Identity</th>
                        <th class="text-center">Detection Status</th>
                        <th class="text-center">Load Profile</th>
                        <th class="text-right">Administration</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($servers as $server)
                        <tr>
                            <td>
                                <a href="{{ route('server.detail', $server->id) }}" class="flex items-center gap-4 group/node">
                                    <div class="h-10 w-10 bg-slate-50 border border-slate-200 flex items-center justify-center transition-all group-hover/node:border-indigo-200 group-hover/node:bg-indigo-50/30">
                                        <svg class="h-5 w-5 text-slate-400 group-hover/node:text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                                        <p class="text-sm font-bold text-slate-900 leading-none group-hover/node:text-indigo-600 transition-colors">{{ $server->name }}</p>
                                        <p class="text-[9px] text-slate-400 font-black uppercase tracking-widest mt-1">{{ $server->ip_address }}</p>
                                    </div>
                                </a>
                            </td>

                            <td class="text-center">
                                @php 
                                    $lastMetric = $server->metrics()->latest()->first(); 
                                    $isOnline = $lastMetric && $lastMetric->created_at->diffInSeconds(now()) < 50;
                                @endphp
                                <div class="flex justify-center">
                                    <span class="status-pill {{ $isOnline ? 'online w-24' : 'offline w-24' }}">
                                        {{ $isOnline ? 'Operational' : 'Disconnected' }}
                                    </span>
                                </div>
                            </td>

                            <td class="text-center">
                                @if($isOnline && $server->check_type === 'agent')
                                    <div class="flex items-center justify-center gap-8">
                                        <div class="flex flex-col items-center">
                                            <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest">CPU</span>
                                            <span class="text-xs font-bold text-slate-900 mt-0.5">{{ $lastMetric->cpu_load }}%</span>
                                        </div>
                                        <div class="flex flex-col items-center">
                                            <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest">RAM</span>
                                            <span class="text-xs font-bold text-slate-900 mt-0.5">{{ $lastMetric->ram_usage }}%</span>
                                        </div>
                                    </div>
                                @elseif($isOnline)
                                    <div class="flex flex-col items-center">
                                        <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Latency</span>
                                        <span class="text-xs font-bold text-slate-900 mt-0.5">{{ round($lastMetric->cpu_load, 1) }} ms</span>
                                    </div>
                                @else
                                    <span class="text-[9px] font-bold text-slate-200 uppercase tracking-widest italic">Signal lost</span>
                                @endif
                            </td>

                            <td class="text-right px-8">
                                <div class="flex items-center justify-end gap-1">
                                    <button wire:click="toggleServer({{ $server->id }})" title="Pause" class="p-2 text-slate-300 hover:text-slate-900 hover:bg-slate-50 transition-all">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </button>
                                    <button onclick="confirm('Permanent deletion?') || event.stopImmediatePropagation()" 
                                            wire:click="deleteServer({{ $server->id }})" 
                                            class="p-2 text-slate-300 hover:text-rose-600 hover:bg-rose-50/50 transition-all">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-10 py-20 text-center text-slate-400 text-xs font-bold uppercase tracking-widest italic">No nodes detected in active cluster.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
