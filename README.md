# 🛡️ Sistema de Gestión de Guardias - Fundación Loyola

Sistema integral diseñado para la automatización, coordinación y seguimiento de las guardias docentes y ausencias del profesorado. La plataforma centraliza la comunicación entre el equipo directivo y el claustro para garantizar que ninguna clase quede desatendida.

---

## 📌 Índice
1. [Sobre el Proyecto](#sobre-el-proyecto)
2. [Estructura del Repositorio](#estructura-del-repositorio)
3. [Instalación](#instalación)

---

## 📖 Sobre el Proyecto
Este software nace de la necesidad de optimizar la logística escolar. Permite a los profesores notificar ausencias y adjuntar material pedagógico automáticamente, mientras que los administradores disponen de un panel inteligente para asignar sustitutos basados en la carga de trabajo y disponibilidad en tiempo real.

## 📂 Estructura del Repositorio
El proyecto sigue una arquitectura de separación de intereses para facilitar el mantenimiento y la implementación:

* **`/diseno`**: Contiene la guía de estilo, bocetos y mockups de la interfaz (UI/UX).
* **`/doc`**: Documentación técnica, manual del programador e instrucciones de instalación.
* **`/sql`**: Scripts SQL para la creación y migración de la base de datos `substitution_db`.
* **`/src`**: Código fuente de la aplicación (entorno de producción).
    * `app/`: Núcleo del Framework (MVC, Helpers, Configuración).
    * `public/`: Punto de acceso único, assets (CSS/JS/Img) y gestión de archivos.
* **`/test`**: Plan de pruebas detallado y scripts de pruebas automáticas.

## 🛠️ Stack Tecnológico
* **Backend:** PHP 8.x (Arquitectura MVC).
* **Frontend:** HTML5, CSS3, JavaScript (jQuery 4.0.0, Bootstrap 5).
* **Base de Datos:** MySQL.
* **Componentes:** SweetAlert (Notificaciones visuales).

## 🚀 Instalación
El proyecto está preparado para funcionar tanto en **Apache** (vía `.htaccess`) como en **IIS** (vía `web.config`), soportando URLs limpias y redirección automática al punto de entrada.

### 1. Preparación Inicial
1. **Clonar el repositorio:** `git clone [url-del-repo]`
2. **Base de Datos:** Importar el script SQL ubicado en `/sql/` para crear la estructura de `substitution_db`.
3. **Configuración de PHP:** - Renombrar `src/app/config/database.php.example` a `database.php` y configurar credenciales.
   - Ajustar la constante `BASE_URL` en `src/app/config/app.php`.

### 2. Configuración en Apache
El proyecto incluye una estructura de redirección de dos niveles:
* **Raíz (`/src/`):** El `.htaccess` redirige automáticamente todo el tráfico a la carpeta `public/`.
* **Punto de entrada (`/src/public/`):** El `.htaccess` interno gestiona las URLs amigables, enviando las peticiones al `index.php` mediante el parámetro `url`.

> **Nota:** Asegúrate de que el módulo `mod_rewrite` esté activado y que la directiva `AllowOverride All` esté configurada para el directorio del proyecto.

### 3. Configuración en IIS
1. **Physical Path:** En el Administrador de IIS, puedes apuntar el sitio directamente a la carpeta `src/public` o a la raíz `src/`.
2. **URL Rewrite:** El archivo `web.config` incluido en la carpeta `public` se encargará de las reglas de reescritura de forma equivalente a Apache.
3. **Módulo necesario:** Es imprescindible tener instalado el módulo **URL Rewrite** en IIS.

### 4. Permisos de Carpeta
Es necesario otorgar permisos de lectura y escritura en la carpeta de subidas para que los profesores puedan adjuntar justificantes y fotos:
* **IIS:** Usuario `IIS_IUSRS`.
* **Apache (Linux):** Usuario `www-data`.
* **Ruta:** `src/public/uploads/`

---
**Desarrollado para:** Fundación Loyola  
**Estado del proyecto:** En desarrollo 🛠️