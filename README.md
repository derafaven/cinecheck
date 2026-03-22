# CineCheck — Buscador Multi-Sitio de Películas y Series

Aplicación web en PHP para verificar si una película o serie está disponible en múltiples sitios simultáneamente. Incluye panel de administración para gestionar los sitios.

---

## Requisitos

- PHP 7.4 o superior
- Extensión **cURL** habilitada
- Extensión **mbstring** habilitada
- Permisos de escritura en la carpeta `config/`

---

## Instalación

Copia la carpeta `cinecheck/` en tu servidor:

| Servidor | Ruta |
|----------|------|
| XAMPP    | `C:\xampp\htdocs\cinecheck\` |
| Laragon  | `C:\laragon\www\cinecheck\` |
| WAMP     | `C:\wamp64\www\cinecheck\` |

Verifica que cURL esté habilitado en `php.ini`:
```ini
extension=curl
```

---

## Acceso

- **Buscador público:** `http://localhost/cinecheck/`
- **Panel admin:** `http://localhost/cinecheck/admin/`
- **Clave por defecto:** `admin`

---

## Uso del Buscador

1. Escribe el nombre de la película o serie
2. Presiona **BUSCAR** o **Enter**
3. Verás solo los sitios donde se encontraron resultados
4. Haz clic en el nombre del sitio para ir directamente a esa búsqueda

---

## Panel de Administración

- **Dashboard** — Resumen y accesos rápidos
- **Sitios** — Agregar, editar, activar/desactivar y eliminar sitios
- **Cambiar Clave** — Actualizar la contraseña

---

## Estructura del Proyecto

```
cinecheck/
├── index.html
├── buscar.php
├── README.md
├── config/
│   ├── sitios.json        ← Configuración de sitios
│   └── auth.json          ← Clave del admin
├── adaptadores/
│   ├── BaseAdaptador.php
│   ├── DooPlayAdaptador.php
│   └── ScraperAdaptador.php
├── helpers/
│   └── HttpHelper.php
└── admin/
    ├── index.php
    ├── dashboard.php
    ├── sitios.php
    ├── cambiar_clave.php
    ├── auth.php
    └── logout.php
```

---

## Tipos de Integración

| Tipo | Descripción |
|------|-------------|
| `dooplay_api` | Sitios con tema WordPress DooPlay |
| `scraping_html` | Sitios sin API, extrae del HTML |
| `api_get` | Sitios con API REST propia |

---

## Cómo Agregar un Nuevo Sitio

1. Abre el sitio en Firefox → F12 → pestaña Red → filtro XHR
2. Escribe algo en el buscador del sitio
3. Observa qué petición se realiza y guarda el HAR
4. Ve al Panel Admin → Sitios → Agregar Sitio
5. Completa los campos según el tipo detectado

---

## Solución de Problemas

| Problema | Solución |
|---------|----------|
| Error cURL | Habilitar `extension=curl` en php.ini |
| No escribe config | `chmod 664 config/*.json` (Linux) |
| No obtiene nonce | Posible protección Cloudflare activa |
| Clave incorrecta | Editar `config/auth.json` y cambiar el campo `password` |
