<?php
/**
 * Event archive card.
 *
 * @package LemonBook
 */

$event = isset( $args['event'] ) && is_array( $args['event'] ) ? $args['event'] : ( is_array( $args ?? null ) ? $args : array() );
$is_past = ! empty( $args['past'] ) || 'past' === ( $event['status'] ?? '' );
$name = isset( $event['name'] ) ? (string) $event['name'] : '';
$short_description = isset( $event['short_description'] ) ? (string) $event['short_description'] : '';
$status = isset( $event['status'] ) ? (string) $event['status'] : 'closed';
$display_image = lemon_event_display_image( $event );
$image_src = $display_image['src'];
$natural = $display_image['natural'];
$srcset = $display_image['srcset'];
$image_alt = isset( $event['image_alt'] ) && $event['image_alt'] ? (string) $event['image_alt'] : $name;
?>
<article class="event-panel event-panel--<?php echo esc_attr( $status ); ?><?php echo esc_attr( $image_src ? ' has-image' : '' ); ?><?php echo esc_attr( $is_past ? ' event-panel--past' : '' ); ?>">
	<?php if ( $image_src ) : ?><div class="event-panel__media<?php echo esc_attr( $natural ? ' is-natural' : '' ); ?>" data-image-fallback><img data-content-image src="<?php echo esc_url( $image_src ); ?>"<?php if ( $srcset ) : ?> srcset="<?php echo esc_attr( $srcset ); ?>" sizes="(max-width: 767px) calc(100vw - 4.5rem), 256px"<?php endif; ?> width="600" height="400" loading="lazy" decoding="async" alt="<?php echo esc_attr( $image_alt ); ?>"><div class="image-placeholder image-fallback" role="img" aria-label="<?php esc_attr_e( 'Imagen no disponible', 'lemonbook' ); ?>"></div></div><?php endif; ?>
	<div class="event-panel__content">
		<div class="event-panel__top">
			<p class="event-date"><?php echo esc_html( lemon_date( $event['starts_at'] ?? '' ) ); ?></p>
			<span class="event-status event-status--<?php echo esc_attr( $status ); ?>"><?php echo esc_html( lemon_event_status_label( $status ) ); ?></span>
		</div>
		<?php if ( ! empty( $event['topic'] ) ) : ?><p class="kicker"><?php echo esc_html( $event['topic'] ); ?></p><?php endif; ?>
		<h2><a href="<?php echo esc_url( lemon_event_url( $event ) ); ?>"><?php echo esc_html( $name ?: __( 'Evento sin título', 'lemonbook' ) ); ?></a></h2>
		<?php if ( $short_description ) : ?><p><?php echo esc_html( $short_description ); ?></p><?php endif; ?>
		<div class="event-panel__footer">
			<?php if ( isset( $event['price'] ) ) : ?><span class="price"><?php echo esc_html( lemon_event_price_text( $event ) ); ?></span><?php endif; ?>
			<a class="text-link" href="<?php echo esc_url( lemon_event_url( $event ) ); ?>"><?php esc_html_e( 'Ver evento', 'lemonbook' ); ?></a>
		</div>
	</div>
</article>
