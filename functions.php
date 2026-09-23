<?php
/**
 * Theme bootstrap.
 *
 * @package LemonBook
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once get_template_directory() . '/inc/data.php';
require_once get_template_directory() . '/inc/template-tags.php';
require_once get_template_directory() . '/inc/forms.php';
require_once get_template_directory() . '/inc/events-seo.php';

/**
 * Register theme features and navigation.
 */
function lemonbook_setup(): void {
	load_theme_textdomain( 'lemonbook', get_template_directory() . '/languages' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo', array( 'height' => 190, 'width' => 320, 'flex-height' => true, 'flex-width' => true ) );
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	register_nav_menus(
		array(
			'primary' => __( 'Navegación principal', 'lemonbook' ),
			'footer'  => __( 'Navegación del pie', 'lemonbook' ),
		)
	);
}
add_action( 'after_setup_theme', 'lemonbook_setup' );

/**
 * Enqueue the non-critical stylesheet and the small navigation script.
 */
function lemonbook_assets(): void {
	$version = wp_get_theme()->get( 'Version' );
	wp_enqueue_style( 'lemonbook-main', get_template_directory_uri() . '/assets/css/main.css', array(), $version );
	wp_enqueue_script( 'lemonbook-main', get_template_directory_uri() . '/assets/js/main.js', array(), $version, array( 'in_footer' => true, 'strategy' => 'defer' ) );
	wp_enqueue_script( 'lemonbook-image-fallback', get_template_directory_uri() . '/assets/js/image-fallback.js', array(), $version, array( 'in_footer' => true, 'strategy' => 'defer' ) );
}
add_action( 'wp_enqueue_scripts', 'lemonbook_assets' );

/**
 * Load non-critical CSS asynchronously, retaining a no-JS fallback.
 */
function lemonbook_defer_stylesheet( string $html, string $handle ): string {
	if ( 'lemonbook-main' !== $handle ) {
		return $html;
	}

	$async = preg_replace( "/media=(['\"])all\\1/", "media='print' onload=\"this.media='all'\"", $html );
	$fallback = preg_replace( "/\\s+id=(['\"])lemonbook-main-css\\1/", '', $html );
	return ( is_string( $async ) ? $async : $html ) . '<noscript>' . ( is_string( $fallback ) ? $fallback : $html ) . '</noscript>';
}
add_filter( 'style_loader_tag', 'lemonbook_defer_stylesheet', 10, 2 );

/**
 * Print the small, static critical stylesheet in the document head.
 */
function lemonbook_critical_css(): void {
	$path = get_template_directory() . '/assets/css/critical.css';
	if ( is_readable( $path ) ) {
		$css = (string) file_get_contents( $path );
		$css = str_replace( '../fonts/', esc_url( get_template_directory_uri() . '/assets/fonts/' ), $css );
		echo '<style id="lemonbook-critical">' . $css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted local stylesheet with a sanitized base URL.
	}
}
add_action( 'wp_head', 'lemonbook_critical_css', 1 );

/**
 * Add a useful body class to API-driven page templates.
 */
function lemonbook_body_classes( array $classes ): array {
	$classes[] = 'lemonbook-theme';
	return $classes;
}
add_filter( 'body_class', 'lemonbook_body_classes' );
