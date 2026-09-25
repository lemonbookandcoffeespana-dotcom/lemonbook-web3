<?php
/**
 * Front page template.
 *
 * @package LemonBook
 */

$site   = lemon_site();
$menu   = lemon_menu();
$events = lemon_events();
$books  = lemon_books();

get_header();
?>
<main id="main-content">
	<?php if ( $events['events'] ) : ?>
	<section class="events-carousel-section" aria-labelledby="events-carousel-title">
		<div class="shell events-carousel-header">
			<div><p class="kicker"><?php esc_html_e( 'Agenda', 'lemonbook' ); ?></p><h2 id="events-carousel-title"><?php esc_html_e( 'Eventos y actividades', 'lemonbook' ); ?></h2></div>
			<a class="text-link" href="<?php echo esc_url( lemon_page_url( 'eventos' ) ); ?>"><?php esc_html_e( 'Ver todos los eventos', 'lemonbook' ); ?></a>
		</div>
		<div class="events-carousel" data-events-carousel tabindex="-1">
			<?php foreach ( $events['events'] as $event ) { get_template_part( 'template-parts/event-carousel-card', null, array( 'event' => $event ) ); } ?>
		</div>
	</section>
	<?php endif; ?>

	<section class="hero" aria-labelledby="hero-title">
		<div class="shell hero-grid">
			<div>
				<p class="eyebrow"><?php echo esc_html( $site['city'] ?: __( 'Café · Librería · Encuentros', 'lemonbook' ) ); ?></p>
				<h1 id="hero-title"><?php echo esc_html( $site['tagline'] ?: __( 'Historias que se saborean.', 'lemonbook' ) ); ?></h1>
				<p class="hero-copy"><?php esc_html_e( 'Un espacio para disfrutar del café, descubrir voces cercanas y compartir buenos momentos.', 'lemonbook' ); ?></p>
				<div class="button-row">
					<a class="button button--accent" href="<?php echo esc_url( lemon_page_url( 'reservas' ) ); ?>"><?php esc_html_e( 'Reservar mesa', 'lemonbook' ); ?></a>
					<a class="button button--ghost" href="<?php echo esc_url( lemon_page_url( 'carta' ) ); ?>"><?php esc_html_e( 'Ver la carta', 'lemonbook' ); ?></a>
				</div>
			</div>
			<div class="hero-visual<?php echo esc_attr( $site['hero_image'] ? ' has-content-image' : '' ); ?>"<?php if ( $site['hero_image'] ) : ?> data-image-fallback<?php endif; ?>>
				<?php if ( $site['hero_image'] ) : ?><div class="hero-photo"><img data-content-image src="<?php echo esc_url( $site['hero_image'] ); ?>" sizes="(max-width: 895px) calc(100vw - 2rem), 30rem" width="1200" height="900" fetchpriority="high" decoding="async" alt=""></div><?php endif; ?>
				<div class="hero-mark" aria-hidden="true">
					<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/logo-circular-541.jpg' ); ?>" srcset="<?php echo esc_url( get_template_directory_uri() . '/assets/img/logo-circular-360.jpg' ); ?> 360w, <?php echo esc_url( get_template_directory_uri() . '/assets/img/logo-circular-541.jpg' ); ?> 541w" sizes="(max-width: 895px) 72vw, 26rem" width="541" height="478" fetchpriority="high" decoding="async" alt="">
				</div>
			</div>
		</div>
	</section>

	<section class="section section--dark" aria-labelledby="menu-title">
		<div class="shell">
			<header class="section-header section-header--split">
				<div><p class="kicker"><?php esc_html_e( 'Para saborear', 'lemonbook' ); ?></p><h2 id="menu-title"><?php esc_html_e( 'Una carta para quedarse.', 'lemonbook' ); ?></h2></div>
				<div><p><?php esc_html_e( 'Consulta nuestra selección y la información de alérgenos antes de visitarnos.', 'lemonbook' ); ?></p><a class="text-link" href="<?php echo esc_url( lemon_page_url( 'carta' ) ); ?>"><?php esc_html_e( 'Carta completa', 'lemonbook' ); ?></a></div>
			</header>
			<?php if ( $menu['categories'] ) : ?>
				<div class="menu-preview">
					<?php $shown = 0; foreach ( $menu['categories'] as $category ) : foreach ( (array) ( $category['items'] ?? array() ) as $item ) : if ( $shown >= 4 ) { break 2; }
						$item_name  = (string) ( $item['name'] ?? __( 'Producto sin nombre', 'lemonbook' ) );
						$item_image = (string) ( $item['image'] ?? '' );
						$item_large = (string) ( $item['image_large'] ?? '' );
						$item_src   = $item_image ?: $item_large;
						$item_srcset = implode( ', ', array_filter( array( $item_image ? esc_url_raw( $item_image ) . ' 400w' : '', $item_large ? esc_url_raw( $item_large ) . ' 1200w' : '' ) ) );
						?>
						<div class="menu-preview__item">
							<div class="menu-preview__media dish-media"<?php if ( $item_src ) : ?> data-image-fallback<?php endif; ?>><?php if ( $item_src ) : ?><img data-content-image src="<?php echo esc_url( $item_src ); ?>"<?php if ( $item_srcset ) : ?> srcset="<?php echo esc_attr( $item_srcset ); ?>" sizes="(max-width: 607px) calc(100vw - 4.5rem), 176px"<?php endif; ?> width="400" height="300" loading="lazy" decoding="async" alt="<?php echo esc_attr( $item_name ); ?>"><div class="image-placeholder image-fallback" role="img" aria-label="<?php esc_attr_e( 'Imagen no disponible', 'lemonbook' ); ?>"></div><?php else : ?><div class="image-placeholder" role="img" aria-label="<?php esc_attr_e( 'Imagen no disponible', 'lemonbook' ); ?>"></div><?php endif; ?></div>
							<div class="menu-preview__content"><h3><?php echo esc_html( $item_name ); ?></h3><?php if ( ! empty( $item['description'] ) ) : ?><p><?php echo esc_html( $item['description'] ); ?></p><?php endif; ?></div>
							<?php if ( isset( $item['price'] ) ) : ?><span class="price"><?php echo esc_html( lemon_price( $item['price'] ) ); ?></span><?php endif; ?>
						</div>
					<?php ++$shown; endforeach; endforeach; ?>
				</div>
			<?php else : ?><p class="empty-state"><?php esc_html_e( 'La carta estará disponible muy pronto.', 'lemonbook' ); ?></p><?php endif; ?>
		</div>
	</section>

	<section class="section section--line" aria-labelledby="books-title">
		<div class="shell">
			<header class="section-header section-header--split"><div><p class="kicker"><?php esc_html_e( 'Librería', 'lemonbook' ); ?></p><h2 id="books-title"><?php esc_html_e( 'Libros con raíces cercanas', 'lemonbook' ); ?></h2></div><div><p><?php esc_html_e( 'Descubre nuestra selección y el talento de autoras y autores murcianos.', 'lemonbook' ); ?></p><a class="text-link" href="<?php echo esc_url( lemon_page_url( 'libreria' ) ); ?>"><?php esc_html_e( 'Explorar la librería', 'lemonbook' ); ?></a></div></header>
			<?php if ( $books['books'] ) : ?><div class="grid book-grid"><?php foreach ( array_slice( $books['books'], 0, 4 ) as $book ) { get_template_part( 'template-parts/book-card', null, $book ); } ?></div><?php else : ?><p class="empty-state"><?php esc_html_e( 'La selección de libros estará disponible muy pronto.', 'lemonbook' ); ?></p><?php endif; ?>
		</div>
	</section>

	<section class="section" aria-labelledby="visit-title">
		<div class="shell location-grid">
			<div><p class="kicker"><?php esc_html_e( 'Visítanos', 'lemonbook' ); ?></p><h2 id="visit-title"><?php esc_html_e( 'Tu pausa en Murcia', 'lemonbook' ); ?></h2><?php if ( $site['address'] || $site['city'] ) : ?><p><?php echo esc_html( implode( ', ', array_filter( array( $site['address'], trim( $site['postal_code'] . ' ' . $site['city'] ) ) ) ) ); ?></p><?php endif; ?><div class="button-row"><?php if ( $site['maps_url'] ) : ?><a class="button" href="<?php echo esc_url( $site['maps_url'] ); ?>" rel="noopener noreferrer"><?php esc_html_e( 'Cómo llegar', 'lemonbook' ); ?></a><?php endif; ?><?php if ( $site['whatsapp_url'] ) : ?><a class="button button--ghost" href="<?php echo esc_url( $site['whatsapp_url'] ); ?>" rel="noopener noreferrer"><?php esc_html_e( 'WhatsApp', 'lemonbook' ); ?></a><?php endif; ?></div></div>
			<?php if ( $site['hours'] ) : ?><div class="location-panel"><h3><?php esc_html_e( 'Horario', 'lemonbook' ); ?></h3><ul class="hours-list"><?php foreach ( $site['hours'] as $slot ) : $days = (string) ( $slot['days'] ?? '' ); $opens = (string) ( $slot['opens'] ?? '' ); $closes = (string) ( $slot['closes'] ?? '' ); if ( ! $days && ! $opens && ! $closes ) { continue; } ?><li><span><?php echo esc_html( $days ); ?></span><?php if ( $opens || $closes ) : ?><strong><?php echo esc_html( trim( $opens . '–' . $closes, '–' ) ); ?></strong><?php endif; ?></li><?php endforeach; ?></ul></div><?php endif; ?>
		</div>
	</section>

	<section class="section section--tertiary"><div class="shell cta-band"><h2><?php esc_html_e( '¿Guardamos una mesa para ti?', 'lemonbook' ); ?></h2><a class="button button--accent" href="<?php echo esc_url( lemon_page_url( 'reservas' ) ); ?>"><?php esc_html_e( 'Hacer una reserva', 'lemonbook' ); ?></a></div></section>
</main>
<?php get_footer(); ?>
