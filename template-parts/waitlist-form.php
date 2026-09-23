<?php
/**
 * Event waitlist form.
 *
 * @package LemonBook
 */

$event = isset( $args['event'] ) && is_array( $args['event'] ) ? $args['event'] : array();
$event_id = isset( $event['id'] ) ? absint( $event['id'] ) : 0;
$form_id = 'waitlist-' . $event_id;
?>
<section class="waitlist" aria-labelledby="<?php echo esc_attr( $form_id . '-title' ); ?>">
	<h3 id="<?php echo esc_attr( $form_id . '-title' ); ?>"><?php esc_html_e( 'Avísame si hay plazas', 'lemonbook' ); ?></h3>
	<p><?php esc_html_e( 'Déjanos tus datos y te avisaremos si vuelve a haber disponibilidad.', 'lemonbook' ); ?></p>
	<form class="waitlist-form" method="post" action="" data-api-form data-endpoint="<?php echo esc_url( rest_url( 'lemonbook/v1/forms/waitlist' ) ); ?>" data-sending-message="<?php echo esc_attr__( 'Enviando solicitud…', 'lemonbook' ); ?>" data-success-message="<?php echo esc_attr__( 'Solicitud registrada.', 'lemonbook' ); ?>" data-unavailable-message="<?php echo esc_attr__( 'No se ha podido registrar la solicitud. Inténtalo de nuevo más tarde.', 'lemonbook' ); ?>">
		<input type="hidden" name="event_id" value="<?php echo esc_attr( (string) $event_id ); ?>">
		<div class="honeypot" aria-hidden="true"><label for="<?php echo esc_attr( $form_id . '-website' ); ?>"><?php esc_html_e( 'No rellenar este campo', 'lemonbook' ); ?></label><input id="<?php echo esc_attr( $form_id . '-website' ); ?>" name="website" type="text" tabindex="-1" autocomplete="off"></div>
		<div class="field-grid field-grid--two">
			<div class="field"><label for="<?php echo esc_attr( $form_id . '-name' ); ?>"><?php esc_html_e( 'Nombre', 'lemonbook' ); ?></label><input id="<?php echo esc_attr( $form_id . '-name' ); ?>" name="name" type="text" autocomplete="name" required></div>
			<div class="field"><label for="<?php echo esc_attr( $form_id . '-email' ); ?>"><?php esc_html_e( 'Correo electrónico', 'lemonbook' ); ?></label><input id="<?php echo esc_attr( $form_id . '-email' ); ?>" name="email" type="email" autocomplete="email" required></div>
			<div class="field"><label for="<?php echo esc_attr( $form_id . '-phone' ); ?>"><?php esc_html_e( 'Teléfono (opcional)', 'lemonbook' ); ?></label><input id="<?php echo esc_attr( $form_id . '-phone' ); ?>" name="phone" type="tel" autocomplete="tel"></div>
			<div class="field"><label for="<?php echo esc_attr( $form_id . '-qty' ); ?>"><?php esc_html_e( 'Plazas', 'lemonbook' ); ?></label><input id="<?php echo esc_attr( $form_id . '-qty' ); ?>" name="qty" type="number" min="1" max="6" step="1" value="1" inputmode="numeric" required></div>
			<label class="field--check field--wide"><input name="consent" type="checkbox" value="1" required><span><?php esc_html_e( 'Acepto el tratamiento de mis datos para gestionar esta solicitud.', 'lemonbook' ); ?></span></label>
		</div>
		<button class="button" type="submit"><?php esc_html_e( 'Avísame si hay plazas', 'lemonbook' ); ?></button>
		<div class="form-status" role="status" tabindex="-1" data-form-status></div>
	</form>
</section>

