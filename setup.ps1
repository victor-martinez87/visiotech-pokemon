Write-Host "🚀 Iniciando instalación..."

# ----------------------------------------
# 1. Levantar contenedores
# ----------------------------------------
docker compose up -d --build

# ----------------------------------------
# 2. Esperar a que el contenedor esté listo
# ----------------------------------------
Write-Host "⏳ Esperando a que Docker esté listo..."

$maxRetries = 10
$retry = 0

while ($retry -lt $maxRetries) {
    $status = docker compose ps | Select-String "app.*Up"
    if ($status) {
        Write-Host "✅ Contenedor listo"
        break
    }
    Start-Sleep -Seconds 2
    $retry++
}

if ($retry -eq $maxRetries) {
    Write-Host "❌ El contenedor no ha arrancado"
    docker compose logs
    exit 1
}

# ----------------------------------------
# 3. Preparar SQLite
# ----------------------------------------
docker compose exec app sh -c "mkdir -p database && touch database/database.sqlite"

# ----------------------------------------
# 4. Backend
# ----------------------------------------
docker compose run --rm app composer install
docker compose run --rm app php artisan key:generate
docker compose run --rm app php artisan migrate:fresh --seed

# ----------------------------------------
# 5. Frontend
# ----------------------------------------
docker compose exec app sh -c "if command -v npm >/dev/null 2>&1; then npm install && npm run build; else echo '⚠️ npm no disponible'; fi"

# ----------------------------------------
# 6. Tests
# ----------------------------------------
Write-Host "🧪 Ejecutando tests..."
docker compose run --rm app php artisan test

# ----------------------------------------
# 7. Done
# ----------------------------------------
Write-Host ""
Write-Host "✅ Instalación completada"
Write-Host "🌐 http://localhost:8000"