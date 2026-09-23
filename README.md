# Lemon Book & Coffee — tema WordPress

Tema clásico, propio y mobile-first para `lemonbookandcoffe.es`. No depende de Elementor ni de ningún constructor. La presentación usa únicamente la paleta entregada, carga CSS crítico en línea, difiere el resto de estilos y utiliza un único JavaScript pequeño con `defer`.

## Instalación y configuración

1. Copiar la carpeta `lemonbook` a `wp-content/themes/` y activar **Lemon Book & Coffee**.
2. Crear páginas con los slugs indicados y asignar estas plantillas:
   - `carta` → **Carta**
   - `eventos` → **Eventos**
   - `evento` → **Ficha de evento** (la ficha recibe `?event_id=ID`)
   - `libreria` → **Librería**
   - `reservas` → **Reservas**
   - `contacto` → **Contacto**
   - `sobre-nosotros` → **Sobre nosotros**
3. Elegir una portada estática que use `front-page.php`.
4. Asignar los menús **Navegación principal** y **Navegación del pie**. Si el principal no se asigna, el tema muestra una navegación mínima por slug.
5. Mantener activos únicamente los plugins aprobados en el encargo: WooCommerce, Stripe, Rank Math, WP Mail SMTP y adapta-rgpd.

Rank Math conserva el control de título, metadatos y schema: el tema no genera ninguno y mantiene `wp_head()`/`wp_footer()`.

## Estructura

```text
lemonbook/
├── assets/
│   ├── css/critical.css       Tokens, fuentes y estilos above-the-fold
│   ├── css/main.css           Estilos no críticos y WooCommerce
│   ├── data/*.json            Copia local del contrato para desarrollo
│   ├── fonts/                 Fuentes locales pendientes
│   ├── img/                   Logos raster redimensionados
│   ├── js/main.js             Menú móvil y envío de formularios
│   └── js/image-fallback.js   Respaldo accesible para imágenes remotas
├── inc/
│   ├── data.php               Única capa de acceso a datos
│   └── template-tags.php      Formatos y utilidades de presentación
├── template-parts/            Tarjetas y formulario reutilizable de lista de espera
├── woocommerce/               Envoltorios mínimos de archivo y producto
├── front-page.php             Portada
├── page-carta.php
├── page-eventos.php
├── page-evento.php
├── page-libreria.php
├── page-reservas.php
├── page-contacto.php
├── page-sobre-nosotros.php
├── header.php / footer.php
├── page.php / single.php / index.php / 404.php
├── functions.php
└── style.css
```

## Datos de `gestion`

Toda lectura pasa por `inc/data.php`. Las plantillas solo llaman a:

- `lemon_site()`
- `lemon_menu()`
- `lemon_events()`
- `lemon_books()`

`lemon_events()` devuelve los próximos eventos; `lemon_events( 'past' )` consulta `when=past`, usa un transient independiente y, en modo local, lee `assets/data/events_past.json`. Cada evento se normaliza con estados, reserva, lugar, textos, menú, `price_label`, `price_changes_at`, `pay_at_venue`, `table_booking` y todas las variantes de imagen, incluso cuando el objeto `images` llega incompleto.

El origen se controla en un solo punto mediante dos constantes. **Por defecto el tema usa la API real**
(`api` y `https://gestion.lemonbookandcoffe.es/api/public.php`); mientras la API no esté publicada o falle,
las plantillas muestran sus textos de reserva, nunca datos de muestra.

Para maquetar en local con los JSON ficticios, definir a mano en `wp-config.php` del entorno de desarrollo
(**nunca en producción**):

```php
define( 'LEMONBOOK_DATA_SOURCE', 'local' );
```

El modo API usa `wp_remote_get()` en servidor, timeout de 5 segundos y transient de 60 segundos. Si falla, devuelve estructuras vacías para que las plantillas muestren sus textos de reserva; no expone datos de muestra. El modo local sí utiliza `assets/data/*.json`, cuyos datos de carta, eventos y libros son exclusivamente ficticios para maquetación.

## Tokens de marca

Existe un solo bloque `:root`, en `assets/css/critical.css`:

| Token | Valor | Uso |
|---|---:|---|
| `--brand-primary` | `#1B3D2E` | Texto, botones y fondos principales |
| `--brand-tertiary` | `#016F60` | Secciones y acentos secundarios |
| `--brand-accent` | `#63C5E2` | Estado informativo |
| `--brand-yellow` | `#F2E500` | Acento visual; nunca texto sobre fondo claro |
| `--brand-olive` | `#999065` | Bordes y detalles de marca |
| `--brand-dark` | `#3C3A19` | Texto secundario |
| `--brand-bg` | `#F2F5DC` | Fondo y texto sobre verde |

No se han añadido blancos, negros ni grises fuera de esta paleta. Los focos son visibles, el contenido es semántico y las animaciones respetan `prefers-reduced-motion`.

## Fuentes locales

Incluidas en `assets/fonts/` (WOFF2, subconjunto latino, licencia OFL, de Fontsource): `playfair-display-regular|medium|bold.woff2`
(400/500/700) y `lato-light|regular|bold.woff2` (300/400/700). Las reglas `@font-face` usan `font-display: swap`.

## Logos e imágenes

Los tres logos entregados se han tratado únicamente como raster, sin redibujar ni vectorizar. Se incluyen tamaños reducidos con dimensiones declaradas y `srcset` donde aporta valor. El logo blanco original ya era WebP; el entorno no disponía de conversor WebP para los PNG/JPG, por lo que esos derivados conservan su formato optimizado y reducido.

Conviene sustituirlos por SVG oficiales cuando el cliente los aporte. No convertir automáticamente los raster actuales a vector.

La capa de datos admite `image` (400 px) e `image_large` (hasta 1200 px) en platos, libros y eventos. Las tarjetas construyen `srcset` con ambas variantes y declaran `sizes`, dimensiones y `loading="lazy"` según su retícula.

Todas las imágenes remotas de contenido tienen respaldo ante errores HTTP o indisponibilidad de `gestion`. Un script diferido detecta tanto el evento `error` como las imágenes que ya habían fallado antes de ejecutarse. Libros, platos, eventos y galería conservan su marco y muestran el marcador de marca; el hero cambia al disco con el logo. En la ficha de evento y en el hero roto, el respaldo visual se reduce y queda centrado dentro del espacio ya reservado: no domina la composición ni provoca CLS.

- Libros: marco vertical 2:3, `object-fit: contain` y fondo crema. La mezcla visual integra el lienzo blanco de origen con `#F2F5DC` sin recortar la portada. Si no existe ninguna variante se mantiene el marcador de marca en la misma proporción.
- Platos: marco 4:3 más amplio, borde oliva, recorte centrado y fondo crema. El tratamiento integra el lienzo blanco de las fotografías con el panel; si faltan ambas variantes aparece el marcador de marca.
- Eventos: imagen 4:3 opcional en listados y portada, y composición 16:9 con prioridad para `image_large` en la ficha. Si no hay imagen, el bloque visual no se renderiza ni reserva espacio.
- Galería: `site.gallery` crea una rejilla semántica, diferida y sin JavaScript; una lista vacía no genera sección.

Cuando `site.hero_image` existe, es la imagen LCP del hero, con recorte 4:3, borde oliva y esquinas redondeadas. Si está vacío, se conserva el logo circular sobre un disco `#F2F5DC` con borde `#1B3D2E` y una holgura aproximada de 1/6. Ambos casos reservan dimensiones y usan `fetchpriority="high"`.

## Módulo de eventos

- `lemon_event_url( $event )` centraliza la URL actual de la ficha. Las plantillas nunca construyen `?event_id=` directamente.
- `lemon_current_event()` busca el ID solicitado primero entre próximos eventos y, solo si no aparece, en el archivo pasado.
- `page-eventos.php` separa **Próximos eventos** y **Eventos anteriores**. El archivo pasado es más discreto y nunca ofrece una acción de reserva.
- `page-evento.php` resuelve exclusivamente mediante `lemon_current_event()` y presenta los estados `open`, `pending`, `sold_out`, `closed`, `cancelled` y `past` con texto e icono, sin depender solo del color.
- `lemon_event_price_text( $event )` unifica el importe de ficha, tarjetas y portada: antepone `price_label` cuando existe y conserva «Gratis» para precio cero.
- `template-parts/waitlist-form.php` contiene la lista de espera reutilizable para eventos agotados con `waitlist_open`. Envía a `/wp-json/lemonbook/v1/forms/waitlist` mediante el manejador existente.
- La acción disponible se denomina **Reservar** y abre `buy_url` en la misma pestaña. `price_changes_at` informa del cambio de precio; `sale_closes_at` se conserva como dato contractual, pero no se presenta como final del precio web. Cuando corresponde, se indican el pago pendiente en el local y la elección de mesa.
- El menú del evento se agrupa por categoría; cada opción distingue **Incluido** o el importe que **se paga aparte**.

`description_html` se vuelve a filtrar con `wp_kses`, enlaces limitados a HTTPS y esta lista blanca propia: `p`, `br`, `strong`, `b`, `em`, `i`, `ul`, `ol`, `li`, `h3`, `h4` y `a` únicamente con `href` y `rel`. Si no hay HTML se muestra `description` como texto plano escapado.

### Escenarios de comprobación visual

- Reservas abiertas: imagen panorámica, etiqueta **Precio web**, acción **Reservar**, cambio de tarifa, pago en el local y elección de mesa.
- Próximamente: estado y aviso «Las reservas abren…», sin acción.
- Agotado: etiqueta explícita y formulario de lista de espera.
- Reservas cerradas: aviso y accesos a Contacto/Reservas.
- Cancelado: aviso destacado sin reserva ni lista de espera.
- Celebrado: tarjeta discreta y ficha sin acciones comerciales.
- Sin imagen: la tarjeta no reserva hueco; si una URL existente falla, conserva el marco y muestra el marcador de marca. En hero y ficha, el respaldo roto es compacto y conserva el espacio reservado.

La cabecera de escritorio mantiene el logotipo a `17–19rem` y el menú con ancho de contenido, elementos no expansibles y alineación derecha dentro de `.shell`; los puntos de comprobación previstos son 1280, 1440 y 1920 px.

No se generaron capturas nuevas en este sandbox porque no había navegador disponible; estos seis estados y las combinaciones nuevas quedan reproducibles con `events.json` y `events_past.json` en modo local.

## URL, SEO y datos estructurados de los eventos (`inc/events-seo.php`)

- Cada evento tiene su URL: `/eventos/{slug}/` (el slug lo entrega la API y termina en `-{id}`). Se sirve con la página
  **evento** (plantilla *Ficha de evento*), que debe existir. Las reglas de reescritura se refrescan solas al activar el tema.
- Una URL con el nombre desactualizado o el antiguo `?event_id=` redirige (301) a la URL correcta; un evento inexistente
  responde 404 con `noindex`.
- Título, descripción y canonical salen de `seo_title` / `meta_description` del evento (o de su nombre y descripción
  corta). Con Rank Math activo se le pasan mediante sus filtros; sin él, el tema imprime las etiquetas. **Verificar en
  producción con Rank Math** (no se ha podido probar aquí).
- Datos estructurados `schema.org/Event`: fechas con zona horaria, estado (programado o cancelado), lugar, imágenes y
  oferta (a la venta, agotado o próximamente). Se omite la oferta si la venta está cerrada, el evento está cancelado o ya pasó.
- Los ayudantes `lemon_event_url()` y `lemon_current_event()` son el único punto que conoce estas URLs.
- Pendiente: los eventos no están en el mapa del sitio (sitemap) de Rank Math.

## Añadir una sección

1. Obtener los datos mediante una de las cuatro funciones de `inc/data.php`; no llamar a la API desde la plantilla ni desde JavaScript.
2. Crear un bloque `<section>` con un encabezado asociado mediante `aria-labelledby`.
3. Reutilizar `.section`, `.shell`, `.section-header`, `.grid`, `.card` y los botones existentes.
4. Si hace falta un componente repetido, crearlo en `template-parts/` y escapar en el último punto de salida (`esc_html`, `esc_url`, `esc_attr`).
5. Usar exclusivamente las variables de color existentes. No crear otro `:root`.
6. Mantener dimensiones en imágenes y aplicar `loading="lazy"`, salvo a la imagen LCP.

## Pendiente

- Confirmar visualmente la asociación exacta de los colores con el PDF del manual ubicado fuera de este encargo; no estaba disponible en la carpeta de trabajo.
- Facilitar y aprobar el texto de historia de marca del manual. La plantilla **Sobre nosotros** muestra mientras tanto un texto de reserva explícito.
- Confirmar el lema definitivo: `site.json` lo entrega vacío. La portada usa el texto de reserva traducible «Historias que se saborean.».
- Confirmar si la denominación pública debe conservar `Lemon Book and Coffe` (dato actual de la API) o corregirse a `Coffee`.
- Publicar en `gestion` el soporte de `image`/`image_large` para eventos y `hero_image`/`gallery` para el sitio; el contrato ya está maquetado, pero estos recursos siguen pendientes en producción.
- Configurar `LEMONBOOK_FORMS_TOKEN` en producción y verificar los estados reales de contacto y reserva contra `gestion`. El tema ya incluye los manejadores, validación, honeypot, consentimiento y estilos de enviando, éxito, error de campo y error general.
- Confirmar campos, límites y política de confirmación de reservas (número máximo de personas, antelación, turnos y cierres). No se han inventado esas reglas.
- Confirmar la URL/página definitiva de ficha de evento y si se desea URL amigable en vez de `evento?event_id=ID`.
- Revisar y aprobar los textos editoriales de reserva de cada plantilla antes de producción.
- Sustituir los logos raster por SVG oficiales cuando el cliente los entregue.
- Ejecutar pruebas en el clon WordPress con WooCommerce, navegación por teclado y lectores de pantalla.
- Completar la tabla Lighthouse en el clon WordPress; las capturas y la auditoría de contraste ya se realizaron durante la revisión visual.
- Validar PHP con `php -l` en el clon o en CI. El sandbox actual no dispone de ejecutable PHP, por lo que no fue posible ejecutar esa comprobación aquí.
