<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * @class AW_Cart_Total_Savings
 */
class My_AutomateWoo_Cart_Total_Savings extends AutomateWoo\Variable {

	/** @var bool - whether to allow setting a fallback value for this variable  */
	public $use_fallback = false;

	
	public function load_admin_details() {
		$this->description = __( "Displays the total savings in a cart based on all products on sale.", 'automatewoo');
	}

	/**
	 * @param $order WC_Order
	 * @param $parameters array
	 * @return string
	 */
	public function get_value( $cart, $parameters) {

		$cart_items = $cart->get_items();

		$products = [];
		$product_ids = [];

		if ( empty( $cart_items ) ) {
			return false;
		}

		foreach ( $cart_items as $item ) {
			if ( $variation_id = $item->get_variation_id() ) {
				$product_ids[] = $variation_id;
			}
			elseif ( $product_id = $item->get_product_id() ) {
				$product_ids[] = $product_id;
			}
		}

		$product_ids = array_unique( $product_ids );

		foreach ( $product_ids as $product_id ) {
			$products[] = wc_get_product( $product_id );
		}


		$total_savings = 0;
		$total_regular_price = 0;

		foreach ( $products as $product ) {

			$regular_price = round($product->get_regular_price());

			if( $product->is_on_sale() ) {
					$sale_price = round($product->get_sale_price());
					$savings = round($regular_price - $sale_price);
					$total_savings = $total_savings + $savings;
					$total_regular_price = $total_regular_price + $regular_price;					
				}
		}

		return '$' . $total_savings;
	}

}

return new My_AutomateWoo_Cart_Total_Savings();