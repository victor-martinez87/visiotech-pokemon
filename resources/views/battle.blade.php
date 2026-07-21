<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Víctor Martínez - Pokémon Battle Simulator — Laravel 12 API</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Press+Start+2P&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen bg-battlefield flex flex-col justify-between selection:bg-amber-400 selection:text-slate-950">

    <!-- Header Principal -->
    <header class="w-full border-b border-slate-800/80 bg-slate-950/60 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <span class="text-2xl animate-bounce">⚡</span>
                <div>
                    <h1 class="font-pixel text-xs sm:text-sm text-amber-400 tracking-wider uppercase">Víctor Martínez - Pokémon Battle Simulator</h1>
                    <p class="text-[11px] text-slate-400 font-medium mt-0.5">Laravel 12 RESTful API & Domain Engine</p>
                </div>
            </div>
            <div class="hidden sm:flex items-center space-x-2 bg-slate-900/80 border border-slate-800 px-3 py-1.5 rounded-full text-xs text-slate-300">
                <span id="api-status-ping" class="w-2 h-2 rounded-full bg-slate-500 animate-pulse"></span>
                <span>API Status: <strong id="api-status-text" class="text-slate-400">Comprobando...</strong></span>
            </div>
        </div>
    </header>

    <!-- Arena Principal -->
    <main class="max-w-6xl mx-auto px-4 py-8 w-full flex-grow flex flex-col justify-center gap-8">
        
        <!-- Grid de Luchadores (Atacante vs Defensor) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative">

            <!-- VS Badge Flotante -->
            <div class="hidden md:flex absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 z-20 w-12 h-12 rounded-full bg-amber-400 text-slate-950 font-pixel text-xs items-center justify-center shadow-lg shadow-amber-500/20 ring-4 ring-slate-950 font-bold">
                VS
            </div>

            <!-- TARJETA ATACANTE -->
            <div class="bg-slate-900/70 backdrop-blur-md border border-slate-800 rounded-2xl p-6 shadow-2xl relative overflow-hidden group hover:border-amber-500/50 transition-all duration-300">
                <div class="absolute -right-12 -bottom-12 w-40 h-40 bg-amber-500/10 rounded-full blur-3xl group-hover:bg-amber-500/20 transition-all"></div>
                
                <div class="flex items-center justify-between border-b border-slate-800/80 pb-3 mb-5">
                    <div class="flex items-center space-x-2">
                        <span class="text-xs uppercase font-bold text-amber-400 tracking-wider font-pixel">Atacante</span>
                    </div>
                    <span class="text-[11px] font-bold bg-amber-400/10 text-amber-300 border border-amber-500/20 px-2.5 py-1 rounded-full">Nv. 50</span>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase tracking-wider">Pokémon Atacante</label>
                        <select id="attacker-select" class="w-full bg-slate-950/80 border border-slate-800 rounded-xl px-4 py-3 text-slate-100 text-sm focus:border-amber-400 focus:ring-1 focus:ring-amber-400 focus:outline-none transition-all">
                            <option value="1">Pikachu (Eléctrico)</option>
                            <option value="2" selected>Charmander (Fuego)</option>
                            <option value="3">Squirtle (Agua)</option>
                            <option value="4">Bulbasaur (Planta)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase tracking-wider">Movimiento</label>
                        <select id="move-select" class="w-full bg-slate-950/80 border border-slate-800 rounded-xl px-4 py-3 text-slate-100 text-sm focus:border-amber-400 focus:ring-1 focus:ring-amber-400 focus:outline-none transition-all">
                            <option value="1">Thunderbolt (Poder: 90 / Eléctrico)</option>
                            <option value="2" selected>Flamethrower (Poder: 90 / Fuego)</option>
                            <option value="3">Water Gun (Poder: 40 / Agua)</option>
                            <option value="4">Vine Whip (Poder: 45 / Planta)</option>
                            <option value="5">Quick Attack (Poder: 40 / Normal)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- TARJETA DEFENSOR -->
            <div class="bg-slate-900/70 backdrop-blur-md border border-slate-800 rounded-2xl p-6 shadow-2xl relative overflow-hidden group hover:border-rose-500/50 transition-all duration-300">
                <div class="absolute -right-12 -bottom-12 w-40 h-40 bg-rose-500/10 rounded-full blur-3xl group-hover:bg-rose-500/20 transition-all"></div>
                
                <div class="flex items-center justify-between border-b border-slate-800/80 pb-3 mb-5">
                    <div class="flex items-center space-x-2">
                        <span class="text-xs uppercase font-bold text-rose-400 tracking-wider font-pixel">Defensor</span>
                    </div>
                    <span class="text-[11px] font-bold bg-rose-400/10 text-rose-300 border border-rose-500/20 px-2.5 py-1 rounded-full">Nv. 50</span>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase tracking-wider">Pokémon Defensor</label>
                        <select id="defender-select" class="w-full bg-slate-950/80 border border-slate-800 rounded-xl px-4 py-3 text-slate-100 text-sm focus:border-rose-400 focus:ring-1 focus:ring-rose-400 focus:outline-none transition-all">
                            <option value="1">Pikachu (Eléctrico)</option>
                            <option value="2">Charmander (Fuego)</option>
                            <option value="3">Squirtle (Agua)</option>
                            <option value="4" selected>Bulbasaur (Planta)</option>
                        </select>
                    </div>

                    <!-- Barra de Salud Dinámica -->
                    <div class="bg-slate-950/60 p-4 rounded-xl border border-slate-800 space-y-2">
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-slate-400 font-semibold uppercase tracking-wider">Puntos de Salud (HP)</span>
                            <span id="hp-text" class="font-bold font-mono text-emerald-400">100 / 100 HP</span>
                        </div>
                        <div class="w-full bg-slate-900 h-3 rounded-full overflow-hidden p-0.5 border border-slate-800">
                            <div id="hp-bar" class="bg-emerald-500 h-full rounded-full w-full transition-all duration-700 ease-out shadow-sm shadow-emerald-500/50"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Botón de Ejecución de Ataque -->
        <div class="flex justify-center my-2">
            <button id="attack-btn" class="relative group overflow-hidden rounded-2xl bg-gradient-to-r from-amber-500 to-amber-400 p-px text-slate-950 font-bold shadow-xl shadow-amber-500/10 hover:shadow-amber-500/25 active:scale-95 transition-all duration-200">
                <div class="px-8 py-4 bg-amber-400 group-hover:bg-amber-300 rounded-[15px] transition-colors flex items-center space-x-3">
                    <span class="text-xl">⚔️</span>
                    <span class="font-pixel text-xs sm:text-sm tracking-wider uppercase">¡Ejecutar Ataque!</span>
                </div>
            </button>
        </div>

        <!-- Log de Combate (Historial) -->
        <div class="bg-slate-900/80 backdrop-blur-md border border-slate-800 rounded-2xl p-5 shadow-2xl space-y-3">
            <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                    <span>Registro de la Batalla</span>
                </h3>
                <span class="text-[11px] text-slate-500 font-mono">POST /api/battle/calculate</span>
            </div>
            
            <div id="combat-log" class="space-y-2.5 max-h-52 overflow-y-auto pr-1 custom-scrollbar">
                <p class="text-slate-500 text-xs italic py-4 text-center">Selecciona los Pokémon y presiona "Ejecutar Ataque" para probar la API...</p>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="w-full border-t border-slate-800/80 bg-slate-950/60 backdrop-blur-md py-4 text-center text-xs text-slate-500">
        <p>Desarrollado por Víctor Martínez para Prueba Técnica Senior con Laravel 12 & Tailwind CSS</p>
    </footer>
</body>
</html>