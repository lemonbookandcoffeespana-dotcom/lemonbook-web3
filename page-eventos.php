<?php
/**
 * Template Name: Eventos
 *
 * @package LemonBook
 */

$upcoming = lemon_events();
$site     = lemon_site();
// Galería de la página: fotos del local (gestionadas en "Sobre nosotros") más las imágenes de los últimos eventos
// celebrados, en vez de la lista de "eventos anteriores" (sin programa ni entradas, no aportaba).
$past_events = lemon_events( 'past' )['events'];
$gallery = array();
foreach ( $site['gallery'] as $index => $image ) {
	$gallery[] = array(
		'src' => $image,
		'alt' => sprintf(
			/* translators: %s: site name */
			__( 'El espacio de %s', 'lemonbook' ),
			$site['name'] ?: __( 'Lemon Book and Coffee', 'lemonbook' )
		),
	);
}
foreach ( array_slice( $past_events, 0, 6 ) as $event ) {
	$event_image = lemon_event_display_image( $event, true );
	if ( '' !== $event_image['src'] ) {
		$gallery[] = array(
			'src' => $event_image['src'],
			'alt' => (string) $event['name'],
		);
	}
}
get_header();
?>
<main id="main-content">
	<header class="page-hero"><div class="shell"><p class="eyebrow"><?php esc_html_e( 'Agenda cultural', 'lemonbook' ); ?></p><h1><?php esc_html_e( 'Eventos', 'lemonbook' ); ?></h1><p><?php esc_html_e( 'Presentaciones, encuentros y planes para compartir alrededor de los libros y el café.', 'lemonbook' ); ?></p></div></header>
	<section class="section" aria-labelledby="upcoming-events-title"><div class="shell">
		<header class="section-header"><h2 id="upcoming-events-title"><?php esc_html_e( 'Próximos eventos', 'lemonbook' ); ?></h2></header>
		<?php if ( $upcoming['events'] ) : ?><div class="events-grid"><?php foreach ( $upcoming['events'] as $event ) { get_template_part( 'template-parts/event-card', null, array( 'event' => $event ) ); } ?></div><?php else : ?><p class="empty-state"><?php esc_html_e( 'No hay próximos eventos publicados.', 'lemonbook' ); ?></p><?php endif; ?>
	</div></section>
	<?php if ( $gallery ) : ?>
		<section class="section section--line" aria-labelledby="events-gallery-title"><div class="shell">
			<header class="section-header"><div><p class="kicker"><?php esc_html_e( 'El espacio', 'lemonbook' ); ?></p><h2 id="events-gallery-title"><?php esc_html_e( 'Momentos entre libros y café', 'lemonbook' ); ?></h2></div></header>
			<div class="site-gallery">
				<?php foreach ( $gallery as $photo ) : ?><figure data-image-fallback><img data-content-image src="<?php echo esc_url( $photo['src'] ); ?>" width="800" height="600" loading="lazy" decoding="async" alt="<?php echo esc_attr( $photo['alt'] ); ?>"><div class="image-placeholder image-fallback" role="img" aria-label="<?php esc_attr_e( 'Imagen no disponible', 'lemonbook' ); ?>"></div></figure><?php endforeach; ?>
			</div>
		</div></section>
	<?php endif; ?>
</main>
<?php get_footer(); ?>

