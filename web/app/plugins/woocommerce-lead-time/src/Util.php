<?php
namespace Barn2\Plugin\WC_Lead_Time;

defined( 'ABSPATH' ) || exit;

/**
 * Utility
 *
 * @package   Barn2/woocommerce-lead-time
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */

final class Util {

	public static function get_db_version() {
		return get_option( 'wclt_db_version', '1.0' );
	}

	public static function get_variation_key() {
		return version_compare( self::get_db_version(), '1.5', '>=' ) ? '_wclt_variation_lead_time' : '_wclt_lead_time';
	}

	/**
	 * Determine if lead time should be visible on the product.
	 *
	 * @param \WC_Product $product
	 * @return boolean
	 */
	public static function is_visible( $product ) {
		$stock_status = $product->get_stock_status();
		$display      = [
			'instock'     => get_option( 'wclt_display_in_stock', 'yes' ),
			'outofstock'  => get_option( 'wclt_display_out_stock', 'yes' ),
			'onbackorder' => get_option( 'wclt_display_backorder', 'yes' ),
		];

		$display = apply_filters( 'wclt_is_visible_stock_status', $display );

		if ( $display[ $stock_status ] === 'yes' ) {
			return true;
		}

		return false;
	}

	/**
	 * Convert timestamp to the date format used in the admin panel
	 * for the date picker.
	 *
	 * @param string $timestamp
	 * @param bool $use_wp_format whether or not we should use the date format
	 * @param bool $relative whether or not the returned date should be relative (eg: in 5 days)
	 * @return string
	 */
	public static function timestamp_to_date( $timestamp, $use_wp_format = false, $relative = true ) {
		$format = 'd M Y';

		if ( $use_wp_format ) {
			$format = get_option( 'date_format', 'd M Y' );
		}

		if ( $relative ) {
			$today = ( new \DateTime() )
				->setTimezone( wp_timezone() )
				->setTime( 0, 0, 0 );

			$target_date = ( new \DateTime() )
				->setTimezone( wp_timezone() )
				->setTime( 0, 0, 0 )
				->setTimestamp( $timestamp );

			$interval = $today->diff( $target_date );

			$month  = __( 'month', 'woocommerce-lead-time' );
			$months = __( 'months', 'woocommerce-lead-time' );
			$day    = __( 'day', 'woocommerce-lead-time' );
			$days   = __( 'days', 'woocommerce-lead-time' );
			$week   = __( 'week', 'woocommerce-lead-time' );
			$weeks  = __( 'weeks', 'woocommerce-lead-time' );
			$hours  = __( 'hours', 'woocommerce-lead-time' );

			if ( $interval->m > 1 ) { // When more than 1 month in the future.
				return sprintf(
					__( '%1$s %2$s and %3$s %4$s', 'woocommerce-lead-time' ),
					$interval->m,
					$months,
					$interval->d,
					$interval->d > 1 ? $days : $day,
				);
			} elseif ( $interval->d >= 14 ) { // When more than 1 week in the future.
				return sprintf(
					__( '%1$s %2$s and %3$s %4$s', 'woocommerce-lead-time' ),
					floor( $interval->d / 7 ),
					$weeks,
					$interval->d,
					$interval->d > 1 ? $days : $day,
				);
			} elseif ( $interval->d === 0 && $interval->h > 1 ) { // When less than 1 day.
				return sprintf(
					'%s %s',
					$interval->h,
					$hours,
				);
			}

			// When equal or less than a week in the future.
			return sprintf( '%s %s', $interval->d, $interval->d > 1 ? $days : $day );
		}

		return date( $format, $timestamp );
	}

	/**
	 * Get the format of the lead time that has been selected for the product.
	 *
	 * @param \WC_Product $product
	 * @return string
	 */
	public static function get_product_lead_time_format( $product ) {
		$allowed = [ 'static', 'dynamic' ];

		if ( self::per_stock_enabled() ) {
			$lead_time_type = self::get_lead_time_format_by_stock_status( $product );
		} else {
			$lead_time_type = $product->get_meta( '_wclt_lead_time_format' );
		}

		if ( ! $lead_time_type || empty( $lead_time_type ) || ! in_array( $lead_time_type, $allowed, true ) ) {
			return 'static';
		}

		return $lead_time_type;

	}

	/**
	 * Determine if the per stock status option is enabled.
	 *
	 * @return bool
	 */
	public static function per_stock_enabled() {
		return get_option( 'wclt_lead_per_stock_status', false ) === 'yes';
	}

	/**
	 * Get the global lead time prefix.
	 *
	 * @return string
	 */
	public static function get_global_lead_time_prefix() {
		return get_option( 'wclt_prefix' );
	}

	/**
	 * Get the global lead time format.
	 * Either dynamic or static.
	 *
	 * @return string
	 */
	public static function get_global_lead_time_format() {
		return get_option( 'wclt_global_format', 'static' );
	}

	/**
	 * Get the global lead time text.
	 *
	 * @return string
	 */
	public static function get_global_lead_time_text() {
		return get_option( 'wclt_global_time', '' );
	}

	/**
	 * Get the global lead time date (timestamp).
	 *
	 * @return string
	 */
	public static function get_global_lead_time_date() {
		return get_option( 'wclt_global_date', '' );
	}

	/**
	 * Retrieve the proper lead time content based on the global settings.
	 * If it's an array, it means the lead time format is dynamic and a timestamp will be returned too.
	 *
	 * @return string|array
	 */
	public static function get_global_lead_time() {

		$format = self::get_global_lead_time_format();
		$time   = self::get_global_lead_time_text();

		if ( $format === 'dynamic' ) {
			return [
				'dynamic'   => true,
				'lead_time' => self::get_global_lead_time_date()
			];
		}

		return $time;

	}

	/**
	 * Retrieve lead time format of a product by it's stock status.
	 *
	 * @param \WC_Product $product
	 * @return string
	 */
	public static function get_lead_time_format_by_stock_status( $product ) {
		$status = $product->get_stock_status();
		return $product->get_meta( "_wclt_lead_time_format_{$status}" );
	}

	/**
	 * Get the lead time text of a product by it's stock status.
	 *
	 * @param \WC_Product $product
	 * @return string
	 */
	public static function get_lead_time_text_by_stock_status( $product ) {
		$status = $product->get_stock_status();
		return $product->get_meta( "_wclt_lead_time_{$status}" );
	}

	/**
	 * Get the lead time date of a product by it's stock status.
	 *
	 * @param \WC_Product $product
	 * @return string
	 */
	public static function get_lead_time_date_by_stock_status( $product ) {
		$status = $product->get_stock_status();
		return $product->get_meta( "_wclt_lead_time_date_{$status}" );
	}

	/**
	 * Retrieve the globally configured lead time prefix by stock status.
	 *
	 * @param string $status
	 * @return string
	 */
	public static function get_global_lead_time_prefix_by_stock_status( $status ) {
		return get_option( "wclt_global_prefix_{$status}", '' );
	}

	/**
	 * Get the lead time prefix.
	 *
	 * If a product is provided and the dynamic stock status option
	 * is enabled, try retrieve the prefix by using the stock status id.
	 *
	 * @param \WC_Product|bool $product
	 * @return string
	 */
	public static function get_lead_time_prefix( $product = false ) {
		$prefix = self::get_global_lead_time_prefix();

		if ( self::per_stock_enabled() && $product ) {
			$status        = $product->get_stock_status();
			$status_prefix = get_option( "wclt_prefix_{$status}" );

			if ( ! empty( $status_prefix ) ) {
				$prefix = $status_prefix;
			}

			if ( empty( $status_prefix ) ) {
				$prefix = self::get_global_lead_time_prefix_by_stock_status( $status );
			}
		}

		return $prefix;

	}

	/**
	 * Retrieve the globally configured lead time format by stock status.
	 *
	 * @param string $status
	 * @return string
	 */
	public static function get_global_lead_time_format_by_status( $status ) {
		return get_option( "wclt_global_lead_time_format_{$status}", 'static' );
	}

	/**
	 * The global lead time by using the product's stock status.
	 * Returns an array when the global lead time format for the specified status
	 * is set to dynamic.
	 *
	 * @param \WC_Product $product
	 * @return string|array
	 */
	public static function get_global_lead_time_by_stock_status( $product ) {
		$lead_time           = get_option( 'wclt_global_time' );
		$status              = $product->get_stock_status();
		$lead_time_by_status = get_option( "wclt_global_lead_time_{$status}" );

		if ( self::get_global_lead_time_format_by_status( $status ) === 'dynamic' ) {
			return [
				'dynamic'   => true,
				'lead_time' => get_option( "wclt_global_lead_time_date_{$status}" )
			];
		}

		if ( ! empty( $lead_time_by_status ) ) {
			return $lead_time_by_status;
		}

		return $lead_time;

	}

	/**
	 * Determine if the given date is in the past.
	 *
	 * @param string $timestamp
	 * @return boolean
	 */
	public static function should_date_display( $timestamp ) {

		$should = true;

		$today = ( new \DateTime() )
				->setTimezone( wp_timezone() )
				->setTime( 0, 0, 0 );

		$target_date = ( new \DateTime() )
				->setTimezone( wp_timezone() )
				->setTime( 0, 0, 0 )
				->setTimestamp( $timestamp );

		if ( $target_date < $today ) {
			return false;
		}

		return $should;

	}

	/**
	 * Get lead time details using the term and stock status of a product.
	 *
	 * @param object $term
	 * @param object $product
	 * @return array
	 */
	public static function get_lead_time_by_term_with_stock_status( $term, $product ) {
		$state        = $product->get_stock_status();
		$format_id    = "wclt_lead_time_format_{$state}";
		$lead_text_id = "wclt_lead_time_{$state}";
		$lead_date_id = "wclt_lead_time_date_{$state}";

		$lead_time_format = get_term_meta( $term->term_id, $format_id, true );
		$lead_time_date   = get_term_meta( $term->term_id, $lead_date_id, true );
		$lead_time        = get_term_meta( $term->term_id, $lead_text_id, true );

		return [
			'format' => $lead_time_format,
			'date'   => $lead_time_date,
			'text'   => $lead_time,
		];

	}

	/**
	 * Adjust the label of a stock status accordingly.
	 *
	 * @param string $state
	 * @param string $label
	 * @return string
	 */
	public static function format_stock_status_label( $state, $label ) {

		switch ( $state ) {
			case 'instock':
				$label = __( 'In stock products', 'woocommerce-lead-time' );
				break;
			case 'outofstock':
				$label = __( 'Out of stock products', 'woocommerce-lead-time' );
				break;
			case 'onbackorder':
				$label = __( 'Products on backorder', 'woocommerce-lead-time' );
				break;
		}

		/**
		 * Filter: allow developers to adjust the label displayed
		 * for the dynamically generated settings of each stock status.
		 *
		 * @param string $label default label
		 * @param string $state stock status id
		 * @return string
		 */
		return apply_filters( 'wclt_settings_status_label', $label, $state );

	}

	/**
	 * Loop through stock statuses and format the label, then return the formatted array.
	 *
	 * @return array
	 */
	public static function get_formatted_wc_stock_statuses_list() {

		$list = [];

		foreach ( wc_get_product_stock_status_options() as $state => $label ) {
			$list[ $state ] = self::format_stock_status_label( $state, $label );
		}

		return $list;

	}

}
