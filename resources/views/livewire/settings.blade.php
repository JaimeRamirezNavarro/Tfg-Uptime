<div class="max-w-4xl mx-auto space-y-12" 
     x-data="{ 
        tempMode: @entangle('tempIsDark'),
        tempColor: @entangle('tempThemeColor'),
        colors: {
            emerald: '#10b981',
            blue: '#0ea5e9',
            purple: '#6366f1',
            orange: '#f43f5e'
        },
        updatePreview() {
            const root = document.documentElement;
            const isLight = !this.tempMode;
            root.style.setProperty('--bg-main', isLight ? '#f8f9fa' : '#09090b');
            root.style.setProperty('--bg-sidebar', isLight ? '#ffffff' : '#121215');
            root.style.setProperty('--bg-card', isLight ? '#ffffff' : '#18181b');
            root.style.setProperty('--text-main', isLight ? '#1a1a1a' : '#fafafa');
            root.style.setProperty('--text-muted', isLight ? '#71717a' : '#a1a1aa');
            root.style.setProperty('--border-color', isLight ? 'rgba(0,0,0,0.06)' : 'rgba(255,255,255,0.05)');
            root.style.setProperty('--accent-primary', this.colors[this.tempColor]);
        }
     }"
     x-init="$watch('tempMode', () => updatePreview()); $watch('tempColor', () => updatePreview())">
     
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-4">
        <div>
            <h1 class="text-4xl font-display font-black tracking-tighter transition-colors">System Preferences</h1>
            <p class="text-[9px] text-[var(--text-muted)] font-black uppercase tracking-[0.3em] mt-2">Core Environment Configuration</p>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="p-4 bg-success/10 border border-success/20 text-success rounded-lg text-[10px] font-black uppercase tracking-widest flex items-center gap-4 animate-in fade-in slide-in-from-top-4 duration-500">
            <x-lucide-check-circle class="h-4 w-4" />
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-10">
        <!-- Application Settings -->
        <div class="bg-[var(--bg-card)] border border-[var(--border-color)] p-12 rounded-3xl shadow-2xl space-y-12 transition-all duration-500">
            <div class="flex items-center gap-5">
                <div class="h-12 w-12 bg-[var(--accent-primary)] rounded-xl flex items-center justify-center shadow-lg shadow-[var(--accent-primary)]/20 transition-all">
                    <x-lucide-layout class="h-6 w-6 text-white" />
                </div>
                <div>
                    <h3 class="text-lg font-bold tracking-tight">Identity & Interface</h3>
                    <p class="text-[9px] text-[var(--text-muted)] font-black uppercase tracking-widest mt-1">Branding and visual protocol</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-16">
                <div class="space-y-10">
                    <div class="space-y-3">
                        <label class="block text-[9px] font-black text-[var(--text-muted)] uppercase tracking-[0.2em]">Platform Title</label>
                        <input type="text" wire:model="appName" class="w-full bg-[var(--bg-main)] border border-[var(--border-color)] focus:border-[var(--accent-primary)] text-[var(--text-main)] rounded-xl px-6 py-4 text-xs font-bold outline-none transition-all">
                    </div>
                    
                    <div class="space-y-3">
                        <label class="block text-[9px] font-black text-[var(--text-muted)] uppercase tracking-[0.2em]">Interface Protocol</label>
                        <div class="flex p-1 bg-[var(--bg-main)] rounded-2xl border border-[var(--border-color)]">
                            <button @click="tempMode = false" class="flex-1 h-12 rounded-xl flex items-center justify-center gap-3 transition-all duration-300" :class="!tempMode ? 'bg-[var(--bg-card)] text-[var(--text-main)] shadow-md border border-[var(--border-color)]' : 'text-[var(--text-muted)] hover:text-[var(--text-main)]'">
                                <x-lucide-sun class="h-4 w-4" />
                                <span class="text-[9px] font-black uppercase tracking-widest">Light</span>
                            </button>
                            <button @click="tempMode = true" class="flex-1 h-12 rounded-xl flex items-center justify-center gap-3 transition-all duration-300" :class="tempMode ? 'bg-[var(--bg-card)] text-[var(--text-main)] shadow-md border border-[var(--border-color)]' : 'text-[var(--text-muted)] hover:text-[var(--text-main)]'">
                                <x-lucide-moon class="h-4 w-4" />
                                <span class="text-[9px] font-black uppercase tracking-widest">Dark</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="space-y-10">
                    <div class="space-y-3">
                        <label class="block text-[9px] font-black text-[var(--text-muted)] uppercase tracking-[0.2em]">Signature Accent</label>
                        <div class="flex flex-wrap gap-4">
                            @foreach(['emerald' => 'bg-[#10b981]', 'blue' => 'bg-[#0ea5e9]', 'purple' => 'bg-[#6366f1]', 'orange' => 'bg-[#f43f5e]'] as $key => $color)
                                <button @click="tempColor = '{{ $key }}'" 
                                        class="h-12 w-12 rounded-xl {{ $color }} transition-all duration-300 relative overflow-hidden group"
                                        :class="tempColor === '{{ $key }}' ? 'ring-2 ring-[var(--text-main)] ring-offset-4 ring-offset-[var(--bg-card)] scale-110 shadow-xl' : 'opacity-40 hover:opacity-100 hover:scale-105'">
                                    <template x-if="tempColor === '{{ $key }}'">
                                        <div class="absolute inset-0 flex items-center justify-center text-white bg-white/10">
                                            <x-lucide-check class="h-4 w-4 stroke-[4]" />
                                        </div>
                                    </template>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="space-y-3">
                        <label class="block text-[9px] font-black text-[var(--text-muted)] uppercase tracking-[0.2em]">Historial de Métricas</label>
                        <div class="relative group">
                            <select class="w-full bg-[var(--bg-main)] border border-[var(--border-color)] focus:border-[var(--accent-primary)] text-[var(--text-main)] rounded-xl px-6 py-4 text-xs font-bold appearance-none outline-none transition-all">
                                <option class="bg-[var(--bg-card)]">Stream: 2s (Real-time)</option>
                                <option class="bg-[var(--bg-card)]">High: 5s</option>
                                <option class="bg-[var(--bg-card)]">Standard: 10s</option>
                            </select>
                            <x-lucide-chevron-down class="absolute right-4 top-1/2 -translate-y-1/2 h-4 w-4 text-[var(--text-muted)] pointer-events-none" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security & 2FA -->
        <div class="bg-[var(--bg-card)] border border-[var(--border-color)] p-12 rounded-3xl shadow-2xl space-y-12 transition-all duration-500">
            <div class="flex items-center gap-5">
                <div class="h-12 w-12 bg-blue-500/10 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/10">
                    <x-lucide-shield-check class="h-6 w-6 text-blue-500" />
                </div>
                <div>
                    <h3 class="text-lg font-bold tracking-tight">Access Authority</h3>
                    <p class="text-[9px] text-[var(--text-muted)] font-black uppercase tracking-widest mt-1">Multi-factor security protocol</p>
                </div>
            </div>

            <div class="max-w-2xl">
                @if($twoFactorEnabled)
                    <div class="flex items-center justify-between p-6 bg-success/5 border border-success/20 rounded-2xl">
                        <div class="flex items-center gap-4">
                            <div class="h-10 w-10 bg-success/10 rounded-full flex items-center justify-center text-success">
                                <x-lucide-check class="h-5 w-5" />
                            </div>
                            <div>
                                <p class="text-xs font-bold text-[var(--text-main)]">Two-Factor Authentication Active</p>
                                <p class="text-[9px] text-[var(--text-muted)] font-black uppercase tracking-widest">Your account is protected by hardware token</p>
                            </div>
                        </div>
                        <button wire:click="disableTwoFactor" class="text-[9px] font-black text-danger uppercase tracking-widest hover:underline">Disable</button>
                    </div>
                @elseif($twoFactorSecret)
                    <div class="space-y-8 animate-in fade-in slide-in-from-top-4 duration-500">
                        <div class="flex flex-col md:flex-row gap-10 items-center bg-[var(--bg-main)] p-8 rounded-3xl border border-[var(--border-color)]">
                            <div class="bg-white p-4 rounded-2xl shadow-xl">
                                {!! $twoFactorQrCode !!}
                            </div>
                            <div class="space-y-4">
                                <h4 class="text-sm font-bold text-[var(--text-main)]">Sync Identity</h4>
                                <p class="text-[10px] text-[var(--text-muted)] font-bold leading-relaxed">Scan this code with Microsoft Authenticator or Google Authenticator to establish the link.</p>
                                <div class="p-3 bg-[var(--bg-card)] border border-[var(--border-color)] rounded-xl">
                                    <p class="text-[8px] text-[var(--text-muted)] font-black uppercase mb-1">Manual Entry Key</p>
                                    <code class="text-[10px] font-mono font-black text-[var(--accent-primary)]">{{ $twoFactorSecret }}</code>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <label class="block text-[9px] font-black text-[var(--text-muted)] uppercase tracking-widest ml-1">Verification Token</label>
                            <div class="flex gap-4">
                                <input type="text" wire:model="verificationCode" placeholder="000000" class="flex-1 bg-[var(--bg-main)] border border-[var(--border-color)] focus:border-[var(--accent-primary)] text-[var(--text-main)] rounded-xl px-6 py-4 text-sm font-black tracking-[0.5em] outline-none transition-all">
                                <button wire:click="confirmTwoFactor" class="bg-[var(--accent-primary)] text-white px-8 rounded-xl text-[9px] font-black uppercase tracking-widest shadow-lg shadow-[var(--accent-primary)]/20">Establish Link</button>
                            </div>
                            @if(session()->has('error'))
                                <p class="text-[9px] font-bold text-danger uppercase tracking-widest ml-1">{{ session('error') }}</p>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="flex items-center justify-between p-8 bg-[var(--bg-main)] border border-[var(--border-color)] rounded-3xl">
                        <div class="space-y-1">
                            <p class="text-xs font-bold text-[var(--text-main)]">Enable 2FA Protection</p>
                            <p class="text-[9px] text-[var(--text-muted)] font-black uppercase tracking-widest">Add an extra layer of authority to your login</p>
                        </div>
                        <button wire:click="generateTwoFactorSecret" class="bg-blue-500 text-white px-8 py-3 rounded-xl text-[9px] font-black uppercase tracking-widest shadow-xl shadow-blue-500/20 hover:scale-105 transition-all">Setup MFA</button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="flex justify-center pt-8 pb-20">
        <button wire:click="saveSettings" 
                class="bg-[var(--accent-primary)] text-white px-16 py-5 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] flex items-center gap-4 hover:scale-[1.05] active:scale-[0.95] transition-all shadow-2xl shadow-[var(--accent-primary)]/40 group">
            <x-lucide-save class="h-4 w-4 group-hover:rotate-12 transition-transform" />
            Commit Changes to Core
        </button>
    </div>
</div>
