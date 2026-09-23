<?php
/**
 * Default page template, including legal pages managed by WordPress/plugins.
 *
 * @package LemonBook
 */

get_header();
?>
<main id="main-content">
	<?php while ( have_posts() ) : the_post(); ?>
		<header class="page-hero"><div class="shell"><h1><?php the_title(); ?></h1></div></header>
		<section class="section"><div class="shell content-entry"><?php the_content(); ?></div></section>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>

