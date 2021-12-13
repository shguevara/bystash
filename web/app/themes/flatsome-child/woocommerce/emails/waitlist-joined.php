<?php

/**
 * The template for the waitlist joined notification sent to a customer on sign up (HTML)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/waitlist-joined.php.
 *
 * HOWEVER, on occasion WooCommerce Waitlist will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @version 3.0.0
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
		<p class="big-heading">🔔<br />Lista De Espera</p>

		<p><?php echo _x('Hola', 'Email salutation', 'woocommerce-waitlist'); ?>, esta es una confirmación de que estas en la lista de espera para:<br /></p>

		<div class="product-details">
			<img src="<?php echo $product_image[0]; ?>" />
			<?php printf(__(' %1$s', 'woocommerce-waitlist'), '<a class="product-title" href="' . esc_attr($product_link) . '?utm_source=waitlist&utm_medium=email&utm_campaign=' . $product_id . '&utm_content=product-title">' . esc_html($product_title) . '</a>', esc_html(get_bloginfo('name'))); ?>
		</div>

		<p>
			Una vez este disponible te vamos a avisar por email.<br>
			<?php $remove_link = apply_filters('wcwl_product_link_joined_email', add_query_arg(array('wcwl_remove_user' => esc_attr($email), 'product_id' => absint($product_id), 'key' => $key,), $product_link)); ?>
			<?php printf(__('Si te agregaste por error y querés salir de la lista podés hacer %sclic&nbsp;acá%s.', 'woocommerce-waitlist'), '<a href="' . esc_attr($remove_link) . '">', '</a>'); ?>
		</p>

		<p>
			<a class="cta-button-large" href="<?php echo $site_url; ?>?utm_source=waitlist&amp;utm_medium=email&amp;utm_campaign=joined&amp;utm_content=btn_top-ver-mas-productos">Ver más productos</a>
		</p>
		</p>

	</div>
	<!-- end: .copy-container -->

</div>
<!-- end: .waitlist-wrapper -->

<?php
// Include common woocommerce marketing FOOTER
include(get_template_directory() . '-child/common/email-footer-mkt.php');
?>