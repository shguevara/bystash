<?php
namespace Barn2\Plugin\WC_Lead_Time;

use Barn2\WLT_Lib\Registerable,
	Barn2\WLT_Lib\Plugin\Licensed_Plugin,
	Barn2\WLT_Lib\Plugin\Premium_Plugin,
	Barn2\WLT_Lib\Translatable,
	Barn2\WLT_Lib\Service_Provider,
	Barn2\WLT_Lib\Service,
	Barn2\WLT_Lib\Util;

/**
 * The main plugin class. Sets up the plugin services and loads them when WordPress runs.
 *
 * @package   Barn2/woocommerce-lead-time
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Plugin extends Premium_Plugin implements Licensed_Plugin, Registerable, Translatable, Service_Provider {

	const NAME    = 'WooCommerce Lead Time';
	const ITEM_ID = 128235;

	/**
	 * @var Service[] The list of plugin services.
	 */
	private $services = [];

	/**
	 * Creates a new Plugin object.
	 */
	public function __construct( $file, $version = '1.0' ) {
		parent::__construct(
			[
				'file'               => $file,
				'version'            => $version,
				'name'               => self::NAME,
				'item_id'            => self::ITEM_ID,
				'is_woocommerce'     => true,
				'settings_path'      => 'admin.php?page=wc-settings&tab=products&section=lead-time',
				'documentation_path' => 'kb-categories/woocommerce-lead-time-kb/',
				'legacy_db_prefix'   => 'wc_lead_time'
			]
		);
	}

	public function register() {
		parent::register();

		add_action( 'plugins_loaded', [ $this, 'load_services' ] );
		add_action( 'init', [ $this, 'load_textdomain' ] );
	}

	/**
	 * Initialise service classes.
	 */
	public function load_services() {
		// Don't load services if WooCommerce is not installed & active.
		if ( ! Util::is_woocommerce_active() ) {
			return;
		}

		// load admin before license check for license entry
		if ( Util::is_admin() ) {
			$this->services['admin'] = new Admin\Admin_Controller( $this );
		}

		// if ( $this->has_valid_license() ) {
		if ( Util::is_admin() ) {
			$this->services['updater']       = new Admin\Updater();
			$this->services['product_data']  = new Admin\Product_Data();
			$this->services['category_edit'] = new Admin\Category_Data();
		}

			$this->services['display']   = new Display();
			$this->services['item_data'] = new Item_Data();

		if ( Util::is_front_end() ) {
			$this->services['product_table']  = new Integration\Product_Table_Integration();
			$this->services['delivery_slots'] = new Integration\Delivery_Slots();
			$this->services['theme_compat']   = new Integration\Theme_Compat();
		}
		// }

		Util::register_services( $this->services );
	}

	/**
	 * Load the plugin language file for i18n
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'woocommerce-lead-time', false, $this->get_slug() . '/languages' );
	}

	/**
	 * Gets the plugin service by ID.
	 *
	 * @param string $id The service ID.
	 * @return null|Service The service object or null if not found.
	 */
	public function get_service( $id ) {
		return isset( $this->services[ $id ] ) ? $this->services[ $id ] : null;
	}

	/**
	 * Gets all the plugin services.
	 *
	 * @return Service[] The array of plugin services.
	 */
	public function get_services() {
		return $this->services;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @return Plugin A single instance of this class.
	 * @deprecated 1.2
	 */
	public static function get_instance() {
		_deprecated_function( __METHOD__, '1.2', 'Barn2\\Plugin\\WC_Lead_Time\\wlt()' );
		return wlt();
	}

	/**
	 * Return the plugin slug.
	 *
	 * @return string The plugin slug.
	 * @deprecated 1.2
	 */
	public function get_plugin_slug() {
		_deprecated_function( __METHOD__, '1.2', '$this->get_slug()' );
		return $this->get_slug();
	}

}
