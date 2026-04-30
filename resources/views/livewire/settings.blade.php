<div class="space-y-12">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-4">
        <div>
            <h1 class="text-4xl font-display font-black text-text-primary tracking-tight">Ajustes de Sistema</h1>
            <p class="text-base text-text-secondary font-medium mt-2">Preferencias globales y configuración del núcleo de red (Core v6.1)</p>
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

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
        <!-- Application Settings -->
        <div class="glass-card p-12 space-y-10">
            <div class="flex items-center gap-5 mb-4">
                <div class="h-14 w-14 bg-bg-tertiary text-text-primary rounded-xl flex items-center justify-center border border-border-subtle">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 5a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2h-2.22l.123.489.804.804A1 1 0 0113 18H7a1 1 0 01-.707-1.707l.804-.804L7.22 15H5a2 2 0 01-2-2V5zm2 1a1 1 0 00-1 1v5a1 1 0 001 1h10a1 1 0 001-1V7a1 1 0 00-1-1H5z" clip-rule="evenodd" /></svg>
                </div>
                <div>
                    <h3 class="text-xl font-display font-black text-text-primary tracking-tight">General & Branding</h3>
                    <p class="text-xs text-text-tertiary font-bold uppercase tracking-widest mt-1">Identidad visual de la instancia</p>
                </div>
            </div>
            
            <div class="space-y-8">
                <div>
                    <label class="block text-xs font-black text-text-tertiary uppercase tracking-[0.2em] mb-3">Nombre de la Plataforma</label>
                    <input type="text" wire:model="appName" class="w-full bg-bg-tertiary border border-border-subtle focus:border-border-strong text-text-primary rounded-xl px-6 py-4 text-sm font-bold outline-none transition-colors">
                </div>
                
                <div>
                    <label class="block text-xs font-black text-text-tertiary uppercase tracking-[0.2em] mb-4">Paleta de Identidad (Acento)</label>
                    <div class="flex flex-wrap gap-5">
                        @foreach(['emerald' => 'bg-emerald-500', 'blue' => 'bg-sky-500', 'purple' => 'bg-indigo-500', 'orange' => 'bg-rose-500'] as $key => $color)
                            <button wire:click="setTheme('{{ $key }}')" 
                                    class="h-14 w-14 rounded-xl {{ $color }} shadow-xl shadow-current/20 {{ $themeColor === $key ? 'ring-4 ring-accent-primary scale-110' : 'hover:scale-110 opacity-80 hover:opacity-100' }} transition-all duration-300 relative">
                                @if($themeColor === $key)
                                    <div class="absolute inset-0 flex items-center justify-center text-white">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                    </div>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-black text-text-tertiary uppercase tracking-[0.2em] mb-3">Frecuencia de Muestreo</label>
                    <div class="relative group">
                        <select class="w-full bg-bg-tertiary border border-border-subtle focus:border-border-strong text-text-primary rounded-xl px-6 py-4 text-sm font-bold appearance-none cursor-pointer pr-12 transition-colors outline-none">
                            <option class="bg-bg-secondary">Stream: 2 segundos (Real-time)</option>
                            <option class="bg-bg-secondary">High: 5 segundos</option>
                            <option class="bg-bg-secondary">Standard: 10 segundos</option>
                            <option class="bg-bg-secondary">Economy: 30 segundos</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-5 text-text-tertiary">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="glass-card p-12 space-y-10">
            <div class="flex items-center gap-5 mb-4">
                <div class="h-14 w-14 bg-bg-tertiary text-text-primary rounded-xl flex items-center justify-center border border-border-subtle">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" viewBox="0 0 20 20" fill="currentColor"><path d="M3 12v3c0 1.657 3.134 3 7 3s7-1.343 7-3v-3c0 1.657-3.134 3-7 3s-7-1.343-7-3z" /><path d="M3 7v3c0 1.657 3.134 3 7 3s7-1.343 7-3V7c0 1.657-3.134 3-7 3s-7-1.343-7-3z" /><path d="M17 5c0 1.657-3.134 3-7 3S3 6.657 3 5s3.134-3 7-3 7 1.343 7 3z" /></svg>
                </div>
                <div>
                    <h3 class="text-xl font-display font-black text-text-primary tracking-tight">Motor de Datos</h3>
                    <p class="text-xs text-text-tertiary font-bold uppercase tracking-widest mt-1">Persistencia y registros SQL</p>
                </div>
            </div>
            
            <div class="space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-black text-text-tertiary uppercase tracking-[0.2em] mb-3">Host Endpoint</label>
                        <input type="text" wire:model="dbHost" class="w-full bg-bg-tertiary border border-border-subtle focus:border-border-strong text-text-primary rounded-xl px-6 py-4 text-sm font-bold font-mono outline-none transition-colors" placeholder="127.0.0.1">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-text-tertiary uppercase tracking-[0.2em] mb-3">Database User</label>
                        <input type="text" wire:model="dbUser" class="w-full bg-bg-tertiary border border-border-subtle focus:border-border-strong text-text-primary rounded-xl px-6 py-4 text-sm font-bold outline-none transition-colors" placeholder="root">
                    </div>
                </div>
                
                <div>
                    <label class="block text-xs font-black text-text-tertiary uppercase tracking-[0.2em] mb-3">Security Access Key (Pass)</label>
                    <input type="password" wire:model="dbPass" class="w-full bg-bg-tertiary border border-border-subtle focus:border-border-strong text-text-primary rounded-xl px-6 py-4 text-sm font-bold outline-none transition-colors" placeholder="••••••••">
                </div>

                <div class="p-6 bg-bg-tertiary rounded-xl border border-border-subtle flex items-start gap-5">
                    <div class="p-2.5 bg-bg-secondary rounded-lg text-text-primary border border-border-subtle shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" /></svg>
                    </div>
                    <p class="text-[11px] text-text-tertiary leading-loose font-bold uppercase tracking-tight">La infraestructura de datos garantiza la integridad de los registros históricos del TFG. Se recomienda el uso de volúmenes persistentes SQL.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-end pt-8">
        <button wire:click="saveSettings" 
                class="btn-primary px-10 py-5 text-xs uppercase tracking-[0.2em] flex items-center gap-4 group">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:translate-y-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
            Commit Changes
        </button>
    </div>
</div>
