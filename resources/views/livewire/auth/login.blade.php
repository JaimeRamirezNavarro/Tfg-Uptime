<div class="w-full max-w-[380px] animate-in fade-in duration-500">
    <!-- Branding -->
    <div class="text-center mb-10">
        <div class="inline-flex items-center justify-center h-12 w-12 bg-[#10b981] text-white rounded-xl shadow-xl shadow-[#10b981]/20 mb-6">
            <x-lucide-zap class="h-6 w-6 fill-current" />
        </div>
        <h1 class="text-2xl font-display font-black text-slate-900 dark:text-white tracking-tight uppercase">Identity Auth</h1>
        <p class="text-[9px] text-slate-500 dark:text-zinc-500 font-black uppercase tracking-[0.3em] mt-2">Authorized Personnel Only</p>
    </div>

    <!-- Main Auth Card -->
    <div class="bg-white dark:bg-zinc-900 border border-slate-200 dark:border-white/5 p-8 rounded-[24px] shadow-xl transition-colors duration-500">
        @if(!$show2fa)
            <form wire:submit.prevent="login" class="space-y-6">
                <div class="space-y-4">
                    <div class="space-y-1.5">
                        <label class="block text-[9px] font-black text-slate-400 dark:text-zinc-600 uppercase tracking-widest ml-1">UID</label>
                        <input type="text" wire:model="username" placeholder="root" class="w-full bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/5 focus:border-[#10b981] text-slate-900 dark:text-white rounded-xl px-5 py-3.5 text-xs font-bold outline-none transition-all placeholder:text-slate-300 dark:placeholder:text-zinc-800 shadow-inner">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-[9px] font-black text-slate-400 dark:text-zinc-600 uppercase tracking-widest ml-1">Key</label>
                        <input type="password" wire:model="password" placeholder="••••••••" class="w-full bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/5 focus:border-[#10b981] text-slate-900 dark:text-white rounded-xl px-5 py-3.5 text-xs font-bold outline-none transition-all placeholder:text-slate-300 dark:placeholder:text-zinc-800 shadow-inner">
                    </div>
                </div>

                @if(session()->has('error'))
                    <div class="p-3 bg-red-500/10 border border-red-500/20 rounded-xl text-[9px] font-black text-red-500 uppercase tracking-widest text-center">
                        {{ session('error') }}
                    </div>
                @endif

                <button type="submit" class="w-full h-12 bg-[#10b981] text-white rounded-xl flex items-center justify-center gap-3 text-[10px] font-black uppercase tracking-[0.2em] shadow-lg shadow-[#10b981]/20 hover:scale-[1.02] active:scale-[0.98] transition-all border-none cursor-pointer">
                    <span wire:loading.remove>Sign In</span>
                    <div wire:loading class="h-3 w-3 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                </button>
            </form>
        @else
            <form wire:submit.prevent="verify2fa" class="space-y-6">
                <div class="text-center">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Security Token</h3>
                    <p class="text-[9px] text-slate-500 dark:text-zinc-500 font-black uppercase tracking-widest mt-1">Check your authenticator app</p>
                </div>

                <div class="space-y-2 text-center">
                    <input type="text" wire:model="token" maxlength="6" placeholder="000000" class="w-full bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/5 focus:border-[#10b981] text-slate-900 dark:text-white text-center rounded-xl px-5 py-3.5 text-2xl font-black tracking-[0.3em] outline-none transition-all">
                </div>

                @if(session()->has('error'))
                    <div class="p-3 bg-red-500/10 border border-red-500/20 rounded-xl text-[9px] font-black text-red-500 uppercase tracking-widest text-center">
                        {{ session('error') }}
                    </div>
                @endif

                <button type="submit" class="w-full h-12 bg-[#10b981] text-white rounded-xl flex items-center justify-center gap-3 text-[10px] font-black uppercase tracking-[0.2em] shadow-lg shadow-[#10b981]/20 hover:scale-[1.02] active:scale-[0.98] transition-all border-none cursor-pointer">
                    <span wire:loading.remove>Verify</span>
                    <div wire:loading class="h-3 w-3 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                </button>
            </form>
        @endif
    </div>

    <!-- Footer -->
    <div class="mt-8 text-center opacity-30">
        <p class="text-[8px] text-slate-900 dark:text-white font-black uppercase tracking-[0.3em]">Encrypted Authentication Layer</p>
    </div>
</div>
