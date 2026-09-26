# Diagrama relacional: tienda de productos coleccionables

Las tablas del proyecto son `clientes`, `categorias`, `productos`, `proveedores` y `ventas`.
Las claves primarias se llaman `id`; las claves foráneas usan el patrón
`nombre_singular_id`. Todas las relaciones usan eliminación restringida y actualización
en cascada.

```mermaid
erDiagram
    clientes ||--o{ ventas : realiza
    categorias ||--o{ productos : clasifica
    proveedores ||--o{ productos : suministra
    productos ||--o{ ventas : incluye

    clientes {
        bigint id PK
        string nombre
        string apellido
        string correo UK
        string telefono
        string direccion
        string contrasena
        string imagen
        date fecha_registro
        boolean estado
    }

    categorias {
        bigint id PK
        string nombre UK
        text descripcion
        string tipo
        tinyint edad_recomendada
        string imagen
        boolean estado
    }

    productos {
        bigint id PK
        bigint categoria_id FK
        bigint proveedor_id FK
        string nombre
        text descripcion
        decimal precio
        integer existencia
        string marca
        string imagen
        boolean estado
    }

    proveedores {
        bigint id PK
        string nombre_empresa
        string contacto
        string correo UK
        string telefono
        string direccion
        string ciudad
        string imagen
        boolean estado
    }

    ventas {
        bigint id PK
        bigint cliente_id FK
        bigint producto_id FK
        datetime fecha_venta
        decimal subtotal
        decimal impuesto
        decimal total
            string metodo_pago
            string imagen
        boolean estado
    }
```

## Migración y conexión

La configuración de desarrollo usa SQLite (`DB_CONNECTION=sqlite`). Para recrear la base
de datos y ejecutar todas las migraciones:

```bash
php artisan migrate:fresh --force
```

Para comprobar la conexión y el estado de las migraciones:

```bash
php artisan migrate:status
php artisan about
```

> Este esquema conserva las tablas técnicas de Laravel para sesiones, caché y trabajos.
> No forman parte del modelo de negocio.

## Nota de normalización

La lista solicitada contiene cinco tablas y por ello `ventas` tiene `producto_id`. Si una
venta debe contener varios productos, el diseño relacional recomendado agrega
`productos_ventas` con `venta_id`, `producto_id`, `cantidad` y `precio`; esa tabla sería
una ampliación posterior del esquema de cinco tablas.