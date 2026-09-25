<?php
/**
 * Full-card link for the homepage events carousel.
 *
 * @package LemonBook
 */

$event = isset( $args['event'] ) && is_array( $args['event'] ) ? $args['event'] : array();
$name = isset( $event['name'] ) ? (string) $event['name'] : '';
$status = isset( $event['status'] ) ? (string) $event['status'] : 'closed';
$images = isset( $event['images'] ) && is_array( $event['images'] ) ? $event['images'] : array();
$wide = isset( $images['wide'] ) && is_array( $images['wide'] ) ? $images['wide'] : array();
$wide_thumb = isset( $wide['thumb'] ) ? (string) $wide['thumb'] : '';
$wide_url = isset( $wide['url'] ) ? (string) $wide['url'] : '';
$image = isset( $event['image'] ) ? (string) $event['image'] : '';
$image_large = isset( $event['image_large'] ) ? (string) $event['image_large'] : '';
$image_src = $wide_thumb ?: $image;
$image_alt = isset( $event['image_alt'] ) && $event['image_alt'] ? (string) $event['image_alt'] : $name;
$srcset = $wide_thumb
	? implode( ', ', array_filter( array( esc_url_raw( $wide_thumb ) . ' 400w', $wide_url ? esc_url_raw( $wide_url ) . ' 1200w' : '' ) ) )
	: implode( ', ', array_filter( array( $image ? esc_url_raw( $image ) . ' 400w' : '', $image_large ? esc_url_raw( $image_large ) . ' 1200w' : '' ) ) );
?>
<a class="event-carousel-card event-carousel-card--<?php echo esc_attr( $status ); ?><?php echo esc_attr( $image_src ? ' has-image' : '' ); ?>" href="<?php echo esc_url( lemon_event_url( $event ) ); ?>">
	<?php if ( $image_src ) : ?><span class="event-carousel-card__media" data-image-fallback><img data-content-image src="<?php echo esc_url( $image_src ); ?>"<?php if ( $srcset ) : ?> srcset="<?php echo esc_attr( $srcset ); ?>" sizes="(max-width: 47.99rem) 78vw, 22rem"<?php endif; ?> width="600" height="400" loading="lazy" decoding="async" alt="<?php echo esc_attr( $image_alt ); ?>"><span class="image-placeholder image-fallback" role="img" aria-label="<?php esc_attr_e( 'Imagen no disponible', 'lemonbook' ); ?>"></span></span><?php endif; ?>
	<span class="event-carousel-card__body">
		<span class="event-status event-status--<?php echo esc_attr( $status ); ?>"><?php echo esc_html( lemon_event_status_label( $status ) ); ?></span>
		<span class="event-date"><?php echo esc_html( lemon_date( $event['starts_at'] ?? '' ) ); ?></span>
		<?php if ( ! empty( $event['topic'] ) ) : ?><span class="kicker"><?php echo esc_html( $event['topic'] ); ?></span><?php endif; ?>
		<span class="event-carousel-card__title"><?php echo esc_html( $name ?: __( 'Evento sin título', 'lemonbook' ) ); ?></span>
	</span>
</a>
