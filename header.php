<?php
/**
 * Site header.
 *
 * @package LemonBook
 */

$site = lemon_site();
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main-content"><?php esc_html_e( 'Saltar al contenido', 'lemonbook' ); ?></a>
<header class="site-header" data-site-header>
	<div class="shell header-inner">
		<a class="site-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" aria-label="<?php echo esc_attr( $site['name'] ?: __( 'Lemon Book and Coffee, inicio', 'lemonbook' ) ); ?>">
			<?php lemon_brand_logo( 'brand-logo brand-logo--header' ); ?>
		</a>
		<button class="menu-toggle" type="button" aria-expanded="false" aria-controls="primary-navigation" data-menu-toggle>
			<span class="menu-toggle__label"><?php esc_html_e( 'Menú', 'lemonbook' ); ?></span>
			<span class="menu-toggle__icon" aria-hidden="true"><span></span><span></span></span>
		</button>
		<nav id="primary-navigation" class="primary-navigation" aria-label="<?php esc_attr_e( 'Navegación principal', 'lemonbook' ); ?>" data-navigation>
			<?php
			// wp_nav_menu() solo llama a fallback_cb cuando la ubicación no tiene ningún menú asignado;
			// si hay un menú asignado pero sin elementos, no renderiza nada y tampoco cae al fallback.
			// Se comprueba el resultado y se fuerza el menú por defecto en ese caso.
			$primary_menu = wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'menu',
					'echo'           => false,
				)
			);
			if ( $primary_menu ) {
				echo $primary_menu;
			} else {
				lemon_fallback_menu();
			}
			?>
		</nav>
		<noscript><style>.menu-toggle{display:none}.primary-navigation{position:static;display:block}</style></noscript>
	</div>
</header>
