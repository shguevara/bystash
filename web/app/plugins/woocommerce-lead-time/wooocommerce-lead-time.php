<?php
/**
 * The main plugin file for WooCommerce Lead Time.
 *
 * This file is included during the WordPress bootstrap process if the plugin is active.
 *
 * @package   Barn2/woocommerce-lead-time
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 *
 * @wordpress-plugin
 * Plugin Name:     WooCommerce Lead Time
 * Plugin URI:      https://barn2.com/wordpress-plugins/woocommerce-lead-time/
 * Description:     Provide individual and global lead times for your WooCommerce products.
 * Version:         2.0.0
 * Author:          Barn2 Plugins
 * Author URI:      https://barn2.com
 * Text Domain:     woocommerce-lead-time
 * Domain Path:     /languages
 *
 * WC requires at least: 3.7
 * WC tested up to: 5.7
 *
 * Copyright:       Barn2 Media Ltd
 * License:         GNU General Public License v3.0
 * License URI:     http://www.gnu.org/licenses/gpl-3.0.html
 */
namespace Barn2\Plugin\WC_Lead_Time;

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const PLUGIN_VERSION = '2.0.0';
const PLUGIN_FILE    = __FILE__;

// Include autoloader.
require_once __DIR__ . '/vendor/autoload.php';

/**
 * Helper function to access the shared plugin instance.
 *
 * @return Plugin
 */
function wlt() {
	return Plugin_Factory::create( PLUGIN_FILE, PLUGIN_VERSION );
}

// Load the plugin.
wlt()->register();

