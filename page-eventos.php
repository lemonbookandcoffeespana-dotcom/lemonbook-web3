<?php
/**
 * Template Name: Eventos
 *
 * @package LemonBook
 */

$upcoming = lemon_events();
$past = lemon_events( 'past' );
get_header();
?>
<main id="main-content">
	<header class="page-hero"><div class="shell"><p class="eyebrow"><?php esc_html_e( 'Agenda cultural', 'lemonbook' ); ?></p><h1><?php esc_html_e( 'Eventos', 'lemonbook' ); ?></h1><p><?php esc_html_e( 'Presentaciones, encuentros y planes para compartir alrededor de los libros y el café.', 'lemonbook' ); ?></p></div></header>
	<section class="section" aria-labelledby="upcoming-events-title"><div class="shell">
		<header class="section-header"><h2 id="upcoming-events-title"><?php esc_html_e( 'Próximos eventos', 'lemonbook' ); ?></h2></header>
		<?php if ( $upcoming['events'] ) : ?><div class="events-grid"><?php foreach ( $upcoming['events'] as $event ) { get_template_part( 'template-parts/event-card', null, array( 'event' => $event ) ); } ?></div><?php else : ?><p class="empty-state"><?php esc_html_e( 'No hay próximos eventos publicados.', 'lemonbook' ); ?></p><?php endif; ?>
	</div></section>
	<?php if ( $past['events'] ) : ?>
		<section class="section section--line events-past" aria-labelledby="past-events-title"><div class="shell">
			<header class="section-header"><div><p class="kicker"><?php esc_html_e( 'Archivo', 'lemonbook' ); ?></p><h2 id="past-events-title"><?php esc_html_e( 'Eventos anteriores', 'lemonbook' ); ?></h2></div></header>
			<div class="events-grid"><?php foreach ( $past['events'] as $event ) { get_template_part( 'template-parts/event-card', null, array( 'event' => $event, 'past' => true ) ); } ?></div>
		</div></section>
	<?php endif; ?>
</main>
<?php get_footer(); ?>

