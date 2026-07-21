#!/usr/bin/env bash

# Detener el script si algún comando falla
set -e

echo "🚀 Iniciando instalación (Linux / macOS)..."

# ----------------------------------------
# 1. Preparar entorno y .env
# ----------------------------------------
if [ ! -f .env ]; then
    echo "📄 Copiando .env.example a .env..."
    cp .env.example .env
fi

# ----------------------------------------
# 2. Construir imágenes (sin levantar el servidor aún)
# ----------------------------------------
echo "🐳 Construyendo contenedores..."
docker compose build

# ----------------------------------------
# 3. Preparar SQLite (Usamos run --rm)
# ----------------------------------------
echo "🗄️ Preparando base de datos..."
docker compose run --rm app sh -c "mkdir -p database && touch database/database.sqlite"

# ----------------------------------------
# 4. Backend (Composer & Artisan)
# ----------------------------------------
echo "📦 Instalando dependencias de PHP..."
docker compose run --rm app composer install
docker compose run --rm app php artisan key:generate

# Añadimos --force para que Laravel no se bloquee en entornos CI/CD
docker compose run --rm app php artisan migrate:fresh --seed --force

# ----------------------------------------
# 5. Frontend (Usamos el contenedor 'node')
# ----------------------------------------
echo "🎨 Compilando assets (Vite/Tailwind)..."
docker compose run --rm node sh -c "npm install && npm run build"

# ----------------------------------------
# 6. Tests
# ----------------------------------------
echo "🧪 Ejecutando tests..."
docker compose run --rm app php artisan test

# ----------------------------------------
# 7. Levantar contenedores definitivos
# ----------------------------------------
echo "🚀 Levantando servidor de la aplicación..."
docker compose up -d

# Verificación rápida de que no ha crasheado
sleep 3
if ! docker compose ps | grep -q "app.*Up"; then
    echo "❌ ERROR: El contenedor se ha caído al intentar arrancar. Revisando logs:"
    docker compose logs app
    exit 1
fi

# ----------------------------------------
# 8. Done
# ----------------------------------------
echo ""
echo "✅ Instalación completada con éxito"
echo "🌐 http://localhost:8000"