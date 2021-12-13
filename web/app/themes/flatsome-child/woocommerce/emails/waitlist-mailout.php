<?php

/**
 * The template for the waitlist in stock notification email (HTML)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/waitlist-mailout.php.
 *
 * HOWEVER, on occasion WooCommerce Waitlist will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @version 2.1.9
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

// Include Global Variables
include(get_template_directory() . '-child/common/global_variables.php');
$product_image = wp_get_attachment_image_src(get_post_thumbnail_id($product_id), 'single-post-thumbnail');
$email = sanitize_email($email);

do_action('woocommerce_email_header', $email_heading, $email); ?>

<div id="waitlist-wrapper">
	<div class="copy-container">

		<p class="big-heading">🥳<br />Ya Esta Disponible!</p>

		<p>La espera terminó, este producto ya está disponible:</p>
		<div class="product-details">
			<img src="<?php echo $product_image[0]; ?>" />
			<?php printf(__(' %1$s', 'woocommerce-waitlist'), '<a class="product-title" href="' . esc_attr($product_link) . '?utm_source=waitlist&utm_medium=email&utm_campaign=' . $product_id . '&utm_content=product-title">' . esc_html($product_title) . '</a>', esc_html(get_bloginfo('name'))); ?>
		</div>
		<p>
			<?php printf(__('%2$s', 'woocommerce-waitlist'), $product_title, '<a class="cta-button-large" target="_blank" href="' . $product_link . '&add-to-cart=' . $product_id . '&utm_source=waitlist&utm_medium=email&utm_campaign=' . $product_id . '&utm_content=btn_top-comprar-ahora">Comprar ahora</a>'); ?>
		</p>
		<p>Hacelo tuyo en 3 clics y empezá a disfrutarlo ahora.</p>

	</div>
	<!-- end: ".copy-container -->

</div>

<?php
// Include common woocommerce marketing FOOTER
include(get_template_directory() . '-child/common/email-footer-mkt.php');
?>