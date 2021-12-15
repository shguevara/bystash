<?php
/**
 * Handles integration with the Delivery Slots plugin plugin.
 *
 * @package   Barn2/woocommerce-lead-time
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */

namespace Barn2\Plugin\WC_Lead_Time\Integration;

use Barn2\Plugin\WC_Lead_Time\Display;
use Barn2\Plugin\WC_Lead_Time\Util as WC_Lead_TimeUtil;
use Barn2\WLT_Lib\Registerable,
	Barn2\WLT_Lib\Util;

/**
 * Provides integration of WLT with the Delivery slots plugin by Iconic.
 *
 * While the Iconic's plugin already provides integration with WLT,
 * their current integration only supports static lead times.
 *
 * Because version 2.0+ of WLT adds support for dynamic lead times,
 * we need to disable their filter 'iconic_wds_min_delivery_date',
 * and attach our new custom logic.
 *
 * Majority of the code is borrowed from Iconic's integration, the code
 * has been adapted to support the new metadata and options.
 */
class Delivery_Slots implements Registerable {

	public function register() {
		remove_filter( 'iconic_wds_min_delivery_date', [ 'Iconic_WDS_Compat_Lead_Time', 'min_delivery_date' ], 10 );
		add_filter( 'iconic_wds_min_delivery_date', [ $this, 'min_delivery_date' ] );
	}

	/**
	 * Modify min delivery date.
	 *
	 * @param array $data Array of "days to add data".
	 *
	 * @return array
	 */
	public static function min_delivery_date( $data = [] ) {
		if ( ! function_exists( 'Barn2\Plugin\WC_Lead_Time\wlt' ) ) {
			return $data;
		}

		$lead_time = self::get_longest_lead_time_from_cart();

		if ( empty( $lead_time ) ) {
			return $data;
		}

		$unit = self::get_lead_time_units();

		if ( self::is_timestamp( strval( $lead_time ) ) && ! WC_Lead_TimeUtil::should_date_display( $lead_time ) ) {
			return $data;
		}

		if ( self::is_timestamp( strval( $lead_time ) ) ) {
			$today = ( new \DateTime() )
				->setTimezone( wp_timezone() )
				->setTime( 0, 0, 0 );

			$target_date = ( new \DateTime() )
				->setTimezone( wp_timezone() )
				->setTime( 0, 0, 0 )
				->setTimestamp( $lead_time );

			$interval = $today->diff( $target_date );

			return [
				'days_to_add' => $interval->d,
				'timestamp'   => $lead_time,
			];
		}

		return [
			'days_to_add' => 'days' === $unit ? $lead_time : $lead_time * 7,
			'timestamp'   => strtotime( '+' . $lead_time . ' ' . $unit, time() ),
		];
	}

	/**
	 * Get lead time units.
	 *
	 * @return mixed|string|void
	 */
	public static function get_lead_time_units() {
		$unit = get_option( 'wclt_units', 'default' );

		return 'weeks' !== $unit ? 'days' : $unit;
	}

	/**
	 * Get longest possible lead time from cart items.
	 *
	 * @return bool
	 */
	public static function get_longest_lead_time_from_cart() {
		$cart_items = WC()->cart->get_cart();

		if ( empty( $cart_items ) ) {
			return false;
		}

		$lead_time = 0;

		foreach ( $cart_items as $cart_item ) {
			$product_lead_time = self::get_product_lead_time( $cart_item['data'] );

			if ( $product_lead_time && $product_lead_time <= $lead_time ) {
				continue;
			}

			$lead_time = $product_lead_time;
		}

		return $lead_time;
	}

	/**
	 * Get product lead time.
	 *
	 * @param WC_Product $product Product.
	 *
	 * @return bool|int
	 */
	public static function get_product_lead_time( $product ) {
		$parent_product = $product->get_parent_id();

		if ( $parent_product ) {
			$product = wc_get_product( $parent_product );
		}

		// Get product lead time.
		$lead_time = Display::get_lead_time_raw( $product );

		return empty( $lead_time ) ? false : absint( $lead_time );
	}

	/**
	 * Check if the string provided is a timestamp.
	 *
	 * @param string $timestamp
	 * @return boolean
	 */
	public static function is_timestamp( $timestamp ) {
		return ( (string) (int) $timestamp === $timestamp )
		&& ( $timestamp <= PHP_INT_MAX )
		&& ( $timestamp >= ~PHP_INT_MAX )
		&& ( ! strtotime( $timestamp ) );
	}

}
