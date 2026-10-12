<?php
/**
 * Envio de formularios (contacto, reserva y lista de espera de eventos) a gestion.
 *
 * El navegador solo habla con esta ruta de WordPress (/wp-json/lemonbook/v1/forms/{contact|reservation}); el servidor
 * reenvia los datos a la API de gestion con un token compartido. El token NO va en el tema: se define en wp-config.php
 * (LEMONBOOK_FORMS_TOKEN, el mismo valor que config.php > web_forms > token de gestion).
 *
 * No se usa nonce de WordPress: las paginas pueden servirse desde cache y un nonce caducado rechazaria mensajes reales.
 * La proteccion es el campo trampa, la validacion y el limite de envios por IP que aplica gestion.
 *
 * @package LemonBook
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'rest_api_init',
	static function (): void {
		register_rest_route(
			'lemonbook/v1',
			'/forms/(?P<kind>contact|reservation|waitlist|book-hold)',
			array(
				'methods'             => 'POST',
				'callback'            => 'lemon_forms_handle',
				'permission_callback' => '__return_true',
			)
		);
	}
);

/**
 * Best-effort client IP (used only by gestion for rate limiting; never stored in clear).
 */
function lemon_forms_client_ip(): string {
	$candidates = array();
	if ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		$candidates = array_map( 'trim', explode( ',', (string) wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) );
	}
	if ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
		$candidates[] = (string) wp_unslash( $_SERVER['REMOTE_ADDR'] );
	}
	foreach ( $candidates as $ip ) {
		if ( false !== filter_var( $ip, FILTER_VALIDATE_IP ) ) {
			return $ip;
		}
	}
	return '';
}

/**
 * Forward one form submission to gestion and return a safe, generic response.
 *
 * @param WP_REST_Request $request Incoming request.
 */
function lemon_forms_handle( WP_REST_Request $request ): WP_REST_Response {
	$kind = (string) $request['kind'];
	$in   = $request->get_json_params();
	if ( ! is_array( $in ) ) {
		$in = $request->get_body_params();
	}

	$text = static function ( string $key ) use ( $in ): string {
		return isset( $in[ $key ] ) && is_scalar( $in[ $key ] ) ? sanitize_text_field( wp_unslash( (string) $in[ $key ] ) ) : '';
	};

	$message = isset( $in['message'] ) && is_scalar( $in['message'] ) ? sanitize_textarea_field( wp_unslash( (string) $in['message'] ) ) : '';
	$payload = array(
		'website'   => $text( 'website' ),
		'name'      => $text( 'name' ),
		'email'     => sanitize_email( $text( 'email' ) ),
		'phone'     => $text( 'phone' ),
		'consent'   => ! empty( $in['consent'] ),
		'client_ip' => lemon_forms_client_ip(),
	);

	if ( 'contact' === $kind ) {
		$subject = $text( 'subject' );
		if ( '' !== $subject ) {
			$message = 'Asunto: ' . $subject . "\n\n" . $message;
		}
		$payload['message'] = $message;
	} elseif ( 'waitlist' === $kind ) {
		$payload['message']  = '';
		$payload['event_id'] = absint( $in['event_id'] ?? 0 );
		$payload['qty']      = absint( $in['qty'] ?? 1 );
	} elseif ( 'book-hold' === $kind ) {
		// Contrato propio de gestion (sin "message"/"client_ip"): solo item_id, qty, name, email, phone, consent.
		// El campo trampa se revisa aquí mismo (gestion no lo conoce para esta acción): si viene relleno, se finge
		// éxito sin reenviar nada, igual que si fuera spam real, para no delatar el filtro al bot.
		if ( '' !== $payload['website'] ) {
			return new WP_REST_Response(
				array(
					'ok'      => true,
					'message' => __( 'Solicitud registrada. Te avisaremos quedando confirmada la reserva.', 'lemonbook' ),
				),
				200
			);
		}
		$payload = array(
			'item_id' => absint( $in['item_id'] ?? 0 ),
			'qty'     => max( 1, min( 5, absint( $in['qty'] ?? 1 ) ) ),
			'name'    => $text( 'name' ),
			'email'   => sanitize_email( $text( 'email' ) ),
			'phone'   => $text( 'phone' ),
			'consent' => ! empty( $in['consent'] ),
		);
	} else {
		$notes = isset( $in['notes'] ) && is_scalar( $in['notes'] ) ? sanitize_textarea_field( wp_unslash( (string) $in['notes'] ) ) : '';
		$payload['message']    = '' !== $message ? $message : $notes;
		$payload['date']       = $text( 'date' );
		$payload['time']       = $text( 'time' );
		$payload['party_size'] = absint( $in['guests'] ?? ( $in['party_size'] ?? 0 ) );
		$payload['event_id']   = absint( $in['event_id'] ?? 0 );
	}

	// Entorno de desarrollo: no se envia nada, solo se simula la respuesta para probar los estados del formulario.
	if ( 'local' === LEMONBOOK_DATA_SOURCE ) {
		return new WP_REST_Response(
			array(
				'ok'      => true,
				'message' => __( 'Modo de desarrollo: el formulario se ha validado pero no se ha enviado.', 'lemonbook' ),
			),
			200
		);
	}

	$unavailable = new WP_REST_Response(
		array(
			'ok'      => false,
			'message' => __( 'No se ha podido enviar ahora mismo. Inténtalo de nuevo más tarde o escríbenos por WhatsApp.', 'lemonbook' ),
		),
		503
	);

	// TEMPORAL (diagnóstico): se quita en cuanto se resuelva. No expone el token, solo si existe y su longitud.
	$lemon_forms_debug = 'token_defined=' . ( defined( 'LEMONBOOK_FORMS_TOKEN' ) ? 'yes_len' . strlen( (string) LEMONBOOK_FORMS_TOKEN ) : 'no' ) . ' api_base=' . ( '' !== LEMONBOOK_API_BASE ? 'set' : 'empty' );

	if ( ! defined( 'LEMONBOOK_FORMS_TOKEN' ) || strlen( (string) LEMONBOOK_FORMS_TOKEN ) < 24 || '' === LEMONBOOK_API_BASE ) {
		if ( isset( $_GET['lbdebug'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Solo lectura de diagnóstico temporal.
			$unavailable->set_data( array_merge( (array) $unavailable->get_data(), array( 'debug' => $lemon_forms_debug . ' reason=token_or_base' ) ) );
		}
		return $unavailable;
	}

	// La API de gestion identifica esta acción como "book_hold" (con guion bajo), no "book-hold" (la ruta REST de WP).
	$gestion_action = 'book-hold' === $kind ? 'book_hold' : $kind;
	$response       = wp_remote_post(
		add_query_arg( 'action', $gestion_action, LEMONBOOK_API_BASE ),
		array(
			'timeout'     => 10,
			'redirection' => 0,
			'headers'     => array(
				'Content-Type' => 'application/json',
				'X-Web-Token'  => (string) LEMONBOOK_FORMS_TOKEN,
			),
			'body'        => wp_json_encode( $payload ),
		)
	);

	if ( is_wp_error( $response ) ) {
		if ( isset( $_GET['lbdebug'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Solo lectura de diagnóstico temporal.
			$unavailable->set_data( array_merge( (array) $unavailable->get_data(), array( 'debug' => $lemon_forms_debug . ' wp_error=' . $response->get_error_message() ) ) );
		}
		return $unavailable;
	}

	$code = (int) wp_remote_retrieve_response_code( $response );
	$data = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( ! is_array( $data ) || ! in_array( $code, array( 200, 422, 429 ), true ) ) {
		if ( isset( $_GET['lbdebug'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Solo lectura de diagnóstico temporal.
			$unavailable->set_data( array_merge( (array) $unavailable->get_data(), array( 'debug' => $lemon_forms_debug . ' http=' . $code . ' body=' . substr( wp_remote_retrieve_body( $response ), 0, 300 ) ) ) );
		}
		return $unavailable; // 401/503/500 y cualquier otro fallo: no se filtran detalles internos.
	}

	$out = array(
		'ok'      => ! empty( $data['ok'] ),
		'message' => isset( $data['message'] ) ? sanitize_text_field( (string) $data['message'] ) : '',
	);
	if ( ! empty( $data['errors'] ) && is_array( $data['errors'] ) ) {
		$out['errors'] = array_map( 'sanitize_text_field', array_map( 'strval', $data['errors'] ) );
	}
	return new WP_REST_Response( $out, $code );
}
