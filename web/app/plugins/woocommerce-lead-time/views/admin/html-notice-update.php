<?php
/**
 * Admin View: Notice - Update
 *
 * @package WooCommerce\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$update_url = wp_nonce_url(
	add_query_arg( 'do_update_woocommerce_lead_time', 'true', admin_url( 'admin.php?page=wc-settings&tab=products&section=lead-time' ) ),
	'wclt_db_update',
	'wclt_db_update_nonce'
);

?>
<div id="message" class="updated woocommerce-message wc-connect">
	<p>
		<strong><?php esc_html_e( 'WooCommerce Lead Time database update required', 'woocommerce-lead-time' ); ?></strong>
	</p>
	<p>
		<?php
			esc_html_e( 'WooCommerce Lead Time has been updated! To keep things running smoothly, we have to update your database to the newest version. We recommend backing up your site first.', 'woocommerce-lead-time' );
		?>
	</p>
	<p class="submit">
		<a href="<?php echo esc_url( $update_url ); ?>" class="wc-update-now button-primary">
			<?php esc_html_e( 'Update WooCommerce Lead Time Database', 'woocommerce-lead-time' ); ?>
		</a>
	</p>
</div>
