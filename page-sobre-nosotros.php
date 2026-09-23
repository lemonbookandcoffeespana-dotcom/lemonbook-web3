<?php
/**
 * Template Name: Sobre nosotros
 *
 * @package LemonBook
 */

$site = lemon_site();
get_header();
?>
<main id="main-content">
	<header class="page-hero"><div class="shell"><p class="eyebrow"><?php esc_html_e( 'Lemon Book & Coffee', 'lemonbook' ); ?></p><h1><?php esc_html_e( 'Sobre nosotros', 'lemonbook' ); ?></h1></div></header>
	<section class="section"><div class="shell story-grid">
		<div class="story-mark"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/logo-circular-541.jpg' ); ?>" srcset="<?php echo esc_url( get_template_directory_uri() . '/assets/img/logo-circular-360.jpg' ); ?> 360w, <?php echo esc_url( get_template_directory_uri() . '/assets/img/logo-circular-541.jpg' ); ?> 541w" sizes="(max-width: 767px) calc(100vw - 6rem), 24rem" width="541" height="478" loading="lazy" decoding="async" alt="<?php echo esc_attr( $site['name'] ?: __( 'Lemon Book and Coffee', 'lemonbook' ) ); ?>"></div>
		<div><p class="kicker"><?php esc_html_e( 'Nuestra historia', 'lemonbook' ); ?></p><h2><?php esc_html_e( 'Café, cultura y comunidad', 'lemonbook' ); ?></h2><p><?php esc_html_e( 'La historia completa de la marca se incorporará cuando el cliente facilite el texto aprobado del manual de identidad.', 'lemonbook' ); ?></p><a class="button" href="<?php echo esc_url( lemon_page_url( 'contacto' ) ); ?>"><?php esc_html_e( 'Contactar', 'lemonbook' ); ?></a></div>
	</div></section>
	<?php if ( $site['gallery'] ) : ?>
		<section class="section section--line" aria-labelledby="gallery-title"><div class="shell">
			<header class="section-header"><div><p class="kicker"><?php esc_html_e( 'El espacio', 'lemonbook' ); ?></p><h2 id="gallery-title"><?php esc_html_e( 'Un lugar para encontrarnos', 'lemonbook' ); ?></h2></div></header>
			<div class="site-gallery">
				<?php foreach ( $site['gallery'] as $index => $gallery_image ) : ?><figure data-image-fallback><img data-content-image src="<?php echo esc_url( $gallery_image ); ?>" width="800" height="600" loading="lazy" decoding="async" alt="<?php printf( esc_attr__( 'Galería de %1$s, imagen %2$s', 'lemonbook' ), esc_attr( $site['name'] ?: __( 'Lemon Book and Coffee', 'lemonbook' ) ), esc_attr( (string) ( $index + 1 ) ) ); ?>"><div class="image-placeholder image-fallback" role="img" aria-label="<?php esc_attr_e( 'Imagen no disponible', 'lemonbook' ); ?>"></div></figure><?php endforeach; ?>
			</div>
		</div></section>
	<?php endif; ?>
</main>
<?php get_footer(); ?>
