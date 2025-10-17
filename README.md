# Proyecto: Sistema de Gestión de Averías

## Descripción
Este proyecto es un sistema simple para registrar y listar averías reportadas por clientes.  
Se implementa con **CodeIgniter 4**, utilizando migraciones, seeders, vistas HTML y una librería de notificaciones simulada.

---

## Tarea Realizada

Se han completado las siguientes tareas:

1. **Creación de la Base de Datos**  
   - Base de datos: `WOWDB`  
   - Configurada en `.env` para conexión con MySQL.

2. **Migración: tabla `averias`**  
   - Campos:
     - `id` INT AUTO_INCREMENT PK
     - `cliente` VARCHAR(50)
     - `problema` VARCHAR(100)
     - `fechahora` DATETIME
     - `status` ENUM('pendiente','solucionado') DEFAULT 'pendiente'
   - Migración ejecutada exitosamente con `php spark migrate`.

3. **Seeder: 3 registros de prueba**  
   - Se insertaron 3 averías iniciales mediante `AveriasSeeder`.
   - Comando utilizado: `php spark db:seed AveriasSeeder`.

4. **Estructura general del proyecto**
   - **Migraciones:** `app/Database/Migrations/CreateAveriasTable.php`  
   - **Seeders:** `app/Database/Seeds/AveriasSeeder.php`  
   - **Modelo:** `app/Models/AveriaModel.php`  
   - **Controlador:** `app/Controllers/Averias.php`  
   - **Vistas HTML:**  
     - `app/Views/averias/registrar.php` → formulario de registro  
     - `app/Views/averias/listar.php` → lista de averías pendientes  
   - **Rutas:** definidas en `app/Config/Routes.php`  
     - `/averias` → listar  
     - `/averias/registrar` → registrar nueva  
     - `/averias/guardar` → guardar en DB  
   - **Librería de notificaciones (socket simulado):** `app/Libraries/Notify.php`  
   - **JS de alerta:** integración básica para notificar nueva avería.

---