<div class="space-y-12" wire:poll.2s>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-4">
        <div>
            <h1 class="text-4xl font-display font-black text-text-primary tracking-tight">Inventario de Nodos</h1>
            <p class="text-base text-text-secondary font-medium mt-2">Gestión centralizada de infraestructura y puntos de monitorización</p>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="p-5 bg-success-glow/10 backdrop-blur-md border border-success/30 text-success rounded-xl text-sm font-bold flex items-center gap-4 animate-in slide-in-from-top-8 duration-500">
            <div class="h-8 w-8 bg-success/20 rounded-lg flex items-center justify-center text-success">
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
            <div class="glass-card p-10">
                <div class="h-16 w-16 bg-bg-tertiary border border-border-subtle text-text-primary rounded-xl flex items-center justify-center mb-10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                </div>
                <h3 class="text-2xl font-display font-black text-text-primary mb-8">Vincular Nodo</h3>
                <form wire:submit.prevent="saveServer" class="space-y-6">
                    <div class="flex gap-2 p-1.5 bg-bg-tertiary rounded-xl mb-6">
                        <button type="button" wire:click="$set('checkType', 'agent')" class="flex-1 py-3 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all {{ $checkType === 'agent' ? 'bg-bg-elevated text-text-primary border border-border-strong shadow-sm' : 'text-text-tertiary hover:text-text-primary' }}">
                            Agent
                        </button>
                        <button type="button" wire:click="$set('checkType', 'ping')" class="flex-1 py-3 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all {{ $checkType === 'ping' ? 'bg-bg-elevated text-text-primary border border-border-strong shadow-sm' : 'text-text-tertiary hover:text-text-primary' }}">
                            Ping
                        </button>
                    </div>

                    <div class="space-y-5">
                        <div>
                            <label class="block text-xs font-black text-text-tertiary uppercase tracking-[0.2em] mb-3">Identificador</label>
                            <input type="text" wire:model="name" placeholder="{{ $checkType === 'agent' ? 'Cluster-Node-01' : 'External Endpoint' }}" class="w-full bg-bg-tertiary border border-border-subtle focus:border-border-strong text-text-primary rounded-xl px-5 py-4 text-sm font-bold outline-none transition-colors">
                            @error('name') <span class="text-xs font-bold text-danger mt-2 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-black text-text-tertiary uppercase tracking-[0.2em] mb-3">{{ $checkType === 'agent' ? 'Endpoint IPv4 / FQDN' : 'Target Host' }}</label>
                            <input type="text" wire:model="ip_address" placeholder="10.0.0.50" class="w-full bg-bg-tertiary border border-border-subtle focus:border-border-strong text-text-primary rounded-xl px-5 py-4 text-sm font-bold font-mono outline-none transition-colors">
                            @error('ip_address') <span class="text-xs font-bold text-red-500 mt-2 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    
                    @if($checkType === 'agent')
                        <div class="pt-2">
                            <label class="flex items-center gap-3 cursor-pointer p-4 rounded-xl border border-border-subtle hover:bg-bg-tertiary transition-colors group">
                                <input type="checkbox" wire:model.live="autoDeploy" class="w-5 h-5 rounded border-border-strong bg-bg-tertiary text-accent-primary focus:ring-accent-primary transition-all">
                                <span class="text-xs font-black text-text-primary uppercase tracking-widest">Auto Deployment</span>
                            </label>
                        </div>
                        
                        @if($autoDeploy)
                            <div class="grid grid-cols-2 gap-4 animate-in slide-in-from-top-4 duration-300">
                                <div>
                                    <label class="block text-[9px] font-black text-text-tertiary uppercase tracking-widest mb-2">User</label>
                                    <input type="text" wire:model="sshUser" class="w-full bg-bg-tertiary border border-border-subtle focus:border-border-strong text-text-primary rounded-xl px-4 py-3 text-[11px] font-bold outline-none transition-colors" placeholder="root">
                                </div>
                                <div>
                                    <label class="block text-[9px] font-black text-text-tertiary uppercase tracking-widest mb-2">Access</label>
                                    <input type="password" wire:model="sshPassword" class="w-full bg-bg-tertiary border border-border-subtle focus:border-border-strong text-text-primary rounded-xl px-4 py-3 text-[11px] font-bold outline-none transition-colors" placeholder="••••">
                                </div>
                            </div>
                        @endif
                    @endif
                    
                    <button type="submit" class="w-full btn-primary py-4 mt-4 flex items-center justify-center gap-3 text-xs uppercase tracking-[0.2em]">
                        <span wire:loading.remove>Commit Node</span>
                        <div wire:loading class="h-4 w-4 border-2 border-white/30 border-t-white rounded-none animate-spin"></div>
                        <span wire:loading>Processing...</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Directorio de Nodos -->
        <div class="lg:col-span-8 glass-card flex flex-col overflow-hidden">
            <div class="px-10 py-8 border-b border-border-subtle flex justify-between items-center bg-bg-tertiary/50">
                <div>
                    <h3 class="text-lg font-display font-black text-text-primary">Directorio de Servidores</h3>
                    <p class="text-xs text-text-tertiary font-bold uppercase tracking-widest mt-1">Nodos activos vinculados a este nodo central</p>
                </div>
                <span class="text-[10px] text-text-secondary font-black bg-bg-tertiary px-4 py-2 rounded-xl border border-border-subtle">{{ count($servers) }} registros</span>
            </div>
            
            <div class="overflow-x-auto flex-1 p-0">
                <table class="dark-table w-full">
                    <thead>
                        <tr>
                            <th class="px-10 py-6">Node Instance</th>
                            <th class="px-6 py-6">Network Access</th>
                            <th class="px-6 py-6">Security Token</th>
                            <th class="px-6 py-6 text-center">Protocol Health</th>
                            <th class="px-10 py-6 text-right">Operations</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($servers as $server)
                            @php 
                                $lastMetric = $server->metrics()->latest()->first(); 
                                $isOnline = $lastMetric && $lastMetric->created_at->diffInSeconds(now()) < 50;
                            @endphp
                            <tr>
                                <td>
                                    <div class="flex items-center gap-4 group/node">
                                        <div class="h-11 w-11 bg-bg-tertiary border border-border-subtle rounded-xl flex items-center justify-center transition-all group-hover/node:border-accent-primary/50 group-hover/node:bg-accent-primary/10">
                                            @if($server->check_type === 'agent')
                                                <svg class="h-5 w-5 text-text-tertiary group-hover/node:text-accent-secondary transition-colors" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z" /><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" /></svg>
                                            @else
                                                <svg class="h-5 w-5 text-text-tertiary group-hover/node:text-accent-secondary transition-colors" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.4503-.385l-7 3.5a1 1 0 00-.553.894v10a1 1 0 001.447.894l7-3.5a1 1 0 00.553-.894v-10zm-2.395 1.777v8.34l-5 2.5V5.83l5-2.502z" clip-rule="evenodd" /></svg>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-text-primary leading-tight group-hover/node:text-accent-secondary transition-colors">{{ $server->name }}</p>
                                            <p class="text-[9px] font-black text-text-tertiary uppercase tracking-widest mt-1">{{ $server->check_type === 'agent' ? 'Enterprise Agent' : 'Ping Protocol' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-xs font-bold text-text-secondary font-mono tracking-tighter">{{ $server->ip_address }}</span>
                                </td>
                                <td>
                                    <button class="text-[10px] font-black text-text-primary bg-bg-tertiary px-4 py-2 rounded-lg border border-border-subtle hover:bg-bg-elevated hover:border-border-strong transition-all shadow-sm" title="Copy Security Token" onclick="navigator.clipboard.writeText('{{ $server->api_token }}'); alert('Security Token Copied')">
                                        {{ substr($server->api_token, 0, 8) }}...
                                    </button>
                                </td>
                                <td class="text-center">
                                    @if(!$server->is_enabled)
                                        <span class="status-pill offline">Suspended</span>
                                    @elseif($isOnline)
                                        <span class="status-pill online">Healthy</span>
                                    @else
                                        <span class="status-pill offline">Critical</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <button wire:click="toggleServer({{ $server->id }})" class="p-2 text-text-tertiary hover:text-text-primary hover:bg-bg-tertiary rounded-lg transition-all" title="Toggle Monitor">
                                            @if($server->is_enabled)
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            @else
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            @endif
                                        </button>
                                        @if(!$isOnline && $server->is_enabled)
                                            <button wire:click="reconnectServer({{ $server->id }})" class="p-2 text-success hover:text-success hover:bg-success/10 rounded-lg transition-all" title="Reconnect">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                                            </button>
                                        @endif
                                        <button onclick="confirm('¿Confirmar desvinculación definitiva del nodo?') || event.stopImmediatePropagation()" wire:click="deleteServer({{ $server->id }})" class="p-2 text-text-tertiary hover:text-danger hover:bg-danger/10 rounded-lg transition-all" title="Delete">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-10 py-32 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="h-24 w-24 bg-bg-tertiary text-text-tertiary rounded-2xl flex items-center justify-center mb-8 border border-border-subtle">
                                            <svg class="h-10 w-10 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" /></svg>
                                        </div>
                                        <p class="font-black text-base text-text-secondary">Canal de Inventario Vacío</p>
                                        <p class="text-[10px] text-text-tertiary font-black uppercase tracking-[0.2em] mt-2">Inicia el despliegue de un nuevo nodo para operar</p>
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
