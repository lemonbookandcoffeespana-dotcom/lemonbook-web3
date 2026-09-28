<?php
/**
 * Fair banner for the homepage.
 *
 * @package LemonBook
 */

$fair = isset( $args['fair'] ) && is_array( $args['fair'] ) ? $args['fair'] : array();
if ( ! $fair ) {
	return;
}
$next = lemon_fair_next_events( $fair, 3 );
?>
<section class="fair-banner" aria-labelledby="fair-banner-title">
	<div class="shell fair-banner__grid">
		<div>
			<p class="kicker"><?php echo esc_html( lemon_fair_dates( $fair ) ); ?></p>
			<h2 id="fair-banner-title"><?php echo esc_html( $fair['name'] ); ?></h2>
			<?php if ( $fair['tagline'] ) : ?><p class="fair-banner__tagline"><?php echo esc_html( $fair['tagline'] ); ?></p><?php endif; ?>
			<?php if ( $fair['venue'] ) : ?><p class="fair-banner__venue"><?php echo esc_html( $fair['venue'] ); ?></p><?php endif; ?>
			<div class="button-row">
				<a class="button button--accent" href="<?php echo esc_url( lemon_fair_url( $fair ) . '#programa' ); ?>"><?php esc_html_e( 'Ver programa y horarios', 'lemonbook' ); ?></a>
				<?php if ( $fair['registration_open'] && $fair['registration_url'] ) : ?><a class="button button--ghost" href="<?php echo esc_url( $fair['registration_url'] ); ?>"><?php esc_html_e( 'Inscribe tu libro', 'lemonbook' ); ?></a><?php endif; ?>
			</div>
		</div>
		<?php if ( $next ) : ?>
		<div class="fair-banner__next">
			<h3><?php esc_html_e( 'Próximas actividades', 'lemonbook' ); ?></h3>
			<ul class="fair-mini-list">
				<?php foreach ( $next as $event ) : ?>
					<li><span class="fair-mini-list__when"><?php echo esc_html( lemon_date( $event['starts_at'], 'D j, H:i' ) ); ?></span><a href="<?php echo esc_url( lemon_event_url( $event ) ); ?>"><?php echo esc_html( $event['name'] ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php endif; ?>
	</div>
</section>
