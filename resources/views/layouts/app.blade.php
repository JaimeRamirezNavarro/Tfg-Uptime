<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ session('app_name', 'UPTIME') }} - {{ $title ?? 'Observability Platform' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&family=Outfit:wght@500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <!-- High-Contrast Theme System -->
    <style>
        :root {
            @php
                $isLight = session('theme_mode', 'dark') === 'light';
                $color = session('theme_color', 'emerald');
                $colors = [
                    'emerald' => '#10b981',
                    'blue' => '#3b82f6',
                    'purple' => '#8b5cf6',
                    'orange' => '#f97316',
                ];
                $accent = $colors[$color] ?? '#10b981';
            @endphp

            /* Core Palette - HIGH CONTRAST */
            --bg-main: {{ $isLight ? '#e2e8f0' : '#09090b' }};
            --bg-sidebar: {{ $isLight ? '#ffffff' : '#121215' }};
            --bg-card: {{ $isLight ? '#ffffff' : '#18181b' }};
            --border-color: {{ $isLight ? 'rgba(0,0,0,0.15)' : 'rgba(255,255,255,0.05)' }};
            --text-main: {{ $isLight ? '#000000' : '#fafafa' }};
            --text-muted: {{ $isLight ? '#334155' : '#a1a1aa' }};
            --accent-primary: {{ $accent }};
        }

        /* Typography & Readability Force */
        body { font-family: 'Inter', sans-serif !important; color: var(--text-main) !important; }
        h1, h2, h3, .font-display { font-family: 'Outfit', sans-serif !important; color: var(--text-main) !important; }

        /* MaryUI Stat Overrides - HIGH VISIBILITY */
        .stat-title { color: var(--text-muted) !important; font-weight: 800 !important; text-transform: uppercase !important; font-size: 11px !important; letter-spacing: 0.1em !important; opacity: 1 !important; }
        .stat-value { color: var(--text-main) !important; font-weight: 900 !important; font-size: 2.5rem !important; }
        .stat { border-color: var(--border-color) !important; padding: 2rem !important; }
        /* Sidebar Link Force Contrast */
        .sidebar-link-active { background: var(--accent-primary) !important; color: white !important; font-weight: 900 !important; }
        .sidebar-link-inactive { color: var(--text-muted) !important; font-weight: 600 !important; }
        .sidebar-link-inactive:hover { background: rgba(0,0,0,0.05) !important; color: var(--text-main) !important; }
    </style>
</head>
<body class="bg-[var(--bg-main)] text-[var(--text-main)] selection:bg-[var(--accent-primary)] selection:text-white font-sans antialiased overflow-hidden transition-colors duration-500">
    <div class="flex h-screen overflow-hidden">
        <!-- Enterprise Sidebar -->
        <aside class="w-64 flex-shrink-0 flex flex-col bg-[var(--bg-sidebar)] border-r border-[var(--border-color)] h-full transition-colors duration-500">
            <div class="h-16 flex items-center px-6 border-b border-white/5">
                <div class="flex items-center gap-3">
                    <div class="h-7 w-7 bg-[var(--accent-primary)] rounded flex items-center justify-center shadow-lg shadow-[var(--accent-primary)]/20">
                        <x-lucide-zap class="h-4 w-4 text-white fill-current" />
                    </div>
                    <span class="text-sm font-black tracking-widest text-[var(--text-main)] uppercase">{{ session('app_name', 'UPTIME') }}</span>
                </div>
            </div>

            <nav class="flex-1 flex flex-col p-4 gap-1 overflow-y-auto custom-scrollbar">
                @php
                    $items = [
                        ['label' => 'Overview', 'route' => 'dashboard', 'icon' => 'o-squares-2x2'],
                        ['label' => 'Infrastructure', 'route' => 'servers', 'icon' => 'o-server-stack'],
                        ['label' => 'Alerts', 'route' => 'alerts', 'icon' => 'o-bell'],
                        ['label' => 'Audit Log', 'route' => 'logs', 'icon' => 'o-document-text'],
                        ['label' => 'Preferences', 'route' => 'settings', 'icon' => 'o-cog-6-tooth'],
                    ];
                @endphp

                @foreach($items as $item)
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs($item['route']) ? 'sidebar-link-active shadow-md' : 'sidebar-link-inactive' }}">
                        <x-mary-icon name="{{ $item['icon'] }}" class="h-4 w-4" />
                        <span class="text-xs">{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>

            <div class="p-4 border-t border-[var(--border-color)]">
                <a href="{{ route('logout') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-danger hover:bg-danger/10 transition-all duration-200">
                    <x-lucide-log-out class="h-4 w-4" />
                    <span class="text-xs font-black uppercase tracking-widest">Terminate Session</span>
                </a>
            </div>

            <div class="p-4 border-t border-[var(--border-color)]">
                <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-white/5 transition-colors cursor-pointer group">
                    <div class="h-8 w-8 rounded bg-[var(--accent-primary)] flex items-center justify-center text-[10px] font-bold text-white shrink-0 shadow-lg shadow-[var(--accent-primary)]/20 uppercase">
                        {{ substr(auth()->user()->name, 0, 2) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[11px] font-bold text-[var(--text-main)] truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[9px] text-[var(--text-muted)] truncate uppercase tracking-tighter">{{ auth()->user()->email }}</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col h-full bg-[var(--bg-main)] overflow-hidden">
            <!-- Sleek Top Bar -->
            <header class="h-16 flex items-center justify-between px-8 border-b border-[var(--border-color)] bg-[var(--bg-sidebar)]/50 backdrop-blur-xl shrink-0">
                <div class="flex items-center gap-4">
                    <h2 class="text-xs font-bold text-[var(--text-muted)] uppercase tracking-[0.2em]">{{ $title ?? 'System Console' }}</h2>
                    <div class="h-3 w-[1px] bg-[var(--border-color)]"></div>
                    <div class="flex items-center gap-2">
                        <div class="h-1.5 w-1.5 rounded-full bg-success"></div>
                        <span class="text-[10px] font-bold text-success uppercase tracking-widest">Live</span>
                    </div>
                </div>
                
                <div class="flex items-center gap-3">
                    <!-- Actions Removed as requested -->
                </div>
            </header>

            <!-- Content Body -->
            <div class="flex-1 overflow-y-auto p-8 custom-scrollbar">
                <div class="max-w-7xl mx-auto">
                    {{ $slot }}
                </div>
            </div>
        </main>
    </div>


    <x-mary-toast />
    @stack('scripts')
    @livewireScripts
</body>
</html>