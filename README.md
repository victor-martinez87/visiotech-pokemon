# ⚡ Pokémon Battle API & Simulator — Laravel 12

API RESTful y Simulador de Combate Pokémon construido con **Laravel 12** y **PHP 8.2+**. 

El proyecto incluye un motor de cálculo de daño basado en la mecánica original de Pokémon (ventajas de tipo, categoría física/especial y stats), un CRUD completo para gestionar el equipo del usuario y un simulador web interactivo construido en Blade y Tailwind CSS que consume la API de forma asíncrona.

---

## 🚀 Características Principales

- **Arquitectura Limpia & Domain Driven:** Separación estricta de responsabilidades usando Services, Form Requests y API Controllers.
- **Motor de Cálculo de Daño (DamageCalculator):** Lógica de negocio pura aislada en un servicio, con cálculo de efectividad de tipos (2.0x, 0.5x, 0.0x) y resolución dinámica entre ataque físico o especial.
- **Relaciones Eloquent Complejas:** Modelos Pokemon, Move y UserPokemon mapeados con relaciones belongsToMany a través de tabla pivote (user_pokemon_move).
- **Tests Automatizados:** Cobertura de tests de integración con Pest / PHPUnit usando RefreshDatabase, factories y seeders.
- **Simulador Web Integrado:** Interfaz retro en Blade + Tailwind CSS que consume la API vía JavaScript fetch.
- **Colección Postman Incluida:** Archivo JSON listo para importar y probar los endpoints.

---

## 🛠️ Requisitos del Sistema

- **PHP** >= 8.2 (con extensión openssl, pdo_sqlite o pdo_mysql)
- **Composer** >= 2.0
- **Git**

---

## ⚙️ Instalación y Puesta en Marcha

Sigue estos sencillos pasos para clonar e instalar el proyecto en tu entorno local:

### 1. Clonar el repositorio
git clone git@github.com:victor-martinez87/visiotech-pokemon.git
cd pokemon

### 2. Instalar dependencias de PHP
composer install

### 3. Configurar el archivo de entorno
Copia el archivo de ejemplo .env.example a .env:
cp .env.example .env

Genera la clave de la aplicación:
php artisan key:generate

### 4. Migraciones y Carga de Datos (Seeders)
Ejecuta las migraciones junto con los Seeders para poblar la base de datos con los Pokémon, Movimientos y Equipos de prueba iniciales:

php artisan migrate:fresh --seed

Datos cargados por defecto:
- Pokémon Base: Pikachu, Charmander, Squirtle, Bulbasaur.
- Movimientos: Thunderbolt, Flamethrower, Water Gun, Vine Whip, Quick Attack, Tackle.
- Equipo Usuario: Pikachu y Charmander equipados con sus movimientos correspondientes.

### 5. Levantar el servidor de desarrollo
php artisan serve
El servidor estará escuchando en http://127.0.0.1:8000.

---

## 💻 Uso del Simulador Web

Una vez levantado el servidor con php artisan serve, abre tu navegador y entra a:

👉 http://127.0.0.1:8000

Podrás seleccionar el Pokémon Atacante, el Movimiento a ejecutar y el Pokémon Defensor. Al hacer clic en "¡EJECUTAR ATAQUE!", se llamará asíncronamente al endpoint de la API, reduciendo la barra de salud del defensor en tiempo real y mostrando un registro detallado del combate.

---

## 🧪 Ejecución de Tests Automatizados

La suite de pruebas valida la estructura JSON de las respuestas, las reglas de validación (HTTP 422) y la integridad de la base de datos tras las operaciones CRUD.

Para ejecutar todos los tests:
php artisan test

Para probar solo un archivo específico:
php artisan test tests/Feature/BattleControllerTest.php
php artisan test tests/Feature/UserPokemonControllerTest.php
php artisan test tests/Unit/DamageCalculatorTest.php

---

## 📡 Documentación de la API REST

### 1. Calculadora de Daño
- POST /api/battle/calculate
- Headers: Content-Type: application/json, Accept: application/json
- Body Payload:
  {
    "attacker_id": 2,
    "defender_id": 4,
    "move_id": 2
  }

- Respuesta Exitosa (200 OK):
  {
    "success": true,
    "data": {
      "attacker": { "id": 2, "name": "Charmander" },
      "defender": { "id": 4, "name": "Bulbasaur" },
      "move": { "id": 2, "name": "Flamethrower", "power": 90 },
      "calculation": {
        "damage": 77,
        "effectiveness": 2.0,
        "is_special": true,
        "message": "¡Es muy eficaz!"
      }
    }
  }

---

### 2. Mochila del Usuario (UserPokemon)

- GET /api/user-pokemons : Lista los Pokémon capturados con sus relaciones (pokemon, moves).
- POST /api/user-pokemons : Añade un Pokémon asignándole hasta 4 movimientos.
- GET /api/user-pokemons/{id} : Muestra el detalle de un Pokémon capturado por su ID.
- DELETE /api/user-pokemons/{id} : Libera/elimina un Pokémon de la mochila.

Ejemplo Payload POST /api/user-pokemons:
{
  "pokemon_id": 3,
  "move_ids": [3, 5]
}

---

## 📫 Pruebas con Postman

El proyecto incluye la colección de Postman configurada para importar y probar de forma inmediata.

1. Abre Postman.
2. Haz clic en Import > Raw Text (o Paste raw text).
3. Copia y pega el contenido del archivo postman_collection.json (ubicado en la raíz del proyecto) o usa el JSON exportado.
4. Asegúrate de que el servidor local está levantado (php artisan serve).
5. La colección utiliza la variable global {{base_url}} (fijada por defecto en http://127.0.0.1:8000).

---

## 📁 Estructura del Proyecto

app/
├── Http/
│   ├── Controllers/Api/
│   │   ├── BattleController.php        # Controlador para cálculo de daño
│   │   └── UserPokemonController.php   # CRUD de la mochila de Pokémon
│   └── Requests/Api/
│       ├── CalculateDamageRequest.php  # Validación de parámetros de combate
│       └── StoreUserPokemonRequest.php  # Validación de creación de UserPokemon
├── Models/
│   ├── Move.php
│   ├── Pokemon.php
│   └── UserPokemon.php
└── Services/
    └── DamageCalculator.php            # Lógica de dominio y fórmula de daño

database/
├── factories/                          # Fábricas para generación de datos de test
├── migrations/                         # Estructura de tablas y claves foráneas
└── seeders/                            # Seeders iniciales con datos de Kanto

resources/views/
└── battle.blade.php                    # Simulador de combate interactivo

routes/
├── api.php                             # Rutas protegidas de la API
└── web.php                             # Ruta del simulador web

tests/Feature/
├── BattleControllerTest.php            # Tests de integración del combate
└── UserPokemonControllerTest.php       # Tests de integración del CRUD

