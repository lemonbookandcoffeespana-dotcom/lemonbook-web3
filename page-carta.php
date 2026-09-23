<?php
/**
 * Template Name: Carta
 *
 * @package LemonBook
 */

$menu = lemon_menu();
get_header();
?>
<main id="main-content">
	<header class="page-hero"><div class="shell"><p class="eyebrow"><?php esc_html_e( 'Cocina y café', 'lemonbook' ); ?></p><h1><?php esc_html_e( 'Nuestra carta', 'lemonbook' ); ?></h1><p><?php esc_html_e( 'Elige con calma. Indicamos los alérgenos disponibles en cada propuesta.', 'lemonbook' ); ?></p></div></header>
	<div class="shell">
		<?php if ( $menu['categories'] ) : ?>
			<nav class="category-nav" aria-label="<?php esc_attr_e( 'Categorías de la carta', 'lemonbook' ); ?>">
				<?php foreach ( $menu['categories'] as $index => $category ) : $category_name = (string) ( $category['name'] ?? __( 'Otros', 'lemonbook' ) ); ?><a href="#categoria-<?php echo esc_attr( (string) $index ); ?>"><?php echo esc_html( $category_name ); ?></a><?php endforeach; ?>
			</nav>
			<?php foreach ( $menu['categories'] as $index => $category ) :
				$category_name = (string) ( $category['name'] ?? __( 'Otros', 'lemonbook' ) );
				$items = isset( $category['items'] ) && is_array( $category['items'] ) ? $category['items'] : array();
				?>
				<section id="categoria-<?php echo esc_attr( (string) $index ); ?>" class="menu-category" aria-labelledby="category-title-<?php echo esc_attr( (string) $index ); ?>">
					<header class="menu-category__heading"><h2 id="category-title-<?php echo esc_attr( (string) $index ); ?>"><?php echo esc_html( $category_name ?: __( 'Otros', 'lemonbook' ) ); ?></h2><span><?php printf( esc_html( _n( '%s propuesta', '%s propuestas', count( $items ), 'lemonbook' ) ), esc_html( (string) count( $items ) ) ); ?></span></header>
					<?php if ( $items ) : ?><ul class="menu-list"><?php foreach ( $items as $item ) { get_template_part( 'template-parts/menu-item', null, $item ); } ?></ul><?php else : ?><p class="empty-state"><?php esc_html_e( 'No hay propuestas publicadas en esta categoría.', 'lemonbook' ); ?></p><?php endif; ?>
				</section>
			<?php endforeach; ?>
		<?php else : ?><section class="section"><p class="empty-state"><?php esc_html_e( 'La carta estará disponible muy pronto.', 'lemonbook' ); ?></p></section><?php endif; ?>
		<section class="section"><p class="form-note"><?php esc_html_e( 'Si tienes alergias o intolerancias, consulta con nuestro equipo antes de realizar tu pedido.', 'lemonbook' ); ?></p></section>
	</div>
</main>
<?php get_footer(); ?>

