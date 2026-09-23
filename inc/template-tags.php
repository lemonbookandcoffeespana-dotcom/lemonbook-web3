<?php
/**
 * Presentation helpers for templates.
 *
 * @package LemonBook
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Format a decimal amount as euros.
 */
function lemon_price( mixed $amount ): string {
	return number_format_i18n( (float) $amount, 2 ) . ' €';
}

/**
 * Return an event amount together with its optional public price label.
 *
 * @param array<string, mixed> $event Event data.
 */
function lemon_event_price_text( array $event ): string {
	$amount = isset( $event['price'] ) && 0.0 === (float) $event['price']
		? __( 'Gratis', 'lemonbook' )
		: lemon_price( $event['price'] ?? 0 );
	$label = isset( $event['price_label'] ) && is_string( $event['price_label'] ) ? trim( $event['price_label'] ) : '';
	return '' !== $label ? $label . ': ' . $amount : $amount;
}

/**
 * Format an API date in Spanish/WordPress locale and Europe/Madrid time.
 */
function lemon_date( mixed $value, string $format = 'j \d\e F, H:i' ): string {
	if ( ! is_string( $value ) || '' === $value ) {
		return '';
	}

	try {
		$date = new DateTimeImmutable( $value, new DateTimeZone( 'Europe/Madrid' ) );
		return wp_date( $format, $date->getTimestamp(), new DateTimeZone( 'Europe/Madrid' ) );
	} catch ( Exception $exception ) {
		return '';
	}
}

/**
 * Return a template page URL by path, with a safe home fallback.
 */
function lemon_page_url( string $path ): string {
	$page = get_page_by_path( $path );
	return $page instanceof WP_Post ? get_permalink( $page ) : home_url( '/' . trim( $path, '/' ) . '/' );
}

/**
 * Return the current event-detail URL.
 *
 * @param array<string, mixed> $event Event data.
 */
function lemon_event_url( array $event ): string {
	$id   = isset( $event['id'] ) ? absint( $event['id'] ) : 0;
	$slug = isset( $event['slug'] ) && is_string( $event['slug'] ) ? sanitize_title( $event['slug'] ) : '';
	if ( ! $id ) {
		return lemon_page_url( 'eventos' );
	}
	// URL amigable /eventos/{slug}/ (ver inc/events-seo.php); sin enlaces permanentes, la antigua con ?event_id=.
	if ( '' !== $slug && '' !== (string) get_option( 'permalink_structure' ) ) {
		return home_url( '/eventos/' . $slug . '/' );
	}
	return add_query_arg( 'event_id', $id, lemon_page_url( 'evento' ) );
}

/**
 * Resolve the requested event from upcoming events first, then the past archive.
 *
 * @return array<string, mixed>|null
 */
function lemon_current_event(): ?array {
	// El id sale del slug de la URL amigable (termina en "-{id}") o, por compatibilidad, del antiguo ?event_id=.
	$event_id = 0;
	$slug     = (string) get_query_var( 'lemon_event' );
	if ( '' !== $slug && preg_match( '/-(\d+)$/', $slug, $matches ) ) {
		$event_id = absint( $matches[1] );
	} elseif ( isset( $_GET['event_id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only routing.
		$event_id = absint( wp_unslash( $_GET['event_id'] ) );
	}
	if ( ! $event_id ) {
		return null;
	}

	$collection = lemon_events();
	foreach ( $collection['events'] as $event ) {
		if ( isset( $event['id'] ) && $event_id === absint( $event['id'] ) ) {
			return $event;
		}
	}

	$collection = lemon_events( 'past' );
	foreach ( $collection['events'] as $event ) {
		if ( isset( $event['id'] ) && $event_id === absint( $event['id'] ) ) {
			return $event;
		}
	}

	return null;
}

/**
 * Return the visible, translated event-status label.
 */
function lemon_event_status_label( string $status ): string {
	$labels = array(
		'open'      => __( 'Reservas abiertas', 'lemonbook' ),
		'pending'   => __( 'Reservas próximamente', 'lemonbook' ),
		'sold_out'  => __( 'Agotado', 'lemonbook' ),
		'closed'    => __( 'Reservas cerradas', 'lemonbook' ),
		'cancelled' => __( 'Cancelado', 'lemonbook' ),
		'past'      => __( 'Celebrado', 'lemonbook' ),
	);
	return $labels[ $status ] ?? $labels['closed'];
}

/**
 * Allowed markup for event descriptions.
 *
 * @return array<string, array<string, bool>>
 */
function lemon_event_allowed_html(): array {
	return array(
		'p'      => array(),
		'br'     => array(),
		'strong' => array(),
		'b'      => array(),
		'em'     => array(),
		'i'      => array(),
		'ul'     => array(),
		'ol'     => array(),
		'li'     => array(),
		'h3'     => array(),
		'h4'     => array(),
		'a'      => array(
			'href' => true,
			'rel'  => true,
		),
	);
}

/**
 * Print the bundled horizontal logo with responsive raster sources.
 */
function lemon_brand_logo( string $class = 'brand-logo' ): void {
	if ( has_custom_logo() ) {
		$logo_id = absint( get_theme_mod( 'custom_logo' ) );
		echo wp_get_attachment_image( $logo_id, 'full', false, array( 'class' => $class, 'loading' => 'eager', 'fetchpriority' => 'high' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WordPress builds safe image markup.
		return;
	}
	?>
	<img class="<?php echo esc_attr( $class ); ?>" src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/logo-horizontal-640.png' ); ?>" srcset="<?php echo esc_url( get_template_directory_uri() . '/assets/img/logo-horizontal-640.png' ); ?> 640w, <?php echo esc_url( get_template_directory_uri() . '/assets/img/logo-horizontal-960.png' ); ?> 960w" sizes="(max-width: 640px) 210px, 304px" width="640" height="377" alt="<?php echo esc_attr__( 'Lemon Book and Coffee', 'lemonbook' ); ?>" decoding="async">
	<?php
}

/**
 * Resolve a local nav fallback when no WordPress menu is assigned.
 *
 * @return array<string, string>
 */
function lemon_default_navigation(): array {
	return array(
		__( 'Inicio', 'lemonbook' )    => home_url( '/' ),
		__( 'Carta', 'lemonbook' )     => lemon_page_url( 'carta' ),
		__( 'Eventos', 'lemonbook' )   => lemon_page_url( 'eventos' ),
		__( 'Librería', 'lemonbook' )  => lemon_page_url( 'libreria' ),
		__( 'Reservas', 'lemonbook' )  => lemon_page_url( 'reservas' ),
		__( 'Contacto', 'lemonbook' )  => lemon_page_url( 'contacto' ),
	);
}

/**
 * Print default menu links.
 */
function lemon_fallback_menu(): void {
	echo '<ul class="menu">';
	foreach ( lemon_default_navigation() as $label => $url ) {
		printf( '<li><a href="%1$s">%2$s</a></li>', esc_url( $url ), esc_html( $label ) );
	}
	echo '</ul>';
}
