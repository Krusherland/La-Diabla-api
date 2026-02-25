# 🍕 La Diabla - API REST para Pizzería

![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![JWT](https://img.shields.io/badge/JWT-Auth-000000?style=for-the-badge&logo=jsonwebtokens&logoColor=white)

API REST completa para gestión de pizzería con autenticación JWT, sistema de pedidos online, gestión de productos y panel de administración. Desarrollada con Laravel 11 siguiendo los patrones de arquitectura de **soy-api** y la lógica de negocio de **Project-Il-Napolitano**.

[![Estado del Proyecto](https://img.shields.io/badge/Estado-Portfolio_Project-success?style=flat-square)]()
[![Licencia](https://img.shields.io/badge/Licencia-Educational-blue?style=flat-square)]()

---

## 📑 Tabla de Contenidos

- [Descripción](#-descripción)
- [Características](#-características)
- [Tecnologías](#-tecnologías-utilizadas)
- [Estructura del Proyecto](#-estructura-del-proyecto)
- [Instalación](#️-instalación)
- [Credenciales de Demo](#-credenciales-de-demo)
- [Endpoints de la API](#-endpoints-de-la-api)
- [Modelos y Relaciones](#-modelos-y-relaciones)
- [Ejemplos de Uso](#-ejemplos-de-uso)

---

## 📋 Descripción

**La Diabla** es una API REST completa para una pizzería que permite:

- 🔐 **Autenticación JWT** - Sistema de usuarios con roles (admin/customer)
- 🍕 **Gestión de Productos** - CRUD completo con categorías, precios y descuentos
- 📦 **Sistema de Pedidos** - Procesamiento completo de órdenes con seguimiento de estados
- 👨‍💼 **Panel de Administración** - Endpoints protegidos para gestión total del negocio
- 📊 **Estadísticas** - Métricas de ventas, pedidos y análisis de negocio

---

## ✨ Características

### Para Clientes
- ✅ Registro y autenticación de usuarios
- ✅ Catálogo de productos por categorías
- ✅ Búsqueda y filtrado de productos
- ✅ Creación de pedidos online
- ✅ Seguimiento de pedidos por ID o email
- ✅ Gestión de perfil de usuario

### Para Administradores
- ✅ **Gestión de Categorías**: CRUD completo
- ✅ **Gestión de Productos**: CRUD con imágenes, stock y descuentos
- ✅ **Gestión de Pedidos**: Actualización de estados y pagos
- ✅ **Estadísticas**: Panel con métricas en tiempo real
- ✅ **Control de Stock**: Reducción automática al procesar pedidos

---

## 🚀 Tecnologías Utilizadas

### Backend
- **Laravel 11** - Framework PHP moderno
- **PHP 8.2+** - Programación orientada a objetos
- **MySQL** - Base de datos relacional
- **JWT (Firebase)** - Autenticación con tokens

### Arquitectura
- **RESTful API** - Arquitectura de servicios REST
- **MVC Pattern** - Separación de responsabilidades
- **Eloquent ORM** - Gestión de base de datos
- **Middleware** - Control de autenticación y autorización
- **Validación de Datos** - Laravel Validator
- **Transacciones DB** - Integridad de datos en pedidos

---

## 📁 Estructura del Proyecto

```
la-diabla/
├── app/
│   ├── Helpers/
│   │   └── JwtAuth.php              # Helper para JWT
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php   # Autenticación
│   │   │   ├── CategoryController.php
│   │   │   ├── ProductController.php
│   │   │   └── OrderController.php
│   │   └── Middleware/
│   │       ├── JwtMiddleware.php    # Protección de rutas
│   │       └── AdminMiddleware.php  # Rol de administrador
│   └── Models/
│       ├── User.php
│       ├── Category.php
│       ├── Product.php
│       ├── Order.php
│       └── OrderItem.php
├── database/
│   ├── migrations/
│   │   ├── create_categories_table.php
│   │   ├── create_products_table.php
│   │   ├── create_orders_table.php
│   │   └── create_order_items_table.php
│   └── seeders/
│       └── DatabaseSeeder.php       # Datos de prueba
├── routes/
│   └── api.php                      # Rutas de la API
└── README.md
```

---

## 🛠️ Instalación

### Requisitos Previos
- PHP 8.2 o superior
- Composer
- MySQL 8.0+
- XAMPP o servidor similar

### Pasos de Instalación

1. **Clonar o navegar al proyecto**
   ```bash
   cd c:\xampp\htdocs\la-diabla
   ```

2. **Instalar dependencias**
   ```bash
   composer install
   ```

3. **Configurar variables de entorno**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configurar base de datos en `.env`**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=la_diabla
   DB_USERNAME=root
   DB_PASSWORD=
   
   JWT_SECRET=la-diabla-secret-key-pizzeria-2026
   ```

5. **Crear base de datos**
   ```sql
   CREATE DATABASE la_diabla;
   ```

6. **Ejecutar migraciones y seeders**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

7. **Crear enlace simbólico para almacenamiento**
   ```bash
   php artisan storage:link
   ```

8. **Iniciar servidor**
   ```bash
   php artisan serve
   ```

La API estará disponible en: `http://localhost:8000/api`

---

## 🔑 Credenciales de Demo

### Usuario Administrador
```
Email: admin@ladiabla.com
Password: diabla2026
Role: admin
```

### Usuario Cliente
```
Email: cliente@ladiabla.com
Password: cliente2026
Role: customer
```

> **Nota**: Estas credenciales son para propósitos de demostración del portfolio. Cambiar en producción.

---

## 📡 Endpoints de la API

### Autenticación

| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| POST | `/api/auth/register` | Registrar nuevo usuario | No |
| POST | `/api/auth/login` | Iniciar sesión | No |
| GET | `/api/auth/profile` | Obtener perfil | Sí |
| PUT | `/api/auth/profile` | Actualizar perfil | Sí |

### Categorías

| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| GET | `/api/categories` | Listar categorías activas | No |
| GET | `/api/categories/{id}` | Ver categoría con productos | No |
| GET | `/api/admin/categories` | Listar todas (admin) | Admin |
| POST | `/api/admin/categories` | Crear categoría | Admin |
| PUT | `/api/admin/categories/{id}` | Actualizar categoría | Admin |
| DELETE | `/api/admin/categories/{id}` | Eliminar categoría | Admin |

### Productos

| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| GET | `/api/products` | Listar productos activos | No |
| GET | `/api/products/{id}` | Ver detalles de producto | No |
| GET | `/api/admin/products` | Listar todos (admin) | Admin |
| POST | `/api/admin/products` | Crear producto | Admin |
| PUT | `/api/admin/products/{id}` | Actualizar producto | Admin |
| DELETE | `/api/admin/products/{id}` | Eliminar producto | Admin |

**Filtros disponibles en GET `/api/products`:**
- `category_id` - Filtrar por categoría
- `search` - Buscar en nombre/descripción
- `min_price` / `max_price` - Rango de precios
- `has_discount=true` - Solo productos con descuento

### Pedidos

| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| POST | `/api/orders` | Crear nuevo pedido | No |
| GET | `/api/orders/{id}` | Ver detalles de pedido | No |
| POST | `/api/orders/track` | Rastrear pedido | No |
| GET | `/api/admin/orders` | Listar pedidos (admin) | Admin |
| PUT | `/api/admin/orders/{id}/status` | Actualizar estado | Admin |
| DELETE | `/api/admin/orders/{id}` | Eliminar pedido | Admin |
| GET | `/api/admin/orders/statistics` | Estadísticas | Admin |

**Filtros disponibles en GET `/api/admin/orders`:**
- `order_status` - pendiente, preparando, enviado, entregado, cancelado
- `payment_status` - pendiente, pagado, rechazado
- `payment_method` - efectivo, tarjeta, transferencia
- `date_from` / `date_to` - Rango de fechas
- `search` - Buscar por nombre/email/teléfono

---

## 🗄️ Modelos y Relaciones

### User
```php
- id, name, email, password, role, phone, address
- Roles: 'admin', 'customer'
```

### Category
```php
- id, name, description, is_active
- Relaciones: hasMany(Product)
```

### Product
```php
- id, category_id, name, description, price, image, stock
- has_discount, discount_percentage, is_active
- Relaciones: belongsTo(Category), hasMany(OrderItem)
- Computed: final_price (precio con descuento aplicado)
```

### Order
```php
- id, customer_name, customer_email, customer_phone
- delivery_address, payment_method, payment_status
- order_status, subtotal, delivery_fee, total, notes
- Relaciones: hasMany(OrderItem)
```

### OrderItem
```php
- id, order_id, product_id, product_name
- product_price, quantity, subtotal
- Relaciones: belongsTo(Order), belongsTo(Product)
```

---

## 💡 Ejemplos de Uso

### 1. Login de Administrador

```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@ladiabla.com",
    "password": "diabla2026"
  }'
```

**Respuesta:**
```json
{
  "status": "success",
  "message": "Login successful",
  "user": {
    "id": 1,
    "name": "Admin Demo",
    "email": "admin@ladiabla.com",
    "role": "admin"
  },
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc..."
}
```

### 2. Listar Productos

```bash
curl -X GET "http://localhost:8000/api/products?category_id=1"
```

### 3. Crear Pedido

```bash
curl -X POST http://localhost:8000/api/orders \
  -H "Content-Type: application/json" \
  -d '{
    "customer_name": "Juan Pérez",
    "customer_email": "juan@example.com",
    "customer_phone": "+34 666 777 888",
    "delivery_address": "Calle Mayor 45, Madrid",
    "payment_method": "tarjeta",
    "delivery_fee": 3.50,
    "notes": "Sin cebolla en la pizza",
    "items": [
      {
        "product_id": 1,
        "quantity": 2
      },
      {
        "product_id": 7,
        "quantity": 1
      }
    ]
  }'
```

### 4. Actualizar Estado de Pedido (Admin)

```bash
curl -X PUT http://localhost:8000/api/admin/orders/1/status \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -d '{
    "order_status": "preparando",
    "payment_status": "pagado"
  }'
```

### 5. Crear Producto (Admin)

```bash
curl -X POST http://localhost:8000/api/admin/products \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -F "category_id=1" \
  -F "name=Pizza Carbonara" \
  -F "description=Pizza con bacon, huevo y nata" \
  -F "price=12.99" \
  -F "stock=25" \
  -F "image=@/path/to/image.jpg"
```

### 6. Obtener Estadísticas (Admin)

```bash
curl -X GET http://localhost:8000/api/admin/orders/statistics \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

---

## 🔒 Seguridad

- **JWT Authentication**: Tokens con expiración de 7 días
- **Password Hashing**: Bcrypt para contraseñas
- **Role-Based Access Control**: Middleware para rutas de admin
- **Validación de Datos**: Laravel Validator en todos los endpoints
- **PDO Prepared Statements**: Protección contra SQL Injection (Eloquent ORM)
- **CORS**: Configurar según necesidades del frontend

---

## 📊 Estados del Sistema

### Estados de Pedido (order_status)
- `pendiente` - Pedido recibido, esperando confirmación
- `preparando` - Pedido en preparación
- `enviado` - Pedido en camino
- `entregado` - Pedido completado
- `cancelado` - Pedido cancelado

### Estados de Pago (payment_status)
- `pendiente` - Pago no recibido
- `pagado` - Pago confirmado
- `rechazado` - Pago rechazado

### Métodos de Pago (payment_method)
- `efectivo` - Pago en efectivo
- `tarjeta` - Tarjeta de crédito/débito
- `transferencia` - Transferencia bancaria

---

## 🧪 Testing

Para probar la API, puedes usar:

- **Postman**: Importar la colección de endpoints
- **Thunder Client** (VS Code Extension)
- **cURL**: Ejemplos incluidos arriba
- **Insomnia**: Cliente REST alternativo

---

## 📈 Datos de Prueba

El seeder incluye:
- **2 usuarios** (admin y cliente)
- **4 categorías** (Pizzas, Bebidas, Entradas, Postres)
- **16 productos** variados con diferentes precios y descuentos

---

## 🎯 Características Técnicas

### Validaciones
- Email único por usuario
- Stock insuficiente no permite pedidos
- Categorías con productos no se pueden eliminar
- Solo pedidos cancelados se pueden eliminar
- Validación de roles en rutas protegidas

### Funcionalidades Automáticas
- Reducción de stock al crear pedido
- Cálculo de precios con descuentos
- Cálculo automático de totales de pedido
- Transacciones DB para integridad de datos

### Optimizaciones
- Eager Loading para relaciones (with())
- Índices en tablas (foreign keys)
- Soft Deletes opcional (puede implementarse)

---

## 🤝 Contribuciones

Este es un proyecto de portfolio educativo. Las sugerencias y mejoras son bienvenidas.

---

## 📝 Licencia

Proyecto educacional - Libre para uso en portfolios y aprendizaje.

---

## 👨‍💻 Autor

Desarrollado como proyecto de portfolio siguiendo las mejores prácticas de:
- **soy-api**: Arquitectura Laravel con JWT
- **Project-Il-Napolitano**: Lógica de negocio de pizzería

---

## 🔗 Recursos Útiles

- [Laravel Documentation](https://laravel.com/docs)
- [JWT Auth Firebase](https://github.com/firebase/php-jwt)
- [Postman API Testing](https://www.postman.com)

---

**⭐ Si te ha gustado este proyecto, por favor dale una estrella!**


| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| GET | `/api/products` | Listar productos activos | No |

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
