<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50/50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UPTIME - {{ $title ?? 'Observability' }}</title>
    
    <!-- Fonts: Standard Enterprise Stack -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&family=Outfit:wght@500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CDN for Local Portability -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'Instrument Sans', 'ui-sans-serif', 'system-ui'],
                        display: ['Outfit', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <style type="text/tailwindcss">
        @layer components {
            .premium-card {
                @apply bg-white border border-slate-200/60 rounded-none shadow-[0_1px_2px_rgba(0,0,0,0.02)] transition-all duration-300;
            }

            .premium-card:hover {
                @apply border-slate-300/80 shadow-[0_8px_30px_rgb(0,0,0,0.04)];
            }

            .status-pill {
                @apply px-2.5 py-1 text-[9px] font-black uppercase tracking-widest border rounded-none flex items-center justify-center;
            }

            .status-pill.online {
                @apply bg-emerald-50 text-emerald-600 border-emerald-100/50;
            }

            .status-pill.offline {
                @apply bg-rose-50 text-rose-600 border-rose-100/50;
            }

            .btn-primary {
                @apply h-11 px-6 bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest hover:bg-slate-800 transition-all rounded-none flex items-center justify-center gap-2 shadow-lg shadow-slate-900/10 active:scale-95;
            }

            .btn-secondary {
                @apply h-11 px-6 bg-white text-slate-900 border border-slate-200 text-[10px] font-black uppercase tracking-widest hover:bg-slate-50 transition-all rounded-none flex items-center justify-center gap-2;
            }

            .premium-input {
                @apply h-11 w-full px-4 bg-slate-50 border border-slate-200 text-sm focus:bg-white focus:border-indigo-500 transition-all outline-none rounded-none;
            }

            .professional-table {
                @apply w-full text-left border-collapse;
            }

            .professional-table th {
                @apply px-8 py-5 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100 bg-slate-50/50;
            }

            .professional-table td {
                @apply px-8 py-6 text-sm border-b border-slate-100/60 transition-colors bg-white;
            }

            .professional-table tr:hover td {
                @apply bg-slate-50/50;
            }
        }

        [x-cloak] { display: none !important; }
    </style>

    @livewireStyles
</head>
<body class="h-full font-sans antialiased text-slate-900 selection:bg-indigo-100">
    
    <div class="min-h-full flex overflow-hidden">
        <!-- Persistent Sidebar -->
        <aside class="w-72 bg-white border-r border-slate-200/60 flex flex-col fixed inset-y-0 z-50">
            <!-- Brand -->
            <div class="h-20 flex items-center px-8 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="h-9 w-9 bg-slate-900 rounded-none flex items-center justify-center text-white shadow-lg">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <span class="block text-lg font-display font-bold tracking-tighter text-slate-900 leading-none">UPTIME</span>
                        <span class="block text-[9px] font-black tracking-widest text-slate-400 uppercase mt-0.5">Observability Platform</span>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-8 space-y-1">
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
                    <div class="pt-4 pb-2 px-4">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $group }}</span>
                    </div>
                    @foreach($items as $item)
                        <a href="{{ route($item['route']) }}" 
                           class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium transition-all group {{ request()->routeIs($item['route']) ? 'text-indigo-600 bg-indigo-50/50' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50' }}">
                            <svg class="h-5 w-5 {{ request()->routeIs($item['route']) ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $item['icon'] }}" />
                            </svg>
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                @endforeach
            </nav>

            <!-- User Context -->
            <div class="p-6 border-t border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="h-9 w-9 bg-slate-900 rounded-none flex items-center justify-center text-white text-xs font-bold ring-4 ring-slate-50">JR</div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-slate-900 truncate">Jaime Ramírez</p>
                        <p class="text-[10px] text-slate-400 font-medium truncate uppercase tracking-tighter">Enterprise Admin</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Workspace -->
        <main class="flex-1 ml-72 min-h-screen bg-[#fbfbfc]">
            <!-- Topbar Context -->
            <header class="h-20 bg-white/80 backdrop-blur-md border-b border-slate-200/60 sticky top-0 z-40 px-10 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-display font-bold text-slate-900 tracking-tight">{{ $title ?? 'System Console' }}</h2>
                </div>
                <div class="flex items-center gap-4">
                    <div class="px-3 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase tracking-widest flex items-center gap-2 border border-emerald-100">
                        <div class="h-1.5 w-1.5 bg-emerald-500 rounded-full animate-pulse"></div>
                        Live Signal
                    </div>
                </div>
            </header>

            <!-- Content Slot -->
            <div class="p-10 max-w-7xl mx-auto">
                {{ $slot }}
            </div>
        </main>
    </div>

    @stack('scripts')
    @livewireScripts
</body>
</html>