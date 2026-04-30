<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UPTIME - {{ $title ?? 'Observability Platform' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&family=Outfit:wght@500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full bg-bg-primary text-text-primary antialiased selection:bg-accent-primary/30">

    <div class="min-h-full flex overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-72 sidebar flex flex-col fixed inset-y-0 z-50">
            <!-- Brand -->
            <div class="h-20 flex items-center px-6 border-b border-border-subtle">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 bg-white/5 border border-white/10 rounded-xl flex items-center justify-center glow-accent shadow-sm">
                        <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <span class="block text-xl font-display font-black tracking-tighter text-text-primary leading-none">UPTIME</span>
                        <span class="block text-[9px] font-black tracking-widest text-text-tertiary uppercase mt-0.5">Observability Platform</span>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-3 py-6 space-y-1">
                @php
                    $navGroups = [
                        'Main' => [
                            ['route' => 'dashboard', 'label' => 'Overview', 'icon' => 'M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z'],
                            ['route' => 'servers', 'label' => 'Infrastructure', 'icon' => 'M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01'],
                        ],
                        'System' => [
                            ['route' => 'alerts', 'label' => 'Alerts', 'icon' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9'],
                            ['route' => 'logs', 'label' => 'Audit Log', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                            ['route' => 'settings', 'label' => 'Preferences', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z'],
                        ]
                    ];
                @endphp

                @foreach($navGroups as $group => $items)
                    <div class="pt-4 pb-2 px-3">
                        <span class="text-[10px] font-black text-text-tertiary uppercase tracking-widest">{{ $group }}</span>
                    </div>
                    @foreach($items as $item)
                        <a href="{{ route($item['route']) }}"
                           class="nav-item {{ request()->routeIs($item['route']) ? 'active' : '' }}">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $item['icon'] }}" />
                            </svg>
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                @endforeach
            </nav>

            <!-- User -->
            <div class="p-4 border-t border-border-subtle">
                <div class="flex items-center gap-3 p-3 rounded-xl bg-bg-tertiary border border-border-subtle shadow-sm">
                    <div class="h-10 w-10 bg-white/5 border border-white/10 rounded-xl flex items-center justify-center text-white text-xs font-bold shadow-inner">
                        JR
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-text-primary truncate">Jaime Ramírez</p>
                        <p class="text-[9px] text-text-tertiary font-black uppercase tracking-tighter mt-0.5">Admin</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 ml-72 min-h-screen bg-bg-primary">
            <!-- Topbar -->
            <header class="h-20 bg-bg-secondary/80 backdrop-blur-xl border-b border-border-subtle sticky top-0 z-40 px-8 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-display font-semibold text-text-primary tracking-tight">{{ $title ?? 'System Console' }}</h2>
                </div>
                <div class="flex items-center gap-3">
                    <div class="px-3 py-1.5 bg-success/10 text-success text-[9px] font-black uppercase tracking-widest flex items-center gap-2 border border-success/30 rounded-full glow-success">
                        <div class="h-1.5 w-1.5 bg-success rounded-full animate-pulse"></div>
                        Live Signal
                    </div>
                </div>
            </header>

            <!-- Content -->
            <div class="p-8 max-w-7xl mx-auto">
                {{ $slot }}
            </div>
        </main>
    </div>

    @stack('scripts')
    @livewireScripts
</body>
</html>