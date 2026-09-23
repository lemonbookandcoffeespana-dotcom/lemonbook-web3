<?php
/**
 * Template Name: Librería
 *
 * @package LemonBook
 */

$books = lemon_books();
get_header();
?>
<main id="main-content">
	<header class="page-hero"><div class="shell"><p class="eyebrow"><?php esc_html_e( 'Leer cerca', 'lemonbook' ); ?></p><h1><?php esc_html_e( 'Librería', 'lemonbook' ); ?></h1><p><?php esc_html_e( 'Una selección para descubrir nuevas historias y apoyar la creación literaria de nuestra región.', 'lemonbook' ); ?></p></div></header>
	<section class="section"><div class="shell">
		<?php if ( $books['books'] ) : ?><div class="grid book-grid"><?php foreach ( $books['books'] as $book ) { get_template_part( 'template-parts/book-card', null, $book ); } ?></div><?php else : ?><p class="empty-state"><?php esc_html_e( 'La selección de libros estará disponible muy pronto.', 'lemonbook' ); ?></p><?php endif; ?>
	</div></section>
</main>
<?php get_footer(); ?>

