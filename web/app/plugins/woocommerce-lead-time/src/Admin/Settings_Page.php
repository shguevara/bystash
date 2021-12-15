<?php
namespace Barn2\Plugin\WC_Lead_Time\Admin;

use Barn2\Plugin\WC_Lead_Time\Util as WC_Lead_TimeUtil;
use Barn2\WLT_Lib\Registerable,
	Barn2\WLT_Lib\Service,
	Barn2\WLT_Lib\Util,
	Barn2\WLT_Lib\Plugin\Licensed_Plugin,
	Barn2\WLT_Lib\WooCommerce\Admin\Custom_Settings_Fields,
	WC_Barn2_Plugin_Promo;

/**
 * Handles the WooCommerce settings page.
 *
 * @package   Barn2/woocommerce-lead-time
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Settings_Page implements Registerable, Service {

	private $id;
	private $label;
	private $plugin;
	private $license;

	public function __construct( Licensed_Plugin $plugin ) {
		$this->id      = 'lead-time';
		$this->label   = __( 'Lead time', 'woocommerce-lead-time' );
		$this->plugin  = $plugin;
		$this->license = $plugin->get_license_setting();
	}

	/**
	 * Register hooks and filters
	 */
	public function register() {
		$settings_fields = new Custom_Settings_Fields();
		$settings_fields->register();

		// Register settings and section
		add_filter( 'woocommerce_get_sections_products', [ $this, 'register_settings_section' ], 10, 1 );
		add_filter( 'woocommerce_get_settings_products', [ $this, 'get_settings' ], 10, 2 );
		add_action( 'woocommerce_admin_field_wclt_react_container', [ $this, 'display_react_container' ] );

		add_action( 'woocommerce_update_options', [ $this, 'update_react_options' ] );
		add_filter( 'woocommerce_admin_settings_sanitize_option_wclt_lead_per_stock_status', [ $this, 'sanitize_react_toggle' ], 10, 3 );

		// Sanitize and save license data
		add_filter( 'woocommerce_admin_settings_sanitize_option_' . $this->license->get_license_setting_name(), [ $this->license, 'save_license_key' ] );

		// Convert date to timestap when saving to database
		add_filter( 'woocommerce_admin_settings_sanitize_option', [ $this, 'sanitize_date' ], 10, 3 );

		// Reset to default if empty color
		add_filter( 'woocommerce_admin_settings_sanitize_option_wclt_text_color', [ $this, 'sanitize_text_color' ], 10, 3 );

		// Add the plugin promo.
		if ( class_exists( 'WC_Barn2_Plugin_Promo' ) ) {
			$promo = new WC_Barn2_Plugin_Promo( $this->plugin->get_item_id(), $this->plugin->get_file(), $this->id );
			$promo->register();
		}
	}

	/**
	 * Register the settings section.
	 *
	 * @param array $sections
	 * @return array $sections
	 */
	public function register_settings_section( $sections ) {
		$sections[ $this->id ] = $this->label;

		return $sections;
	}

	/**
	 * Register section settings
	 *
	 * @param array $settings
	 * @param string $current_section
	 * @return array $settings
	 */
	public function get_settings( $settings, $current_section ) {

		if ( $this->id !== $current_section ) {
			return $settings;
		}

		$plugin_settings = [];

		$plugin_settings[] = [
			'id'    => 'lead_time_settings_start',
			'type'  => 'settings_start',
			'class' => 'lead-time-settings barn2-settings'
		];

		$plugin_settings[] = [
			'title' => __( 'Lead time', 'woocommerce-lead-time' ),
			'type'  => 'title',
			'id'    => 'lead_time_license_section',
			'desc'  => '<p>' . __( 'The following options control the WooCommerce Lead Time extension.', 'woocommerce-lead-time' ) . '</p>'
			. '<p>'
			. Util::format_link( $this->plugin->get_documentation_url(), __( 'Documentation', 'woocommerce-lead-time' ) )
			. ' | '
			. Util::format_link( $this->plugin->get_support_url(), __( 'Support', 'woocommerce-lead-time' ) )
			. '</p>',
			'id'    => 'lead_time_options'
		];

		$plugin_settings[] = $this->license->get_license_key_setting();
		$plugin_settings[] = $this->license->get_license_override_setting();

		$plugin_settings[] = [
			'title'         => __( 'Display lead times on', 'woocommerce-lead-time' ),
			'desc'          => __( 'Single product page', 'woocommerce-lead-time' ),
			'id'            => 'wclt_display_on_single_product',
			'type'          => 'checkbox_tooltip',
			'default'       => 'yes',
			'checkboxgroup' => 'start',
		];

		$plugin_settings[] = [
			'desc'          => __( 'Cart', 'woocommerce-lead-time' ),
			'id'            => 'wclt_display_on_cart_item',
			'type'          => 'checkbox_tooltip',
			'default'       => 'no',
			'checkboxgroup' => '',
		];

		$plugin_settings[] = [
			'desc'          => __( 'Checkout', 'woocommerce-lead-time' ),
			'id'            => 'wclt_display_on_checkout_item',
			'type'          => 'checkbox_tooltip',
			'default'       => 'no',
			'checkboxgroup' => '',
		];

		$plugin_settings[] = [
			'desc'          => __( 'Shop/category pages', 'woocommerce-lead-time' ),
			'id'            => 'wclt_display_on_shop',
			'type'          => 'checkbox_tooltip',
			'default'       => 'no',
			'checkboxgroup' => '',
		];

		$plugin_settings[] = [
			'desc'          => __( 'Order details', 'woocommerce-lead-time' ),
			'desc_tip'      => __( 'Displays the lead time on order information, including in email notifications and on the order details page.', 'woocommerce-lead-time' ),
			'id'            => 'wclt_display_on_order_item',
			'type'          => 'checkbox_tooltip',
			'default'       => 'no',
			'checkboxgroup' => 'end'
		];

		$plugin_settings[] = [
			'title'         => __( 'Display lead times for', 'woocommerce-lead-time' ),
			'desc'          => __( 'In stock products', 'woocommerce-lead-time' ),
			'id'            => 'wclt_display_in_stock',
			'type'          => 'checkbox',
			'default'       => 'yes',
			'checkboxgroup' => 'start',
		];

		$plugin_settings[] = [
			'desc'          => __( 'Out of stock products', 'woocommerce-lead-time' ),
			'id'            => 'wclt_display_out_stock',
			'type'          => 'checkbox',
			'default'       => 'yes',
			'checkboxgroup' => '',
		];

		$plugin_settings[] = [
			'desc'          => __( 'Products on backorder', 'woocommerce-lead-time' ),
			'id'            => 'wclt_display_backorder',
			'type'          => 'checkbox',
			'default'       => 'yes',
			'checkboxgroup' => 'end'
		];

		$plugin_settings[] = [
			'id'   => 'wclt_react_container',
			'type' => 'wclt_react_container',
		];

		$plugin_settings[] = [
			'id'   => 'wclt_lead_per_stock_status',
			'type' => 'hidden',
		];

		$plugin_settings[] = [
			'id'   => 'wclt_prefix',
			'type' => 'hidden',
		];

		$plugin_settings[] = [
			'id'   => 'wclt_global_format',
			'type' => 'hidden',
		];

		$plugin_settings[] = [
			'id'   => 'wclt_global_time',
			'type' => 'hidden',
		];

		$plugin_settings[] = [
			'id'   => 'wclt_global_date',
			'type' => 'hidden',
		];

		$plugin_settings[] = [
			'name'     => __( 'Lead time units', 'woocommerce-lead-time' ),
			'id'       => 'wclt_units',
			'type'     => 'select',
			'default'  => 'default',
			'css'      => 'width:8.6em;',
			'options'  => [
				'default' => __( 'Select unit', 'woocommerce-lead-time' ),
				'days'    => __( 'Days', 'woocommerce-lead-time' ),
				'weeks'   => __( 'Weeks', 'woocommerce-lead-time' ),
			],
			'desc_tip' => __( 'Optional. This will be displayed after the static lead time. For dynamic lead times, the days/weeks are calculated and labelled automatically.', 'woocommerce-lead-time' ),
		];

		$plugin_settings[] = [
			'title'    => __( 'Lead time text color', 'woocommerce-lead-time' ),
			/* translators: %s: default color */
			'desc'     => sprintf( __( 'The lead time text color. Default %s.', 'woocommerce-lead-time' ), '<code>#60646c</code>' ),
			'id'       => 'wclt_text_color',
			'type'     => 'color',
			'css'      => 'width:6em;height: 30px; margin-top: 2px;',
			'default'  => '#60646c',
			'desc_tip' => true,
		];

		$plugin_settings[] = [
			'type' => 'sectionend',
			'id'   => 'lead_time_options',
		];

		$plugin_settings[] = [
			'id'   => 'lead_time_settings_end',
			'type' => 'settings_end'
		];

		return $plugin_settings;
	}

	/**
	 * Handle empty color value and set back to default.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value
	 * @return string $value
	 */
	public function sanitize_text_color( $value ) {
		if ( empty( $value ) ) {
			$value = '#60646c';
		}

		return $value;
	}

	/**
	 * Echo's the react js container dom element.
	 *
	 * @return void
	 */
	public function display_react_container() {
		echo '
		<tbody id="wclt-root"></tbody>
		<input type="hidden" name="wclt_react_data" id="wclt_react_data" value="">
		<input type="hidden" name="wclt_react_lead_time_per_stock" id="wclt_react_lead_time_per_stock" value="">';
	}

	/**
	 * Convert date options to timestamp when saving them.
	 *
	 * @param mixed $value
	 * @param array $option
	 * @param string $raw_value
	 * @return mixed
	 */
	public function sanitize_date( $value, $option, $raw_value ) {

		if ( WC_Lead_TimeUtil::per_stock_enabled() ) {
			$status = wc_get_product_stock_status_options();
			foreach ( $status as $state => $label ) {

				if ( isset( $option['id'] ) && $option['id'] === "wclt_global_time_date_{$state}" ) {
					$value = strtotime( $value );
				}
			}
		}

		return $value;
	}

	/**
	 * Make sure the toggle is saved properly, it can either be yer or no.
	 *
	 * @param mixed $value
	 * @param array $option
	 * @param string $raw_value
	 * @return mixed
	 */
	public function sanitize_react_toggle( $value, $option, $raw_value ) {

		$new_value = 'no';

		if ( $value === 'yes' ) {
			$new_value = $value;
		}

		return $new_value;

	}

	/**
	 * Update the react js options into the database.
	 *
	 * @return void
	 */
	public function update_react_options() {

		if ( ! isset( $_POST['wclt_react_data'] ) || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$react_data = json_decode( stripslashes( $_POST['wclt_react_data'] ), true );

		if ( ! empty( $react_data ) && is_array( $react_data ) ) {
			foreach ( $react_data as $stock_status_id => $settings ) {
				$prefix_id    = "wclt_global_prefix_{$stock_status_id}";
				$format_id    = "wclt_global_lead_time_format_{$stock_status_id}";
				$lead_text_id = "wclt_global_lead_time_{$stock_status_id}";
				$lead_date_id = "wclt_global_lead_time_date_{$stock_status_id}";
				$prefix       = isset( $settings['prefix'] ) && ! empty( $settings['prefix'] ) ? sanitize_text_field( $settings['prefix'] ) : '';
				$format       = isset( $settings['format'] ) && ! empty( $settings['format'] ) ? sanitize_text_field( $settings['format'] ) : '';
				$text         = isset( $settings['text'] ) && ! empty( $settings['text'] ) ? sanitize_text_field( $settings['text'] ) : '';
				$date         = isset( $settings['date'] ) && ! empty( $settings['date'] ) ? sanitize_text_field( $settings['date'] ) : '';

				update_option( $prefix_id, stripslashes( $prefix ) );
				update_option( $format_id, stripslashes( $format ) );
				update_option( $lead_text_id, $text );
				update_option( $lead_date_id, $date );
			}
		}
	}

}
