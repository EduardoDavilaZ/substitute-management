# Manual de Estándares de Codificación
## Proyecto: Guardias

**Versión:** 1.0  
**Última actualización:** Abril 2026  
**Equipo:** Desarrollo

---

## 📋 Tabla de Contenidos

- [1. Principios Generales](#1-principios-generales)
- [2. Estructura del Proyecto](#2-estructura-del-proyecto)
- [3. Estándares PHP](#3-estándares-php)
- [4. Convenciones de Nombres](#4-convenciones-de-nombres)
- [5. Arquitectura MVC](#5-arquitectura-mvc)
- [6. Base de Datos](#6-base-de-datos)
- [7. Frontend (HTML, CSS, JavaScript)](#7-frontend-html-css-javascript)
- [8. Control de Versiones](#8-control-de-versiones)
- [9. Seguridad](#9-seguridad)
- [10. Testing y Debugging](#10-testing-y-debugging)

---

## 1. Principios Generales

### 1.1 Filosofía de Código
- **Legibilidad primero**: El código debe ser fácil de entender a primera vista
- **DRY (Don't Repeat Yourself)**: Evita duplicación de código
- **SOLID**: Aplica estos principios en la arquitectura
- **Consistencia**: Sigue el mismo estilo en todo el proyecto
- **Mantenibilidad**: Escribe código que otros puedan mantener fácilmente

### 1.2 Indentación y Espacios
```
- Usar 4 espacios para indentación (NO tabs)
- Máximo 120 caracteres por línea
- Una línea en blanco entre métodos
- Dos líneas en blanco entre clases
```

### 1.3 Encoding
```
- Usar UTF-8 sin BOM en todos los archivos
- Charset UTF-8 en bases de datos (utf8mb4)
- Declarar charset en HTML: <meta charset="UTF-8">
```

---

## 2. Estructura del Proyecto

### 2.1 Árbol de Directorios

```
proyecto-guardias/
├── src/
│   └── app/
│       ├── config/              # Configuración de la aplicación
│       │   ├── app.php           # Constantes y rutas
│       │   ├── database.php      # Credenciales DB
│       │   └── database.example.php
│       ├── controllers/          # Controladores MVC
│       ├── models/               # Modelos de datos
│       ├── helpers/              # Funciones reutilizables
│       └── views/                # Vistas (templates PHP)
│           ├── admin/            # Vistas de administrador
│           ├── teacher/          # Vistas de profesor
│           ├── errors/           # Vistas de errores
│           └── layouts/          # Layouts base
├── public/
│   ├── index.php                # Punto de entrada
│   ├── assets/                  # Recursos estáticos
│   │   ├── css/
│   │   ├── js/
│   │   ├── img/
│   │   ├── fonts/
│   │   └── vendor/              # Librerías third-party
│   ├── uploads/                 # Archivos subidos por usuarios
│   └── web.config               # Configuración para IIS
├── sql/                         # Scripts de base de datos
└── test/                        # Tests (futuros)
```

### 2.2 Propósito de Cada Carpeta

| Carpeta | Propósito | Responsabilidades |
|---------|-----------|-------------------|
| `config` | Configuración centralizada | Constantes, rutas, credenciales |
| `controllers` | Lógica de negocio | Manejo de peticiones, respuestas |
| `models` | Acceso a datos | Consultas, manipulación de BD |
| `helpers` | Funciones reutilizables | Autenticación, validación, utilidades |
| `views` | Presentación | Templates HTML |

---

## 3. Estándares PHP

### 3.1 Apertura y Cierre de Etiquetas

```php
// ✅ CORRECTO
<?php
// código aquí
?>

// ❌ INCORRECTO
<?
// código aquí sin punto y coma al final
```

**Reglas:**
- Abrir con `<?php` (siempre)
- Cerrar con `?>` solo si hay contenido después
- No agregar vacío o espacios después de `?>`

### 3.2 Type Hints (Tipado)

```php
// ✅ CORRECTO - Con type hints
public function query(string $sql, array $params = [], bool $fetchAll = true): array
{
    // ...
}

protected function update(string $sql, array $params = []): array
{
    // ...
}

function init_session(): void
{
    // ...
}

// ❌ INCORRECTO - Sin type hints
public function query($sql, $params, $fetchAll)
{
    // ...
}
```

**Tipos permitidos:**
- `string`, `int`, `float`, `bool`, `array`
- Nombres de clases
- `void` (para métodos que no retornan)
- `mixed` (último recurso, evitar)

### 3.3 Documentación (PhpDoc)

```php
/**
 * Inicia la sesión si no ha sido iniciada.
 * 
 * @return void
 */
function init_session(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Middleware: Redirige al login si el usuario no está autenticado.
 * 
 * @param string $redirectTo Página a la que redirigir después de login
 * @return void
 */
function auth(string $redirectTo = ''): void {
    init_session();
    
    if (!isset($_SESSION['user_id'])) {
        redirect($redirectTo ?? '');
    }
}
```

**Componentes obligatorios:**
- Descripción breve (primera línea)
- `@param` para cada parámetro
- `@return` para el tipo de retorno
- `@throws` si lanza excepciones (futura)

### 3.4 Comentarios en Código

```php
// Comentarios de una línea para explicaciones breves
$userId = $user['id']; // Obtener ID del usuario

/*
 * Comentarios multilínea para explicaciones complejas
 * Usar cuando se necesita más de una línea
 */
if ($condition) {
    // ...
}

// ❌ EVITAR: Comentarios obvios
$x = 5; // Asignar 5 a x
```

---

## 4. Convenciones de Nombres

### 4.1 Clases

```php
// ✅ CORRECTO - PascalCase
class AdminController { }
class TeacherModel { }
class UserRepository { }

// ❌ INCORRECTO
class adminController { }
class admin_controller { }
class Admin_Controller { }
```

**Reglas:**
- Nombres en **PascalCase** (CamelCase con mayúscula inicial)
- Nombres descriptivos y sustantivos
- Sufijo "Controller", "Model", "Helper" cuando corresponda
- Un archivo por clase

### 4.2 Métodos y Funciones

```php
// ✅ CORRECTO - camelCase
public function getUserData() { }
public function validateEmail($email) { }
function is_logged(): bool { }
function init_session(): void { }

// ❌ INCORRECTO
public function get_user_data() { }
public function GetUserData() { }
```

**Reglas:**
- Nombres en **camelCase** (primera palabra minúscula)
- Verbos que describan la acción
- Nombres cortos pero descriptivos

### 4.3 Variables y Propiedades

```php
class UserController {
    // ✅ CORRECTO - camelCase para propiedades
    public $view;
    public $message;
    protected $userId;
    private $userEmail;

    // ❌ INCORRECTO
    public $View;
    public $user_email;
    public $UserEmail;
}
```

**Reglas:**
- Nombres en **camelCase**
- Sustantivos descriptivos
- Usar nombres completos, no abreviaturas

### 4.4 Constantes

```php
// ✅ CORRECTO - UPPERCASE_SNAKE_CASE
define("BASE_PATH", dirname(__DIR__, 2) . '/');
define("DATABASE_HOST", "localhost");
const DEFAULT_CONTROLLER = "IndexController";
const MAX_UPLOAD_SIZE = 5242880; // 5MB

// ❌ INCORRECTO
define("base_path", "...");
define("BasePath", "...");
```

**Reglas:**
- Nombres en **UPPERCASE_SNAKE_CASE**
- Deben ser autoexplicativas
- Agrupar relacionadas cerca unas de otras

### 4.5 Archivos

```
✅ CORRECTO:
- AdminController.php
- UserModel.php
- auth_helper.php
- daily_substitutions.php
- app.php

❌ INCORRECTO:
- admin_controller.php (mixed case)
- UserModel.PHP (extensión mayúscula)
- admincontroller.php (todo minúscula)
```

**Reglas:**
- Clases: **PascalCase** (AdminController.php)
- Funciones/Helpers: **snake_case** (auth_helper.php)
- Vistas/Layout: **snake_case** (daily_substitutions.php)

---

## 5. Arquitectura MVC

### 5.1 Controladores

```php
<?php

/**
 * Controlador de administrador
 */
class AdminController {
    public $view;           // Nombre de la vista a renderizar
    public $msg;            // Mensajes para la vista
    public $layout = 'admin/layout';  // Layout a usar

    public function __construct() {
        $this->view = '';
        $this->msg = '';
    }

    /**
     * Acción: Página de inicio del administrador
     */
    public function home() {
        $this->view = 'admin/home';
        // Aquí iría lógica adicional si es necesaria
    }

    /**
     * Acción: Obtener datos via AJAX
     */
    public function getData() {
        $this->view = '';  // No renderizar vista
        return json([
            'success' => true,
            'data' => [...]
        ]);
    }
}

?>
```

**Estándares de Controladores:**

| Aspecto | Estándar |
|--------|----------|
| Nombre | `{Nombre}Controller` |
| Herencia | Ninguna (puede extender de base si es necesario) |
| Propiedades | `$view`, `$msg`, `$layout` |
| Métodos | Acciones públicas (nombres descriptivos en camelCase) |
| Responsabilidad | Orquestar modelos y vistas |

### 5.2 Modelos

```php
<?php

require_once __DIR__ . '/../config/database.php';

/**
 * Clase base para todos los modelos
 */
abstract class Model {
    protected $connection;

    public function __construct() {
        $dsn = DB_CONNECTION . ":host=" . DB_HOST . ";dbname=" . DB_DATABASE . ";charset=utf8mb4;";
        try {
            $this->connection = new PDO($dsn, DB_USERNAME, DB_PASSWORD);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Error en la conexión: " . $e->getMessage());
        }
    }

    public function __destruct() {
        $this->connection = null;
    }

    /**
     * Ejecuta una consulta SELECT
     * @param string $sql Consulta SQL
     * @param array $params Parámetros preparados
     * @param bool $fetchAll true para múltiples registros, false para uno
     * @return array Array con 'success' y 'data'
     */
    protected function query(string $sql, array $params = [], bool $fetchAll = true): array {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);

            if ($fetchAll) {
                return ['success' => true, 'data' => $stmt->fetchAll()];
            } else {
                return ['success' => true, 'data' => $stmt->fetch()];
            }
        } catch (PDOException $e) {
            return [
                'success' => false,
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Inserta un registro
     */
    protected function insert(string $sql, array $params = []): array {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return ['success' => true, 'lastInsertId' => $this->connection->lastInsertId()];
        } catch (PDOException $e) {
            return ['success' => false, 'code' => $e->getCode(), 'message' => $e->getMessage()];
        }
    }
}

?>
```

**Estándares de Modelos:**

| Aspecto | Estándar |
|--------|----------|
| Nombre | `{Entidad}Model` (ej: `UserModel`) |
| Herencia | Extiende `Model` (clase base) |
| Propiedades | `protected` para conexión y datos |
| Métodos | Público para operaciones CRUD, protected para helpers |
| Responsabilidad | Consultas a BD y lógica de datos |

### 5.3 Vistas

```php
<!-- ✅ CORRECTO -->
<div class="container">
    <h1><?php echo htmlspecialchars($title); ?></h1>
    <p><?php foreach ($items as $item): ?>
        <li><?php echo htmlspecialchars($item['name']); ?></li>
    <?php endforeach; ?></p>
</div>

<!-- ❌ INCORRECTO - Lógica compleja en vista -->
<div class="container">
    <h1><?php echo $title; ?></h1>
    <?php 
        $items = $controller->getItems();
        // ... más lógica aquí
    ?>
</div>
```

**Estándares de Vistas:**

| Aspecto | Estándar |
|--------|----------|
| Ubicación | `src/app/views/{rol}/{nombre}.php` |
| Variables | Pasadas desde el controlador |
| Lógica | `foreach`, `if` simples solamente |
| Escape de datos | Usar `htmlspecialchars()` para XSS |
| Includes | Usar rutas relativas o constantes |

---

## 6. Base de Datos

### 6.1 Conexión

```php
// ✅ CORRECTO - Usar prepared statements
$sql = "SELECT * FROM usuarios WHERE id = :id AND status = :status";
$stmt = $connection->prepare($sql);
$stmt->execute(['id' => $userId, 'status' => 'activo']);
$result = $stmt->fetchAll();

// ❌ INCORRECTO - SQL Injection
$sql = "SELECT * FROM usuarios WHERE id = " . $userId;
$stmt = $connection->query($sql);
```

**Reglas:**
- Siempre usar **prepared statements** con parámetros
- Nunca concatenar variables en SQL
- Usar `:param` o `?` para placeholders

### 6.2 Nombres de Tablas y Columnas

```sql
-- ✅ CORRECTO - snake_case, plural para tablas
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    email_address VARCHAR(255) UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ❌ INCORRECTO
CREATE TABLE User (
    ID INT,
    firstName VARCHAR(100),
    LastName VARCHAR(100)
);
```

**Reglas:**
- Nombres en **snake_case** (minúsculas con guiones bajos)
- Tablas en **plural** (usuarios, productos)
- Columnas descriptivas y completas
- Incluir timestamps (`created_at`, `updated_at`)

### 6.3 Tipos de Datos

| Tipo | Uso |
|------|-----|
| `INT` | Números enteros (IDs, contadores) |
| `VARCHAR(255)` | Texto corto (nombres, emails) |
| `TEXT` | Texto largo (descripciones) |
| `DECIMAL(10,2)` | Números con decimales (precios) |
| `DATETIME` | Fechas y horas |
| `TIMESTAMP` | Automático con cambios |
| `BOOLEAN` | true/false |
| `JSON` | Datos estructurados |

### 6.4 Convenciones de Consultas

```php
// ✅ CORRECTO - Claro y legible
$sql = "
    SELECT u.id, u.name, COUNT(g.id) as guardias_count
    FROM usuarios u
    LEFT JOIN guardias g ON u.id = g.usuario_id
    WHERE u.status = :status
    GROUP BY u.id
";

// Usar alias cortos y consistentes

// ❌ INCORRECTO - Difícil de leer
$sql = "SELECT * FROM usuarios WHERE id IN (SELECT usuario_id FROM guardias WHERE MONTH(fecha)=MONTH(NOW()))";
```

---

## 7. Frontend (HTML, CSS, JavaScript)

### 7.1 HTML

```html
<!-- ✅ CORRECTO -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Título Descriptivo</title>
    <link rel="stylesheet" href="<?php echo CSS_URL; ?>app.css">
</head>
<body>
    <div class="container">
        <h1>Título Principal</h1>
        <p>Contenido</p>
    </div>
    <script src="<?php echo JS_URL; ?>app.js"></script>
</body>
</html>

<!-- ❌ INCORRECTO - HTML4, scripts en head, rutas hardcodeadas -->
<html>
<head>
    <title>Page</title>
    <script src="js/app.js"></script>
</head>
```

**Estándares HTML:**
- DOCTYPE HTML5
- `lang="es"` en tag `html`
- Meta charset UTF-8
- Meta viewport para responsive
- Scripts al final del body
- Usar constantes para rutas de assets

### 7.2 CSS

```css
/* ✅ CORRECTO - Selectores claros, BEM notation */
.btn {
    padding: 10px 20px;
    border-radius: 4px;
    cursor: pointer;
}

.btn--primary {
    background-color: #007bff;
    color: white;
}

.btn--primary:hover {
    background-color: #0056b3;
}

.navbar__item {
    list-style: none;
    margin: 0 10px;
}

/* ❌ INCORRECTO - IDs para estilos, selectores genéricos */
#button {
    padding: 10px 20px;
}

div span {
    color: red;
}
```

**Estándares CSS:**
- Usar **BEM** (Block Element Modifier) para clases
- Clases en **snake-case**
- Evitar IDs para estilos
- Organizar por componentes
- Mobile-first approach
- Variables CSS para colores/tamaños

### 7.3 JavaScript

```javascript
// ✅ CORRECTO - Modular y legible
const UserManager = {
    init() {
        this.cacheDom();
        this.bindEvents();
    },

    cacheDom() {
        this.$userList = document.getElementById('userList');
        this.$addBtn = document.getElementById('addBtn');
    },

    bindEvents() {
        this.$addBtn.addEventListener('click', () => this.addUser());
    },

    addUser() {
        const userData = this.getUserFormData();
        this.sendRequest('/api/users', 'POST', userData);
    },

    sendRequest(url, method, data) {
        fetch(url, {
            method: method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => this.handleResponse(data));
    }
};

document.addEventListener('DOMContentLoaded', () => UserManager.init());

// ❌ INCORRECTO - Global scope, inline code
<script>
    function addUser() {
        // lógica aquí
    }
</script>
```

**Estándares JavaScript:**
- Usar `const` por defecto, `let` si es necesario, evitar `var`
- Nombres en **camelCase**
- Separar en módulos/objetos
- Usar event listeners, no inline handlers
- Validar datos en cliente y servidor
- Error handling con try-catch

### 7.4 Estructura de Assets

```
public/assets/
├── css/
│   ├── app.css           # Estilos generales
│   ├── fonts.css         # Tipos de letra
│   └── ...
├── js/
│   ├── app.js            # JS general
│   ├── admin/
│   │   └── home.js       # JS específico de sección
│   └── teacher/
│       └── ...
├── img/                  # Imágenes
├── fonts/                # Tipografías
└── vendor/               # Librerías externas (Bootstrap, jQuery, etc.)
```

---

## 8. Control de Versiones

### 8.1 Commits

```bash
# ✅ CORRECTO - Mensajes descriptivos
git commit -m "Agregar validación de email en registro"
git commit -m "Corregir problema de sesión en AdminController"
git commit -m "Refactorizar método getUsers() para mejorar rendimiento"

# ❌ INCORRECTO - Mensajes vagos
git commit -m "Fix bug"
git commit -m "Update stuff"
git commit -m "asfsadf"
```

**Formato de mensaje:**
```
[TIPO] Descripción breve (imperativo)

Descripción más detallada si es necesaria.
- Punto 1
- Punto 2
```

**Tipos:**
- `FEAT`: Nueva funcionalidad
- `FIX`: Corrección de bug
- `REFACTOR`: Cambio de código sin cambiar funcionalidad
- `DOCS`: Cambios en documentación
- `STYLE`: Formateo, sin cambios de lógica
- `PERF`: Mejora de rendimiento
- `TEST`: Agregar o modificar tests

### 8.2 Ramas

```bash
# ✅ CORRECTO - Nombres descriptivos y en kebab-case
git checkout -b feature/user-authentication
git checkout -b fix/email-validation-bug
git checkout -b refactor/model-structure
git checkout -b docs/setup-guide

# ❌ INCORRECTO
git checkout -b bugfix
git checkout -b mybranch
git checkout -b WIP_stuff
```

**Estructura:**
- `main` o `master`: Código en producción
- `develop`: Rama de desarrollo
- `feature/{nombre}`: Nuevas funcionalidades
- `fix/{nombre}`: Correcciones de bugs
- `refactor/{nombre}`: Refactorización

---

## 9. Seguridad

### 9.1 Autenticación

```php
// ✅ CORRECTO - Uso de sesiones seguras
function init_session(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
        // Considerar configurar:
        // ini_set('session.use_only_cookies', 1);
        // ini_set('session.http_only', true);
    }
}

function is_logged(): bool {
    init_session();
    return isset($_SESSION['user_id']);
}

function auth(): void {
    init_session();
    if (!isset($_SESSION['user_id'])) {
        redirect('login');
    }
}

// ❌ INCORRECTO - Sin validación
if ($username == 'admin' && $password == 'password') {
    $_SESSION['logged'] = true;
}
```

### 9.2 Validación de Entrada

```php
// ✅ CORRECTO - Validar y desinfectar
function validateEmail(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
if (validateEmail($email)) {
    // Procesar
}

// ❌ INCORRECTO - Confiar en entrada del usuario
if (!empty($_POST['email'])) {
    $email = $_POST['email'];
    // Usar directamente
}
```

### 9.3 Prevención de XSS

```php
// ✅ CORRECTO - Escapar con htmlspecialchars
<?php echo htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8'); ?>

// ❌ INCORRECTO - Sin escapar
<?php echo $user['name']; ?>
```

### 9.4 SQL Injection

```php
// ✅ CORRECTO - Prepared statements
$stmt = $connection->prepare("SELECT * FROM users WHERE email = :email");
$stmt->execute(['email' => $userEmail]);

// ❌ INCORRECTO - Query concatenada
$sql = "SELECT * FROM users WHERE email = '" . $userEmail . "'";
$stmt = $connection->query($sql);
```

### 9.5 CSRF Protection

```php
// ✅ CORRECTO - Generar y validar token CSRF
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// En formulario:
<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

// Validar:
if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('CSRF token validation failed');
}
```

---

## 10. Testing y Debugging

### 10.1 Debugging

```php
// ✅ CORRECTO - Usar var_dump con <pre> para desarrollo
echo '<pre>';
var_dump($data);
echo '</pre>';

// O mejor, usar xdebug en producción

// ❌ INCORRECTO - Dejar dumps de debug en producción
echo json_encode($internalData);
```

### 10.2 Error Handling

```php
// ✅ CORRECTO - Manejar errores apropiadamente
try {
    $stmt = $this->connection->prepare($sql);
    $stmt->execute($params);
} catch (PDOException $e) {
    error_log('DB Error: ' . $e->getMessage());
    return ['success' => false, 'message' => 'Error en la base de datos'];
}

// ❌ INCORRECTO - Mostrar errores en producción
die("Error: " . $e->getMessage());
```

### 10.3 Logging

```php
// ✅ CORRECTO - Registrar eventos importantes
error_log("Usuario " . $userId . " inició sesión");
error_log("Error en query: " . $e->getMessage(), 3, "/var/log/app.log");

// Para futuros logs:
// Implementar un logger (Monolog, etc.)
```

---

## 11. Checklist de Calidad

Antes de hacer commit, verificar:

- [ ] El código sigue los estándares de nombres
- [ ] Las funciones tienen type hints y documentación
- [ ] No hay código duplicado (DRY principle)
- [ ] Variables bien nombradas y significativas
- [ ] Máximo 120 caracteres por línea
- [ ] Indentación consistente (4 espacios)
- [ ] Sin variables/funciones sin usar
- [ ] Sin debug/var_dump en el código
- [ ] Strings sensibles no están hardcodeados
- [ ] Uso correcto de prepared statements
- [ ] Datos escapados en vistas (XSS prevention)
- [ ] Mensajes de commit descriptivos
- [ ] Tests pasando (si aplica)

---

## 12. Herramientas Recomendadas

| Herramienta | Propósito | Instalación |
|-------------|----------|-------------|
| **PHP_CodeSniffer** | Validar estándares PHP | `composer require squizlabs/php_codesniffer` |
| **PHPStan** | Análisis estático de código | `composer require phpstan/phpstan` |
| **Prettier** | Formateo de código | `npm install prettier` |
| **ESLint** | Linting de JavaScript | `npm install eslint` |
| **Xdebug** | Debugger para PHP | Extensión de PHP |

---

## 13. Contacto y Actualizaciones

- **Última revisión**: Abril 2026
- **Próxima revisión**: Julio 2026
- **Responsable**: Equipo de Desarrollo
- **Para sugerencias**: Crear issue o discutir en reunión de equipo

---

**Documento versión 1.0 - Válido a partir de Abril 2026**
