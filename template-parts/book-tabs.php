<?php
/**
 * Homepage book tabs: novedades, más vendidos, autoría murciana, libros de la feria.
 *
 * Las pestañas sin libros no se muestran. "Más vendidos" aparece cuando gestion envía `units_sold` > 0 y
 * "Novedades" ordena por `created_at` si gestion lo envía (si no, usa el orden de la API).
 *
 * @package LemonBook
 */

$books      = isset( $args['books'] ) && is_array( $args['books'] ) ? $args['books'] : array();
$fair       = isset( $args['fair'] ) && is_array( $args['fair'] ) ? $args['fair'] : null;
$categories = isset( $args['categories'] ) && is_array( $args['categories'] ) ? $args['categories'] : array();
$limit      = 4;

if ( ! $books ) {
	?>
	<p class="empty-state"><?php esc_html_e( 'La selección de libros estará disponible muy pronto.', 'lemonbook' ); ?></p>
	<?php
	return;
}

$newest = $books;
if ( isset( $newest[0]['created_at'] ) ) {
	usort( $newest, static fn ( array $a, array $b ): int => strcmp( (string) ( $b['created_at'] ?? '' ), (string) ( $a['created_at'] ?? '' ) ) );
}

$bestsellers = array_values( array_filter( $books, static fn ( array $book ): bool => (int) ( $book['units_sold'] ?? 0 ) > 0 ) );
usort( $bestsellers, static fn ( array $a, array $b ): int => (int) ( $b['units_sold'] ?? 0 ) <=> (int) ( $a['units_sold'] ?? 0 ) );

$murcianos = array_values( array_filter( $books, static fn ( array $book ): bool => ! empty( $book['murciano'] ) ) );

$fair_books = array();
if ( $fair ) {
	$fair_books = lemon_books( (string) $fair['slug'] )['books'];
}

$tabs = array(
	array(
		'key'   => 'novedades',
		'label' => __( 'Novedades', 'lemonbook' ),
		'books' => $newest,
		'url'   => lemon_page_url( 'libreria' ),
		'cta'   => __( 'Ver toda la librería', 'lemonbook' ),
	),
	array(
		'key'   => 'vendidos',
		'label' => __( 'Más vendidos', 'lemonbook' ),
		'books' => $bestsellers,
		'url'   => lemon_page_url( 'libreria' ),
		'cta'   => __( 'Ver toda la librería', 'lemonbook' ),
	),
	array(
		'key'   => 'murcia',
		'label' => __( 'Autores de Murcia', 'lemonbook' ),
		'books' => $murcianos,
		'url'   => lemon_page_url( 'libreria' ),
		'cta'   => __( 'Ver toda la librería', 'lemonbook' ),
	),
);
foreach ( $categories as $category ) {
	// Se compara con "category" (la principal), la misma que usa el filtro desplegable de /libreria/.
	$category_books = array_values(
		array_filter(
			$books,
			static fn ( array $book ): bool => $category === ( $book['category'] ?? '' )
		)
	);
	if ( ! $category_books ) {
		continue;
	}
	$tabs[] = array(
		'key'   => 'cat-' . sanitize_title( $category ),
		'label' => $category,
		'books' => $category_books,
		'url'   => add_query_arg( 'categoria', $category, lemon_page_url( 'libreria' ) ),
		'cta'   => __( 'Ver toda la librería', 'lemonbook' ),
	);
}
if ( $fair && $fair_books ) {
	$tabs[] = array(
		'key'   => 'feria',
		'label' => sprintf(
			/* translators: %s: fair name */
			__( 'Libros de %s', 'lemonbook' ),
			(string) $fair['name']
		),
		'books' => $fair_books,
		'url'   => lemon_fair_url( $fair ) . '#libros',
		'cta'   => __( 'Ver todos los libros de la feria', 'lemonbook' ),
	);
}
$tabs = array_values( array_filter( $tabs, static fn ( array $tab ): bool => ! empty( $tab['books'] ) ) );
if ( ! $tabs ) {
	return;
}
?>
<div class="book-tabs" data-book-tabs>
	<div class="book-tabs__list" role="tablist" aria-label="<?php esc_attr_e( 'Selección de libros', 'lemonbook' ); ?>">
		<?php foreach ( $tabs as $i => $tab ) : ?>
			<button class="book-tabs__tab" type="button" role="tab" id="book-tab-<?php echo esc_attr( $tab['key'] ); ?>" aria-controls="book-panel-<?php echo esc_attr( $tab['key'] ); ?>" aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>"<?php echo 0 === $i ? '' : ' tabindex="-1"'; ?>><?php echo esc_html( $tab['label'] ); ?></button>
		<?php endforeach; ?>
	</div>
	<?php foreach ( $tabs as $i => $tab ) : ?>
		<div class="book-tabs__panel" role="tabpanel" id="book-panel-<?php echo esc_attr( $tab['key'] ); ?>" aria-labelledby="book-tab-<?php echo esc_attr( $tab['key'] ); ?>"<?php echo 0 === $i ? '' : ''; ?>>
			<div class="grid book-grid">
				<?php foreach ( array_slice( $tab['books'], 0, $limit ) as $book ) { get_template_part( 'template-parts/book-card', null, $book ); } ?>
			</div>
			<p class="book-tabs__more"><a class="text-link" href="<?php echo esc_url( $tab['url'] ); ?>"><?php echo esc_html( $tab['cta'] ); ?></a></p>
		</div>
	<?php endforeach; ?>
</div>
