<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Mini Gestor de Tareas TODO

Aplicación web para la gestión de tareas desarrollada con **Laravel**, **Livewire**, **Alpine.js** y **Tailwind CSS**, aplicando buenas prácticas de desarrollo, arquitectura limpia y experiencia de usuario eficiente.

---

## Características

- **Crear tareas** con título obligatorio y descripción opcional (editable después)
- **Editar descripción** de tareas existentes
- **Marcar/desmarcar** tareas como completadas sin recargar la página (Livewire)
- **Eliminar tareas** con confirmación previa
- **Asignar categorías** a tareas (relación uno a muchos)
- **Filtrar tareas:** todas, pendientes o completadas
- **Toggle de descripción** con Alpine.js (mostrar/ocultar al hacer clic)
- **Validación en frontend** del formulario con Alpine.js
- Diseño **responsive** con Tailwind CSS

### Opcionales

- Búsqueda en tiempo real (Livewire)
- Dark/Light Mode (Tailwind CSS)
- Drag-and-drop para reordenar tareas
- Exportar tareas a PDF/CSV

---

## Tecnologías Utilizadas

| Tecnología | Versión | Tipo |
|---|---|---|
| **Laravel** | 12.x (11+) | Backend |
| **Livewire** | 3+ | Componentes reactivos |
| **Alpine.js** | 3+ | Interactividad frontend |
| **Tailwind CSS** | 4.x | Estilos CSS |
| **Vite** | 7.x | Bundler de assets |
| **MySQL** | 8.4 | Base de datos |
| **Redis** | Alpine | Caché y colas |
| **Pest** | — | Testing |
| **Docker / Sail** | — | Entorno de desarrollo |

---

## Requisitos Previos

- **Docker** y **Docker Compose**
- **Git**

> 💡 Si usas Docker con Laravel Sail, **no necesitas** tener PHP, Composer, Node.js, MySQL ni Redis instalados localmente. Docker se encargará de todo.

**Si NO usas Docker**, necesitarás:

- PHP 8.2+
- Composer
- Node.js y NPM
- MySQL o SQLite

---

## Instalación desde Cero (Nuevo Proyecto)

Estos pasos explican cómo se montó el proyecto desde cero con todas las librerías necesarias.

### 1. Crear el Proyecto Laravel

```bash
composer create-project laravel/laravel Mini-Gestor-de-Tareas-TODO
cd Mini-Gestor-de-Tareas-TODO
```

### 2. Configurar el Archivo de Entorno (.env)

```bash
cp .env.example .env
```

Editar el archivo `.env` con la configuración para Docker/Sail:

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=pruebas
DB_USERNAME=sail
DB_PASSWORD=password
```

> ⚠️ **Importante:** El `DB_HOST` debe ser `mysql` (nombre del servicio Docker), **no** `127.0.0.1` ni `localhost`.

### 3. Instalar Laravel Sail (Docker)

```bash
php artisan sail:install --with=mysql,redis
```

> 💡 **Tip:** Crear un alias para simplificar los comandos:
>
> ```bash
> alias sail='./vendor/bin/sail'
> ```

### 4. Levantar los Contenedores Docker

```bash
# Primera vez (construir imágenes)
./vendor/bin/sail up -d --build

# Las siguientes veces
./vendor/bin/sail up -d
```

### 5. Generar Clave y Ejecutar Migraciones

```bash
sail artisan key:generate
sail artisan migrate
```

**Otros comandos útiles de migraciones:**

| Comando | Descripción |
|---|---|
| `sail artisan migrate` | Ejecutar migraciones pendientes |
| `sail artisan migrate:fresh` | Eliminar todas las tablas y recrearlas |
| `sail artisan migrate:fresh --seed` | Recrear tablas y ejecutar seeders |
| `sail artisan migrate:rollback` | Revertir la última migración |
| `sail artisan migrate:status` | Ver estado de las migraciones |

### 6. Detener los Contenedores

```bash
# Detener los contenedores sin eliminarlos
sail down

# Detener y eliminar los volúmenes (borra la base de datos)
sail down -v
```

---

## Instalación de Librerías

### Tailwind CSS 4 (ya incluido en `package.json`)

Tailwind CSS v4 viene preconfigurado con el plugin de Vite. Solo necesitas instalar las dependencias de Node:

```bash
sail npm install
```

**Configuración relevante:**

**`vite.config.js`** — Tailwind se integra como plugin de Vite:

```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
```

**`resources/css/app.css`** — Importar Tailwind (sintaxis v4):

```css
@import 'tailwindcss';
```

> 📝 **Nota:** Tailwind CSS v4 **no usa** el archivo `tailwind.config.js` tradicional. La configuración se hace directamente en el CSS con directivas como `@theme` y `@source`.

---

### Livewire 3

Livewire permite crear componentes interactivos del lado del servidor sin JavaScript.

```bash
sail composer require livewire/livewire
```

En el layout principal (`resources/views/layouts/app.blade.php`) incluir las directivas:

```html
<head>
    ...
    @livewireStyles
</head>
<body>
    ...
    @livewireScripts
</body>
```

Para crear un componente Livewire:

```bash
sail artisan make:livewire NombreComponente
```

Esto genera:

- **Clase PHP:** `app/Livewire/NombreComponente.php` (lógica)
- **Vista Blade:** `resources/views/livewire/nombre-componente.blade.php` (interfaz)

---

### Alpine.js 3

Alpine.js permite añadir interactividad ligera directamente en el HTML (toggles, modales, validaciones).

```bash
sail npm install alpinejs
```

Importarlo en **`resources/js/app.js`**:

```javascript
import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();
```

Ejemplo de uso en Blade:

```html
<!-- Toggle para mostrar/ocultar descripción -->
<div x-data="{ mostrar: false }">
    <button @click="mostrar = !mostrar">Ver descripción</button>
    <p x-show="mostrar" x-transition>Descripción de la tarea...</p>
</div>
```

---

### Pest (Testing)

Pest es el framework de testing requerido para este proyecto.

```bash
# Instalar Pest y el plugin para Laravel
sail composer require pestphp/pest --dev --with-all-dependencies
sail composer require pestphp/pest-plugin-laravel --dev
```

Inicializar Pest en el proyecto:

```bash
sail artisan pest:install
```

Ejecutar los tests:

```bash
# Si usas Sail (Docker)
sail artisan test

# O usando el binario de Pest directamente
sail bin/pest

# Si NO usas Docker (ejecución local)
php artisan test
```

---

### Librerías Opcionales

**Exportar tareas a Excel/CSV:**

```bash
sail composer require maatwebsite/excel
```

**Exportar tareas a PDF:**

```bash
sail composer require barryvdh/laravel-dompdf
```

---

## Compilar Assets (Frontend)

```bash
# Modo desarrollo (con hot reload)
sail npm run dev

# Modo producción (optimizado)
sail npm run build
```

---

## Levantar el Proyecto desde GitHub

Si quieres clonar y ejecutar este proyecto desde el repositorio de GitHub, sigue estos pasos:

### 1. Clonar el Repositorio

```bash
git clone https://github.com/tu-usuario/Mini-Gestor-de-Tareas-TODO.git
cd Mini-Gestor-de-Tareas-TODO
```

### 2. Instalar Dependencias de PHP

```bash
# Si tienes PHP y Composer instalados localmente:
composer install

# Si NO tienes PHP local, usa Docker directamente:
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs
```

### 3. Configurar el Entorno

```bash
cp .env.example .env
```

Editar `.env` con la configuración de base de datos:

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=pruebas
DB_USERNAME=sail
DB_PASSWORD=password
```

### 4. Levantar Docker con Sail

```bash
./vendor/bin/sail up -d --build
```

### 5. Generar Clave de Aplicación

```bash
./vendor/bin/sail artisan key:generate
```

### 6. Ejecutar Migraciones y Seeders

```bash
# Solo migraciones
./vendor/bin/sail artisan migrate

# Migraciones + datos de ejemplo (si hay seeders)
./vendor/bin/sail artisan migrate --seed
```

### 7. Instalar Dependencias de Node y Compilar Assets

```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```

### 8. Acceder a la Aplicación

Abrir en el navegador: **[http://localhost](http://localhost)**

---

## Consideraciones y Solución de Errores Comunes

Durante el desarrollo de la prueba técnica, nos encontramos con algunos errores de integración entre las librerías modernas utilizadas (Livewire 3, Alpine 3 y Pest). Dejamos constancia de las soluciones para evitar futuros bloqueos:

### 1. Interferencia entre Alpine.js y eventos de Livewire 3 (`@click.stop` vs `wire:click.stop`)

Cuando usamos Alpine.js para abrir/cerrar un desplegable (ej. clickeando la fila de una tabla de tareas) y colocamos botones de acción de Livewire dentro de ella (ej. "Editar" o "Eliminar"), es tentador usar `@click.stop` en el botón para que la fila no se expanda.
**Problema:** Livewire 3 escucha eventos a nivel global del DOM. Si Alpine detiene de golpe la propagación del clic con `@click.stop`, Livewire nunca se entera y el botón (ej. `wire:click="deleteTask"`) deja de funcionar mágicamente.
**Solución:** En lugar del modificador de Alpine, se debe usar el modificador de Livewire: **`wire:click.stop="nombreFuncion"`**. Esto permite que Livewire procese la petición en backend y, al mismo tiempo, frena el evento de clics anidados del frontend.

### 2. Inicialización duplicada de Alpine.js con Livewire 3

**Problema:** En proyectos tradicionales de Laravel + Alpine, típicamente se hace la inicialización manual abriendo `resources/js/app.js` y usando `Alpine.start()`. Sin embargo, Livewire 3 **ya inyecta e inicializa Alpine.js automáticamente**. Si tú inicializas Alpine manualmente en `app.js` y usas componentes Livewire en la misma pantalla, surgirán conflictos silenciosos de estado global y los botones del backend dejarán de responder.
**Solución:** No hacer `import Alpine from 'alpinejs'` ni `Alpine.start()` en `resources/js/app.js` si tu proyecto usa Livewire 3. Deja que Livewire controle el ciclo de vida de Alpine por sí solo.

### 3. Pest Tests fallando por base de datos (Problema de Conexión)

Al ejecutar pruebas Unitarias y de Feature con Pest en local usando bases de datos en memoria (SQLite), es posible toparse con el error `Call to a member function connection() on null`.
**Problema:** Ocurre cuando el entorno de Testing de Laravel no arranca completamente en un archivo Pest antes de ejecutar un modelo.
**Solución:**

1. Asegurarse de que en `phpunit.xml` se esté sobrescribiendo la configuración de BD a memoria pura: `<env name="DB_CONNECTION" value="sqlite"/>` y `<env name="DB_DATABASE" value=":memory:"/>`.
2. En la estructura de Pest `tests/Unit/..` asegurarse de hacer que el archivo inicie como TestCase nativo de Laravel para cargar el framework, añadiendo: `uses(Tests\TestCase::class, RefreshDatabase::class);`.

---

## Estructura del Proyecto

```
Mini-Gestor-de-Tareas-TODO/
├── app/
│   ├── Http/Controllers/       # Controladores
│   ├── Livewire/               # Componentes Livewire
│   ├── Models/                 # Modelos Eloquent
│   ├── Services/               # Servicios (lógica de negocio)
│   └── Providers/              # Service Providers
├── database/
│   ├── migrations/             # Migraciones
│   ├── factories/              # Factories (testing)
│   └── seeders/                # Seeders
├── resources/
│   ├── css/app.css             # Estilos (Tailwind CSS)
│   ├── js/app.js               # JavaScript (Alpine.js)
│   ├── lang/                   # Traducciones
│   └── views/                  # Vistas Blade
├── routes/
│   └── web.php                 # Rutas web
├── tests/                      # Tests con Pest
├── compose.yaml                # Docker (Sail)
├── vite.config.js              # Vite + Tailwind
├── composer.json               # Dependencias PHP
└── package.json                # Dependencias Node.js
```

---

## Comandos Útiles

| Comando | Descripción |
|---|---|
| `sail up -d` | Levantar contenedores Docker |
| `sail down` | Detener contenedores |
| `sail down -v` | Detener y eliminar volúmenes (borra BD) |
| `sail shell` | Terminal dentro del contenedor |
| `sail artisan migrate` | Ejecutar migraciones |
| `sail artisan migrate:fresh --seed` | Recrear BD con datos |
| `sail artisan make:livewire Nombre` | Crear componente Livewire |
| `sail artisan test` | Ejecutar tests (Pest) |
| `sail npm run dev` | Servidor de desarrollo (Vite) |
| `sail npm run build` | Compilar para producción |
| `composer dev` | Inicia servidor + queue + logs + Vite |

---

## Licencia

Este proyecto utiliza el framework [Laravel](https://laravel.com), licenciado bajo la [MIT License](https://opensource.org/licenses/MIT).
