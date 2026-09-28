<?php
/**
 * Information popup about the fair (native dialog, opened by assets/js/fair-popup.js).
 *
 * @package LemonBook
 */

$fair = isset( $args['fair'] ) && is_array( $args['fair'] ) ? $args['fair'] : array();
if ( ! $fair ) {
	return;
}
$next = lemon_fair_next_events( $fair, 3 );
?>
<dialog class="fair-popup" data-fair-popup data-fair="<?php echo esc_attr( $fair['slug'] ); ?>" aria-labelledby="fair-popup-title">
	<button type="button" class="fair-popup__close" data-close aria-label="<?php esc_attr_e( 'Cerrar aviso', 'lemonbook' ); ?>"><span aria-hidden="true">×</span></button>
	<p class="kicker"><?php echo esc_html( lemon_fair_dates( $fair ) ); ?></p>
	<h2 id="fair-popup-title"><?php echo esc_html( $fair['name'] ); ?></h2>
	<?php if ( $fair['venue'] ) : ?><p class="fair-popup__venue"><?php echo esc_html( $fair['venue'] ); ?></p><?php endif; ?>
	<?php if ( $next ) : ?>
		<ul class="fair-mini-list">
			<?php foreach ( $next as $event ) : ?><li><span class="fair-mini-list__when"><?php echo esc_html( lemon_date( $event['starts_at'], 'D j, H:i' ) ); ?></span><a href="<?php echo esc_url( lemon_event_url( $event ) ); ?>"><?php echo esc_html( $event['name'] ); ?></a></li><?php endforeach; ?>
		</ul>
	<?php endif; ?>
	<div class="button-row">
		<a class="button button--accent" href="<?php echo esc_url( lemon_fair_url( $fair ) . '#programa' ); ?>"><?php esc_html_e( 'Ver programa y horarios', 'lemonbook' ); ?></a>
		<button type="button" class="button button--ghost" data-close><?php esc_html_e( 'Ahora no', 'lemonbook' ); ?></button>
	</div>
</dialog>
