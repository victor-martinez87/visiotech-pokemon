#!/bin/bash

echo "🚀 Iniciando instalación..."

# Build + levantar contenedores
docker compose up -d --build

echo "⏳ Esperando a que Docker esté listo..."
sleep 5

# Instalar dependencias
docker compose exec app composer install

# Copiar .env si no existe
docker compose exec app bash -c "[ -f .env ] || cp .env.example .env"

# Generar APP KEY
docker compose exec app php artisan key:generate

# Migraciones + seeders (SQLite)
docker compose exec app php artisan migrate:fresh --seed

# Tests
echo "🧪 Ejecutando tests..."
docker compose exec app php artisan test

echo "✅ Instalación completada"
echo "🌐 http://localhost:8000"