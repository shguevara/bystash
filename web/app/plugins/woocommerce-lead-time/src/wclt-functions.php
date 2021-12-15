<?php

use Barn2\Plugin\WC_Lead_Time\Display;

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the lead time for a product.
 *
 * @param WC_Product $product The product.
 * @return string The unformatted lead time string.
 */
function wclt_get_lead_time( $product ) {
	return Display::get_lead_time( $product );
}

/**
 * Get the lead time for a product without the prefix or units.
 *
 * @param WC_Product $product The product.
 * @return string The unformatted lead time string.
 */
function wclt_get_lead_time_raw( $product ) {
	return Display::get_lead_time_raw( $product );
}
