<?php
/**
 * URL propia de cada evento (/eventos/{slug}/), su SEO y los datos estructurados (schema.org/Event).
 *
 * El slug lo entrega la API de gestion y termina en "-{id}"; el evento se resuelve por ese id, de modo que una URL con
 * el nombre desactualizado redirige (301) a la correcta. La pagina "evento" (plantilla page-evento.php) debe existir.
 *
 * @package LemonBook
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const LEMONBOOK_REWRITE_VERSION = '1';

/**
 * Register /eventos/{slug}/ and the query variable that carries the slug.
 */
function lemon_events_rewrite(): void {
	add_rewrite_tag( '%lemon_event%', '([^/]+)' );
	add_rewrite_rule( '^eventos/(?!page/)([^/]+)/?$', 'index.php?pagename=evento&lemon_event=$matches[1]', 'top' );
}
add_action( 'init', 'lemon_events_rewrite' );

/**
 * Flush the rewrite rules once per rules version (and when the theme is activated).
 */
function lemon_events_maybe_flush(): void {
	if ( get_option( 'lemonbook_rewrite_version' ) !== LEMONBOOK_REWRITE_VERSION ) {
		flush_rewrite_rules( false );
		update_option( 'lemonbook_rewrite_version', LEMONBOOK_REWRITE_VERSION, false );
	}
}
add_action( 'init', 'lemon_events_maybe_flush', 99 );
add_action( 'after_switch_theme', 'lemon_events_maybe_flush' );

/**
 * True when the current request is the event-detail page.
 */
function lemon_is_event_page(): bool {
	return is_page( 'evento' ) && ( '' !== (string) get_query_var( 'lemon_event' ) || isset( $_GET['event_id'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only routing.
}

/**
 * Canonical redirect (old ?event_id= links and stale slugs) and 404 status for unknown events.
 */
function lemon_events_template_redirect(): void {
	if ( ! lemon_is_event_page() ) {
		return;
	}

	$event = lemon_current_event();
	if ( null === $event ) {
		status_header( 404 );
		nocache_headers();
		return;
	}

	// Solo se redirige si el enlace canonico es una URL amigable (sin ?): compara la ruta pedida con la correcta.
	$canonical = lemon_event_url( $event );
	if ( false !== strpos( $canonical, '?' ) ) {
		return;
	}
	$requested = isset( $_SERVER['REQUEST_URI'] ) ? (string) strtok( wp_unslash( $_SERVER['REQUEST_URI'] ), '?' ) : '/'; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$wanted    = (string) wp_parse_url( $canonical, PHP_URL_PATH );
	if ( untrailingslashit( rawurldecode( $requested ) ) !== untrailingslashit( rawurldecode( $wanted ) ) ) {
		wp_safe_redirect( $canonical, 301 );
		exit;
	}
}
add_action( 'template_redirect', 'lemon_events_template_redirect' );

/**
 * Title, description and image used by the head tags for one event.
 *
 * @param array<string, mixed> $event Event data.
 * @return array{title: string, description: string, image: string, url: string}
 */
function lemon_event_seo( array $event ): array {
	$site_name = (string) ( lemon_site()['name'] ?? '' );
	$site_name = '' !== $site_name ? $site_name : get_bloginfo( 'name' );
	$title     = '' !== trim( (string) ( $event['seo_title'] ?? '' ) ) ? trim( (string) $event['seo_title'] ) : trim( $event['name'] . ' | ' . $site_name );
	$desc      = '' !== trim( (string) ( $event['meta_description'] ?? '' ) ) ? trim( (string) $event['meta_description'] ) : trim( '' !== $event['short_description'] ? $event['short_description'] : wp_strip_all_tags( $event['description'] ) );
	$image     = '';
	foreach ( array( $event['images']['wide']['url'] ?? '', $event['image_large'] ?? '', $event['images']['square']['url'] ?? '' ) as $candidate ) {
		if ( is_string( $candidate ) && '' !== $candidate ) {
			$image = $candidate;
			break;
		}
	}
	return array(
		'title'       => $title,
		'description' => wp_html_excerpt( $desc, 300, '…' ),
		'image'       => $image,
		'url'         => lemon_event_url( $event ),
	);
}

/**
 * Document title for the event page.
 */
add_filter(
	'pre_get_document_title',
	static function ( $title ) {
		if ( ! lemon_is_event_page() ) {
			return $title;
		}
		$event = lemon_current_event();
		return $event ? lemon_event_seo( $event )['title'] : $title;
	},
	99
);

/**
 * Keep unknown events out of the index.
 */
add_filter(
	'wp_robots',
	static function ( array $robots ): array {
		if ( lemon_is_event_page() && null === lemon_current_event() ) {
			$robots['noindex'] = true;
			$robots['follow']  = true;
		}
		return $robots;
	}
);

/**
 * Rank Math (when active) owns title, description and canonical: feed it the event values.
 */
add_filter(
	'rank_math/frontend/title',
	static function ( $title ) {
		$event = lemon_is_event_page() ? lemon_current_event() : null;
		return $event ? lemon_event_seo( $event )['title'] : $title;
	}
);
add_filter(
	'rank_math/frontend/description',
	static function ( $description ) {
		$event = lemon_is_event_page() ? lemon_current_event() : null;
		return $event ? lemon_event_seo( $event )['description'] : $description;
	}
);
add_filter(
	'rank_math/frontend/canonical',
	static function ( $canonical ) {
		$event = lemon_is_event_page() ? lemon_current_event() : null;
		return $event ? lemon_event_seo( $event )['url'] : $canonical;
	}
);

/**
 * Convert an API datetime (Europe/Madrid) to ISO 8601 with offset.
 */
function lemon_iso_date( string $value ): string {
	if ( '' === trim( $value ) ) {
		return '';
	}
	try {
		return ( new DateTimeImmutable( $value, new DateTimeZone( 'Europe/Madrid' ) ) )->format( 'c' );
	} catch ( Exception $exception ) {
		return '';
	}
}

/**
 * Build the schema.org/Event data for one event.
 *
 * @param array<string, mixed> $event Event data.
 * @param array<string, mixed> $site  Site data.
 * @return array<string, mixed>
 */
function lemon_event_jsonld( array $event, array $site ): array {
	$seo    = lemon_event_seo( $event );
	$images = array();
	foreach ( array( $event['images']['wide']['url'] ?? '', $event['images']['square']['url'] ?? '', $event['images']['vertical']['url'] ?? '', $event['image_large'] ?? '' ) as $image ) {
		if ( is_string( $image ) && '' !== $image ) {
			$images[] = $image;
		}
	}

	$statuses = array(
		'cancelled' => 'https://schema.org/EventCancelled',
	);
	$place    = array(
		'@type'   => 'Place',
		'name'    => '' !== $event['venue'] ? $event['venue'] : (string) ( $site['name'] ?? '' ),
		'address' => array_filter(
			array(
				'@type'           => 'PostalAddress',
				'streetAddress'   => (string) ( $site['address'] ?? '' ),
				'addressLocality' => (string) ( $site['city'] ?? '' ),
				'postalCode'      => (string) ( $site['postal_code'] ?? '' ),
				'addressCountry'  => 'ES',
			)
		),
	);
	if ( '' !== $event['venue_url'] ) {
		$place['hasMap'] = $event['venue_url'];
	}

	$data = array(
		'@context'            => 'https://schema.org',
		'@type'               => 'Event',
		'name'                => $event['name'],
		'description'         => $seo['description'],
		'startDate'           => lemon_iso_date( $event['starts_at'] ),
		'endDate'             => lemon_iso_date( $event['ends_at'] ),
		'eventStatus'         => $statuses[ $event['status'] ] ?? 'https://schema.org/EventScheduled',
		'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
		'location'            => $place,
		'image'               => $images,
		'url'                 => $seo['url'],
		'organizer'           => array_filter(
			array(
				'@type' => 'Organization',
				'name'  => (string) ( $site['name'] ?? '' ),
				'url'   => home_url( '/' ),
			)
		),
	);

	// Oferta: solo mientras la entrada tiene sentido (a la venta, agotada o a punto de abrir).
	$availability = array(
		'open'     => 'https://schema.org/InStock',
		'sold_out' => 'https://schema.org/SoldOut',
		'pending'  => 'https://schema.org/InStock',
	);
	if ( isset( $availability[ $event['status'] ] ) ) {
		$offer = array(
			'@type'         => 'Offer',
			'price'         => number_format( (float) $event['price'], 2, '.', '' ),
			'priceCurrency' => 'EUR',
			'availability'  => $availability[ $event['status'] ],
			'url'           => '' !== $event['buy_url'] ? $event['buy_url'] : $seo['url'],
		);
		$valid_from = lemon_iso_date( 'pending' === $event['status'] ? $event['sale_opens_at'] : '' );
		if ( '' !== $valid_from ) {
			$offer['validFrom'] = $valid_from;
		}
		$data['offers'] = $offer;
	}

	return array_filter(
		$data,
		static function ( $value ) {
			return ! ( '' === $value || array() === $value );
		}
	);
}

/**
 * Head output: JSON-LD always; description, canonical and Open Graph only when Rank Math is not active.
 */
function lemon_event_head(): void {
	if ( ! lemon_is_event_page() ) {
		return;
	}
	$event = lemon_current_event();
	if ( null === $event ) {
		return;
	}

	$seo  = lemon_event_seo( $event );
	$site = lemon_site();

	if ( ! defined( 'RANK_MATH_VERSION' ) ) {
		echo '<meta name="description" content="' . esc_attr( $seo['description'] ) . '">' . "\n";
		echo '<link rel="canonical" href="' . esc_url( $seo['url'] ) . '">' . "\n";
		echo '<meta property="og:type" content="article">' . "\n";
		echo '<meta property="og:title" content="' . esc_attr( $seo['title'] ) . '">' . "\n";
		echo '<meta property="og:description" content="' . esc_attr( $seo['description'] ) . '">' . "\n";
		echo '<meta property="og:url" content="' . esc_url( $seo['url'] ) . '">' . "\n";
		if ( '' !== $seo['image'] ) {
			echo '<meta property="og:image" content="' . esc_url( $seo['image'] ) . '">' . "\n";
		}
	}

	echo '<script type="application/ld+json">' . wp_json_encode( lemon_event_jsonld( $event, $site ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP ) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON-LD encoded with HEX_TAG/HEX_AMP.
}
add_action( 'wp_head', 'lemon_event_head', 5 );
