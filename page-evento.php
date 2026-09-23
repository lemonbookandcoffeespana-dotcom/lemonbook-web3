<?php
/**
 * Template Name: Ficha de evento
 *
 * @package LemonBook
 */

$selected = lemon_current_event();
get_header();
?>
<main id="main-content">
	<?php if ( is_array( $selected ) ) :
		$name = (string) $selected['name'];
		$status = (string) $selected['status'];
		$description = (string) $selected['description'];
		$description_html = (string) $selected['description_html'];
		$images = is_array( $selected['images'] ) ? $selected['images'] : array();
		$wide = isset( $images['wide'] ) && is_array( $images['wide'] ) ? $images['wide'] : array();
		$wide_thumb = isset( $wide['thumb'] ) ? (string) $wide['thumb'] : '';
		$wide_url = isset( $wide['url'] ) ? (string) $wide['url'] : '';
		$event_src = $wide_url ?: (string) $selected['image_large'];
		$image_alt = $selected['image_alt'] ? (string) $selected['image_alt'] : $name;
		$event_srcset = $wide_url
			? implode( ', ', array_filter( array( $wide_thumb ? esc_url_raw( $wide_thumb ) . ' 400w' : '', esc_url_raw( $wide_url ) . ' 1200w' ) ) )
			: implode( ', ', array_filter( array( $selected['image'] ? esc_url_raw( $selected['image'] ) . ' 400w' : '', $selected['image_large'] ? esc_url_raw( $selected['image_large'] ) . ' 1200w' : '' ) ) );
		$price_changes_at = $selected['price_changes_at'] ? lemon_date( $selected['price_changes_at'] ) : '';
		$sale_opens_at = $selected['sale_opens_at'] ? lemon_date( $selected['sale_opens_at'] ) : '';
		$menu_groups = array();
		foreach ( $selected['menu_options'] as $option ) {
			if ( empty( $option['name'] ) ) {
				continue;
			}
			$category = $option['category'] ?: __( 'Menú', 'lemonbook' );
			$menu_groups[ $category ][] = $option;
		}
		$has_main_content = $description_html || $description || $menu_groups;
		?>
		<header class="page-hero"><div class="shell">
			<?php if ( $selected['topic'] ) : ?><p class="eyebrow"><?php echo esc_html( $selected['topic'] ); ?></p><?php endif; ?>
			<h1><?php echo esc_html( $name ?: __( 'Evento sin título', 'lemonbook' ) ); ?></h1>
			<div class="event-heading-meta">
				<p><strong><?php esc_html_e( 'Fecha y hora:', 'lemonbook' ); ?></strong> <?php echo esc_html( lemon_date( $selected['starts_at'] ) ); ?><?php if ( $selected['ends_at'] ) : ?>–<?php echo esc_html( lemon_date( $selected['ends_at'], 'H:i' ) ); ?><?php endif; ?></p>
				<?php if ( $selected['venue'] ) : ?><p><strong><?php esc_html_e( 'Lugar:', 'lemonbook' ); ?></strong> <?php echo esc_html( $selected['venue'] ); ?><?php if ( $selected['venue_url'] ) : ?> · <a href="<?php echo esc_url( $selected['venue_url'] ); ?>" rel="noopener noreferrer"><?php esc_html_e( 'Cómo llegar', 'lemonbook' ); ?></a><?php endif; ?></p><?php endif; ?>
			</div>
		</div></header>
		<section class="section"><div class="shell">
			<?php if ( $event_src ) : ?><figure class="event-hero-media" data-image-fallback><img data-content-image src="<?php echo esc_url( $event_src ); ?>"<?php if ( $event_srcset ) : ?> srcset="<?php echo esc_attr( $event_srcset ); ?>" sizes="(max-width: 1215px) calc(100vw - 2rem), 1216px"<?php endif; ?> width="1200" height="800" decoding="async" alt="<?php echo esc_attr( $image_alt ); ?>"><div class="image-placeholder image-fallback" role="img" aria-label="<?php esc_attr_e( 'Imagen no disponible', 'lemonbook' ); ?>"></div></figure><?php endif; ?>
			<div class="detail-layout<?php echo esc_attr( $has_main_content ? '' : ' detail-layout--sidebar-only' ); ?>">
				<?php if ( $has_main_content ) : ?><div class="event-detail-content">
					<?php if ( $description_html || $description ) : ?><section aria-labelledby="event-description-title"><h2 id="event-description-title"><?php esc_html_e( 'Sobre el evento', 'lemonbook' ); ?></h2><?php if ( $description_html ) : ?><?php echo wp_kses( $description_html, lemon_event_allowed_html(), array( 'https' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Filtered with the event-specific allowlist and HTTPS-only links. ?><?php else : ?><p><?php echo esc_html( $description ); ?></p><?php endif; ?></section><?php endif; ?>
					<?php if ( $menu_groups ) : ?><section class="event-menu" aria-labelledby="event-menu-title"><h2 id="event-menu-title"><?php esc_html_e( 'Menú del evento', 'lemonbook' ); ?></h2><?php foreach ( $menu_groups as $category => $options ) : ?><div class="event-menu__group"><h3><?php echo esc_html( $category ); ?></h3><ul><?php foreach ( $options as $option ) : ?><li><span><?php echo esc_html( $option['name'] ); ?></span><strong><?php echo ! empty( $option['included'] ) ? esc_html__( 'Incluido', 'lemonbook' ) : esc_html( lemon_price( $option['extra_price'] ) . ' (' . __( 'se paga aparte', 'lemonbook' ) . ')' ); ?></strong></li><?php endforeach; ?></ul></div><?php endforeach; ?></section><?php endif; ?>
				</div><?php endif; ?>
				<aside class="detail-sidebar" aria-label="<?php esc_attr_e( 'Reserva y datos del evento', 'lemonbook' ); ?>">
					<span class="event-status event-status--<?php echo esc_attr( $status ); ?>"><?php echo esc_html( lemon_event_status_label( $status ) ); ?></span>
					<dl><dt><?php esc_html_e( 'Precio', 'lemonbook' ); ?></dt><dd><?php echo esc_html( lemon_event_price_text( $selected ) ); ?></dd><dt><?php esc_html_e( 'Plazas disponibles', 'lemonbook' ); ?></dt><dd><?php echo esc_html( (string) absint( $selected['remaining'] ) ); ?></dd></dl>
					<?php if ( 'open' === $status ) : ?>
						<?php if ( $selected['sale_open'] && $selected['buy_url'] ) : ?><div class="event-booking"><a class="button" href="<?php echo esc_url( $selected['buy_url'] ); ?>"><?php esc_html_e( 'Reservar', 'lemonbook' ); ?></a><?php if ( $selected['pay_at_venue'] ) : ?><p class="event-booking__note"><strong><?php esc_html_e( 'La reserva queda pendiente de pago y se paga en el local.', 'lemonbook' ); ?></strong></p><?php endif; ?><?php if ( $selected['table_booking'] ) : ?><p class="event-booking__note"><?php esc_html_e( 'Podrás elegir mesa al reservar.', 'lemonbook' ); ?></p><?php endif; ?></div><?php endif; ?>
						<?php if ( $price_changes_at ) : ?><p class="event-deadline"><?php printf( esc_html__( 'El precio web termina el %s; después se reserva al precio del local.', 'lemonbook' ), esc_html( $price_changes_at ) ); ?></p><?php endif; ?>
					<?php elseif ( 'pending' === $status ) : ?>
						<div class="event-notice event-notice--pending"><strong><?php echo $sale_opens_at ? esc_html( sprintf( __( 'Las reservas abren el %s.', 'lemonbook' ), $sale_opens_at ) ) : esc_html__( 'Las reservas abrirán próximamente.', 'lemonbook' ); ?></strong></div>
					<?php elseif ( 'sold_out' === $status ) : ?>
						<?php if ( $selected['waitlist_open'] ) { get_template_part( 'template-parts/waitlist-form', null, array( 'event' => $selected ) ); } ?>
					<?php elseif ( 'closed' === $status ) : ?>
						<div class="event-notice event-notice--closed"><strong><?php esc_html_e( 'Reservas cerradas', 'lemonbook' ); ?></strong><p><?php esc_html_e( 'Puedes consultar la disponibilidad directamente con el local.', 'lemonbook' ); ?></p></div><div class="button-row"><a class="button button--ghost" href="<?php echo esc_url( lemon_page_url( 'contacto' ) ); ?>"><?php esc_html_e( 'Contacto', 'lemonbook' ); ?></a><a class="button button--ghost" href="<?php echo esc_url( lemon_page_url( 'reservas' ) ); ?>"><?php esc_html_e( 'Reservas', 'lemonbook' ); ?></a></div>
					<?php elseif ( 'cancelled' === $status ) : ?>
						<div class="event-notice event-notice--cancelled"><strong><?php esc_html_e( 'Este evento se ha cancelado', 'lemonbook' ); ?></strong></div>
					<?php elseif ( 'past' === $status ) : ?>
						<div class="event-notice event-notice--past"><strong><?php esc_html_e( 'Evento celebrado', 'lemonbook' ); ?></strong></div>
					<?php endif; ?>
				</aside>
			</div>
		</div></section>
	<?php else : ?>
		<header class="page-hero"><div class="shell"><h1><?php esc_html_e( 'Evento no encontrado', 'lemonbook' ); ?></h1><p><?php esc_html_e( 'El evento solicitado no está disponible.', 'lemonbook' ); ?></p><a class="button" href="<?php echo esc_url( lemon_page_url( 'eventos' ) ); ?>"><?php esc_html_e( 'Ver todos los eventos', 'lemonbook' ); ?></a></div></header>
	<?php endif; ?>
</main>
<?php get_footer(); ?>
