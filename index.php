<?php
/**
 * Required fallback template.
 *
 * @package LemonBook
 */

get_header();
?>
<main id="main-content" class="section"><div class="shell">
	<header class="section-header"><h1><?php echo esc_html( single_post_title( '', false ) ?: __( 'Actualidad', 'lemonbook' ) ); ?></h1></header>
	<?php if ( have_posts() ) : ?><div class="posts-list"><?php while ( have_posts() ) : the_post(); ?><article <?php post_class( 'post-card' ); ?>><h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2><p class="meta"><?php echo esc_html( get_the_date() ); ?></p><?php the_excerpt(); ?></article><?php endwhile; ?></div><?php the_posts_pagination(); ?><?php else : ?><p class="empty-state"><?php esc_html_e( 'No hay contenido publicado.', 'lemonbook' ); ?></p><?php endif; ?>
</div></main>
<?php get_footer(); ?>

