<?php

namespace Barn2\Plugin\WC_Lead_Time\Integration;

use Barn2\WLT_Lib\Registerable,
	Barn2\WLT_Lib\Util;

/**
 * Handles integration with the WooCommerce Product Table plugin.
 *
 * @package   Barn2/woocommerce-lead-time
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright    Barn2 Media Ltd
 */
class Product_Table_Integration implements Registerable {

	public function register() {
		if ( ! Util::is_product_table_active() ) {
			return;
		}

		// Register product table data for 'lead-time' column.
		add_filter( 'wc_product_table_custom_table_data_lead-time', [ $this, 'get_lead_time_data' ], 10, 3 );
	}

	public function get_lead_time_data( $data, $product, $args ) {
		return new Product_Table_Data_Lead_Time( $product );
	}

}
