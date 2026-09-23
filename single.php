<?php
/**
 * Default single post template.
 *
 * @package LemonBook
 */

get_header();
?>
<main id="main-content">
	<?php while ( have_posts() ) : the_post(); ?><header class="page-hero"><div class="shell"><p class="kicker"><?php echo esc_html( get_the_date() ); ?></p><h1><?php the_title(); ?></h1></div></header><article class="section"><div class="shell content-entry"><?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'large', array( 'loading' => 'eager' ) ); } ?><?php the_content(); ?></div></article><?php endwhile; ?>
</main>
<?php get_footer(); ?>

