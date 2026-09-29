<?php
/**
 * Template Name: Librería
 *
 * @package LemonBook
 */

$books = lemon_books();
$fair  = lemon_fair();
$fair_books = $fair ? lemon_books( $fair['slug'] ) : array( 'books' => array() );
// Los libros de la feria van en su propia sección; el catálogo general no los repite.
$catalog = array_values( array_filter( $books['books'], static fn ( array $book ): bool => '' === $book['fair'] ) );
get_header();
?>
<main id="main-content">
	<header class="page-hero"><div class="shell"><p class="eyebrow"><?php esc_html_e( 'Leer cerca', 'lemonbook' ); ?></p><h1><?php esc_html_e( 'Librería', 'lemonbook' ); ?></h1><p><?php esc_html_e( 'Una selección para descubrir nuevas historias y apoyar la creación literaria de nuestra región.', 'lemonbook' ); ?></p></div></header>
	<?php if ( $fair && $fair_books['books'] ) : ?>
		<section class="section section--line fair-books" id="libros-feria" aria-labelledby="fair-books-title"><div class="shell">
			<header class="section-header section-header--split">
				<div><p class="kicker"><?php echo esc_html( lemon_fair_dates( $fair ) ); ?></p><h2 id="fair-books-title"><?php echo esc_html( sprintf( /* translators: %s: fair name */ __( 'Libros de %s', 'lemonbook' ), $fair['name'] ) ); ?></h2></div>
				<div><p><?php esc_html_e( 'Ejemplares de autoras y autores que firman durante la feria.', 'lemonbook' ); ?></p><a class="text-link" href="<?php echo esc_url( lemon_fair_url( $fair ) ); ?>"><?php esc_html_e( 'Ver el programa completo', 'lemonbook' ); ?></a></div>
			</header>
			<div class="grid book-grid"><?php foreach ( $fair_books['books'] as $book ) { get_template_part( 'template-parts/book-card', null, $book ); } ?></div>
		</div></section>
	<?php endif; ?>
	<section class="section"><div class="shell">
		<?php if ( $fair && $fair_books['books'] ) : ?><header class="section-header"><h2><?php esc_html_e( 'Catálogo', 'lemonbook' ); ?></h2></header><?php endif; ?>
		<?php if ( $catalog ) : ?>
			<div class="book-filters" data-book-filters>
				<div class="book-filters__search">
					<label class="screen-reader-text" for="book-search"><?php esc_html_e( 'Buscar por título o autor', 'lemonbook' ); ?></label>
					<input type="search" id="book-search" data-book-search placeholder="<?php esc_attr_e( 'Buscar por título o autor…', 'lemonbook' ); ?>">
				</div>
				<label class="book-filters__toggle"><input type="checkbox" data-book-murciano-filter> <?php esc_html_e( 'Solo autoría murciana', 'lemonbook' ); ?></label>
			</div>
			<div class="grid book-grid" data-book-grid><?php foreach ( $catalog as $book ) { get_template_part( 'template-parts/book-card', null, $book ); } ?></div>
			<p class="empty-state" data-book-empty hidden><?php esc_html_e( 'Ningún libro coincide con esa búsqueda.', 'lemonbook' ); ?></p>
		<?php else : ?><p class="empty-state"><?php esc_html_e( 'La selección de libros estará disponible muy pronto.', 'lemonbook' ); ?></p><?php endif; ?>
	</div></section>
</main>
<?php get_footer(); ?>
