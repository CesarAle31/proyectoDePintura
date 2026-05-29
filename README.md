# proyectoDePintura

Sistema POS para una tienda de pinturas conectado a una base MySQL local.

## Conectar con tu base de datos local `pinturadb`

1. Importa o crea la base de datos local en MySQL/MariaDB con el nombre `pinturadb`.
2. Copia `.env.example` como `.env` en la raíz del proyecto.
3. Ajusta las credenciales según tu instalación local:

```dotenv
DB_HOST=localhost
DB_PORT=3306
DB_NAME=pinturadb
DB_USER=root
DB_PASS=
DB_CHARSET=utf8mb4
```

> Si no existe `.env`, la aplicación intenta conectarse a `localhost:3306`, base `pinturadb`, usuario `root` y contraseña vacía, que es la configuración común de Laragon/XAMPP.

## Compatibilidad de esquema

El proyecto acepta las variantes de nombres que aparecen en distintos scripts/descripciones de `pinturadb`:

- `direccion.idColonia` o `direccion.claveColonia`.
- `colonia.idColonia` o `colonia.claveColonia`.
- `colonia.colonia` o `colonia.nombreColonia`.
- `municipio.claveM` o `municipio.claveMunicipio`.
- `municipio.nombre` o `municipio.nombreMunicipio`.
- `telefonocliente.telefonoCliente` o `telefonocliente.telefono`.

Con esto, los módulos de clientes y proveedores pueden trabajar con la base local descrita por el archivo de tablas sin tener que renombrar columnas manualmente.
