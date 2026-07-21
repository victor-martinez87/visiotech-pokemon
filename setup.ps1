$ErrorActionPreference = "Stop"

Write-Host "🚀 Iniciando instalación..."

# ----------------------------------------
# 1. Preparar entorno y .env
# ----------------------------------------
if (-not (Test-Path ".env")) {
    Write-Host "📄 Copiando .env.example a .env..."
    Copy-Item ".env.example" ".env"
}

# ----------------------------------------
# 2. Construir imágenes (sin levantar el servidor aún)
# ----------------------------------------
Write-Host "🐳 Construyendo contenedores..."
docker compose build

# ----------------------------------------
# 3. Preparar SQLite (Usamos run --rm para no depender de que el contenedor principal esté vivo)
# ----------------------------------------
Write-Host "🗄️ Preparando base de datos..."
docker compose run --rm app sh -c "mkdir -p database && touch database/database.sqlite"

# ----------------------------------------
# 4. Backend (Composer & Artisan)
# ----------------------------------------
Write-Host "📦 Instalando dependencias de PHP..."
docker compose run --rm app composer install
docker compose run --rm app php artisan key:generate

# Añadimos --force para que Laravel no se bloquee preguntando si estamos en producción
docker compose run --rm app php artisan migrate:fresh --seed --force

# ----------------------------------------
# 5. Frontend (Usamos tu contenedor 'node' en lugar de 'app')
# ----------------------------------------
Write-Host "🎨 Compilando assets (Vite/Tailwind)..."
docker compose run --rm node sh -c "npm install && npm run build"

# ----------------------------------------
# 6. Tests
# ----------------------------------------
Write-Host "🧪 Ejecutando tests..."
docker compose run --rm app php artisan test

# ----------------------------------------
# 7. Levantar contenedores definitivos
# ----------------------------------------
Write-Host "🚀 Levantando servidor de la aplicación..."
docker compose up -d

# Verificación rápida de que no ha crasheado
Start-Sleep -Seconds 3
$status = docker compose ps | Select-String "app.*Up"

if (-not $status) {
    Write-Host "❌ El contenedor se ha caído al intentar arrancar. Revisando logs:" -ForegroundColor Red
    docker compose logs app
    exit 1
}

# ----------------------------------------
# 8. Done
# ----------------------------------------
Write-Host ""
Write-Host "✅ Instalación completada"
Write-Host "🌐 http://localhost:8000"