<?php
/**
 * Template Name: Contacto
 *
 * @package LemonBook
 */

$site          = lemon_site();
$address_parts = array_filter( array( $site['address'], trim( $site['postal_code'] . ' ' . $site['city'] ) ) );
get_header();
?>
<main id="main-content">
	<header class="page-hero"><div class="shell"><p class="eyebrow"><?php esc_html_e( 'Hablemos', 'lemonbook' ); ?></p><h1><?php esc_html_e( 'Contacto', 'lemonbook' ); ?></h1><p><?php esc_html_e( 'Escríbenos para consultas, propuestas culturales o cualquier pregunta.', 'lemonbook' ); ?></p></div></header>
	<section class="section"><div class="shell form-layout">
		<div class="form-card">
			<form method="post" action="" data-api-form data-endpoint="<?php echo esc_url( rest_url( 'lemonbook/v1/forms/contact' ) ); ?>" data-unavailable-message="<?php echo esc_attr__( 'No se ha podido enviar el mensaje. Inténtalo de nuevo más tarde.', 'lemonbook' ); ?>">
				<input type="hidden" name="action" value="contact">
				<?php wp_nonce_field( 'lemonbook_contact', 'token' ); ?>
				<div class="honeypot" aria-hidden="true"><label for="contact-website"><?php esc_html_e( 'No rellenar este campo', 'lemonbook' ); ?></label><input id="contact-website" name="website" type="text" tabindex="-1" autocomplete="off"></div>
				<div class="field-grid field-grid--two">
					<div class="field"><label for="contact-name"><?php esc_html_e( 'Nombre', 'lemonbook' ); ?></label><input id="contact-name" name="name" type="text" autocomplete="name" required></div>
					<div class="field"><label for="contact-email"><?php esc_html_e( 'Correo electrónico', 'lemonbook' ); ?></label><input id="contact-email" name="email" type="email" autocomplete="email" required></div>
					<div class="field field--wide"><label for="contact-subject"><?php esc_html_e( 'Asunto', 'lemonbook' ); ?></label><input id="contact-subject" name="subject" type="text" required></div>
					<div class="field field--wide"><label for="contact-message"><?php esc_html_e( 'Mensaje', 'lemonbook' ); ?></label><textarea id="contact-message" name="message" required></textarea></div>
					<label class="field--check field--wide"><input name="consent" type="checkbox" value="1" required><span><?php esc_html_e( 'Acepto el tratamiento de mis datos para responder a esta consulta.', 'lemonbook' ); ?></span></label>
				</div>
				<button class="button" type="submit"><?php esc_html_e( 'Enviar mensaje', 'lemonbook' ); ?></button>
				<div class="form-status" role="status" tabindex="-1" data-form-status></div>
			</form>
		</div>
		<aside>
			<h2><?php esc_html_e( 'Dónde estamos', 'lemonbook' ); ?></h2>
			<?php if ( $address_parts ) : ?><address><?php echo esc_html( implode( ', ', $address_parts ) ); ?></address><?php endif; ?>
			<ul class="footer-contact">
				<?php if ( $site['phone'] ) : ?><li><a href="<?php echo esc_url( 'tel:' . preg_replace( '/[^0-9+]/', '', (string) $site['phone'] ) ); ?>"><?php echo esc_html( $site['phone'] ); ?></a></li><?php endif; ?>
				<?php if ( $site['email'] ) : ?><li><a href="<?php echo esc_url( 'mailto:' . sanitize_email( (string) $site['email'] ) ); ?>"><?php echo esc_html( $site['email'] ); ?></a></li><?php endif; ?>
			</ul>
			<div class="button-row"><?php if ( $site['maps_url'] ) : ?><a class="button" href="<?php echo esc_url( $site['maps_url'] ); ?>" rel="noopener noreferrer"><?php esc_html_e( 'Abrir mapa', 'lemonbook' ); ?></a><?php endif; ?><?php if ( $site['instagram'] ) : ?><a class="button button--ghost" href="<?php echo esc_url( $site['instagram'] ); ?>" rel="noopener noreferrer">Instagram</a><?php endif; ?></div>
		</aside>
	</div></section>
</main>
<?php get_footer(); ?>

