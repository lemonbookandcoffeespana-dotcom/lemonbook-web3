<?php
/**
 * Site footer.
 *
 * @package LemonBook
 */

$site = lemon_site();
$address_parts = array_filter( array( $site['address'], trim( $site['postal_code'] . ' ' . $site['city'] ) ) );
?>
<footer class="site-footer">
	<div class="shell footer-grid">
		<div class="footer-brand">
			<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/logo-blanco-1600.webp' ); ?>" width="1600" height="1600" loading="lazy" decoding="async" alt="<?php echo esc_attr( $site['name'] ?: __( 'Lemon Book and Coffee', 'lemonbook' ) ); ?>">
		</div>
		<div>
			<h2 class="footer-heading"><?php esc_html_e( 'Encuéntranos', 'lemonbook' ); ?></h2>
			<?php if ( $address_parts ) : ?>
				<address><?php echo esc_html( implode( ', ', $address_parts ) ); ?></address>
			<?php endif; ?>
			<ul class="footer-contact">
				<?php if ( $site['phone'] ) : ?><li><a href="<?php echo esc_url( 'tel:' . preg_replace( '/[^0-9+]/', '', (string) $site['phone'] ) ); ?>"><?php echo esc_html( $site['phone'] ); ?></a></li><?php endif; ?>
				<?php if ( $site['email'] ) : ?><li><a href="<?php echo esc_url( 'mailto:' . sanitize_email( (string) $site['email'] ) ); ?>"><?php echo esc_html( $site['email'] ); ?></a></li><?php endif; ?>
				<?php if ( $site['maps_url'] ) : ?><li><a href="<?php echo esc_url( $site['maps_url'] ); ?>" rel="noopener noreferrer"><?php esc_html_e( 'Cómo llegar', 'lemonbook' ); ?></a></li><?php endif; ?>
			</ul>
		</div>
		<?php if ( $site['hours'] ) : ?>
			<div>
				<h2 class="footer-heading"><?php esc_html_e( 'Horario', 'lemonbook' ); ?></h2>
				<ul class="hours-list">
					<?php foreach ( $site['hours'] as $slot ) :
						$days   = isset( $slot['days'] ) ? (string) $slot['days'] : '';
						$opens  = isset( $slot['opens'] ) ? (string) $slot['opens'] : '';
						$closes = isset( $slot['closes'] ) ? (string) $slot['closes'] : '';
						if ( '' === $days && '' === $opens && '' === $closes ) { continue; }
						?>
						<li><span><?php echo esc_html( $days ); ?></span><?php if ( $opens || $closes ) : ?><strong><?php echo esc_html( trim( $opens . '–' . $closes, '–' ) ); ?></strong><?php endif; ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>
		<div>
			<h2 class="footer-heading"><?php esc_html_e( 'Síguenos', 'lemonbook' ); ?></h2>
			<ul class="footer-contact">
				<?php if ( $site['instagram'] ) : ?><li><a href="<?php echo esc_url( $site['instagram'] ); ?>" rel="noopener noreferrer">Instagram</a></li><?php endif; ?>
				<?php if ( $site['facebook'] ) : ?><li><a href="<?php echo esc_url( $site['facebook'] ); ?>" rel="noopener noreferrer">Facebook</a></li><?php endif; ?>
			</ul>
		</div>
	</div>
	<div class="shell footer-bottom">
		<p>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php echo esc_html( $site['name'] ?: __( 'Lemon Book and Coffee', 'lemonbook' ) ); ?></p>
		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'footer',
				'container'      => 'nav',
				'container_aria_label' => __( 'Enlaces legales', 'lemonbook' ),
				'fallback_cb'    => false,
				'depth'          => 1,
			)
		);
		?>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>

