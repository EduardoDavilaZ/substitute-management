# Ejemplos Prácticos - Antes y Después

## 1. Nombres de Variables y Funciones

### ❌ INCORRECTO
```php
<?php
class Admin {
    public $v;
    public $m;
    public $l = 'admin/layout';

    public function __construct() {
        $this->v = '';
        $this->m = '';
    }

    public function getd() {
        // obtener datos
    }

    public function proc_data($d) {
        $x = $d['id'];
        $y = $d['name'];
        return ['s' => true, 'd' => $x];
    }
}
?>
```

### ✅ CORRECTO
```php
<?php
/**
 * Controlador de administrador del sistema
 */
class AdminController {
    public $view;
    public $message;
    public $layout = 'admin/layout';

    public function __construct() {
        $this->view = '';
        $this->message = '';
    }

    /**
     * Obtiene los datos del administrador
     * @return array
     */
    public function getData() {
        // obtener datos
    }

    /**
     * Procesa los datos recibidos
     * @param array $data Datos a procesar
     * @return array Array con éxito y datos procesados
     */
    public function processData(array $data): array {
        $userId = $data['id'];
        $userName = $data['name'];
        return ['success' => true, 'data' => $userId];
    }
}
?>
```

---

## 2. Type Hints y Documentación

### ❌ INCORRECTO
```php
<?php
function validateUser($user) {
    if (!empty($user['email'])) {
        return true;
    }
    return false;
}

function findUser($id) {
    // ...
}
?>
```

### ✅ CORRECTO
```php
<?php
/**
 * Valida si un usuario tiene datos requeridos
 * 
 * @param array $user Array con datos del usuario
 * @return bool true si usuario es válido, false en caso contrario
 */
function validateUser(array $user): bool {
    return !empty($user['email']) && !empty($user['name']);
}

/**
 * Busca un usuario por su ID
 * 
 * @param int $id ID del usuario
 * @return array|null Array con datos del usuario o null si no existe
 */
function findUser(int $id): ?array {
    // ...
}
?>
```

---

## 3. Consultas a Base de Datos

### ❌ INCORRECTO - SQL Injection Risk
```php
<?php
class UserModel extends Model {
    public function getUserByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = '" . $email . "'";
        return $this->connection->query($sql);
    }

    public function searchUsers($name) {
        $sql = "SELECT * FROM users WHERE name LIKE '%" . $name . "%'";
        return $this->connection->query($sql);
    }
}
?>
```

### ✅ CORRECTO - Prepared Statements
```php
<?php
class UserModel extends Model {
    /**
     * Obtiene un usuario por email
     * @param string $email Email del usuario
     * @return array
     */
    public function getUserByEmail(string $email): array {
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        return $this->query($sql, ['email' => $email], false);
    }

    /**
     * Busca usuarios por nombre
     * @param string $name Texto a buscar
     * @return array
     */
    public function searchUsers(string $name): array {
        $sql = "SELECT * FROM users WHERE name LIKE :search ORDER BY name ASC";
        return $this->query($sql, ['search' => '%' . $name . '%'], true);
    }
}
?>
```

---

## 4. Lógica en Controladores

### ❌ INCORRECTO - Lógica mezclada
```php
<?php
class ProductController {
    public function show() {
        $id = $_GET['id'];
        
        // Lógica de BD en controlador
        $sql = "SELECT * FROM products WHERE id = '" . $id . "'";
        $result = mysqli_query($connection, $sql);
        $product = mysqli_fetch_assoc($result);
        
        // Lógica de validación
        if (!$product) {
            echo "Producto no encontrado";
            return;
        }
        
        // Lógica de view
        $this->view = 'products/show';
        $this->product = $product;
    }
}
?>
```

### ✅ CORRECTO - Separación de responsabilidades
```php
<?php
class ProductController {
    public $view;
    public $data;
    public $layout = 'default/layout';

    /**
     * Muestra un producto específico
     */
    public function show(): void {
        $productId = (int) $_GET['id'] ?? 0;

        if ($productId === 0) {
            $this->view = 'errors/invalid_product';
            return;
        }

        $productModel = new ProductModel();
        $result = $productModel->findById($productId);

        if (!$result['success'] || empty($result['data'])) {
            $this->view = 'errors/product_not_found';
            return;
        }

        $this->view = 'products/show';
        $this->data = $result['data'];
    }
}

class ProductModel extends Model {
    /**
     * Encuentra un producto por ID
     * @param int $id ID del producto
     * @return array
     */
    public function findById(int $id): array {
        $sql = "SELECT * FROM products WHERE id = :id LIMIT 1";
        return $this->query($sql, ['id' => $id], false);
    }
}
?>
```

---

## 5. Validación en Vistas

### ❌ INCORRECTO - XSS Vulnerability
```php
<div class="container">
    <h1><?php echo $title; ?></h1>
    <p><?php echo $description; ?></p>
    <p>Usuario: <?php echo $_SESSION['user_name']; ?></p>
    
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <input type="text" value="<?php echo $_POST['username']; ?>">
    </form>
</div>
```

### ✅ CORRECTO - Escape Output
```php
<div class="container">
    <h1><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h1>
    <p><?php echo htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?></p>
    <p>Usuario: <?php echo htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8'); ?></p>
    
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8'); ?>" method="POST">
        <input type="text" value="<?php echo htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
    </form>
</div>
```

---

## 6. Helpers y Funciones Reutilizables

### ❌ INCORRECTO - Código duplicado
```php
<?php
// En AuthController
class AuthController {
    public function login() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            redirect('');
        }
    }
}

// En AdminController
class AdminController {
    public function home() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            redirect('');
        }
    }
}
?>
```

### ✅ CORRECTO - DRY Principle
```php
<?php
// auth_helper.php
/**
 * Inicia la sesión si no ha sido iniciada
 * @return void
 */
function init_session(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Middleware: Redirige al login si no está autenticado
 * @return void
 */
function auth(): void {
    init_session();
    
    if (!isset($_SESSION['user_id'])) {
        redirect('');
    }
}

/**
 * Verifica si el usuario está loguado
 * @return bool
 */
function is_logged(): bool {
    init_session();
    return isset($_SESSION['user_id']);
}

// En cualquier controlador
class AuthController {
    public function login() {
        auth(); // Una línea!
    }
}

class AdminController {
    public function home() {
        auth(); // Una línea!
    }
}
?>
```

---

## 7. Manejo de Errores

### ❌ INCORRECTO - Sin manejo
```php
<?php
class UserModel extends Model {
    public function createUser(array $data): void {
        $sql = "INSERT INTO users (name, email) VALUES (:name, :email)";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['name' => $data['name'], 'email' => $data['email']]);
    }
}

// En controlador
$userModel->createUser($userData); // ¿Y si falla?
echo "Usuario creado";
?>
```

### ✅ CORRECTO - Con manejo de errores
```php
<?php
class UserModel extends Model {
    /**
     * Crea un nuevo usuario
     * @param array $data Array con name y email
     * @return array
     */
    public function createUser(array $data): array {
        $sql = "INSERT INTO users (name, email) VALUES (:name, :email)";
        return $this->insert($sql, [
            'name' => $data['name'],
            'email' => $data['email']
        ]);
    }
}

// En controlador
$userModel = new UserModel();
$result = $userModel->createUser($userData);

if ($result['success']) {
    $this->message = "Usuario creado exitosamente";
    $this->view = 'users/show';
} else {
    $this->message = "Error al crear usuario: " . $result['message'];
    $this->view = 'errors/500';
    error_log("DB Error: " . $result['message']);
}
?>
```

---

## 8. CSS con BEM

### ❌ INCORRECTO - IDs, selectores genéricos
```css
#button {
    padding: 10px;
    background: blue;
}

#button:hover {
    background: darkblue;
}

.form input {
    border: 1px solid gray;
}

.form input:focus {
    border-color: blue;
}

div span {
    color: red;
}
```

### ✅ CORRECTO - BEM Notation
```css
/* Block */
.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 1rem;
}

/* Element */
.btn__icon {
    margin-right: 5px;
}

/* Modifier */
.btn--primary {
    background-color: #007bff;
    color: white;
}

.btn--primary:hover {
    background-color: #0056b3;
}

.btn--secondary {
    background-color: #6c757d;
    color: white;
}

.btn--large {
    padding: 15px 30px;
    font-size: 1.2rem;
}

/* Form */
.form {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.form__field {
    display: flex;
    flex-direction: column;
}

.form__label {
    margin-bottom: 0.5rem;
    font-weight: bold;
}

.form__input {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
}

.form__input:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25);
}
```

---

## 9. JavaScript Modular

### ❌ INCORRECTO - Global scope, inline
```html
<button onclick="deleteUser(123)">Eliminar</button>

<script>
    function deleteUser(id) {
        fetch('/api/users/' + id, {method: 'DELETE'})
            .then(r => r.json())
            .then(d => alert(d.message));
    }

    // Contaminación del scope global
    var userId = 123;
    var userName = "John";
</script>
```

### ✅ CORRECTO - Módulo encapsulado
```html
<div id="userManager">
    <button class="btn-delete" data-user-id="123">Eliminar</button>
</div>

<script>
const UserManager = {
    selectors: {
        deleteBtn: '.btn-delete',
        userList: '#userList'
    },

    init() {
        this.cacheDom();
        this.bindEvents();
    },

    cacheDom() {
        this.$container = document.getElementById('userManager');
        this.$deleteBtn = this.$container.querySelector(this.selectors.deleteBtn);
    },

    bindEvents() {
        if (this.$deleteBtn) {
            this.$deleteBtn.addEventListener('click', (e) => this.deleteUser(e));
        }
    },

    deleteUser(e) {
        const userId = e.target.dataset.userId;
        const confirmed = confirm('¿Está seguro?');

        if (!confirmed) return;

        this.sendRequest(`/api/users/${userId}`, 'DELETE')
            .then(response => this.handleSuccess(response))
            .catch(error => this.handleError(error));
    },

    sendRequest(url, method, data = null) {
        const options = {
            method: method,
            headers: {
                'Content-Type': 'application/json'
            }
        };

        if (data) {
            options.body = JSON.stringify(data);
        }

        return fetch(url, options).then(response => {
            if (!response.ok) throw new Error('Network error');
            return response.json();
        });
    },

    handleSuccess(response) {
        console.log('Éxito:', response.message);
        this.$deleteBtn.closest('tr').remove();
    },

    handleError(error) {
        console.error('Error:', error);
        alert('Ocurrió un error. Intente nuevamente.');
    }
};

// Inicializar cuando DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    UserManager.init();
});
</script>
```

---

## 10. Git Commits

### ❌ INCORRECTO - Mensajes vagos
```bash
git commit -m "fix"
git commit -m "update"
git commit -m "asfsadf changes"
git commit -m "WIP"
git commit -m "cambios varios"
```

### ✅ CORRECTO - Mensajes descriptivos
```bash
git commit -m "FEAT: Agregar autenticación con sesiones"

git commit -m "FIX: Corregir bug en validación de email"

git commit -m "REFACTOR: Reorganizar UserModel para mejorar legibilidad"

git commit -m "DOCS: Agregar documentación de API"

git commit -m "STYLE: Aplicar estándares de formateo PHP"

git commit -m "PERF: Optimizar consulta de usuarios con índices"

# Con descripción detallada
git commit -m "FEAT: Implementar sistema de notificaciones

- Crear tabla de notificaciones en BD
- Agregar Helper para envío de emails
- Implementar vista de notificaciones en panel
- Agregar tests unitarios"
```

---

## Conclusión

La clave es:
1. **Nombres claros** que describan el propósito
2. **Type hints** que eviten errores
3. **Documentación** para explicar el por qué
4. **Separación de responsabilidades** (MVC)
5. **Seguridad primero** (validación, escape, prepared statements)
6. **DRY** (reutilizar, no duplicar)
7. **Consistencia** en todo el proyecto
