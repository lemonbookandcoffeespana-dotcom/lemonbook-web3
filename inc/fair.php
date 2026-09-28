<?php
/**
 * La feria (actividad de varios días con identidad propia): URL /feria/{slug}/, SEO y utilidades de programa.
 *
 * Todos los datos llegan de gestion (lemon_fair()). Si no hay feria publicada, nada de esto se muestra.
 *
 * @package LemonBook
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register /feria/{slug}/ and the query variable that carries the slug.
 */
function lemon_fair_rewrite(): void {
	add_rewrite_tag( '%lemon_fair%', '([^/]+)' );
	add_rewrite_rule( '^feria/([^/]+)/?$', 'index.php?lemon_fair=$matches[1]', 'top' );
}
add_action( 'init', 'lemon_fair_rewrite' );

/**
 * True when the current request targets a fair landing.
 */
function lemon_is_fair_page(): bool {
	return '' !== (string) get_query_var( 'lemon_fair' );
}

/**
 * Fair requested by the current URL, or null.
 *
 * @return array<string, mixed>|null
 */
function lemon_current_fair(): ?array {
	if ( ! lemon_is_fair_page() ) {
		return null;
	}
	$slug = sanitize_title( (string) get_query_var( 'lemon_fair' ) );
	$fair = lemon_fair( $slug );
	// Si la API devolviera otra feria (o el modo local ignora el slug), esa URL no existe.
	return $fair && $slug === $fair['slug'] ? $fair : null;
}

/**
 * Landing URL of a fair.
 *
 * @param array<string, mixed> $fair Fair data.
 */
function lemon_fair_url( array $fair ): string {
	$slug = sanitize_title( (string) ( $fair['slug'] ?? '' ) );
	if ( '' === $slug ) {
		return home_url( '/' );
	}
	if ( '' !== (string) get_option( 'permalink_structure' ) ) {
		return home_url( '/feria/' . $slug . '/' );
	}
	return add_query_arg( 'lemon_fair', $slug, home_url( '/' ) );
}

/**
 * Percentage without needless decimals: 30 -> "30", 12.5 -> "12,5".
 */
function lemon_percent( float $value ): string {
	return rtrim( rtrim( number_format( $value, 2, ',', '' ), '0' ), ',' );
}

/**
 * Human date range: "16–18 de octubre" or "30 de octubre – 2 de noviembre".
 *
 * @param array<string, mixed> $fair Fair data.
 */
function lemon_fair_dates( array $fair ): string {
	$from = lemon_date( $fair['starts_on'], 'j \d\e F' );
	$to   = lemon_date( $fair['ends_on'], 'j \d\e F' );
	if ( '' === $from ) {
		return '';
	}
	if ( '' === $to || $to === $from ) {
		return $from;
	}
	if ( lemon_date( $fair['starts_on'], 'F Y' ) === lemon_date( $fair['ends_on'], 'F Y' ) ) {
		return lemon_date( $fair['starts_on'], 'j' ) . '–' . $to;
	}
	return $from . ' – ' . $to;
}

/**
 * Program grouped by day: events and signing slots, sorted by date and time.
 *
 * @param array<string, mixed> $fair Fair data.
 * @return array<int, array{date: string, events: array<int, array<string, mixed>>, slots: array<int, array<string, mixed>>}>
 */
function lemon_fair_schedule( array $fair ): array {
	$days = array();
	foreach ( $fair['days'] as $day ) {
		$days[ $day['date'] ] = array(
			'date'   => $day['date'],
			'events' => array(),
			'slots'  => $day['slots'],
		);
	}
	foreach ( $fair['events'] as $event ) {
		$date = substr( (string) $event['starts_at'], 0, 10 );
		if ( '' === $date ) {
			continue;
		}
		if ( ! isset( $days[ $date ] ) ) {
			$days[ $date ] = array(
				'date'   => $date,
				'events' => array(),
				'slots'  => array(),
			);
		}
		$days[ $date ]['events'][] = $event;
	}
	ksort( $days );
	foreach ( $days as &$day ) {
		usort( $day['events'], static fn ( array $a, array $b ): int => strcmp( $a['starts_at'], $b['starts_at'] ) );
		usort( $day['slots'], static fn ( array $a, array $b ): int => strcmp( $a['starts_at'], $b['starts_at'] ) );
	}
	unset( $day );
	return array_values( $days );
}

/**
 * Next activities of the fair (events not yet over), used by the banner and the popup.
 *
 * @param array<string, mixed> $fair  Fair data.
 * @param int                  $limit Maximum items.
 * @return array<int, array<string, mixed>>
 */
function lemon_fair_next_events( array $fair, int $limit = 3 ): array {
	$events = array_filter(
		$fair['events'],
		static fn ( array $event ): bool => ! in_array( $event['status'], array( 'past', 'cancelled' ), true )
	);
	usort( $events, static fn ( array $a, array $b ): int => strcmp( $a['starts_at'], $b['starts_at'] ) );
	return array_slice( array_values( $events ), 0, $limit );
}

/**
 * SEO values for the fair landing.
 *
 * @param array<string, mixed> $fair Fair data.
 * @return array{title: string, description: string, image: string, url: string}
 */
function lemon_fair_seo( array $fair ): array {
	$site_name = (string) ( lemon_site()['name'] ?? '' );
	$site_name = '' !== $site_name ? $site_name : get_bloginfo( 'name' );
	$summary   = '' !== $fair['tagline'] ? $fair['tagline'] : wp_strip_all_tags( $fair['description_html'] );
	$dates     = lemon_fair_dates( $fair );
	return array(
		'title'       => trim( $fair['name'] . ' | ' . $site_name ),
		'description' => wp_html_excerpt( trim( ( '' !== $dates ? $dates . '. ' : '' ) . $summary ), 300, '…' ),
		'image'       => '' !== $fair['image_large'] ? $fair['image_large'] : $fair['image'],
		'url'         => lemon_fair_url( $fair ),
	);
}

/**
 * The landing is a virtual page: mark it as found and load its template.
 */
function lemon_fair_template_redirect(): void {
	if ( ! lemon_is_fair_page() ) {
		return;
	}
	if ( null === lemon_current_fair() ) {
		status_header( 404 );
		nocache_headers();
		return;
	}
	global $wp_query;
	$wp_query->is_404 = false;
	status_header( 200 );
}
add_action( 'template_redirect', 'lemon_fair_template_redirect' );

add_filter(
	'template_include',
	static function ( string $template ): string {
		if ( lemon_is_fair_page() && null !== lemon_current_fair() ) {
			$landing = locate_template( 'page-feria.php' );
			return '' !== $landing ? $landing : $template;
		}
		return $template;
	}
);

add_filter(
	'pre_get_document_title',
	static function ( $title ) {
		$fair = lemon_current_fair();
		return $fair ? lemon_fair_seo( $fair )['title'] : $title;
	},
	99
);

add_filter(
	'wp_robots',
	static function ( array $robots ): array {
		if ( lemon_is_fair_page() && null === lemon_current_fair() ) {
			$robots['noindex'] = true;
			$robots['follow']  = true;
		}
		return $robots;
	}
);

add_filter(
	'body_class',
	static function ( array $classes ): array {
		if ( lemon_is_fair_page() && null !== lemon_current_fair() ) {
			$classes = array_diff( $classes, array( 'error404' ) );
			$classes[] = 'page-feria';
		}
		return $classes;
	}
);

add_filter(
	'rank_math/frontend/title',
	static function ( $title ) {
		$fair = lemon_current_fair();
		return $fair ? lemon_fair_seo( $fair )['title'] : $title;
	}
);
add_filter(
	'rank_math/frontend/description',
	static function ( $description ) {
		$fair = lemon_current_fair();
		return $fair ? lemon_fair_seo( $fair )['description'] : $description;
	}
);
add_filter(
	'rank_math/frontend/canonical',
	static function ( $canonical ) {
		$fair = lemon_current_fair();
		return $fair ? lemon_fair_seo( $fair )['url'] : $canonical;
	}
);

/**
 * schema.org/Event of the fair, with its activities as subEvent.
 *
 * @param array<string, mixed> $fair Fair data.
 * @param array<string, mixed> $site Site data.
 * @return array<string, mixed>
 */
function lemon_fair_jsonld( array $fair, array $site ): array {
	$seo   = lemon_fair_seo( $fair );
	$place = array(
		'@type' => 'Place',
		'name'  => '' !== $fair['venue'] ? $fair['venue'] : (string) ( $site['name'] ?? '' ),
	);
	if ( '' !== $fair['venue_url'] ) {
		$place['hasMap'] = $fair['venue_url'];
	}

	$sub = array();
	foreach ( $fair['events'] as $event ) {
		$sub[] = array_filter(
			array(
				'@type'     => 'Event',
				'name'      => $event['name'],
				'startDate' => lemon_iso_date( $event['starts_at'] ),
				'endDate'   => lemon_iso_date( $event['ends_at'] ),
				'url'       => lemon_event_url( $event ),
			)
		);
	}

	$data = array(
		'@context'            => 'https://schema.org',
		'@type'               => 'Event',
		'name'                => $fair['name'],
		'description'         => $seo['description'],
		'startDate'           => $fair['starts_on'],
		'endDate'             => $fair['ends_on'],
		'eventStatus'         => 'https://schema.org/EventScheduled',
		'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
		'location'            => $place,
		'image'               => '' !== $seo['image'] ? array( $seo['image'] ) : array(),
		'url'                 => $seo['url'],
		'subEvent'            => $sub,
		'organizer'           => array_filter(
			array(
				'@type' => 'Organization',
				'name'  => (string) ( $site['name'] ?? '' ),
				'url'   => home_url( '/' ),
			)
		),
	);

	return array_filter(
		$data,
		static function ( $value ) {
			return ! ( '' === $value || array() === $value );
		}
	);
}

/**
 * Head output for the landing: JSON-LD always; description, canonical and Open Graph when Rank Math is not active.
 */
function lemon_fair_head(): void {
	$fair = lemon_current_fair();
	if ( null === $fair ) {
		return;
	}
	$seo = lemon_fair_seo( $fair );

	if ( ! defined( 'RANK_MATH_VERSION' ) ) {
		echo '<meta name="description" content="' . esc_attr( $seo['description'] ) . '">' . "\n";
		echo '<link rel="canonical" href="' . esc_url( $seo['url'] ) . '">' . "\n";
		echo '<meta property="og:type" content="website">' . "\n";
		echo '<meta property="og:title" content="' . esc_attr( $seo['title'] ) . '">' . "\n";
		echo '<meta property="og:description" content="' . esc_attr( $seo['description'] ) . '">' . "\n";
		echo '<meta property="og:url" content="' . esc_url( $seo['url'] ) . '">' . "\n";
		if ( '' !== $seo['image'] ) {
			echo '<meta property="og:image" content="' . esc_url( $seo['image'] ) . '">' . "\n";
		}
	}

	echo '<script type="application/ld+json">' . wp_json_encode( lemon_fair_jsonld( $fair, lemon_site() ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP ) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON-LD encoded with HEX_TAG/HEX_AMP.
}
add_action( 'wp_head', 'lemon_fair_head', 5 );

/**
 * Information popup (native <dialog>) on every page except the landing itself.
 */
function lemon_fair_popup(): void {
	if ( is_admin() || lemon_is_fair_page() || is_404() || is_page( array( 'reservas', 'contacto' ) ) ) {
		return;
	}
	$fair = lemon_fair();
	if ( null === $fair ) {
		return;
	}
	get_template_part( 'template-parts/fair-popup', null, array( 'fair' => $fair ) );
}
add_action( 'wp_footer', 'lemon_fair_popup', 5 );

/**
 * Load the popup script only when there is a fair to announce.
 */
function lemon_fair_assets(): void {
	if ( lemon_is_fair_page() || null === lemon_fair() ) {
		return;
	}
	wp_enqueue_script( 'lemonbook-fair-popup', get_template_directory_uri() . '/assets/js/fair-popup.js', array(), wp_get_theme()->get( 'Version' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );
}
add_action( 'wp_enqueue_scripts', 'lemon_fair_assets' );
