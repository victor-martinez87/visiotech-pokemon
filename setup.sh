#!/usr/bin/env bash

# Detener el script si ocurre un error inesperado
set -e

echo "⚡ =========================================="
echo "⚡ Instalador Automático — Pokémon Battle API"
echo "⚡ Compatibilidad: macOS / Linux / Git Bash"
echo "⚡ =========================================="
echo ""

# --------------------------------------------------
# 1. Verificación de PHP (Prerrequisito imprescindible)
# --------------------------------------------------
if ! command -v php &> /dev/null; then
    echo "❌ ERROR CRÍTICO: 'php' no está instalado en tu sistema."
    echo "   Por favor, instala PHP >= 8.2 antes de ejecutar este instalador."
    exit 1
fi
echo "✅ PHP detectado: $(php -v | head -n 1)"

# --------------------------------------------------
# 2. Verificación / Instalación Automática de Composer
# --------------------------------------------------
COMPOSER_CMD=""

if command -v composer &> /dev/null; then
    echo "✅ Composer global detectado."
    COMPOSER_CMD="composer"
else
    echo "⚠️  Composer no está instalado de forma global."
    echo "📥 Descargando e instalando 'composer.phar' localmente..."
    
    if [ ! -f composer.phar ]; then
        php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
        php composer-setup.php --quiet
        php -r "unlink('composer-setup.php');"
    fi
    COMPOSER_CMD="php composer.phar"
    echo "✅ Composer local listo para usarse."
fi

# --------------------------------------------------
# 3. Configuración del archivo de entorno (.env)
# --------------------------------------------------
if [ ! -f .env ]; then
    echo "📄 [1/6] Copiando .env.example a .env..."
    cp .env.example .env
else
    echo "📄 [1/6] El archivo .env ya existe."
fi

# --------------------------------------------------
# 4. Instalación de dependencias de PHP
# --------------------------------------------------
echo "📦 [2/6] Instalando dependencias de PHP con Composer..."
$COMPOSER_CMD install

# --------------------------------------------------
# 5. Generación de Key
# --------------------------------------------------
echo "🔑 [3/6] Generando clave de aplicación..."
php artisan key:generate

# --------------------------------------------------
# 6. Verificación de NPM / Compilación de Frontend
# --------------------------------------------------
if command -v npm &> /dev/null; then
    echo "🎨 [4/6] Instalando dependencias de Node y compilando frontend..."
    npm install
    npm run build
else
    echo "⚠️  'npm' no está instalado. Omitiendo compilación de assets."
    echo "   (Si los assets precompilados ya están en public/build, la web funcionará)."
fi

# --------------------------------------------------
# 7. Migraciones y Seeders
# --------------------------------------------------
echo "🗄️  [5/6] Preparando la base de datos (Migraciones + Seeders)..."
php artisan migrate:fresh --seed

# --------------------------------------------------
# 8. Verificación final con Tests
# --------------------------------------------------
echo "🧪 [6/6] Verificando la instalación con la suite de tests..."
php artisan test

echo ""
echo "================================================="
echo "✅ ¡Instalación completada con éxito!"
echo "🚀 Ejecuta 'php artisan serve' e ingresa a http://127.0.0.1:8000"
echo "================================================="