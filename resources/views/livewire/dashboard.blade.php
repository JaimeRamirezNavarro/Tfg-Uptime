<div class="space-y-12" wire:poll.5s="checkServers">
    <!-- Encabezado y Acciones -->
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8 mb-6">
        <div>
            <h1 class="text-4xl font-display font-black text-surface-900 tracking-tight">Dashboard General</h1>
            <p class="text-base text-surface-500 font-medium mt-2">Estado global de la infraestructura y conectividad en tiempo real</p>
        </div>
        
        <div x-data="{ open: false }">
            <button @click="open = true" class="bg-primary-600 hover:bg-primary-500 text-white font-bold px-8 py-4 rounded-none transition-all shadow-xl shadow-primary-500/25 hover:-translate-y-0.5 active:translate-y-0 flex items-center gap-3 group">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:rotate-90 transition-transform duration-300" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Registrar Nodo
            </button>
            
            <!-- Modal -->
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 style="display: none;" 
                 class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-surface-950/60 backdrop-blur-md">
                
                <div @click.away="open = false" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     class="bg-white p-10 rounded-none shadow-2xl w-full max-w-lg relative border border-surface-200">
                    
                    <button @click="open = false" class="absolute top-8 right-8 text-surface-400 hover:text-surface-600 bg-surface-100/50 hover:bg-surface-100 rounded-none p-2.5 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                    
                    <div class="h-16 w-16 bg-primary-100 text-primary-600 rounded-none flex items-center justify-center mb-8 shadow-inner shadow-primary-200/50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" /></svg>
                    </div>
                    
                    <h4 class="text-2xl font-display font-black text-surface-900 mb-2">Nuevo Servidor</h4>
                    <p class="text-surface-500 font-medium mb-8">Configura un nuevo agente de monitorización de alta precisión</p>
                    
                    <form wire:submit.prevent="addServer" class="space-y-6">
                        <div class="flex gap-2 p-1.5 bg-surface-100/80 rounded-none mb-6">
                            <button type="button" wire:click="$set('checkType', 'agent')" class="flex-1 py-3 rounded-none text-xs font-black uppercase tracking-widest transition-all {{ $checkType === 'agent' ? 'bg-white text-primary-600 shadow-sm border border-surface-200' : 'text-surface-500 hover:text-surface-700' }}">
                                Node Agent
                            </button>
                            <button type="button" wire:click="$set('checkType', 'ping')" class="flex-1 py-3 rounded-none text-xs font-black uppercase tracking-widest transition-all {{ $checkType === 'ping' ? 'bg-white text-emerald-600 shadow-sm border border-surface-200' : 'text-surface-500 hover:text-surface-700' }}">
                                ICMP Ping
                            </button>
                            <button type="button" wire:click="$set('checkType', 'http')" class="flex-1 py-3 rounded-none text-xs font-black uppercase tracking-widest transition-all {{ $checkType === 'http' ? 'bg-white text-sky-600 shadow-sm border border-surface-200' : 'text-surface-500 hover:text-surface-700' }}">
                                HTTP Check
                            </button>
                        </div>

                        <div class="space-y-5">
                            <div>
                                <label class="block text-xs font-black text-surface-500 uppercase tracking-widest mb-2.5">Nombre del nodo</label>
                                <input type="text" wire:model="newName" class="w-full premium-input px-5 py-4 text-sm font-semibold" placeholder="{{ $checkType === 'agent' ? 'Servidor de Producción 01' : ($checkType === 'http' ? 'E-commerce API' : 'Puerta de Enlace') }}">
                                @error('newName') <span class="text-xs font-bold text-red-500 mt-2 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-black text-surface-500 uppercase tracking-widest mb-2.5">{{ $checkType === 'agent' ? 'Dominio o Dirección IP' : ($checkType === 'http' ? 'URL completa del servicio' : 'Endpoint IP / Host') }}</label>
                                <input type="text" wire:model="ip" class="w-full premium-input px-5 py-4 text-sm font-semibold" placeholder="{{ $checkType === 'http' ? 'https://api.empresa.com' : '10.0.0.1' }}">
                                @error('ip') <span class="text-xs font-bold text-red-500 mt-2 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        @if($checkType === 'agent')
                            <div class="pt-2">
                                <label class="flex items-center gap-3 cursor-pointer p-4 rounded-none border border-surface-100 hover:bg-surface-50 transition-colors group">
                                    <input type="checkbox" wire:model.live="autoDeploy" class="w-5 h-5 text-primary-600 rounded-none border-surface-300 focus:ring-primary-500 transition-all">
                                    <span class="text-sm font-bold text-surface-700">Opciones de Despliegue Automatizado</span>
                                </label>
                            </div>
                            
                            @if($autoDeploy)
                                <div class="grid grid-cols-2 gap-4 animate-in slide-in-from-top-4 duration-300">
                                    <div>
                                        <label class="block text-xs font-black text-surface-500 uppercase tracking-widest mb-2">Usuario SSH</label>
                                        <input type="text" wire:model="sshUser" class="w-full premium-input px-4 py-3 text-sm font-semibold" placeholder="root">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-black text-surface-500 uppercase tracking-widest mb-2">Clave SSH</label>
                                        <input type="password" wire:model="sshPassword" class="w-full premium-input px-4 py-3 text-sm font-semibold" placeholder="••••••••">
                                    </div>
                                </div>
                            @endif
                        @elseif($checkType === 'http')
                            <div class="p-5 bg-sky-50 rounded-none border border-sky-100 flex gap-4">
                                <div class="flex-shrink-0 text-sky-500 mt-0.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" /></svg>
                                </div>
                                <p class="text-xs text-sky-800 leading-relaxed font-semibold">Se verificará la disponibilidad mediante una petición HTTP GET periódica. Ideal para APIs y sitios web.</p>
                            </div>
                        @else
                            <div class="p-5 bg-emerald-50 rounded-none border border-emerald-100 flex gap-4">
                                <div class="flex-shrink-0 text-emerald-500 mt-0.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" /></svg>
                                </div>
                                <p class="text-xs text-emerald-800 leading-relaxed font-semibold">Monitorización básica de conectividad mediante protocolo ICMP. Proporciona latencia pura de red.</p>
                            </div>
                        @endif
                        
                        <button type="submit" class="w-full bg-primary-600 hover:bg-primary-500 text-white font-black py-4.5 mt-4 rounded-none transition-all shadow-xl shadow-primary-500/20 active:scale-95 text-sm uppercase tracking-widest">
                            Vincular a la Red de Datos
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="p-5 bg-emerald-50/80 backdrop-blur-sm border border-emerald-100 text-emerald-700 rounded-none text-sm font-bold shadow-sm flex items-center gap-4 animate-in slide-in-from-top-8 duration-500">
            <div class="h-8 w-8 bg-emerald-100 rounded-none flex items-center justify-center text-emerald-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
            </div>
            {{ session('message') }}
        </div>
    @endif

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <div class="premium-card p-10 relative overflow-hidden group">
            <div class="flex items-start justify-between mb-6 relative z-10">
                <div class="h-14 w-14 rounded-none bg-primary-50 text-primary-600 flex items-center justify-center shadow-inner group-hover:scale-110 transition-transform duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" /></svg>
                </div>
            </div>
            <h3 class="text-xs font-black text-surface-400 uppercase tracking-[0.2em] mb-2 relative z-10">Total Infrastructure</h3>
            <div class="flex items-end gap-3 relative z-10">
                <span class="text-5xl font-display font-black text-surface-900 tracking-tighter">{{ count($servers) }}</span>
                <span class="text-sm font-bold text-surface-400 mb-2">nodos</span>
            </div>
        </div>

        <div class="premium-card p-10 relative overflow-hidden group">
            <div class="flex items-start justify-between mb-6 relative z-10">
                <div class="h-14 w-14 rounded-none bg-emerald-50 text-emerald-600 flex items-center justify-center shadow-inner group-hover:scale-110 transition-transform duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
            </div>
            <h3 class="text-xs font-black text-surface-400 uppercase tracking-[0.2em] mb-2 relative z-10">Health Status</h3>
            <div class="flex items-end gap-3 relative z-10">
                <span class="text-5xl font-display font-black text-emerald-600 tracking-tighter">{{ $stats['activeServers'] ?? 0 }}</span>
                <span class="text-sm font-bold text-surface-400 mb-2">online</span>
            </div>
        </div>

        <div class="premium-card p-10 relative overflow-hidden group">
            <div class="flex items-start justify-between mb-6 relative z-10">
                <div class="h-14 w-14 rounded-none bg-rose-50 text-rose-600 flex items-center justify-center shadow-inner group-hover:scale-110 transition-transform duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                </div>
            </div>
            <h3 class="text-xs font-black text-surface-400 uppercase tracking-[0.2em] mb-2 relative z-10">Critical Alerts</h3>
            <div class="flex items-end gap-3 relative z-10">
                <span class="text-5xl font-display font-black {{ ($stats['activeAlerts'] ?? 0) > 0 ? 'text-rose-600' : 'text-surface-900' }} tracking-tighter">{{ $stats['activeAlerts'] ?? 0 }}</span>
                <span class="text-sm font-bold text-surface-400 mb-2">activas</span>
            </div>
        </div>

        <div class="premium-card p-10 relative overflow-hidden group">
            <div class="flex items-start justify-between mb-6 relative z-10">
                <div class="h-14 w-14 rounded-none bg-sky-50 text-sky-600 flex items-center justify-center shadow-inner group-hover:scale-110 transition-transform duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
            </div>
            <h3 class="text-xs font-black text-surface-400 uppercase tracking-[0.2em] mb-2 relative z-10">Network SLA</h3>
            <div class="flex items-end gap-2 relative z-10">
                <span class="text-5xl font-display font-black text-surface-900 tracking-tighter">{{ $stats['averageUptime'] ?? 0 }}</span>
                <span class="text-2xl font-bold text-surface-300 mb-2">%</span>
            </div>
        </div>
    </div>

    <!-- Middle Section: Bento Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Main Table Column (Left) -->
        <div class="lg:col-span-8 premium-card overflow-hidden flex flex-col border-none shadow-2xl shadow-black/5 bg-white">
            <div class="px-10 py-8 border-b border-surface-100 flex justify-between items-center bg-surface-50/40">
                <div>
                    <h3 class="text-lg font-display font-black text-surface-900">Infraestructura Distribuida</h3>
                    <p class="text-xs text-surface-400 font-bold uppercase tracking-widest mt-1">Nodos bajo monitorización activa</p>
                </div>
                <span class="text-xs text-primary-600 font-black bg-primary-100/50 px-4 py-2 rounded-none border border-primary-200 shadow-sm">{{ count($servers) }} activos</span>
            </div>

            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] font-black uppercase tracking-widest text-surface-400 border-b border-surface-100 bg-surface-50/80">
                            <th class="px-10 py-5">Node Identity</th>
                            <th class="px-6 py-5 text-center">Resources</th>
                            <th class="px-6 py-5 text-center">Health</th>
                            <th class="px-10 py-5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100">
                        @forelse($servers as $server)
                            <tr class="hover:bg-surface-50/50 transition-all duration-200 group">
                                <td class="px-10 py-6">
                                    <a href="{{ route('server.detail', $server->id) }}" class="flex items-center gap-6 group/item">
                                        <div class="h-14 w-14 rounded-none {{ $server->check_type === 'agent' ? 'bg-primary-50 text-primary-600' : ($server->check_type === 'http' ? 'bg-sky-50 text-sky-600' : 'bg-emerald-50 text-emerald-600') }} flex items-center justify-center font-black shadow-inner transition-all group-hover/item:rotate-3 group-hover/item:scale-105 border border-transparent group-hover/item:border-current/10">
                                            @if($server->check_type === 'agent')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z" /><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" /></svg>
                                            @elseif($server->check_type === 'http')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM4.332 8.027a6.012 6.012 0 011.912-2.706C6.512 5.73 6.974 6 7.5 6A1.5 1.5 0 019 7.5V9a2 2 0 002 2h1a2 2 0 002-2V7.5a.5.5 0 011 0V9a4 4 0 01-4 4H12v1.5a1.5 1.5 0 01-3 0V13a1 1 0 00-1-1 2 2 0 01-2-2v-1.973z" clip-rule="evenodd" /></svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.4503-.385l-7 3.5a1 1 0 00-.553.894v10a1 1 0 001.447.894l7-3.5a1 1 0 00.553-.894v-10zm-2.395 1.777v8.34l-5 2.5V5.83l5-2.502z" clip-rule="evenodd" /></svg>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-base font-bold text-surface-900 group-hover/item:text-primary-600 transition-colors truncate">{{ $server->name }}</p>
                                            <p class="text-[10px] text-surface-400 font-black uppercase tracking-widest mt-0.5">{{ $server->check_type === 'agent' ? 'Enterprise Agent' : ($server->check_type === 'http' ? 'Web Service' : 'Network Endpoint') }}</p>
                                        </div>
                                    </a>
                                </td>
                                
                                @php 
                                    $lastMetric = $server->metrics()->latest()->first(); 
                                    $isOnline = $lastMetric && $lastMetric->created_at->diffInSeconds(now()) < 50;
                                @endphp

                                <td class="px-6 py-6">
                                    <div class="flex items-center justify-center gap-10">
                                        @if($isOnline)
                                            @if($server->check_type === 'agent')
                                                <div class="flex flex-col items-center gap-1">
                                                    <span class="text-[9px] font-black uppercase text-surface-400 tracking-wider">CPU</span>
                                                    <span class="text-xs font-black {{ $lastMetric->cpu_load > 85 ? 'text-rose-600' : 'text-surface-700' }}">{{ $lastMetric->cpu_load }}%</span>
                                                </div>
                                                <div class="flex flex-col items-center gap-1">
                                                    <span class="text-[9px] font-black uppercase text-surface-400 tracking-wider">RAM</span>
                                                    <span class="text-xs font-black {{ $lastMetric->ram_usage > 90 ? 'text-rose-600' : 'text-surface-700' }}">{{ $lastMetric->ram_usage }}%</span>
                                                </div>
                                            @else
                                                @php 
                                                    $details = json_decode($lastMetric->details ?? '{}', true);
                                                    $realLatency = $details['latency'] ?? $lastMetric->cpu_load;
                                                @endphp
                                                <div class="flex flex-col items-center gap-1">
                                                    <span class="text-[9px] font-black uppercase text-surface-400 tracking-wider">Latency</span>
                                                    <span class="text-sm font-black {{ $realLatency > 200 ? 'text-rose-600' : 'text-emerald-600' }} tracking-tight">{{ $realLatency }}<span class="text-[10px] ml-0.5">ms</span></span>
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-[10px] font-black text-surface-300 uppercase tracking-widest">No Signals</span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-6 py-6 text-center">
                                    @if(!$server->is_enabled)
                                        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-none bg-surface-100 text-surface-500 text-[10px] font-black uppercase tracking-widest">
                                            Paused
                                        </div>
                                    @elseif($isOnline)
                                        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-none bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase tracking-widest border border-emerald-100 shadow-sm">
                                            <div class="h-1.5 w-1.5 bg-emerald-500 rounded-full animate-pulse"></div>
                                            Healthy
                                        </div>
                                    @else
                                        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-none bg-rose-50 text-rose-600 text-[10px] font-black uppercase tracking-widest border border-rose-100 shadow-sm">
                                            <div class="h-1.5 w-1.5 bg-rose-500 rounded-full"></div>
                                            Critical
                                        </div>
                                    @endif
                                </td>

                                <td class="px-10 py-6 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button wire:click="toggleServer({{ $server->id }})" class="h-10 w-10 flex items-center justify-center rounded-none bg-surface-50 text-surface-400 hover:text-primary-600 hover:bg-primary-50 transition-all" title="Toggle Active State">
                                            @if($server->is_enabled)
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            @endif
                                        </button>
                                        
                                        <button onclick="confirm('¿Confirmar desvinculación definitiva del nodo?') || event.stopImmediatePropagation()" wire:click="deleteServer({{ $server->id }})" class="h-10 w-10 flex items-center justify-center rounded-none bg-surface-50 text-surface-400 hover:text-rose-600 hover:bg-rose-50 transition-all">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-10 py-32 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="h-20 w-20 bg-surface-50 text-surface-200 rounded-none flex items-center justify-center mb-6 shadow-inner">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                                        </div>
                                        <p class="font-bold text-base text-surface-400">Canal de infraestructura vacío</p>
                                        <p class="text-sm text-surface-300 mt-2">Inicia el despliegue de un nuevo nodo para ver datos</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Logs & Alerts Column (Right) -->
        <div class="lg:col-span-4 flex flex-col gap-8">
            <div class="premium-card flex-1 flex flex-col p-10 overflow-hidden relative border-none bg-white shadow-2xl shadow-black/5">
                <h3 class="text-lg font-display font-black text-surface-900 mb-8 flex items-center gap-3">
                    <div class="h-3 w-3 rounded-full bg-primary-500 ring-4 ring-primary-500/10"></div>
                    Sucesos de Red
                </h3>
                
                <div class="space-y-5 flex-1 relative z-10">
                    @forelse($recentLogs as $log)
                        @php
                            $isPing = $log->server->check_type === 'ping';
                            $isAlert = !$isPing && ($log->cpu_load > 90 || $log->ram_usage > 95);
                            $isWarn = !$isPing && (($log->cpu_load > 70 && $log->cpu_load <= 90) || ($log->ram_usage > 80 && $log->ram_usage <= 95));
                        @endphp
                        <div class="flex items-start gap-5 p-5 rounded-none bg-surface-50/50 border border-surface-100 transition-all hover:bg-white hover:border-primary-500/20 hover:shadow-lg shadow-black/5 group/log cursor-pointer">
                            <div class="mt-1.5">
                                @if($isAlert)
                                    <div class="h-2.5 w-2.5 rounded-full bg-rose-500 shadow-[0_0_12px_rgba(244,63,94,0.6)] animate-pulse"></div>
                                @elseif($isWarn)
                                    <div class="h-2.5 w-2.5 rounded-full bg-orange-500 shadow-[0_0_12px_rgba(249,115,22,0.4)]"></div>
                                @elseif($isPing)
                                    <div class="h-2.5 w-2.5 rounded-full bg-emerald-500 shadow-[0_0_12px_rgba(16,185,129,0.4)]"></div>
                                @else
                                    <div class="h-2.5 w-2.5 rounded-full bg-primary-500 group-hover/log:scale-150 transition-transform duration-300"></div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-center mb-1.5">
                                    <span class="text-sm font-black text-surface-900 truncate">{{ $log->server->name }}</span>
                                    <span class="text-[10px] text-surface-400 font-black uppercase tracking-widest">{{ $log->created_at->diffForHumans(null, true, true) }}</span>
                                </div>
                                <div class="flex items-center gap-5 text-[10px] font-black tracking-widest text-surface-400 uppercase">
                                    @if($isPing)
                                        @php $realLat = json_decode($log->details ?? '{}', true)['latency'] ?? $log->cpu_load; @endphp
                                        <span class="text-emerald-500 flex items-center gap-1.5">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" /></svg>
                                            ACK OK <span class="text-surface-600 font-black ml-1">{{ $realLat }}ms</span>
                                        </span>
                                    @else
                                        <span class="flex items-center gap-1.5">CPU <span class="{{ $log->cpu_load > 90 ? 'text-rose-600 font-black' : 'text-surface-900' }}">{{ $log->cpu_load }}%</span></span>
                                        <span class="flex items-center gap-1.5">RAM <span class="{{ $log->ram_usage > 95 ? 'text-rose-600 font-black' : 'text-surface-900' }}">{{ $log->ram_usage }}%</span></span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-16">
                            <p class="text-xs font-black text-surface-300 uppercase tracking-[0.2em]">Silence Detected</p>
                        </div>
                    @endforelse
                </div>
                
                <a href="{{ route('logs') }}" class="mt-8 text-center text-[10px] font-black uppercase tracking-[0.2em] text-surface-400 hover:text-primary-600 transition-all w-full border border-surface-100 rounded-none py-4 hover:bg-surface-50 block bg-white relative z-10 group">
                    Explorar Auditoría Completa
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 inline-block ml-2 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Performance Chart -->
    <div class="premium-card p-12 relative overflow-hidden bg-white border-none shadow-2xl shadow-black/5 group">
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6">
            <div>
                <h3 class="text-xl font-display font-black text-surface-900 flex items-center gap-3 mb-2">
                    <div class="h-10 w-10 bg-primary-50 text-primary-600 rounded-none flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" /></svg>
                    </div>
                    Predicción y Telemetría Histórica
                </h3>
                <p class="text-sm text-surface-400 font-bold">Consumo consolidado de recursos - Últimas 60 muestras de red</p>
            </div>
            
            <div class="flex gap-8 bg-surface-50 px-6 py-3 rounded-none border border-surface-100 shadow-inner">
                <div class="flex items-center gap-3">
                    <div class="w-3 h-3 rounded-none bg-primary-500 shadow-[0_0_10px_rgba(99,102,241,0.5)]"></div>
                    <span class="text-[10px] font-black text-surface-600 uppercase tracking-widest">CPU Load</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-3 h-3 rounded-none bg-sky-500 shadow-[0_0_10px_rgba(56,189,248,0.5)]"></div>
                    <span class="text-[10px] font-black text-surface-600 uppercase tracking-widest">RAM Usage</span>
                </div>
            </div>
        </div>

        <div class="w-full h-[400px] relative z-10">
            <canvas id="globalPerformanceChart"></canvas>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const initChart = () => {
                const canvas = document.getElementById('globalPerformanceChart');
                if (!canvas) return;
                
                const ctx = canvas.getContext('2d');
                
                const primaryColor = '{{ $primary["500"] ?? '#6366f1' }}';
                const accentColor = '#38bdf8'; // Sky 400
                
                const gradientPrimary = ctx.createLinearGradient(0, 0, 0, 400);
                gradientPrimary.addColorStop(0, primaryColor + '22');
                gradientPrimary.addColorStop(1, primaryColor + '00');
                
                const gradientAccent = ctx.createLinearGradient(0, 0, 0, 400);
                gradientAccent.addColorStop(0, accentColor + '22');
                gradientAccent.addColorStop(1, accentColor + '00');
        
                const chartData = @json($chartData);
        
                Chart.defaults.color = '#94a3b8';
                Chart.defaults.font.family = "'Inter', sans-serif";
                Chart.defaults.font.weight = '600';
                
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: chartData.labels,
                        datasets: [
                            {
                                label: 'CPU (%)',
                                data: chartData.cpu,
                                borderColor: primaryColor,
                                backgroundColor: gradientPrimary,
                                borderWidth: 4,
                                pointBackgroundColor: '#ffffff',
                                pointBorderColor: primaryColor,
                                pointBorderWidth: 3,
                                pointRadius: 0,
                                pointHoverRadius: 8,
                                pointHoverBorderWidth: 4,
                                fill: true,
                                tension: 0.4
                            },
                            {
                                label: 'RAM (%)',
                                data: chartData.ram,
                                borderColor: accentColor,
                                backgroundColor: gradientAccent,
                                borderWidth: 4,
                                pointBackgroundColor: '#ffffff',
                                pointBorderColor: accentColor,
                                pointBorderWidth: 3,
                                pointRadius: 0,
                                pointHoverRadius: 8,
                                pointHoverBorderWidth: 4,
                                fill: true,
                                tension: 0.4
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(255, 255, 255, 0.98)',
                                titleFont: { family: "'Outfit', sans-serif", size: 14, weight: '900' },
                                bodyFont: { family: "'Inter', sans-serif", size: 13, weight: '600' },
                                padding: 16,
                                borderColor: 'rgba(226, 232, 240, 0.8)',
                                borderWidth: 1,
                                displayColors: true,
                                boxPadding: 8,
                                usePointStyle: true,
                                titleColor: '#0f172a',
                                bodyColor: '#475569'
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { font: { size: 11 }, padding: 15 }
                            },
                            y: {
                                min: 0, max: 100,
                                border: { display: false },
                                grid: { color: 'rgba(241, 245, 249, 1)' },
                                ticks: {
                                    stepSize: 25,
                                    font: { size: 11 },
                                    padding: 20,
                                    callback: function(value) { return value + '%'; }
                                }
                            }
                        }
                    }
                });
            };
        
            initChart();
            document.addEventListener('livewire:navigated', initChart);
        });
    </script>
    @endpush
</div>
