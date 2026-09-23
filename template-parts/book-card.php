<?php
/**
 * Book card.
 *
 * @package LemonBook
 */

$book        = is_array( $args ?? null ) ? $args : array();
$name        = isset( $book['name'] ) ? (string) $book['name'] : '';
$author      = isset( $book['author'] ) ? (string) $book['author'] : '';
$description = isset( $book['description'] ) ? (string) $book['description'] : '';
$image       = isset( $book['image'] ) ? (string) $book['image'] : '';
$image_large = isset( $book['image_large'] ) ? (string) $book['image_large'] : '';
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
$buy_url     = isset( $book['buy_url'] ) ? (string) $book['buy_url'] : '';
?>
<article class="card book-card">
	<?php if ( $image_src ) : ?>
		<div class="book-card__media" data-image-fallback><img data-content-image src="<?php echo esc_url( $image_src ); ?>"<?php if ( $srcset ) : ?> srcset="<?php echo esc_attr( $srcset ); ?>" sizes="(max-width: 767px) calc(50vw - 1.5rem), (max-width: 1119px) calc(33.333vw - 2rem), 17rem"<?php endif; ?> width="400" height="600" loading="lazy" decoding="async" alt="<?php echo esc_attr( $name ); ?>"><div class="image-placeholder image-fallback" role="img" aria-label="<?php esc_attr_e( 'Imagen no disponible', 'lemonbook' ); ?>"></div></div>
	<?php else : ?>
		<div class="image-placeholder book-card__media" role="img" aria-label="<?php esc_attr_e( 'Imagen no disponible', 'lemonbook' ); ?>"></div>
	<?php endif; ?>
	<div class="card__body">
		<?php if ( ! empty( $book['murciano'] ) ) : ?><p><span class="tag tag--yellow"><?php esc_html_e( 'Autoría murciana', 'lemonbook' ); ?></span></p><?php endif; ?>
		<h3><?php echo esc_html( $name ?: __( 'Libro sin título', 'lemonbook' ) ); ?></h3>
		<?php if ( $author ) : ?><p class="meta"><?php echo esc_html( $author ); ?></p><?php endif; ?>
		<?php if ( $description ) : ?><p><?php echo esc_html( $description ); ?></p><?php endif; ?>
		<?php if ( isset( $book['price'] ) ) : ?><p class="price"><?php echo esc_html( lemon_price( $book['price'] ) ); ?></p><?php endif; ?>
		<?php if ( $buy_url ) : ?><p><a class="button button--ghost" href="<?php echo esc_url( $buy_url ); ?>"><?php esc_html_e( 'Ver en la tienda', 'lemonbook' ); ?></a></p><?php endif; ?>
	</div>
</article>
