<div class="p-6 bg-gray-900 min-h-screen text-white" wire:poll.5s>
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-green-400 font-mono text-uppercase">🚀 Monitor de Servidores</h1>
            <p class="text-xs text-gray-500">Panel de Control en Tiempo Real</p>
        </div>
        <div class="text-right">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-900 text-green-300 animate-pulse">
                SISTEMA ACTIVO
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($servers as $server)
            <div class="bg-gray-800 border border-gray-700 p-6 rounded-xl shadow-2xl transition-all hover:border-green-500">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-white">{{ $server->name }}</h2>
                        <code class="text-[10px] text-gray-500 uppercase">ID: {{ $server->api_token }}</code>
                    </div>
                </div>

                @php $lastMetric = $server->metrics->first(); @endphp

                @if($lastMetric)
                    <div class="space-y-6">
                        <div>
                            <div class="flex justify-between text-xs mb-2">
                                <span class="text-gray-400 font-bold">CPU LOAD</span>
                                <span class="text-green-400">{{ $lastMetric->cpu_load }}%</span>
                            </div>
                            <div class="w-full bg-gray-700 h-3 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-1000 {{ $lastMetric->cpu_load > 80 ? 'bg-red-500' : 'bg-green-500' }}" 
                                     style="width: {{ $lastMetric->cpu_load }}%">
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between text-xs mb-2">
                                <span class="text-gray-400 font-bold">RAM USAGE</span>
                                <span class="text-blue-400">{{ $lastMetric->ram_usage }}%</span>
                            </div>
                            <div class="w-full bg-gray-700 h-3 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-1000 {{ $lastMetric->ram_usage > 85 ? 'bg-red-500' : 'bg-blue-500' }}" 
                                     style="width: {{ $lastMetric->ram_usage }}%">
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-gray-700 flex justify-between items-center">
                            <span class="text-[10px] text-gray-500 uppercase tracking-widest">Sincronizado</span>
                            <span class="text-[10px] text-gray-400">{{ $lastMetric->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                @else
                    <div class="py-12 text-center">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-green-500 mb-4"></div>
                        <p class="text-sm text-gray-500 italic">Esperando datos del agente Python...</p>
                    </div>
                @endif
            </div>
        @empty
            <div class="col-span-full bg-gray-800 p-12 rounded-xl text-center border-2 border-dashed border-gray-700">
                <p class="text-gray-400">No hay servidores registrados en la base de datos.</p>
                <p class="text-xs text-gray-600 mt-2">Usa 'php artisan tinker' para crear tu primer servidor.</p>
            </div>
        @endforelse
    </div>
</div>