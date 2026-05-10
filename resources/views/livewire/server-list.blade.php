<div class="space-y-10" wire:poll.10s>
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-4">
        <div>
            <h1 class="text-4xl font-display font-black tracking-tighter transition-colors">Node Inventory</h1>
            <p class="text-[10px] text-[var(--text-muted)] font-black uppercase tracking-[0.3em] mt-2">Centralized Infrastructure Asset Management</p>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="p-4 bg-success/10 border border-success/20 text-success rounded-xl text-[10px] font-black uppercase tracking-widest flex items-center gap-4 animate-in fade-in slide-in-from-top-4 duration-500">
            <x-lucide-check-circle class="h-4 w-4" />
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Provisioning Sidebar -->
        <div class="lg:col-span-4 lg:sticky lg:top-24 h-fit">
            <div class="bg-[var(--bg-card)] border border-[var(--border-color)] p-8 rounded-3xl shadow-xl space-y-8 transition-colors duration-500">
                <div class="h-14 w-14 bg-[var(--accent-primary)]/10 border border-[var(--accent-primary)]/20 text-[var(--accent-primary)] rounded-2xl flex items-center justify-center">
                    <x-lucide-plus-circle class="h-7 w-7" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-[var(--text-main)] tracking-tight">Vincular Nodo</h3>
                    <p class="text-[9px] text-[var(--text-muted)] font-black uppercase tracking-widest mt-1">Register new cluster resource</p>
                </div>

                <form wire:submit.prevent="saveServer" class="space-y-6">
                    <div class="flex p-1 bg-[var(--bg-main)] rounded-2xl border border-[var(--border-color)]">
                        <button type="button" wire:click="$set('checkType', 'agent')" class="flex-1 h-11 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all {{ $checkType === 'agent' ? 'bg-[var(--bg-card)] text-[var(--text-main)] shadow-md border border-[var(--border-color)]' : 'text-[var(--text-muted)] hover:text-[var(--text-main)]' }}">
                            Agent
                        </button>
                        <button type="button" wire:click="$set('checkType', 'ping')" class="flex-1 h-11 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all {{ $checkType === 'ping' ? 'bg-[var(--bg-card)] text-[var(--text-main)] shadow-md border border-[var(--border-color)]' : 'text-[var(--text-muted)] hover:text-[var(--text-main)]' }}">
                            Ping
                        </button>
                    </div>

                    <div class="space-y-5">
                        <div class="space-y-2">
                            <label class="block text-[9px] font-black text-[var(--text-muted)] uppercase tracking-[0.2em] px-1">Identificador</label>
                            <input type="text" wire:model="name" placeholder="ZimaBlade-02" class="w-full bg-[var(--bg-main)] border border-[var(--border-color)] focus:border-[var(--accent-primary)] text-[var(--text-main)] rounded-xl px-5 py-4 text-xs font-bold outline-none transition-all">
                            @error('name') <span class="text-[9px] font-bold text-danger mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[9px] font-black text-[var(--text-muted)] uppercase tracking-[0.2em] px-1">Network Target</label>
                            <input type="text" wire:model="ip_address" placeholder="192.168.1.100" class="w-full bg-[var(--bg-main)] border border-[var(--border-color)] focus:border-[var(--accent-primary)] text-[var(--text-main)] rounded-xl px-5 py-4 text-xs font-bold font-mono outline-none transition-all">
                            @error('ip_address') <span class="text-[9px] font-bold text-danger mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    
                    @if($checkType === 'agent')
                        <div class="p-4 bg-[var(--bg-main)] border border-[var(--border-color)] rounded-xl flex items-center gap-4 group cursor-pointer hover:bg-[var(--accent-primary)]/5 transition-all">
                            <x-mary-checkbox wire:model.live="autoDeploy" class="checkbox-primary !border-[var(--border-color)]" />
                            <span class="text-[9px] font-black text-[var(--text-main)] uppercase tracking-widest">Auto Deployment</span>
                        </div>
                        
                        @if($autoDeploy)
                            <div class="grid grid-cols-2 gap-4 animate-in slide-in-from-top-4 duration-300">
                                <div class="space-y-2">
                                    <label class="block text-[8px] font-black text-[var(--text-muted)] uppercase px-1">User</label>
                                    <input type="text" wire:model="sshUser" class="w-full bg-[var(--bg-main)] border border-[var(--border-color)] focus:border-[var(--accent-primary)] text-[var(--text-main)] rounded-xl px-4 py-3 text-[10px] font-bold" placeholder="root">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[8px] font-black text-[var(--text-muted)] uppercase px-1">Pass</label>
                                    <input type="password" wire:model="sshPassword" class="w-full bg-[var(--bg-main)] border border-[var(--border-color)] focus:border-[var(--accent-primary)] text-[var(--text-main)] rounded-xl px-4 py-3 text-[10px] font-bold" placeholder="••••">
                                </div>
                            </div>
                        @endif
                    @endif
                    
                    <button type="submit" class="w-full bg-[var(--accent-primary)] text-white h-14 rounded-2xl flex items-center justify-center gap-3 text-[10px] font-black uppercase tracking-[0.2em] shadow-xl shadow-[var(--accent-primary)]/20 hover:scale-[1.02] active:scale-[0.98] transition-all border-none">
                        <span wire:loading.remove>Commit Node</span>
                        <div wire:loading class="h-4 w-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                        <span wire:loading>Processing...</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Directory Matrix -->
        <div class="lg:col-span-8 bg-[var(--bg-card)] border border-[var(--border-color)] rounded-3xl shadow-xl flex flex-col overflow-hidden transition-colors duration-500">
            <div class="px-8 py-6 border-b border-[var(--border-color)] flex justify-between items-center bg-[var(--bg-sidebar)]/30">
                <div>
                    <h3 class="text-xs font-bold text-[var(--text-main)] uppercase tracking-widest">Asset Directory</h3>
                    <p class="text-[9px] text-[var(--text-muted)] font-black uppercase tracking-widest mt-1">Cluster node instances</p>
                </div>
                <span class="text-[9px] text-[var(--text-muted)] font-black bg-[var(--bg-main)] px-4 py-2 rounded-xl border border-[var(--border-color)] shadow-sm">{{ count($servers) }} registros</span>
            </div>
            
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-[var(--bg-sidebar)]/50">
                        <tr>
                            <th class="px-8 py-5 text-[9px] font-bold text-[var(--text-muted)] uppercase tracking-widest">Instance Name</th>
                            <th class="px-6 py-5 text-[9px] font-bold text-[var(--text-muted)] uppercase tracking-widest text-center">Network Info</th>
                            <th class="px-6 py-5 text-[9px] font-bold text-[var(--text-muted)] uppercase tracking-widest text-center">Security Status</th>
                            <th class="px-8 py-5 text-[9px] font-bold text-[var(--text-muted)] uppercase tracking-widest text-right">Operations</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--border-color)]">
                        @forelse($servers as $server)
                            @php 
                                $lastMetric = $server->metrics()->latest()->first(); 
                                $isOnline = $lastMetric && $lastMetric->created_at->diffInSeconds(now()) < 50;
                            @endphp
                            <tr class="hover:bg-[var(--accent-primary)]/[0.02] transition-colors group/row">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="h-10 w-10 bg-[var(--bg-main)] border border-[var(--border-color)] rounded-xl flex items-center justify-center transition-all group-hover/row:border-[var(--accent-primary)]/40 group-hover/row:shadow-sm">
                                            <x-mary-icon name="{{ $server->check_type === 'agent' ? 'o-server' : 'o-bolt' }}" class="h-5 w-5 text-[var(--text-muted)] group-hover/row:text-[var(--accent-primary)] transition-colors" />
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-[var(--text-main)] group-hover/row:text-[var(--accent-primary)] transition-colors">{{ $server->name }}</p>
                                            <p class="text-[8px] font-black text-[var(--text-muted)] uppercase tracking-widest mt-1">{{ $server->check_type === 'agent' ? 'Agent Node' : 'Latency Tracker' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-6 text-center">
                                    <span class="text-[10px] font-mono font-bold text-[var(--text-muted)]">{{ $server->ip_address }}</span>
                                </td>
                                <td class="px-6 py-6 text-center">
                                    @if(!$server->is_enabled)
                                        <span class="text-[8px] font-black px-3 py-1 rounded-md border border-[var(--border-color)] text-[var(--text-muted)] bg-[var(--bg-main)] uppercase tracking-widest">Suspended</span>
                                    @elseif($isOnline)
                                        <span class="text-[8px] font-black px-3 py-1 rounded-md border border-success/30 text-success bg-success/5 uppercase tracking-widest">Healthy</span>
                                    @else
                                        <span class="text-[8px] font-black px-3 py-1 rounded-md border border-danger/30 text-danger bg-danger/5 uppercase tracking-widest">Offline</span>
                                    @endif
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex items-center justify-end gap-1 opacity-40 group-hover/row:opacity-100 transition-opacity">
                                        <button wire:click="toggleServer({{ $server->id }})" class="p-2 text-[var(--text-muted)] hover:text-[var(--text-main)] transition-all">
                                            <x-mary-icon name="{{ $server->is_enabled ? 'o-pause' : 'o-play' }}" class="h-4 w-4" />
                                        </button>
                                        <button wire:click="deleteServer({{ $server->id }})" class="p-2 text-[var(--text-muted)] hover:text-danger transition-all">
                                            <x-mary-icon name="o-trash" class="h-4 w-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-8 py-32 text-center opacity-30">
                                    <x-lucide-hard-drive class="h-12 w-12 mx-auto mb-4" />
                                    <p class="text-[10px] font-black uppercase tracking-widest">No active node deployments found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
