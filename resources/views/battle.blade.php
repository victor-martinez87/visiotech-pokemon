<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulador de Batalla Pokémon</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-white min-h-screen p-6 font-mono">
    <div class="max-w-4xl mx-auto space-y-6">
        
        <!-- Header -->
        <header class="text-center py-4 border-b border-slate-700">
            <h1 class="text-3xl font-bold tracking-wider text-yellow-400">⚡ POKÉMON BATTLE SIMULATOR ⚡</h1>
            <p class="text-slate-400 text-sm mt-1">Laravel 12 API Damage Calculator Engine</p>
        </header>

        <!-- Arena -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Atacante -->
            <div class="bg-slate-800 border-2 border-slate-700 rounded-xl p-5 shadow-lg space-y-4">
                <div class="flex justify-between items-center border-b border-slate-700 pb-2">
                    <span class="text-xs uppercase font-bold text-emerald-400">Atacante</span>
                    <span id="attacker-level" class="text-xs bg-slate-700 px-2 py-1 rounded">Nv. 50</span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Selecciona Pokémon:</label>
                    <select id="attacker-select" class="w-full bg-slate-900 border border-slate-700 rounded p-2 text-white text-sm focus:border-yellow-400 focus:outline-none">
                        <option value="1">Pikachu (Eléctrico)</option>
                        <option value="2" selected>Charmander (Fuego)</option>
                        <option value="3">Squirtle (Agua)</option>
                        <option value="4">Bulbasaur (Planta)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Selecciona Movimiento:</label>
                    <select id="move-select" class="w-full bg-slate-900 border border-slate-700 rounded p-2 text-white text-sm focus:border-yellow-400 focus:outline-none">
                        <option value="1">Thunderbolt (Poder: 90 / Eléctrico)</option>
                        <option value="2" selected>Flamethrower (Poder: 90 / Fuego)</option>
                        <option value="3">Water Gun (Poder: 40 / Agua)</option>
                        <option value="4">Vine Whip (Poder: 45 / Planta)</option>
                        <option value="5">Quick Attack (Poder: 40 / Normal)</option>
                    </select>
                </div>
            </div>

            <!-- Defensor -->
            <div class="bg-slate-800 border-2 border-slate-700 rounded-xl p-5 shadow-lg space-y-4">
                <div class="flex justify-between items-center border-b border-slate-700 pb-2">
                    <span class="text-xs uppercase font-bold text-rose-400">Defensor</span>
                    <span class="text-xs bg-slate-700 px-2 py-1 rounded">Nv. 50</span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Selecciona Pokémon:</label>
                    <select id="defender-select" class="w-full bg-slate-900 border border-slate-700 rounded p-2 text-white text-sm focus:border-yellow-400 focus:outline-none">
                        <option value="1">Pikachu (Eléctrico)</option>
                        <option value="2">Charmander (Fuego)</option>
                        <option value="3">Squirtle (Agua)</option>
                        <option value="4" selected>Bulbasaur (Planta)</option>
                    </select>
                </div>

                <!-- Barra de HP -->
                <div class="space-y-1">
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-400">Salud (HP):</span>
                        <span id="hp-text" class="font-bold text-white">100 / 100 HP</span>
                    </div>
                    <div class="w-full bg-slate-900 h-4 rounded-full overflow-hidden border border-slate-700">
                        <div id="hp-bar" class="bg-emerald-500 h-full w-full transition-all duration-500"></div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Botón Atacar -->
        <div class="text-center">
            <button id="attack-btn" class="bg-yellow-500 hover:bg-yellow-400 text-slate-950 font-bold px-8 py-3 rounded-xl shadow-lg transition transform active:scale-95 text-lg">
                ⚔️ ¡EJECUTAR ATAQUE!
            </button>
        </div>

        <!-- Log de Combate -->
        <div class="bg-slate-950 border border-slate-800 rounded-xl p-4 shadow-inner">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 border-b border-slate-800 pb-1">Registro de Combate</h3>
            <div id="combat-log" class="space-y-2 text-sm max-h-48 overflow-y-auto">
                <p class="text-slate-500 italic">Selecciona los Pokémon y presiona "Ejecutar Ataque" para probar la API...</p>
            </div>
        </div>

    </div>

    <!-- Script Fetch a la API -->
    <script>
        let currentHpPercent = 100;

        document.getElementById('attack-btn').addEventListener('click', async () => {
            const attackerId = document.getElementById('attacker-select').value;
            const defenderId = document.getElementById('defender-select').value;
            const moveId = document.getElementById('move-select').value;
            const logContainer = document.getElementById('combat-log');

            try {
                const response = await fetch('/api/battle/calculate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        attacker_id: parseInt(attackerId),
                        defender_id: parseInt(defenderId),
                        move_id: parseInt(moveId)
                    })
                });

                const json = await response.json();

                if (json.success) {
                    const data = json.data;
                    const damage = data.calculation.damage;
                    const msg = data.calculation.message;

                    // Actualizar HP ficticio visual
                    currentHpPercent = Math.max(0, currentHpPercent - (damage / 2));
                    const hpBar = document.getElementById('hp-bar');
                    hpBar.style.width = currentHpPercent + '%';

                    if (currentHpPercent < 25) {
                        hpBar.className = 'bg-rose-500 h-full transition-all duration-500';
                    } else if (currentHpPercent < 50) {
                        hpBar.className = 'bg-yellow-500 h-full transition-all duration-500';
                    }

                    document.getElementById('hp-text').innerText = `${Math.round(currentHpPercent)} / 100 HP`;

                    // Agregar log
                    const logItem = document.createElement('div');
                    logItem.className = 'p-2 bg-slate-900 border border-slate-800 rounded flex justify-between items-center';
                    logItem.innerHTML = `
                        <div>
                            <span class="font-bold text-yellow-400">${data.attacker.name}</span> usó 
                            <span class="text-cyan-300 font-bold">${data.move.name}</span> contra 
                            <span class="font-bold text-rose-400">${data.defender.name}</span>. 
                            <span class="text-xs text-slate-400">(${msg})</span>
                        </div>
                        <div class="bg-rose-950 text-rose-300 border border-rose-800 px-2 py-1 rounded text-xs font-bold">
                            -${damage} HP
                        </div>
                    `;

                    logContainer.prepend(logItem);
                } else {
                    alert('Error en la validación de la API');
                }
            } catch (error) {
                console.error(error);
                alert('No se pudo conectar con la API local.');
            }
        });
    </script>
</body>
</html>