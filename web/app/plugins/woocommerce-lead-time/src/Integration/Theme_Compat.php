<?php

namespace Barn2\Plugin\WC_Lead_Time\Integration;

use Barn2\WLT_Lib\Registerable,
	Barn2\WLT_Lib\Service;

defined( 'ABSPATH' ) || exit;

/**
 * Theme-specific overrides for WooCommerce Lead Time
 *
 * @package   Barn2/woocommerce-lead-time
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Theme_Compat implements Registerable, Service {

	public function register() {

		/**
		 * Filter: allows developers to disable the escaping and sanitization of the stock status html.
		 * While this may be required for the majority of themes, some themes & plugins may
		 * need to add custom functionality with markup that is stripped out by wp_kses_post.
		 *
		 * @param bool $enabled
		 * @return bool
		 */
		$enabled = apply_filters( 'wclt_theme_compatibility_stock_escape', true );

		if ( ! $enabled ) {
			return;
		}

		$theme = \wp_get_theme();
		$name  = $theme->get( 'Name' );

		if ( $name === 'Porto' ) {
			\add_filter( 'porto_woocommerce_stock_html', [ $this, 'decode_htmlspecialchars_and_sanitize' ], \PHP_INT_MAX, 1 );
		}

		// general catch-all for other themes which esc_html on the stock html (e.g. Astra) or availability
		\add_filter( 'woocommerce_get_stock_html', [ $this, 'decode_htmlspecialchars_and_sanitize' ], \PHP_INT_MAX, 1 );
		\add_filter( 'woocommerce_get_availability_text', [ $this, 'decode_htmlspecialchars_and_sanitize' ], \PHP_INT_MAX, 1 );
	}

	public function decode_htmlspecialchars_and_sanitize( $html ) {
		return \wp_kses_post( \htmlspecialchars_decode( $html ) );
	}
}
