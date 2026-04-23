<div class="space-y-12" wire:poll.2s>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-4">
        <div>
            <h1 class="text-4xl font-display font-black text-surface-900 tracking-tight">Inventario de Nodos</h1>
            <p class="text-base text-surface-500 font-medium mt-2">Gestión centralizada de infraestructura y puntos de monitorización</p>
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

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        <!-- Formulario Agregar Nodo -->
        <div class="lg:col-span-4 lg:sticky lg:top-32 h-fit">
            <div class="premium-card bg-white p-10 border-none shadow-2xl shadow-black/5">
                <div class="h-16 w-16 bg-primary-50 text-primary-600 rounded-none flex items-center justify-center mb-10 shadow-inner">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                </div>
                <h3 class="text-2xl font-display font-black text-surface-900 mb-8">Vincular Nodo</h3>
                <form wire:submit.prevent="saveServer" class="space-y-6">
                    <div class="flex gap-2 p-1.5 bg-surface-100/50 rounded-none mb-6 shadow-inner">
                        <button type="button" wire:click="$set('checkType', 'agent')" class="flex-1 py-3 rounded-none text-[10px] font-black uppercase tracking-widest transition-all {{ $checkType === 'agent' ? 'bg-white text-primary-600 shadow-xl border border-surface-200' : 'text-surface-400 hover:text-surface-700' }}">
                            Agent
                        </button>
                        <button type="button" wire:click="$set('checkType', 'ping')" class="flex-1 py-3 rounded-none text-[10px] font-black uppercase tracking-widest transition-all {{ $checkType === 'ping' ? 'bg-white text-emerald-600 shadow-xl border border-surface-200' : 'text-surface-400 hover:text-surface-700' }}">
                            Ping
                        </button>
                    </div>

                    <div class="space-y-5">
                        <div>
                            <label class="block text-xs font-black text-surface-400 uppercase tracking-[0.2em] mb-3">Identificador</label>
                            <input type="text" wire:model="name" placeholder="{{ $checkType === 'agent' ? 'Cluster-Node-01' : 'External Endpoint' }}" class="w-full premium-input px-5 py-4 text-sm font-bold">
                            @error('name') <span class="text-xs font-bold text-red-500 mt-2 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-black text-surface-400 uppercase tracking-[0.2em] mb-3">{{ $checkType === 'agent' ? 'Endpoint IPv4 / FQDN' : 'Target Host' }}</label>
                            <input type="text" wire:model="ip_address" placeholder="10.0.0.50" class="w-full premium-input px-5 py-4 text-sm font-bold font-mono">
                            @error('ip_address') <span class="text-xs font-bold text-red-500 mt-2 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    
                    @if($checkType === 'agent')
                        <div class="pt-2">
                            <label class="flex items-center gap-3 cursor-pointer p-4 rounded-none border border-surface-100 hover:bg-surface-50 transition-colors group">
                                <input type="checkbox" wire:model.live="autoDeploy" class="w-5 h-5 text-primary-600 rounded-none border-surface-300 focus:ring-primary-500 transition-all">
                                <span class="text-xs font-black text-surface-700 uppercase tracking-widest">Auto Deployment</span>
                            </label>
                        </div>
                        
                        @if($autoDeploy)
                            <div class="grid grid-cols-2 gap-4 animate-in slide-in-from-top-4 duration-300">
                                <div>
                                    <label class="block text-[9px] font-black text-surface-400 uppercase tracking-widest mb-2">User</label>
                                    <input type="text" wire:model="sshUser" class="w-full premium-input px-4 py-3 text-[11px] font-bold" placeholder="root">
                                </div>
                                <div>
                                    <label class="block text-[9px] font-black text-surface-400 uppercase tracking-widest mb-2">Access</label>
                                    <input type="password" wire:model="sshPassword" class="w-full premium-input px-4 py-3 text-[11px] font-bold" placeholder="••••">
                                </div>
                            </div>
                        @endif
                    @endif
                    
                    <button type="submit" class="w-full bg-primary-600 hover:bg-primary-500 text-white font-black py-4.5 mt-4 rounded-none transition-all shadow-xl shadow-primary-500/20 active:scale-95 text-xs uppercase tracking-[0.2em] flex items-center justify-center gap-3">
                        <span wire:loading.remove>Commit Node</span>
                        <div wire:loading class="h-4 w-4 border-2 border-white/30 border-t-white rounded-none animate-spin"></div>
                        <span wire:loading>Processing...</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Directorio de Nodos -->
        <div class="lg:col-span-8 premium-card flex flex-col overflow-hidden bg-white border-none shadow-2xl shadow-black/5">
            <div class="px-10 py-8 border-b border-surface-100 flex justify-between items-center bg-surface-50/40">
                <div>
                    <h3 class="text-lg font-display font-black text-surface-900">Directorio de Servidores</h3>
                    <p class="text-xs text-surface-400 font-bold uppercase tracking-widest mt-1">Nodos activos vinculados a este nodo central</p>
                </div>
                <span class="text-xs text-surface-500 font-black bg-white px-4 py-2 rounded-none border border-surface-200 shadow-sm">{{ count($servers) }} registros</span>
            </div>
            
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] font-black uppercase tracking-[0.2em] text-surface-400 border-b border-surface-100 bg-surface-50/80">
                            <th class="px-10 py-6">Node Instance</th>
                            <th class="px-6 py-6">Network Access</th>
                            <th class="px-6 py-6">Security Token</th>
                            <th class="px-6 py-6 text-center">Protocol Health</th>
                            <th class="px-10 py-6 text-right">Operations</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100">
                        @forelse($servers as $server)
                            @php 
                                $lastMetric = $server->metrics()->latest()->first(); 
                                $isOnline = $lastMetric && $lastMetric->created_at->diffInSeconds(now()) < 50;
                            @endphp
                            <tr class="hover:bg-surface-50/50 transition-all duration-300 group">
                                <td class="px-10 py-6">
                                    <div class="flex items-center gap-5">
                                        <div class="h-12 w-12 rounded-none {{ $server->check_type === 'agent' ? 'bg-primary-50 text-primary-600' : 'bg-emerald-50 text-emerald-600' }} flex items-center justify-center font-black shadow-inner border border-current/5 group-hover:scale-110 transition-transform">
                                            @if($server->check_type === 'agent')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z" /><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" /></svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.4503-.385l-7 3.5a1 1 0 00-.553.894v10a1 1 0 001.447.894l7-3.5a1 1 0 00.553-.894v-10zm-2.395 1.777v8.34l-5 2.5V5.83l5-2.502z" clip-rule="evenodd" /></svg>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-surface-900 leading-tight group-hover:text-primary-600 transition-colors">{{ $server->name }}</p>
                                            <p class="text-[9px] font-black text-surface-400 uppercase tracking-widest mt-1">{{ $server->check_type === 'agent' ? 'Enterprise Agent' : 'Ping Protocol' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-6">
                                    <span class="text-xs font-bold text-surface-500 font-mono tracking-tighter">{{ $server->ip_address }}</span>
                                </td>
                                <td class="px-6 py-6">
                                    <button class="text-[10px] font-black text-primary-600 bg-primary-100/50 px-4 py-2 rounded-none border border-primary-200 hover:bg-primary-600 hover:text-white transition-all shadow-sm" title="Copy Security Token" onclick="navigator.clipboard.writeText('{{ $server->api_token }}'); alert('Security Token Copied')">
                                        {{ substr($server->api_token, 0, 8) }}...
                                    </button>
                                </td>
                                <td class="px-6 py-6 text-center">
                                    @if(!$server->is_enabled)
                                        <div class="inline-flex items-center px-4 py-1.5 rounded-none bg-surface-100 text-surface-500 text-[9px] font-black uppercase tracking-widest">Suspended</div>
                                    @elseif($isOnline)
                                        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-none bg-emerald-50 text-emerald-600 text-[9px] font-black uppercase tracking-widest border border-emerald-100 shadow-sm">
                                            <div class="h-1.5 w-1.5 bg-emerald-500 rounded-full animate-pulse"></div> Healthy
                                        </div>
                                    @else
                                        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-none bg-rose-50 text-rose-600 text-[9px] font-black uppercase tracking-widest border border-rose-100 shadow-sm">
                                            <div class="h-1.5 w-1.5 bg-rose-500 rounded-full"></div> Critical
                                        </div>
                                    @endif
                                </td>
                                <td class="px-10 py-6 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button wire:click="toggleServer({{ $server->id }})" class="h-10 w-10 flex items-center justify-center rounded-none bg-surface-50 text-surface-400 hover:text-primary-600 hover:bg-primary-50 transition-all">
                                            @if($server->is_enabled)
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            @endif
                                        </button>
                                        @if(!$isOnline && $server->is_enabled)
                                            <button wire:click="reconnectServer({{ $server->id }})" class="h-10 w-10 flex items-center justify-center rounded-none bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white transition-all shadow-sm">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                                            </button>
                                        @endif
                                        <button onclick="confirm('¿Confirmar desvinculación definitiva del nodo?') || event.stopImmediatePropagation()" wire:click="deleteServer({{ $server->id }})" class="h-10 w-10 flex items-center justify-center rounded-none bg-surface-50 text-surface-400 hover:text-rose-600 hover:bg-rose-50 transition-all">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-10 py-32 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="h-24 w-24 bg-surface-50 text-surface-200 rounded-none flex items-center justify-center mb-8 shadow-inner">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" /></svg>
                                        </div>
                                        <p class="font-black text-base text-surface-400">Canal de Inventario Vacío</p>
                                        <p class="text-[10px] text-surface-300 font-black uppercase tracking-[0.2em] mt-2">Inicia el despliegue de un nuevo nodo para operar</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
