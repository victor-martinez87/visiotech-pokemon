import './bootstrap';
let currentHpPercent = 100;

// Resetear la barra si cambia el defensor
document.getElementById('defender-select').addEventListener('change', () => {
    currentHpPercent = 100;
    updateHpDisplay(100);
});

document.getElementById('attack-btn').addEventListener('click', async () => {
    const attackerId = document.getElementById('attacker-select').value;
    const defenderId = document.getElementById('defender-select').value;
    const moveId = document.getElementById('move-select').value;
    const logContainer = document.getElementById('combat-log');
    const btn = document.getElementById('attack-btn');

    btn.classList.add('opacity-75', 'pointer-events-none');

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

            // Reducir barra de vida progresivamente con cada ataque
            currentHpPercent = Math.max(0, currentHpPercent - damage);
            updateHpDisplay(currentHpPercent);

            if (logContainer.querySelector('p.italic')) {
                logContainer.innerHTML = '';
            }

            let badgeColor = 'bg-slate-800 text-slate-300 border-slate-700';
            if (data.calculation.effectiveness > 1) {
                badgeColor = 'bg-emerald-950/80 text-emerald-300 border-emerald-800/80';
            } else if (data.calculation.effectiveness < 1 && data.calculation.effectiveness > 0) {
                badgeColor = 'bg-rose-950/80 text-rose-300 border-rose-800/80';
            }

            const logItem = document.createElement('div');
            logItem.className = 'p-3 bg-slate-950/80 border border-slate-800/80 rounded-xl flex items-center justify-between space-x-3 text-xs';
            logItem.innerHTML = `
                <div class="flex items-center space-x-2">
                    <span class="text-slate-400">⚔️</span>
                    <span>
                        <strong class="text-amber-400">${data.attacker.name}</strong> usó 
                        <strong class="text-cyan-300">${data.move.name}</strong> sobre 
                        <strong class="text-rose-400">${data.defender.name}</strong>.
                    </span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full border ${badgeColor} font-semibold">${msg}</span>
                </div>
                <div class="font-mono font-bold text-rose-400 bg-rose-500/10 border border-rose-500/20 px-2.5 py-1 rounded-lg shrink-0">
                    -${damage} HP
                </div>
            `;

            logContainer.prepend(logItem);
        } else {
            alert('Error en la respuesta de la API');
        }
    } catch (error) {
        console.error(error);
        alert('No se pudo conectar con el endpoint /api/battle/calculate');
    } finally {
        btn.classList.remove('opacity-75', 'pointer-events-none');
    }
});

function updateHpDisplay(percent) {
    const hpBar = document.getElementById('hp-bar');
    const hpText = document.getElementById('hp-text');

    hpBar.style.width = percent + '%';
    hpText.innerText = `${Math.round(percent)} / 100 HP`;

    if (percent < 25) {
        hpBar.className = 'bg-rose-500 h-full rounded-full transition-all duration-700 ease-out shadow-sm shadow-rose-500/50';
        hpText.className = 'font-bold font-mono text-rose-400';
    } else if (percent < 50) {
        hpBar.className = 'bg-amber-400 h-full rounded-full transition-all duration-700 ease-out shadow-sm shadow-amber-400/50';
        hpText.className = 'font-bold font-mono text-amber-400';
    } else {
        hpBar.className = 'bg-emerald-500 h-full rounded-full transition-all duration-700 ease-out shadow-sm shadow-emerald-500/50';
        hpText.className = 'font-bold font-mono text-emerald-400';
    }
}

async function checkApiHealth() {
    const ping = document.getElementById('api-status-ping');
    const text = document.getElementById('api-status-text');

    try {
        // Hacemos una petición rápida a cualquier endpoint ligero GET de la API
        const response = await fetch('/api/user-pokemons', {
            method: 'GET',
            headers: { 'Accept': 'application/json' }
        });

        if (response.ok) {
            ping.className = 'w-2 h-2 rounded-full bg-emerald-400 animate-pulse';
            text.className = 'text-emerald-400';
            text.innerText = 'Online';
        } else {
            throw new Error('API Error');
        }
    } catch (error) {
        ping.className = 'w-2 h-2 rounded-full bg-rose-500';
        text.className = 'text-rose-400';
        text.innerText = 'Offline';
    }
}

// Ejecutar al cargar la vista
checkApiHealth();