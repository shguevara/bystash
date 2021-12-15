<?php

namespace Barn2\Plugin\WC_Lead_Time\Admin;

use Barn2\WLT_Lib\Registerable,
	Barn2\WLT_Lib\Service,
	Barn2\Plugin\WC_Lead_Time\Util;

use const Barn2\Plugin\WC_Lead_Time\PLUGIN_FILE,
		  Barn2\Plugin\WC_Lead_Time\PLUGIN_VERSION;

/**
 * This class handles updating the DB
 *
 * @package   Barn2/woocommerce-lead-time
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Updater implements Registerable, Service {
	/**
	 * Register hooks and filters.
	 */
	public function register() {
		add_action( 'admin_notices', [ $this, 'update_notices' ] );
		add_action( 'admin_init', [ $this, 'update_action' ] );
		add_action( 'admin_init', [ $this, 'dismiss_success_notice_action' ] );
	}

	/**
	 * If we need to update the database, include a message with the DB update button.
	 */
	public function update_notices() {
		$screen          = get_current_screen();
		$screen_id       = $screen ? $screen->id : '';
		$show_on_screens = [
			'dashboard',
			'plugins',
		];

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Notices should only show on WooCommerce screens, the main dashboard, and on the plugins screen.
		if ( ! in_array( $screen_id, wc_get_screen_ids(), true ) && ! in_array( $screen_id, $show_on_screens, true ) ) {
			return;
		}

		$db_version = Util::get_db_version();
		$db_updated = get_option( 'wclt_db_updated_150' );

		if ( $db_version === '1.0' && version_compare( PLUGIN_VERSION, '1.5', '>=' ) ) {
			include dirname( PLUGIN_FILE ) . '/views/admin/html-notice-update.php';
		}

		if ( $db_updated ) {
			include dirname( PLUGIN_FILE ) . '/views/admin/html-notice-updated.php';
		}
	}

	/**
	 * Handles the update action for the admin notice
	 */
	public function update_action() {
		if ( ! empty( $_GET['do_update_woocommerce_lead_time'] ) ) { // WPCS: input var ok.
			$db_version = Util::get_db_version();
			check_admin_referer( 'wclt_db_update', 'wclt_db_update_nonce' );

			$success = false;

			if ( $db_version === '1.0' && version_compare( PLUGIN_VERSION, '1.5', '>=' ) ) {
				$success = $this->update_150_variation_lead_time();
			}

			if ( $success ) {
				update_option( 'wclt_db_version', '1.5' );
				update_option( 'wclt_db_updated_150', true );
			}
		}
	}

	/**
	 * Handles the dismiss action for the upgrade success admin notice
	 */
	public function dismiss_success_notice_action() {
		if ( ! empty( $_GET['do_dismiss_wclt_db_update_success'] ) ) { // WPCS: input var ok.
			check_admin_referer( 'wclt_db_success_dismiss', 'wclt_db_success_dismiss_nonce' );

			delete_option( 'wclt_db_updated_150' );
		}
	}

	/**
	 * Converts all _wclt_lead_time keys on variations to _wclt_variation_lead_time
	 *
	 * @return bool Whether the upgrade was processed successfully
	 */
	private function update_150_variation_lead_time() {
		global $wpdb;

		$query_results = $wpdb->query(
			"UPDATE
                {$wpdb->postmeta} postmeta
            LEFT JOIN {$wpdb->posts} posts ON posts.ID = postmeta.post_id
            SET postmeta.meta_key = '_wclt_variation_lead_time'
            WHERE posts.post_type = 'product_variation' 
            AND postmeta.meta_key = '_wclt_lead_time'"
		);

		if ( $query_results !== false ) {
			return true;
		}

		return false;
	}
}
