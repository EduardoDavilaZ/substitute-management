# Guía Rápida de Referencia
## Estándares de Codificación - Proyecto Guardias

### Convenciones de Nombres

```
Clases:       PascalCase        → AdminController, UserModel
Métodos:      camelCase         → getUserData(), validateEmail()
Variables:    camelCase         → $userId, $userEmail
Constantes:   UPPERCASE_SNAKE   → BASE_PATH, MAX_UPLOAD_SIZE
Archivos:     Clases:PascalCase, Helpers/Vistas:snake_case
```

### Estructura de Archivos

```
Controllers/  → Controladores MVC (extienden nada por ahora)
Models/       → Modelos (extienden Model base)
helpers/      → Funciones reutilizables
views/        → Templates HTML (variables desde controller)
config/       → Configuración y constantes
```

### PHP - Lo Más Importante

```php
<?php // Apertura obligatoria
// Cierre opcional si hay HTML después

// Type hints obligatorios
public function query(string $sql, array $params = []): array {}
function init_session(): void {}

// PhpDoc para funciones públicas
/**
 * Descripción breve
 * @param string $email Email del usuario
 * @return bool
 */
function validateEmail(string $email): bool {}

// Spacing: 4 espacios, máx 120 chars/línea
if ($condition) {
    doSomething();
}
?>
```

### Prepared Statements (SIEMPRE)

```php
// ✅ CORRECTO
$stmt = $connection->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute(['id' => $userId]);

// ❌ NUNCA
$sql = "SELECT * FROM users WHERE id = $userId";
```

### HTML en Vistas

```php
<!-- Variables escapadas -->
<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>

<!-- Loops simples -->
<?php foreach ($items as $item): ?>
    <li><?php echo htmlspecialchars($item['name']); ?></li>
<?php endforeach; ?>

<!-- Usar constantes para assets -->
<link rel="stylesheet" href="<?php echo CSS_URL; ?>app.css">
```

### Base de Datos

```sql
-- Tabla plural, snake_case
users, products, daily_substitutions

-- Columnas snake_case
user_id, first_name, email_address, created_at

-- Siempre incluir timestamps
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
```

### JavaScript

```javascript
const UserManager = {
    init() {
        this.cacheDom();
        this.bindEvents();
    },
    
    cacheDom() {
        this.$element = document.getElementById('id');
    },
    
    bindEvents() {
        this.$element.addEventListener('click', () => this.handleClick());
    },
    
    handleClick() {
        // Acción aquí
    }
};

document.addEventListener('DOMContentLoaded', () => UserManager.init());
```

### CSS - BEM

```css
/* Bloque */
.card { }

/* Elemento */
.card__title { }
.card__content { }

/* Modificador */
.card--featured { }
```

### Git Commits

```bash
[TIPO] Descripción (máx 50 chars)

Descripción detallada
- Punto 1
- Punto 2

# Tipos: FEAT, FIX, REFACTOR, DOCS, STYLE, PERF, TEST
```

### Ramas

```
main/master    → Producción
develop        → Desarrollo
feature/x      → Nueva funcionalidad
fix/x          → Corrección
refactor/x     → Refactorización
```

### Checklist Pre-Commit

- [ ] Nombres: PascalCase (clases), camelCase (métodos/variables)
- [ ] Type hints en funciones
- [ ] PhpDoc en públicas
- [ ] Prepared statements para BD
- [ ] htmlspecialchars() en vistas
- [ ] Sin hardcoded secrets
- [ ] Sin console.log, var_dump, print_r
- [ ] 4 espacios indentación
- [ ] Máx 120 caracteres/línea
- [ ] DRY (sin duplicados)

### Seguridad

```php
// Sesiones
init_session();  // Iniciar siempre
isset($_SESSION['user_id'])  // Validar login

// Input
filter_input(INPUT_POST, 'field', FILTER_SANITIZE_STRING)
validateEmail($email)

// Output
htmlspecialchars($data, ENT_QUOTES, 'UTF-8')

// BD
$stmt->execute(['param' => $value])  // Prepared always
```

### Atajos Útiles

| Problema | Solución |
|----------|----------|
| `Undefined variable` | Inicializar o validar isset() |
| `Database error` | Usar try-catch, prepared statements |
| `XSS vulnerability` | htmlspecialchars() en vistas |
| `SQL injection` | Prepared statements, NUNCA concatenar |
| `CSRF attack` | Validar token CSRF en formularios |

---

**Consejo**: Si algo no está claro, consulta ESTANDARES_CODIFICACION.md para la documentación completa.
