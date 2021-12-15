<?php

namespace Barn2\Plugin\WC_Lead_Time\Integration;

use Abstract_Product_Table_Data,
	Barn2\Plugin\WC_Product_Table\Data\Abstract_Product_Data,
	Barn2\Plugin\WC_Lead_Time\Display,
	Barn2\Plugin\WC_Lead_Time\Util;

/**
 * Handles the Product Table data for the lead time column.
 *
 * @package   Barn2\woocommerce-lead-time
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
if ( ! class_exists( 'Abstract_Product_Table_Data' ) && ! class_exists( '\Barn2\Plugin\WC_Product_Table\Data\Abstract_Product_Data' ) ) {
	return;
}

if ( class_exists( '\Barn2\Plugin\WC_Product_Table\Data\Abstract_Product_Data' ) ) {
	class Product_Table_Data_Lead_Time extends Abstract_Product_Data {

		public function __construct( \WC_Product $product ) {
			parent::__construct( $product, '' );
		}

		public function get_data() {
			// Retrieve the lead time

			if ( ! Util::is_visible( $this->product ) ) {
				return '';
			}

			$lead_time = Display::get_lead_time( $this->product );

			// Return the lead time.
			return apply_filters( 'wc_product_table_data_lead_time', $lead_time, $this->product );
		}

	}

} else {
	class Product_Table_Data_Lead_Time extends Abstract_Product_Table_Data {

		public function __construct( \WC_Product $product ) {
			parent::__construct( $product, '' );
		}

		public function get_data() {
			// Retrieve the lead time
			$lead_time = Display::get_lead_time( $this->product );

			// Return the lead time.
			return apply_filters( 'wc_product_table_data_lead_time', $lead_time, $this->product );
		}

	}
}


