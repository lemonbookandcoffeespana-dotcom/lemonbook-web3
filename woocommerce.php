<?php
/**
 * WooCommerce fallback wrapper.
 *
 * @package LemonBook
 */

get_header();
?>
<main id="main-content" class="woocommerce-main"><div class="shell"><?php woocommerce_content(); ?></div></main>
<?php get_footer(); ?>

