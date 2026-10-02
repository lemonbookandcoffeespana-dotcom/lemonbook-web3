<?php
/**
 * Landing de la feria (se sirve en /feria/{slug}/ desde inc/fair.php; no es una página de WordPress).
 *
 * @package LemonBook
 */

$fair = lemon_current_fair();
if ( null === $fair ) {
	get_template_part( '404' );
	return;
}

$site       = lemon_site();
$schedule   = lemon_fair_schedule( $fair );
$fair_books = lemon_books( $fair['slug'] );
$percent    = lemon_percent( $fair['commission_percent'] );
$image_src  = '' !== $fair['image_large'] ? $fair['image_large'] : $fair['image'];
$image_srcset = implode( ', ', array_filter( array( $fair['image'] ? esc_url_raw( $fair['image'] ) . ' 400w' : '', $fair['image_large'] ? esc_url_raw( $fair['image_large'] ) . ' 1200w' : '' ) ) );
get_header();
?>
<main id="main-content">
	<header class="fair-hero">
		<div class="shell fair-hero__grid">
			<div>
				<p class="eyebrow"><?php echo esc_html( lemon_fair_dates( $fair ) ); ?></p>
				<h1><?php echo esc_html( $fair['name'] ); ?></h1>
				<?php if ( $fair['tagline'] ) : ?><p class="fair-hero__tagline"><?php echo esc_html( $fair['tagline'] ); ?></p><?php endif; ?>
				<?php if ( $fair['venue'] ) : ?><p class="fair-hero__venue"><strong><?php esc_html_e( 'Lugar:', 'lemonbook' ); ?></strong> <?php echo esc_html( $fair['venue'] ); ?><?php if ( $fair['venue_url'] ) : ?> · <a href="<?php echo esc_url( $fair['venue_url'] ); ?>" rel="noopener noreferrer"><?php esc_html_e( 'Cómo llegar', 'lemonbook' ); ?></a><?php endif; ?></p><?php endif; ?>
				<div class="button-row">
					<a class="button button--accent" href="#programa"><?php esc_html_e( 'Ver el programa', 'lemonbook' ); ?></a>
					<?php foreach ( lemon_fair_register_links( $fair ) as $link ) : ?><a class="button button--ghost" href="<?php echo esc_url( $link['url'] ); ?>"><?php echo esc_html( $link['label'] ); ?></a><?php endforeach; ?>
				</div>
			</div>
			<?php if ( $image_src ) : ?><figure class="fair-hero__media" data-image-fallback><img data-content-image src="<?php echo esc_url( $image_src ); ?>"<?php if ( $image_srcset ) : ?> srcset="<?php echo esc_attr( $image_srcset ); ?>" sizes="(max-width: 55.99rem) 60vw, 22rem"<?php endif; ?> width="800" height="1200" fetchpriority="high" decoding="async" alt="<?php echo esc_attr( $fair['image_alt'] ?: $fair['name'] ); ?>"><div class="image-placeholder image-fallback" role="img" aria-label="<?php esc_attr_e( 'Imagen no disponible', 'lemonbook' ); ?>"></div></figure><?php endif; ?>
		</div>
	</header>

	<?php if ( $fair['description_html'] ) : ?>
	<section class="section" id="sobre" aria-labelledby="fair-about-title"><div class="shell">
		<div class="fair-intro__card">
			<p class="kicker"><?php esc_html_e( 'El encuentro', 'lemonbook' ); ?></p>
			<h2 id="fair-about-title" class="screen-reader-text"><?php esc_html_e( 'Sobre el encuentro', 'lemonbook' ); ?></h2>
			<?php echo wp_kses( $fair['description_html'], lemon_event_allowed_html(), array( 'https' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Filtered with the event allowlist and HTTPS-only links. ?>
		</div>
	</div></section>
	<?php endif; ?>

	<section class="section section--line" id="programa" aria-labelledby="fair-program-title"><div class="shell">
		<header class="section-header"><div><p class="kicker"><?php esc_html_e( 'Día a día', 'lemonbook' ); ?></p><h2 id="fair-program-title"><?php esc_html_e( 'Programa y firmas', 'lemonbook' ); ?></h2></div></header>
		<?php if ( $schedule ) : ?>
			<nav class="fair-days-nav" aria-label="<?php esc_attr_e( 'Días del encuentro', 'lemonbook' ); ?>"><?php foreach ( $schedule as $day ) : ?><a href="#dia-<?php echo esc_attr( $day['date'] ); ?>"><?php echo esc_html( lemon_date( $day['date'], 'D j' ) ); ?></a><?php endforeach; ?></nav>
			<div class="fair-days">
			<?php foreach ( $schedule as $day ) : ?>
				<article class="fair-day" id="dia-<?php echo esc_attr( $day['date'] ); ?>" aria-labelledby="day-title-<?php echo esc_attr( $day['date'] ); ?>">
					<h3 id="day-title-<?php echo esc_attr( $day['date'] ); ?>"><?php echo esc_html( lemon_date( $day['date'], 'l j \d\e F' ) ); ?></h3>
					<?php if ( ! $day['events'] && ! $day['slots'] ) : ?><p class="fair-day__empty"><?php esc_html_e( 'Programa por confirmar.', 'lemonbook' ); ?></p><?php endif; ?>
					<?php if ( $day['events'] ) : ?>
						<h4><?php esc_html_e( 'Actividades', 'lemonbook' ); ?></h4>
						<ul class="fair-list">
						<?php foreach ( $day['events'] as $event ) : ?>
							<li><span class="fair-list__time"><?php echo esc_html( lemon_date( $event['starts_at'], 'H:i' ) ); ?></span><span class="fair-list__body"><a href="<?php echo esc_url( lemon_event_url( $event ) ); ?>"><?php echo esc_html( $event['name'] ); ?></a><?php if ( $event['short_description'] ) : ?><span class="fair-list__note"><?php echo esc_html( $event['short_description'] ); ?></span><?php endif; ?></span><span class="event-status event-status--<?php echo esc_attr( $event['status'] ); ?>"><?php echo esc_html( lemon_event_status_label( $event['status'] ) ); ?></span></li>
						<?php endforeach; ?>
						</ul>
					<?php endif; ?>
					<?php if ( $day['slots'] ) : ?>
						<h4><?php esc_html_e( 'Firmas de autores', 'lemonbook' ); ?></h4>
						<ul class="fair-list">
						<?php foreach ( $day['slots'] as $slot ) : ?>
							<li><span class="fair-list__time"><?php echo esc_html( lemon_date( $slot['starts_at'], 'H:i' ) ); ?>–<?php echo esc_html( lemon_date( $slot['ends_at'], 'H:i' ) ); ?></span>
								<span class="fair-list__body">
								<?php if ( $slot['authors'] ) : ?>
									<?php foreach ( $slot['authors'] as $author ) : ?><span class="fair-author"><strong><?php echo esc_html( $author['name'] ); ?></strong><?php if ( $author['books'] ) : ?> · <?php echo esc_html( implode( ', ', $author['books'] ) ); ?><?php endif; ?></span><?php endforeach; ?>
								<?php else : ?>
									<span class="fair-list__note"><?php esc_html_e( 'Aún sin autores confirmados.', 'lemonbook' ); ?></span>
								<?php endif; ?>
								</span>
								<?php if ( $fair['registration_open'] && $slot['free'] > 0 ) : ?><span class="tag tag--yellow"><?php echo esc_html( sprintf( /* translators: %d: free places */ _n( 'Queda %d plaza', 'Quedan %d plazas', $slot['free'], 'lemonbook' ), $slot['free'] ) ); ?></span><?php elseif ( 0 === $slot['free'] ) : ?><span class="tag"><?php esc_html_e( 'Franja completa', 'lemonbook' ); ?></span><?php endif; ?>
							</li>
						<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</article>
			<?php endforeach; ?>
			</div>
		<?php else : ?><p class="empty-state"><?php esc_html_e( 'El programa se publicará muy pronto.', 'lemonbook' ); ?></p><?php endif; ?>
	</div></section>

	<?php if ( $fair_books['books'] ) : ?>
	<section class="section" id="libros" aria-labelledby="fair-books-title"><div class="shell">
		<header class="section-header section-header--split">
			<div><p class="kicker"><?php esc_html_e( 'Librería', 'lemonbook' ); ?></p><h2 id="fair-books-title"><?php esc_html_e( 'Libros del encuentro', 'lemonbook' ); ?></h2></div>
			<div><p><?php esc_html_e( 'Los ejemplares de las autoras y autores que participan, en nuestra librería.', 'lemonbook' ); ?></p><a class="text-link" href="<?php echo esc_url( lemon_page_url( 'libreria' ) . '#libros-feria' ); ?>"><?php esc_html_e( 'Verlos en la librería', 'lemonbook' ); ?></a></div>
		</header>
		<div class="grid book-grid"><?php foreach ( array_slice( $fair_books['books'], 0, 8 ) as $book ) { get_template_part( 'template-parts/book-card', null, $book ); } ?></div>
	</div></section>
	<?php endif; ?>

	<section class="section section--tertiary" id="autores" aria-labelledby="fair-authors-title"><div class="shell fair-authors">
		<div>
			<p class="kicker"><?php esc_html_e( 'Para autoras y autores', 'lemonbook' ); ?></p>
			<h2 id="fair-authors-title"><?php esc_html_e( '¿Quieres presentar tu libro?', 'lemonbook' ); ?></h2>
			<p><?php echo esc_html( sprintf( /* translators: %s: commission percentage */ __( 'Elige tu día y tu franja de firma, cuéntanos tus obras y cuántos ejemplares nos dejas. Tus libros se venderán en Lemon Book & Coffee y aparecerán en la sección de libros del encuentro. El local cobra una comisión del %s %% sobre cada venta, que aceptas de forma expresa al inscribirte. Revisamos cada inscripción antes de publicarla.', 'lemonbook' ), $percent ) ); ?></p>
			<?php if ( count( lemon_fair_register_links( $fair ) ) > 1 ) : ?><p><?php esc_html_e( '¿Traes una actividad (cuentacuentos, presentación, taller…) o quieres vender en el mercadillo? Usa el formulario que corresponda.', 'lemonbook' ); ?></p><?php endif; ?>
		</div>
		<div>
			<?php $register_links = lemon_fair_register_links( $fair ); if ( $register_links ) : ?>
				<div class="button-row"><?php foreach ( $register_links as $index => $link ) : ?><a class="button <?php echo esc_attr( 0 === $index ? 'button--accent' : 'button--ghost' ); ?>" href="<?php echo esc_url( $link['url'] ); ?>"><?php echo esc_html( $link['label'] ); ?></a><?php endforeach; ?></div>
			<?php else : ?>
				<p><strong><?php esc_html_e( 'Las inscripciones están cerradas.', 'lemonbook' ); ?></strong></p>
				<a class="button button--accent" href="<?php echo esc_url( lemon_page_url( 'contacto' ) ); ?>"><?php esc_html_e( 'Escríbenos', 'lemonbook' ); ?></a>
			<?php endif; ?>
		</div>
	</div></section>

	<?php
	$map_address = $fair['venue'] ?: (string) ( $site['address'] ?? '' );
	$map_url     = $fair['venue_url'] ?: (string) ( $site['maps_url'] ?? '' );
	?>
	<section class="section" id="como-llegar" aria-labelledby="fair-visit-title"><div class="shell">
		<header class="section-header"><p class="kicker"><?php esc_html_e( 'Información práctica', 'lemonbook' ); ?></p><h2 id="fair-visit-title"><?php esc_html_e( 'Te esperamos', 'lemonbook' ); ?></h2></header>
		<div class="fair-practical__grid">
			<?php if ( $map_address ) : ?>
				<div class="fair-practical__map">
					<iframe src="<?php echo esc_url( 'https://maps.google.com/maps?q=' . rawurlencode( $map_address ) . '&t=m&z=16&output=embed' ); ?>" title="<?php echo esc_attr( sprintf( /* translators: %s: venue address */ __( 'Mapa de %s', 'lemonbook' ), $map_address ) ); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
					<?php if ( $map_url ) : ?><a class="button button--ghost" href="<?php echo esc_url( $map_url ); ?>" rel="noopener noreferrer"><?php esc_html_e( 'Cómo llegar', 'lemonbook' ); ?></a><?php endif; ?>
				</div>
			<?php endif; ?>
			<div class="fair-practical__side">
				<div class="fair-practical__block">
					<h3><?php esc_html_e( 'Lugar', 'lemonbook' ); ?></h3>
					<?php if ( $map_address ) : ?><p><?php echo esc_html( $map_address ); ?></p><?php endif; ?>
					<?php if ( $site['whatsapp_url'] ) : ?><p><a href="<?php echo esc_url( $site['whatsapp_url'] ); ?>" rel="noopener noreferrer"><?php esc_html_e( 'WhatsApp', 'lemonbook' ); ?></a></p><?php endif; ?>
				</div>
			</div>
		</div>
	</div></section>
</main>
<?php get_footer(); ?>
