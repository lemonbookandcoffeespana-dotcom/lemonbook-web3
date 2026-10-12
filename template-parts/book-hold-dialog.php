<?php
/**
 * Shared dialog for "Reservar y recoger en la librería". One copy per page; book-hold.js
 * opens it and rellena el libro elegido a partir del botón que lo activó.
 *
 * @package LemonBook
 */

?>
<dialog class="book-hold-dialog" data-book-hold-dialog aria-labelledby="book-hold-title">
	<form class="book-hold-form" method="post" action="" data-api-form data-endpoint="<?php echo esc_url( rest_url( 'lemonbook/v1/forms/book-hold' ) ); ?>" data-sending-message="<?php echo esc_attr__( 'Enviando solicitud…', 'lemonbook' ); ?>" data-success-message="<?php echo esc_attr__( 'Reserva registrada. Te avisaremos para confirmarla.', 'lemonbook' ); ?>" data-unavailable-message="<?php echo esc_attr__( 'No se ha podido registrar la reserva. Inténtalo de nuevo más tarde o escríbenos por WhatsApp.', 'lemonbook' ); ?>">
		<button type="button" class="book-hold-dialog__close" data-close aria-label="<?php esc_attr_e( 'Cerrar', 'lemonbook' ); ?>">&times;</button>
		<h2 id="book-hold-title"><?php esc_html_e( 'Reservar y recoger en la librería', 'lemonbook' ); ?></h2>
		<p class="book-hold-form__book" data-book-hold-name></p>
		<input type="hidden" name="item_id" value="" data-book-hold-item-id>
		<div class="honeypot" aria-hidden="true"><label for="book-hold-website"><?php esc_html_e( 'No rellenar este campo', 'lemonbook' ); ?></label><input id="book-hold-website" name="website" type="text" tabindex="-1" autocomplete="off"></div>
		<div class="field-grid field-grid--two">
			<div class="field"><label for="book-hold-name"><?php esc_html_e( 'Nombre', 'lemonbook' ); ?></label><input id="book-hold-name" name="name" type="text" autocomplete="name" required></div>
			<div class="field"><label for="book-hold-email"><?php esc_html_e( 'Correo electrónico', 'lemonbook' ); ?></label><input id="book-hold-email" name="email" type="email" autocomplete="email" required></div>
			<div class="field"><label for="book-hold-phone"><?php esc_html_e( 'Teléfono', 'lemonbook' ); ?></label><input id="book-hold-phone" name="phone" type="tel" autocomplete="tel" required></div>
			<div class="field"><label for="book-hold-qty"><?php esc_html_e( 'Ejemplares', 'lemonbook' ); ?></label><input id="book-hold-qty" name="qty" type="number" min="1" max="5" step="1" value="1" inputmode="numeric" required></div>
			<label class="field--check field--wide"><input name="consent" type="checkbox" value="1" required><span><?php esc_html_e( 'Acepto el tratamiento de mis datos para gestionar esta reserva.', 'lemonbook' ); ?></span></label>
		</div>
		<button class="button button--accent" type="submit"><?php esc_html_e( 'Reservar', 'lemonbook' ); ?></button>
		<div class="form-status" role="status" tabindex="-1" data-form-status></div>
	</form>
</dialog>
