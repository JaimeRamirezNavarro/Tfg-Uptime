<!DOCTYPE html>
<html lang="es" class="h-full dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UPTIME - {{ $title ?? 'Auth' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        // Aplicar el tema guardado inmediatamente
        const savedTheme = localStorage.getItem('theme_mode') || 'dark';
        if (savedTheme === 'light') {
            document.documentElement.classList.remove('dark');
        } else {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
<body class="h-full flex items-center justify-center p-6 bg-slate-50 dark:bg-[#09090b] transition-colors duration-300 antialiased">
    
    <!-- BOTÓN DE TEMA INDESTRUCTIBLE -->
    <div style="position: fixed; top: 2rem; right: 2rem; z-index: 9999;">
        <button type="button" 
                onclick="document.documentElement.classList.toggle('dark'); localStorage.setItem('theme_mode', document.documentElement.classList.contains('dark') ? 'dark' : 'light');" 
                class="h-12 w-12 rounded-2xl bg-white dark:bg-zinc-800 border border-slate-200 dark:border-white/10 flex items-center justify-center text-slate-900 dark:text-white shadow-2xl cursor-pointer hover:scale-110 active:scale-95 transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 9H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
        </button>
    </div>

    {{ $slot }}

    @livewireScripts
</body>
</html>
