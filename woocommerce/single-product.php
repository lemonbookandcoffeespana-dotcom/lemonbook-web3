<?php
/**
 * Branded WooCommerce single product wrapper.
 *
 * @package LemonBook
 */

defined( 'ABSPATH' ) || exit;
get_header();
?>
<main id="main-content" class="woocommerce-main"><div class="shell"><?php woocommerce_content(); ?></div></main>
<?php get_footer(); ?>

