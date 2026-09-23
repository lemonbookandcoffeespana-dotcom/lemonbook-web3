<?php
/**
 * Menu item row.
 *
 * @package LemonBook
 */

$item        = is_array( $args ?? null ) ? $args : array();
$name        = isset( $item['name'] ) ? (string) $item['name'] : '';
$description = isset( $item['description'] ) ? (string) $item['description'] : '';
$allergens   = isset( $item['allergens'] ) && is_array( $item['allergens'] ) ? $item['allergens'] : array();
$image       = isset( $item['image'] ) ? (string) $item['image'] : '';
$image_large = isset( $item['image_large'] ) ? (string) $item['image_large'] : '';
$image_src   = $image ?: $image_large;
$srcset      = implode(
	', ',
	array_filter(
		array(
			$image ? esc_url_raw( $image ) . ' 400w' : '',
			$image_large ? esc_url_raw( $image_large ) . ' 1200w' : '',
		)
	)
);
?>
<li class="menu-item">
	<div class="menu-item__media dish-media"<?php if ( $image_src ) : ?> data-image-fallback<?php endif; ?>>
		<?php if ( $image_src ) : ?><img data-content-image src="<?php echo esc_url( $image_src ); ?>"<?php if ( $srcset ) : ?> srcset="<?php echo esc_attr( $srcset ); ?>" sizes="(max-width: 607px) calc(100vw - 2rem), 176px"<?php endif; ?> width="400" height="300" loading="lazy" decoding="async" alt="<?php echo esc_attr( $name ); ?>"><div class="image-placeholder image-fallback" role="img" aria-label="<?php esc_attr_e( 'Imagen no disponible', 'lemonbook' ); ?>"></div><?php else : ?><div class="image-placeholder" role="img" aria-label="<?php esc_attr_e( 'Imagen no disponible', 'lemonbook' ); ?>"></div><?php endif; ?>
	</div>
	<div class="menu-item__content">
		<div class="menu-item__title-row"><h3><?php echo esc_html( $name ?: __( 'Producto sin nombre', 'lemonbook' ) ); ?></h3></div>
		<?php if ( $description ) : ?><p><?php echo esc_html( $description ); ?></p><?php endif; ?>
		<?php if ( $allergens ) : ?>
			<ul class="allergen-list" aria-label="<?php esc_attr_e( 'Alérgenos', 'lemonbook' ); ?>">
				<?php foreach ( $allergens as $allergen ) :
					$label = isset( $allergen['label'] ) ? (string) $allergen['label'] : '';
					if ( ! $label ) { continue; }
					?>
					<li class="tag"><?php echo esc_html( $label ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
	<?php if ( isset( $item['price'] ) ) : ?><span class="price"><?php echo esc_html( lemon_price( $item['price'] ) ); ?></span><?php endif; ?>
</li>
