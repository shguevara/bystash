<?php

namespace Barn2\Plugin\WC_Lead_Time;

use Barn2\WLT_Lib\Registerable,
	Barn2\WLT_Lib\Service;

/**
 * This class handles carrying the lead time on the cart and order item data
 *
 * @package   Barn2/woocommerce-lead-time
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Item_Data implements Registerable, Service {

	/**
	 * Register hooks and filters.
	 */
	public function register() {
		add_filter( 'woocommerce_add_cart_item_data', [ $this, 'add_cart_item_data' ], 10, 3 );
		add_filter( 'woocommerce_get_item_data', [ $this, 'display_cart_item_data' ], 10, 2 );
		add_action( 'woocommerce_checkout_create_order_line_item', [ $this, 'add_order_item_data' ], 10, 4 );
	}

	/**
	 * Add lead time data to cart item.
	 *
	 * @param array $cart_item_data
	 * @param int   $product_id
	 * @param int   $variation_id
	 *
	 * @return array
	 */
	public function add_cart_item_data( $cart_item_data, $product_id, $variation_id ) {
		$product = $variation_id === 0 ? wc_get_product( $product_id ) : wc_get_product( $variation_id );

		if ( ! $product || ! Util::is_visible( $product ) || ! in_array( $product->get_type(), [ 'simple', 'variable', 'variation' ] ) ) {
			return $cart_item_data;
		}

		$lead_time = Display::get_lead_time( $product );

		if ( ! $lead_time ) {
			return $cart_item_data;
		}

		$prefix = Util::get_lead_time_prefix( $product );

		if ( ! empty( $prefix ) ) {
			$lead_time = trim( str_replace( $prefix, '', $lead_time ) );
		}

		$cart_item_data['wclt-lead-time'] = $lead_time;

		return $cart_item_data;
	}

	/**
	 * Display lead time data in the cart.
	 *
	 * @param array $item_data
	 * @param array $cart_item
	 *
	 * @return array
	 */
	public function display_cart_item_data( $item_data, $cart_item ) {
		if ( empty( $cart_item['wclt-lead-time'] ) ) {
			return $item_data;
		}

		$cart_display_option     = get_option( 'wclt_display_on_cart_item', 'no' );
		$checkout_display_option = get_option( 'wclt_display_on_checkout_item', 'no' );

		if ( is_checkout() && $checkout_display_option !== 'yes' ) {
			return $item_data;
		}

		if ( ! is_checkout() && $cart_display_option !== 'yes' ) {
			return $item_data;
		}

		$product_id = isset( $cart_item['variation_id'] ) && ! empty( $cart_item['variation_id'] ) ? absint( $cart_item['variation_id'] ) : $cart_item['product_id'];
		$p          = wc_get_product( $product_id );

		$item_meta = $this->format_item_meta( wc_clean( $cart_item['wclt-lead-time'] ), $p );

		$item_data[] = [
			'key'     => $item_meta['prefix'],
			'value'   => $item_meta['lead_time'],
			'display' => '',
		];

		return $item_data;
	}

	/**
	 * Add lead time data to order item
	 *
	 * @param WC_Order_Item_Product $item
	 * @param string                $cart_item_key
	 * @param array                 $values
	 * @param WC_Order              $order
	 */
	public function add_order_item_data( $item, $cart_item_key, $values, $order ) {
		$display_option = get_option( 'wclt_display_on_order_item', 'no' );

		if ( $display_option !== 'yes' ) {
			return;
		}

		if ( empty( $values['wclt-lead-time'] ) ) {
			return;
		}

		$product_id = isset( $values['variation_id'] ) && ! empty( $values['variation_id'] ) ? absint( $values['variation_id'] ) : $values['product_id'];
		$p          = wc_get_product( $product_id );

		$item_meta = $this->format_item_meta( $values['wclt-lead-time'], $p );

		$item->add_meta_data( $item_meta['prefix'], $item_meta['lead_time'] );
	}

	/**
	 * Handles seperators ( :, - ) and formats item meta for better display in WC
	 *
	 * @param   string $lead_time
	 * @param object $product
	 * @return  array
	 */
	private function format_item_meta( $lead_time, $product = false ) {
		$prefix = Util::get_lead_time_prefix( $product );

		if ( empty( $prefix ) ) {
			if ( strpos( $lead_time, ':' ) === false ) {
				$prefix = __( 'Note', 'woocommerce-lead-time' );
			} else {
				$parts     = explode( ':', $lead_time, 2 );
				$prefix    = isset( $parts[0] ) ? trim( $parts[0] ) : __( 'Note', 'woocommerce-lead-time' );
				$lead_time = isset( $parts[1] ) ? trim( $parts[1] ) : $lead_time;
			}
		} else {
			$prefix = str_replace( ':', '', Util::get_lead_time_prefix( $product ) );
			$prefix = str_replace( ' - ', '', $prefix );
		}

		return [
			'prefix'    => $prefix,
			'lead_time' => $lead_time
		];
	}
}
