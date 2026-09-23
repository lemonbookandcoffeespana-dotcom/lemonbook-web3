<?php
/**
 * Template Name: Reservas
 *
 * @package LemonBook
 */

$site = lemon_site();
get_header();
?>
<main id="main-content">
	<header class="page-hero"><div class="shell"><p class="eyebrow"><?php esc_html_e( 'Tu mesa', 'lemonbook' ); ?></p><h1><?php esc_html_e( 'Reservas', 'lemonbook' ); ?></h1><p><?php esc_html_e( 'Cuéntanos cuándo vienes y te confirmaremos la disponibilidad.', 'lemonbook' ); ?></p></div></header>
	<section class="section"><div class="shell form-layout">
		<div class="form-card">
			<form method="post" action="" data-api-form data-endpoint="<?php echo esc_url( rest_url( 'lemonbook/v1/forms/reservation' ) ); ?>" data-unavailable-message="<?php echo esc_attr__( 'No se ha podido enviar la reserva. Inténtalo de nuevo más tarde.', 'lemonbook' ); ?>">
				<input type="hidden" name="action" value="reservation">
				<?php wp_nonce_field( 'lemonbook_reservation', 'token' ); ?>
				<div class="honeypot" aria-hidden="true"><label for="reservation-website"><?php esc_html_e( 'No rellenar este campo', 'lemonbook' ); ?></label><input id="reservation-website" name="website" type="text" tabindex="-1" autocomplete="off"></div>
				<div class="field-grid field-grid--two">
					<div class="field"><label for="reservation-name"><?php esc_html_e( 'Nombre y apellidos', 'lemonbook' ); ?></label><input id="reservation-name" name="name" type="text" autocomplete="name" required></div>
					<div class="field"><label for="reservation-phone"><?php esc_html_e( 'Teléfono', 'lemonbook' ); ?></label><input id="reservation-phone" name="phone" type="tel" autocomplete="tel" required></div>
					<div class="field"><label for="reservation-email"><?php esc_html_e( 'Correo electrónico', 'lemonbook' ); ?></label><input id="reservation-email" name="email" type="email" autocomplete="email" required></div>
					<div class="field"><label for="reservation-guests"><?php esc_html_e( 'Personas', 'lemonbook' ); ?></label><input id="reservation-guests" name="guests" type="number" min="1" step="1" inputmode="numeric" required></div>
					<div class="field"><label for="reservation-date"><?php esc_html_e( 'Fecha', 'lemonbook' ); ?></label><input id="reservation-date" name="date" type="date" min="<?php echo esc_attr( wp_date( 'Y-m-d' ) ); ?>" required></div>
					<div class="field"><label for="reservation-time"><?php esc_html_e( 'Hora', 'lemonbook' ); ?></label><input id="reservation-time" name="time" type="time" required></div>
					<div class="field field--wide"><label for="reservation-notes"><?php esc_html_e( 'Notas', 'lemonbook' ); ?></label><textarea id="reservation-notes" name="notes" aria-describedby="reservation-notes-help"></textarea><span id="reservation-notes-help" class="form-note"><?php esc_html_e( 'Indica aquí alergias, necesidades de accesibilidad u otra información útil.', 'lemonbook' ); ?></span></div>
					<label class="field--check field--wide"><input name="consent" type="checkbox" value="1" required><span><?php esc_html_e( 'Acepto el tratamiento de mis datos para gestionar esta reserva.', 'lemonbook' ); ?></span></label>
				</div>
				<p class="form-note"><?php esc_html_e( 'La reserva no queda confirmada hasta que recibas respuesta.', 'lemonbook' ); ?></p>
				<button class="button" type="submit"><?php esc_html_e( 'Solicitar reserva', 'lemonbook' ); ?></button>
				<div class="form-status" role="status" tabindex="-1" data-form-status></div>
			</form>
		</div>
		<aside>
			<h2><?php esc_html_e( '¿Prefieres hablar?', 'lemonbook' ); ?></h2>
			<?php if ( $site['phone'] ) : ?><p><a class="text-link" href="<?php echo esc_url( 'tel:' . preg_replace( '/[^0-9+]/', '', (string) $site['phone'] ) ); ?>"><?php echo esc_html( $site['phone'] ); ?></a></p><?php endif; ?>
			<?php if ( $site['whatsapp_url'] ) : ?><p><a class="button button--ghost" href="<?php echo esc_url( $site['whatsapp_url'] ); ?>" rel="noopener noreferrer"><?php esc_html_e( 'Escribir por WhatsApp', 'lemonbook' ); ?></a></p><?php endif; ?>
			<?php if ( $site['hours'] ) : ?><h3><?php esc_html_e( 'Horario', 'lemonbook' ); ?></h3><ul class="hours-list"><?php foreach ( $site['hours'] as $slot ) : $days = (string) ( $slot['days'] ?? '' ); $opens = (string) ( $slot['opens'] ?? '' ); $closes = (string) ( $slot['closes'] ?? '' ); if ( ! $days && ! $opens && ! $closes ) { continue; } ?><li><span><?php echo esc_html( $days ); ?></span><?php if ( $opens || $closes ) : ?><strong><?php echo esc_html( trim( $opens . '–' . $closes, '–' ) ); ?></strong><?php endif; ?></li><?php endforeach; ?></ul><?php endif; ?>
		</aside>
	</div></section>
</main>
<?php get_footer(); ?>

