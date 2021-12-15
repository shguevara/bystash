<?php
/**
 * Admin View: Notice - Updated.
 *
 * @package WooCommerce\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$dismiss_url = wp_nonce_url(
	add_query_arg( 'do_dismiss_wclt_db_update_success', 'true', $_SERVER['REQUEST_URI'] ),
	'wclt_db_success_dismiss',
	'wclt_db_success_dismiss_nonce'
);

?>
<div id="message" class="updated woocommerce-message wc-connect woocommerce-message--success">
	<a class="woocommerce-message-close notice-dismiss" href="<?php echo esc_url( $dismiss_url ); ?>"><?php esc_html_e( 'Dismiss', 'woocommerce-lead-time' ); ?></a>

	<p><?php esc_html_e( 'WooCommerce Lead Time database update complete. Thank you for updating to the latest version!', 'woocommerce-lead-time' ); ?></p>
</div>
