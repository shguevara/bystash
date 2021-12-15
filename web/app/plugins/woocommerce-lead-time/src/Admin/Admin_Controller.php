<?php
namespace Barn2\Plugin\WC_Lead_Time\Admin;

use Barn2\Plugin\WC_Lead_Time\Util as WC_Lead_TimeUtil;
use Barn2\WLT_Lib\Registerable,
	Barn2\WLT_Lib\Service,
	Barn2\WLT_Lib\Util,
	Barn2\WLT_Lib\Conditional,
	Barn2\WLT_Lib\Plugin\Licensed_Plugin,
	Barn2\WLT_Lib\Service_Container,
	Barn2\WLT_Lib\Plugin\Admin\Admin_Links,
	Barn2\WLT_Lib\WooCommerce\Admin\Navigation;

/**
 * Sets up the admin services (e.g. the plugin settings page).
 *
 * @package   Barn2/woocommerce-lead-time
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Admin_Controller implements Registerable, Service, Conditional {

	use Service_Container;

	private $plugin;

	/**
	 * Constructor
	 */
	public function __construct( Licensed_Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	public function is_required() {
		return Util::is_admin();
	}

	/**
	 * Register hooks and filters
	 */
	public function register() {
		$this->register_services();

		add_action( 'admin_enqueue_scripts', [ $this, 'register_admin_scripts' ] );
	}

	public function get_services() {
		$services = [
			'admin_links'   => new Admin_Links( $this->plugin ),
			'settings_page' => new Settings_Page( $this->plugin ),
			'wc_navigation' => new Navigation( $this->plugin, 'products&section=lead-time', __( 'Lead Time', 'woocommerce-lead-time' ) ),
		];

		return $services;
	}

	public function register_admin_scripts( $hook ) {
		$min    = Util::get_script_suffix();
		$screen = get_current_screen();

		$admin_script_path       = 'assets/react/build/wc-lead-time-admin-settings.js';
		$admin_script_asset_path = $this->plugin->get_dir_path() . 'assets/react/build/wc-lead-time-admin-settings.asset.php';
		$admin_script_asset      = file_exists( $admin_script_asset_path )
		? require $admin_script_asset_path
		: [
			'dependencies' => [],
			'version'      => filemtime( $admin_script_path )
		];
		$script_url              = $this->plugin->get_dir_url() . $admin_script_path;

		wp_register_script(
			'wclt-admin-react-settings',
			$script_url,
			$admin_script_asset['dependencies'],
			$admin_script_asset['version'],
			true
		);

		$category_script_path       = 'assets/react/build/wc-lead-time-category.js';
		$category_script_asset_path = $this->plugin->get_dir_path() . 'assets/react/build/wc-lead-time-category.asset.php';
		$category_script_asset      = file_exists( $category_script_asset_path )
		? require $category_script_asset_path
		: [
			'dependencies' => [],
			'version'      => filemtime( $category_script_path )
		];
		$script_url                 = $this->plugin->get_dir_url() . $category_script_path;

		wp_register_script(
			'wclt-category-react-settings',
			$script_url,
			$category_script_asset['dependencies'],
			$category_script_asset['version'],
			true
		);

		$product_script_path       = 'assets/react/build/wc-lead-time-product.js';
		$product_script_asset_path = $this->plugin->get_dir_path() . 'assets/react/build/wc-lead-time-product.asset.php';
		$product_script_asset      = file_exists( $product_script_asset_path )
		? require $product_script_asset_path
		: [
			'dependencies' => [],
			'version'      => filemtime( $product_script_path )
		];
		$script_url                = $this->plugin->get_dir_url() . $product_script_path;

		wp_register_script(
			'wclt-product-react-settings',
			$script_url,
			$product_script_asset['dependencies'],
			$product_script_asset['version'],
			true
		);

		if ( $screen->post_type === 'product' && $screen->base !== 'edit-tags' ) {
			wp_enqueue_script( 'wclt-product-react-settings' );
			wp_enqueue_style( 'wclt-product', $this->plugin->get_dir_url() . 'assets/react/build/wc-lead-time-product.css', [], $product_script_asset['version'] );
			wp_add_inline_script( 'wclt-product-react-settings', 'const WCLT_Product = ' . json_encode( $this->get_product_config() ), 'before' );
		}

		if ( isset( $screen->taxonomy ) && $screen->taxonomy === 'product_cat' ) {
			wp_enqueue_script( 'wclt-category-react-settings' );
			wp_enqueue_style( 'wclt-category', $this->plugin->get_dir_url() . 'assets/react/build/wc-lead-time-category.css', [], $category_script_asset['version'] );
		}

		if ( 'woocommerce_page_wc-settings' === $hook ) {
			wp_enqueue_style( 'wclt-admin', $this->plugin->get_dir_url() . 'assets/react/build/wc-lead-time-admin-settings.css', [], $admin_script_asset['version'] );
		}

		if ( $screen->base === 'woocommerce_page_wc-settings' && isset( $_GET['section'] ) && $_GET['section'] === 'lead-time' ) {
			wp_enqueue_script( 'wclt-admin-react-settings' );
			wp_add_inline_script( 'wclt-admin-react-settings', 'const wclt_admin_settings = ' . json_encode( $this->get_status_config() ), 'before' );
		}

		wp_add_inline_script( 'wclt-category-react-settings', 'const WCLT_Category = ' . json_encode( $this->get_category_config() ), 'before' );

	}

	/**
	 * Get the javascript object array that is used on the settings panel.
	 *
	 * @return array
	 */
	private function get_status_config() {

		$config = [
			'status_list'      => [],
			'per_stock_status' => get_option( 'wclt_lead_per_stock_status', false ) === 'yes',
		];

		foreach ( wc_get_product_stock_status_options() as $state => $label ) {
			$config['status_list'][] = $state;
		}

		$config['statuses'] = WC_Lead_TimeUtil::get_formatted_wc_stock_statuses_list();

		$config['labels'] = [
			'lead_time_per_stock_title' => esc_html__( 'Lead time per stock status', 'woocommerce-lead-time' ),
			'lead_time_per_stock'       => esc_html__( 'Display a different lead time depending on the product’s stock status ', 'woocommerce-lead-time' ),
			'prefix_help'               => esc_html__( 'Add text to appear before all lead times, e.g. Lead time:', 'woocommerce-lead-time' ),
			'prefix'                    => esc_html__( 'Lead time prefix', 'woocommerce-lead-time' ),
			'format'                    => __( 'Global lead time format', 'woocommerce-lead-time' ),
			'format_options'            => [
				[
					'label' => __( 'Static', 'woocommerce-lead-time' ),
					'value' => 'static',
				],
				[
					'label' => __( 'Dynamic', 'woocommerce-lead-time' ),
					'value' => 'dynamic',
				]
			],
			'date_single'               => __( 'Date available', 'woocommerce-lead-time' ),
			'date'                      => esc_html__( 'Global lead time date', 'woocommerce-lead-time' ),
			'date_help'                 => __( 'The number of days remaining until the selected date will be displayed, along with the lead time prefix if you have specified it on the WooCommerce Lead Time settings page.', 'woocommerce-lead-time' ),
			'text'                      => esc_html__( 'Global lead time', 'woocommerce-lead-time' ),
			'text_help'                 => __( 'Enter a lead time for all products. Can be overridden for categories and individual products.', 'woocommerce-lead-time' ),
		];

		$config['data'] = [
			'lead_per_stock' => get_option( 'wclt_lead_per_stock_status' ) === 'yes',
			'statuses'       => $this->get_lead_time_per_status_data(),
			'prefix'         => get_option( 'wclt_prefix' ),
			'format'         => WC_Lead_TimeUtil::get_global_lead_time_format(),
			'date'           => WC_Lead_TimeUtil::get_global_lead_time_date(),
			'text'           => WC_Lead_TimeUtil::get_global_lead_time_text()
		];

		return $config;

	}

	/**
	 * Generate an array of data regarding the stock's details
	 * that is used by the react settings.
	 *
	 * @return array
	 */
	private function get_lead_time_per_status_data() {

		$data = [];

		foreach ( wc_get_product_stock_status_options() as $state => $label ) {

			$format_id    = "wclt_global_lead_time_format_{$state}";
			$lead_text_id = "wclt_global_lead_time_{$state}";
			$lead_date_id = "wclt_global_lead_time_date_{$state}";

			$data[ $state ] = [
				'prefix' => WC_Lead_TimeUtil::get_global_lead_time_prefix_by_stock_status( $state ),
				'format' => get_option( $format_id, 'static' ),
				'text'   => get_option( $lead_text_id, '' ),
				'date'   => get_option( $lead_date_id ),
			];
		}

		return $data;

	}


	/**
	 * Returns the array for the javascript object used by the react
	 * app on the category panel.
	 *
	 * @return array
	 */
	private function get_category_config() {

		$term         = false;
		$screen       = get_current_screen();
		$is_edit_page = $screen->id === 'edit-product_cat' && isset( $_GET['tag_ID'] ) && ! empty( $_GET['tag_ID'] );

		if ( $is_edit_page ) {
			$term = get_term_by( 'id', absint( $_GET['tag_ID'] ), 'product_cat' );
		}

		$config = [
			'per_stock_enabled' => WC_Lead_TimeUtil::per_stock_enabled(),
			'statuses'          => WC_Lead_TimeUtil::get_formatted_wc_stock_statuses_list(),
			'labels'            => [
				'title'          => esc_html__( 'Lead time', 'woocommerce-lead-time' ),
				'format'         => esc_html__( 'Lead time', 'woocommerce-lead-time' ),
				'format_options' => [
					[
						'label' => __( 'Static', 'woocommerce-lead-time' ),
						'value' => 'static',
					],
					[
						'label' => __( 'Dynamic', 'woocommerce-lead-time' ),
						'value' => 'dynamic',
					]
				],
				'text'           => esc_html__( 'Lead time text', 'woocommerce-lead-time' ),
				'text_help'      => __( 'Enter a lead time for all products in this category. Can be overridden for individual products.', 'woocommerce-lead-time' ),
				'date'           => esc_html__( 'Date available', 'woocommerce-lead-time' ),
				'date_help'      => __( 'Enter a lead time date for all products in this category. Can be overridden for individual products.', 'woocommerce-lead-time' ),
			],
			'data'              => $this->get_lead_time_config_for_category( $term ),
			'singular_data'     => $is_edit_page ? $this->get_lead_time_config_singular_category( $term ) : '',
		];

		return $config;

	}

	/**
	 * Return a formatted array with the data belonging to a product category.
	 * If no term is provided return an empty but formatted array.
	 *
	 * @param object|boolean $term
	 * @return array
	 */
	private function get_lead_time_config_for_category( $term = false ) {

		$data = [];

		foreach ( wc_get_product_stock_status_options() as $state => $label ) {

			$format_id    = "wclt_lead_time_format_{$state}";
			$lead_text_id = "wclt_lead_time_{$state}";
			$lead_date_id = "wclt_lead_time_date_{$state}";

			$lead_time_date   = '';
			$lead_time_format = '';
			$lead_time        = '';

			if ( $term ) {
				$lead_time        = get_term_meta( $term->term_id, $lead_text_id, true );
				$lead_time_format = get_term_meta( $term->term_id, $format_id, true );
				$lead_time_date   = get_term_meta( $term->term_id, $lead_date_id, true );
			}

			if ( empty( $lead_time_format ) ) {
				$lead_time_format = 'static';
			}

			$data[ $state ] = [
				'format' => $lead_time_format,
				'text'   => $lead_time,
				'date'   => $lead_time_date,
			];
		}

		return $data;

	}

	/**
	 * Return a formatted array with the singular data belonging to a product category.
	 *
	 * @param object|boolean $term
	 * @return array
	 */
	private function get_lead_time_config_singular_category( $term = false ) {

		$format_id    = 'wclt_lead_time_format';
		$lead_text_id = 'wclt_lead_time';
		$lead_date_id = 'wclt_lead_time_date';

		$lead_time        = get_term_meta( $term->term_id, $lead_text_id, true );
		$lead_time_format = get_term_meta( $term->term_id, $format_id, true );
		$lead_time_date   = get_term_meta( $term->term_id, $lead_date_id, true );

		return [
			'format' => $lead_time_format,
			'text'   => $lead_time,
			'date'   => $lead_time_date,
		];

	}

	/**
	 * Get the json data used on the product edit page.
	 *
	 * @return array
	 */
	private function get_product_config() {

		return [
			'statuses' => WC_Lead_TimeUtil::get_formatted_wc_stock_statuses_list(),
			'data'     => $this->get_lead_times_per_stock_config_by_product(),

			'labels'   => [
				'block_prefix'   => esc_html__( 'Lead time', 'woocommerce-lead-time' ),
				'date'           => esc_html__( 'Date available', 'woocommerce-lead-time' ),
				'date_help'      => __( 'The number of days remaining until the selected date will be displayed, along with the lead time prefix if you have specified it on the WooCommerce Lead Time settings page.', 'woocommerce-lead-time' ),
				'format'         => esc_html__( 'Lead time', 'woocommerce-lead-time' ),
				'format_options' => [
					[
						'label' => __( 'Static', 'woocommerce-lead-time' ),
						'value' => 'static',
					],
					[
						'label' => __( 'Dynamic', 'woocommerce-lead-time' ),
						'value' => 'dynamic',
					]
				],
				'text'           => esc_html__( 'Lead time text', 'woocommerce-lead-time' ),
				'text_help'      => __( 'Enter a lead time to be displayed on the product page', 'woocommerce-lead-time' ),
			]
		];

	}

	/**
	 * Get the json data of lead times per stock for the product edit page.
	 *
	 * @return array
	 */
	private function get_lead_times_per_stock_config_by_product() {
		$data    = [];
		$post_id = isset( $_GET['post'] ) && get_post_type( absint( $_GET['post'] ) ) === 'product' ? absint( absint( $_GET['post'] ) ) : false;

		foreach ( wc_get_product_stock_status_options() as $state => $label ) {
			$data[ $state ] = [
				'format' => $post_id ? get_post_meta( $post_id, "_wclt_lead_time_format_{$state}", true ) : '',
				'text'   => $post_id ? get_post_meta( $post_id, "_wclt_lead_time_{$state}", true ) : '',
				'date'   => $post_id ? get_post_meta( $post_id, "_wclt_lead_time_date_{$state}", true ) : '',
			];
		}

		return $data;

	}

}
