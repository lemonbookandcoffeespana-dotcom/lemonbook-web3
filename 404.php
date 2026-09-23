<?php
/**
 * Not found template.
 *
 * @package LemonBook
 */

get_header();
?>
<main id="main-content"><section class="page-hero"><div class="shell"><p class="eyebrow">404</p><h1><?php esc_html_e( 'Esta página no está en la estantería.', 'lemonbook' ); ?></h1><p><?php esc_html_e( 'Puedes volver al inicio o seguir explorando nuestra carta y agenda.', 'lemonbook' ); ?></p><a class="button" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Volver al inicio', 'lemonbook' ); ?></a></div></section></main>
<?php get_footer(); ?>

