<!DOCTYPE html>
<html lang="es" class="bg-slate-50 scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UPTIME - {{ $title ?? 'Dashboard' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
    
    @php
        $theme = session('theme_color', 'indigo');
        $themeMap = [
            'emerald' => ['500' => '#10b981', '400' => '#34d399', '600' => '#059669', 'accent' => 'rgba(16, 185, 129, 0.15)'],
            'blue'    => ['500' => '#3b82f6', '400' => '#60a5fa', '600' => '#2563eb', 'accent' => 'rgba(59, 130, 246, 0.15)'],
            'indigo'  => ['500' => '#6366f1', '400' => '#818cf8', '600' => '#4f46e5', 'accent' => 'rgba(99, 102, 241, 0.15)'],
            'purple'  => ['500' => '#8b5cf6', '400' => '#a78bfa', '600' => '#7c3aed', 'accent' => 'rgba(139, 92, 246, 0.15)'],
            'rose'    => ['500' => '#f43f5e', '400' => '#fb7185', '600' => '#e11d48', 'accent' => 'rgba(244, 63, 94, 0.15)'],
        ];
        $primary = $themeMap[$theme] ?? $themeMap['indigo'];
    @endphp

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f5f3ff', 100: '#ede9fe', 200: '#ddd6fe', 300: '#c4b5fd',
                            400: '{{ $primary["400"] }}', 
                            500: '{{ $primary["500"] }}', 
                            600: '{{ $primary["600"] }}', 
                            700: '#4338ca',
                            800: '#3730a3', 900: '#312e81', 950: '#1e1b4b',
                        },
                        surface: {
                            50: '#f8fafc', 100: '#f1f5f9', 200: '#e2e8f0', 300: '#cbd5e1',
                            400: '#94a3b8', 500: '#64748b', 600: '#475569', 700: '#334155',
                            800: '#1e293b', 900: '#0f172a', 950: '#020617',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Outfit', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(0, 0, 0, 0.1); border-radius: 10px; }
        .dark ::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); }

        /* Premium Spotlight Effect */
        .spotlight-bg {
            position: fixed;
            inset: 0;
            z-index: 0;
            background: radial-gradient(circle at 50% -20%, {{ $primary['accent'] }} 0%, transparent 70%),
                        radial-gradient(circle at 100% 100%, {{ $primary['accent'] }} 0%, transparent 50%),
                        #f8fafc;
        }

        .dark .spotlight-bg {
            background: radial-gradient(circle at 50% -20%, {{ $primary['accent'] }} 0%, transparent 60%),
                        #0a0a0b;
        }

        .premium-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 0; /* serious */
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .dark .premium-card {
            background: rgba(23, 23, 26, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .premium-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px -10px rgba(0, 0, 0, 0.08);
            border-color: {{ $primary['500'] }}33;
        }

        .premium-input {
            background: #ffffff !important;
            border: 1.5px solid #e2e8f0 !important;
            border-radius: 0 !important; /* serious */
            transition: all 0.2s ease;
        }

        .dark .premium-input {
            background: #121214 !important;
            border-color: #27272a !important;
            color: #ffffff !important;
        }

        .premium-input:focus {
            border-color: {{ $primary['500'] }} !important;
            box-shadow: 0 0 0 4px {{ $primary['500'] }}1a;
            outline: none;
        }

        .sidebar-item-active {
            background: {{ $primary['500'] }}10;
            color: {{ $primary['500'] }};
            border-color: {{ $primary['500'] }}20;
        }
    </style>
    @livewireStyles
</head>
<body class="text-surface-700 font-sans selection:bg-primary-500/30 selection:text-primary-900 antialiased min-h-screen relative overflow-x-hidden">
    <div class="spotlight-bg"></div>
    <div class="flex min-h-screen relative z-10 w-full">
        <!-- Sidebar -->
        <aside class="w-72 bg-white/70 dark:bg-surface-950/80 backdrop-blur-3xl border-r border-surface-200/50 dark:border-white/5 flex flex-col fixed inset-y-0 left-0 z-40 shadow-2xl shadow-black/5">
            <div class="p-8">
                <div class="flex items-center gap-4">
                    <div class="h-10 w-10 bg-primary-600 rounded-none flex items-center justify-center shadow-lg shadow-primary-500/30">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3 3a1 1 0 011 1v12a1 1 0 11-2 0V4a1 1 0 011-1zm7.707 3.293a1 1 0 010 1.414L9.414 9H17a1 1 0 110 2H9.414l1.293 1.293a1 1 0 01-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <span class="text-2xl font-display font-black text-surface-900 dark:text-white tracking-tighter">UPTIME</span>
                </div>
            </div>

            <nav class="flex-1 px-6 space-y-1.5 mt-4">
                @php
                    $navItems = [
                        ['route' => 'dashboard', 'label' => 'Resumen', 'icon' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z'],
                        ['route' => 'servers', 'label' => 'Servidores', 'icon' => 'M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01'],
                        ['route' => 'alerts', 'label' => 'Alertas', 'icon' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9'],
                        ['route' => 'logs', 'label' => 'Logs de Red', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                        ['route' => 'settings', 'label' => 'Ajustes', 'icon' => 'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4'],
                    ];
                @endphp

                @foreach($navItems as $item)
                    <a href="{{ route($item['route']) }}" 
                       class="flex items-center gap-3.5 px-4 py-3 rounded-none font-semibold transition-all duration-200 group {{ request()->routeIs($item['route']) ? 'sidebar-item-active shadow-sm shadow-primary-500/5' : 'text-surface-500 hover:text-surface-900 dark:hover:text-white hover:bg-surface-50 dark:hover:bg-white/5' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 opacity-70 group-hover:opacity-100 transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}" />
                        </svg>
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="p-6 mt-auto">
                <div class="bg-surface-50 dark:bg-white/5 p-4 rounded-none border border-surface-200/50 dark:border-white/5 transition-all hover:border-primary-500/30">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-none bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-extrabold text-sm shadow-inner">J</div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-black text-surface-900 dark:text-white truncate">Jaime Ramírez</p>
                            <p class="text-[10px] text-surface-400 font-bold uppercase tracking-wider">PRO Account</p>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 ml-72 min-h-screen flex flex-col bg-transparent relative z-10">
            <!-- Top bar -->
            <header class="h-24 border-b border-surface-200/50 dark:border-white/5 bg-white/40 dark:bg-surface-950/40 backdrop-blur-3xl flex items-center justify-between px-12 sticky top-0 z-30 transition-all duration-300">
                <h2 class="text-2xl font-display font-black text-surface-900 dark:text-white tracking-tight">{{ $title ?? 'Dashboard' }}</h2>
                <div class="flex items-center gap-6">
                    <div class="hidden md:flex items-center bg-white dark:bg-surface-900 border border-surface-200 dark:border-white/5 rounded-none px-4 py-2 shadow-sm focus-within:ring-2 focus-within:ring-primary-500/20 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        <input type="text" placeholder="Buscar nodo..." class="bg-transparent border-none text-sm ml-2 focus:ring-0 text-surface-700 dark:text-surface-200 w-48">
                    </div>
                    <button class="h-12 px-6 flex items-center gap-2 bg-surface-900 dark:bg-white text-white dark:text-surface-900 rounded-none hover:bg-primary-600 dark:hover:bg-primary-400 transition-all font-bold shadow-xl shadow-black/10 hover:shadow-primary-500/30 hover:-translate-y-0.5 active:translate-y-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        NUEVO
                    </button>
                </div>
            </header>

            <div class="p-12 pb-24">
                {{ $slot }}
            </div>
        </main>
    </div>

    @stack('scripts')
    @livewireScripts
</body>
</html>