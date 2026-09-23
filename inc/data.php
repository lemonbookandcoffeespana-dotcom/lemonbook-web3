<?php
/**
 * Single data-access layer for the public site API.
 *
 * @package LemonBook
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Por defecto se lee la API real: si falla, las plantillas muestran sus textos de reserva y nunca datos de muestra.
// 'local' (JSON ficticios de assets/data) solo debe definirse a mano en un entorno de desarrollo, nunca en producción.
if ( ! defined( 'LEMONBOOK_DATA_SOURCE' ) ) {
	define( 'LEMONBOOK_DATA_SOURCE', 'api' );
}

if ( ! defined( 'LEMONBOOK_API_BASE' ) ) {
	define( 'LEMONBOOK_API_BASE', 'https://gestion.lemonbookandcoffe.es/api/public.php' );
}

/**
 * Fetch and decode one resource envelope.
 *
 * @param string               $resource Allowed resource name.
 * @param array<string, mixed> $query    Supported resource arguments.
 * @return array<string, mixed>
 */
function lemon_data_resource( string $resource, array $query = array() ): array {
	$allowed = array( 'site', 'menu', 'events', 'books' );
	if ( ! in_array( $resource, $allowed, true ) ) {
		return array();
	}

	$events_when = 'events' === $resource && isset( $query['when'] ) && 'past' === $query['when'] ? 'past' : 'upcoming';
	$envelope = array();

	if ( 'api' === LEMONBOOK_DATA_SOURCE && '' !== LEMONBOOK_API_BASE ) {
		$cache_key = 'lemonbook_' . $resource . ( 'events' === $resource ? '_' . $events_when : '' ) . '_v3';
		$cached    = get_transient( $cache_key );

		if ( is_array( $cached ) ) {
			$envelope = $cached;
		} else {
			$request_args = array( 'resource' => $resource );
			if ( 'books' === $resource ) {
				$request_args['limit'] = 24;
			}
			if ( 'events' === $resource && 'past' === $events_when ) {
				$request_args['when'] = 'past';
			}
			$url = add_query_arg( $request_args, LEMONBOOK_API_BASE );
			$response = wp_remote_get( $url, array( 'timeout' => 5, 'redirection' => 2 ) );
			if ( ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ) ) {
				$decoded = json_decode( wp_remote_retrieve_body( $response ), true );
				if ( is_array( $decoded ) && ! empty( $decoded['ok'] ) && isset( $decoded['data'] ) && is_array( $decoded['data'] ) ) {
					$envelope = $decoded;
					set_transient( $cache_key, $envelope, MINUTE_IN_SECONDS );
				}
			}
		}
	}

	if ( empty( $envelope ) && 'local' === LEMONBOOK_DATA_SOURCE ) {
		$filename = 'events' === $resource && 'past' === $events_when ? 'events_past.json' : $resource . '.json';
		$path = get_template_directory() . '/assets/data/' . $filename;
		if ( is_readable( $path ) ) {
			$decoded = json_decode( (string) file_get_contents( $path ), true );
			if ( is_array( $decoded ) && ! empty( $decoded['ok'] ) && isset( $decoded['data'] ) && is_array( $decoded['data'] ) ) {
				$envelope = $decoded;
			}
		}
	}

	return isset( $envelope['data'] ) && is_array( $envelope['data'] ) ? $envelope['data'] : array();
}

/**
 * Return site details with stable keys.
 *
 * @return array<string, mixed>
 */
function lemon_site(): array {
	$defaults = array(
		'name'         => '',
		'tagline'      => '',
		'address'      => '',
		'city'         => '',
		'postal_code'  => '',
		'phone'        => '',
		'email'        => '',
		'whatsapp'     => '',
		'whatsapp_url' => '',
		'maps_url'     => '',
		'instagram'    => '',
		'facebook'     => '',
		'hours'        => array(),
		'hero_image'   => '',
		'gallery'      => array(),
	);
	$data = wp_parse_args( lemon_data_resource( 'site' ), $defaults );
	$data['hours'] = is_array( $data['hours'] ) ? $data['hours'] : array();
	$data['hero_image'] = is_string( $data['hero_image'] ) ? $data['hero_image'] : '';
	$data['gallery'] = is_array( $data['gallery'] )
		? array_values(
			array_filter(
				array_map(
					static fn ( mixed $image ): string => is_string( $image ) ? trim( $image ) : '',
					$data['gallery']
				)
			)
		)
		: array();
	return $data;
}

/**
 * Add stable image keys to a menu item, event or book.
 *
 * @param mixed $record API record.
 * @return array<string, mixed>
 */
function lemon_normalize_image_record( mixed $record ): array {
	$record = is_array( $record ) ? $record : array();
	$record = wp_parse_args(
		$record,
		array(
			'image'       => '',
			'image_large' => '',
		)
	);
	$record['image'] = is_string( $record['image'] ) ? $record['image'] : '';
	$record['image_large'] = is_string( $record['image_large'] ) ? $record['image_large'] : '';
	return $record;
}

/**
 * Normalize one event image-format map.
 *
 * @param mixed $images API image variants.
 * @return array<string, array{thumb: string, url: string}>
 */
function lemon_normalize_event_images( mixed $images ): array {
	$images = is_array( $images ) ? $images : array();
	$normalized = array();
	foreach ( array( 'square', 'vertical', 'wide' ) as $format ) {
		$variant = isset( $images[ $format ] ) && is_array( $images[ $format ] ) ? $images[ $format ] : array();
		$normalized[ $format ] = array(
			'thumb' => isset( $variant['thumb'] ) && is_string( $variant['thumb'] ) ? $variant['thumb'] : '',
			'url'   => isset( $variant['url'] ) && is_string( $variant['url'] ) ? $variant['url'] : '',
		);
	}
	return $normalized;
}

/**
 * Normalize one event, including statuses, menu and image variants.
 *
 * @param mixed $event API event record.
 * @return array<string, mixed>
 */
function lemon_normalize_event( mixed $event ): array {
	$event = lemon_normalize_image_record( $event );
	$event = wp_parse_args(
		$event,
		array(
			'id'                => 0,
			'slug'              => '',
			'name'              => '',
			'short_description' => '',
			'description'       => '',
			'description_html'  => '',
			'starts_at'         => '',
			'ends_at'           => '',
			'topic'             => '',
			'venue'             => '',
			'venue_url'         => '',
			'price'             => 0.0,
			'price_label'       => '',
			'price_changes_at'  => '',
			'remaining'         => 0,
			'status'            => 'closed',
			'sale_open'         => false,
			'sale_opens_at'     => '',
			'sale_closes_at'    => '',
			'sold_out'          => false,
			'waitlist_open'     => false,
			'pay_at_venue'      => false,
			'table_booking'     => false,
			'menu_options'      => array(),
			'image_alt'         => '',
			'images'            => array(),
			'buy_url'           => '',
		)
	);

	foreach ( array( 'slug', 'name', 'short_description', 'description', 'description_html', 'starts_at', 'ends_at', 'topic', 'venue', 'venue_url', 'status', 'sale_opens_at', 'sale_closes_at', 'price_label', 'price_changes_at', 'image_alt', 'buy_url' ) as $key ) {
		$event[ $key ] = is_string( $event[ $key ] ) ? $event[ $key ] : '';
	}
	$event['id'] = absint( $event['id'] );
	$event['price'] = (float) $event['price'];
	$event['remaining'] = absint( $event['remaining'] );
	$event['sale_open'] = (bool) $event['sale_open'];
	$event['sold_out'] = (bool) $event['sold_out'];
	$event['waitlist_open'] = (bool) $event['waitlist_open'];
	$event['pay_at_venue'] = (bool) $event['pay_at_venue'];
	$event['table_booking'] = (bool) $event['table_booking'];
	$event['status'] = in_array( $event['status'], array( 'open', 'pending', 'sold_out', 'closed', 'cancelled', 'past' ), true ) ? $event['status'] : 'closed';
	$event['images'] = lemon_normalize_event_images( $event['images'] );

	$options = is_array( $event['menu_options'] ) ? $event['menu_options'] : array();
	$event['menu_options'] = array_values(
		array_map(
			static function ( mixed $option ): array {
				$option = is_array( $option ) ? $option : array();
				return array(
					'name'        => isset( $option['name'] ) && is_string( $option['name'] ) ? $option['name'] : '',
					'category'    => isset( $option['category'] ) && is_string( $option['category'] ) ? $option['category'] : '',
					'extra_price' => isset( $option['extra_price'] ) ? (float) $option['extra_price'] : 0.0,
					'included'    => ! empty( $option['included'] ),
				);
			},
			$options
		)
	);
	return $event;
}

/**
 * Return menu data with a stable categories list.
 *
 * @return array{categories: array<int, array<string, mixed>>}
 */
function lemon_menu(): array {
	$data = lemon_data_resource( 'menu' );
	$categories = isset( $data['categories'] ) && is_array( $data['categories'] ) ? $data['categories'] : array();
	$categories = array_map(
		static function ( mixed $category ): array {
			$category = is_array( $category ) ? $category : array();
			$items = isset( $category['items'] ) && is_array( $category['items'] ) ? $category['items'] : array();
			$category['items'] = array_values( array_map( 'lemon_normalize_image_record', $items ) );
			return $category;
		},
		$categories
	);
	return array( 'categories' => array_values( $categories ) );
}

/**
 * Return upcoming or past event data with a stable events list.
 *
 * @param string $when Use "past" for already celebrated events.
 * @return array{events: array<int, array<string, mixed>>}
 */
function lemon_events( string $when = 'upcoming' ): array {
	$when = 'past' === $when ? 'past' : 'upcoming';
	$data = lemon_data_resource( 'events', 'past' === $when ? array( 'when' => 'past' ) : array() );
	$events = isset( $data['events'] ) && is_array( $data['events'] ) ? $data['events'] : array();
	return array( 'events' => array_values( array_map( 'lemon_normalize_event', $events ) ) );
}

/**
 * Return book data with a stable books list.
 *
 * @return array{books: array<int, array<string, mixed>>}
 */
function lemon_books(): array {
	$data = lemon_data_resource( 'books' );
	$books = isset( $data['books'] ) && is_array( $data['books'] ) ? $data['books'] : array();
	return array( 'books' => array_values( array_map( 'lemon_normalize_image_record', $books ) ) );
}
