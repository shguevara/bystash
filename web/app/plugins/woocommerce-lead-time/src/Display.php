<?php

namespace Barn2\Plugin\WC_Lead_Time;

use Barn2\WLT_Lib\Registerable,
	Barn2\WLT_Lib\Service;

/**
 * This class handles the display of lead times in the stock / availability area.
 *
 * @package   Barn2/woocommerce-lead-time
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Display implements Registerable, Service {

	/**
	 * Register hooks and filters.
	 */
	public function register() {
		add_filter( 'woocommerce_get_stock_html', [ $this, 'maybe_add_lead_time_stock_html' ], 10, 2 );
		add_filter( 'woocommerce_get_availability_text', [ $this, 'maybe_add_lead_time_availability_text' ], 10, 2 );
		add_action( 'woocommerce_after_shop_loop_item', [ $this, 'maybe_add_lead_time_to_shop_pages' ], 10 );
	}

	/**
	 * Display lead time inside availability.
	 *
	 * @param string $availability
	 * @param WC_Product $product
	 * @return string $availability
	 */
	public function maybe_add_lead_time_availability_text( $availability, $product ) {

		if ( apply_filters( 'wclt_disable_default_output', false ) ) {
			return $availability;
		}

		if ( ! Util::is_visible( $product ) || empty( $availability ) || ( ! wp_doing_ajax() && ! is_shop() && ! is_product_category() && ! is_product() ) ) {
			return $availability;
		}

		$display_option = get_option( 'wclt_display_on_single_product', 'yes' );

		if ( $display_option !== 'yes' ) {
			return $availability;
		}

		$lead_time = self::get_lead_time( $product );

		if ( ! $lead_time ) {
			return $availability;
		}

		$text_color    = get_option( 'wclt_text_color', '#60646c' );
		$availability .= sprintf( '<span style="color: %s;" class="wclt_lead_time">&nbsp;| %s</span>', $text_color, $lead_time );

		return $availability;
	}

	/**
	 * Display lead time if conditions are met
	 *
	 * @since 1.0.0
	 *
	 * @param string $html
	 * @param WC_Product $product
	 * @return string $html
	 */
	public function maybe_add_lead_time_stock_html( $html, $product ) {
		if ( apply_filters( 'wclt_disable_default_output', false ) ) {
			return $html;
		}

		if ( ! Util::is_visible( $product ) ) {
			return $html;
		}

		$availability = $product->get_availability();

		if ( ! empty( $availability['availability'] ) ) {
			return $html;
		}

		$display_option = get_option( 'wclt_display_on_single_product', 'yes' );

		if ( $display_option !== 'yes' ) {
			return $html;
		}

		$lead_time = self::get_lead_time( $product );

		if ( ! $lead_time ) {
			return $html;
		}

		$text_color = get_option( 'wclt_text_color', '#60646c' );
		$html      .= sprintf( '<p style="color: %s;" class="stock wclt_lead_time">%s</p>', $text_color, $lead_time );

		return $html;
	}

	/**
	 * Get the global or individual lead time for the product.
	 *
	 * @param WC_Product $product
	 * @return string
	 */
	public static function get_lead_time( $product ) {
		$prefix    = Util::get_lead_time_prefix( $product );
		$units     = get_option( 'wclt_units', 'default' );
		$lead_time = self::get_lead_time_raw( $product );
		$format    = 'static';

		// This means the lead time has been retrieved from the product category or global settings and it's dynamic.
		if ( is_array( $lead_time ) ) {
			$lead_time = $lead_time['lead_time'];
			$format    = 'dynamic';
		} else {
			$format = Util::get_product_lead_time_format( $product );
		}

		if ( $format === 'dynamic' ) {
			if ( ! Util::should_date_display( $lead_time ) ) {
				return;
			}
			$lead_time = Util::timestamp_to_date( $lead_time, true );
		}

		// check if lead time is empty
		if ( empty( $lead_time ) ) {
			return false;
		}

		// output with our without prefix
		if ( empty( $prefix ) ) {
			$output = $lead_time;
		} else {
			$output = sprintf( '%s %s', $prefix, $lead_time );
		}

		// add units if we have them
		if ( in_array( $units, [ 'weeks', 'days' ] ) && $format !== 'dynamic' ) {
			$unit_strings = [
				'days'  => __( 'Days', 'woocommerce-lead-time' ),
				'weeks' => __( 'Weeks', 'woocommerce-lead-time' ),
			];

			// Fix: Don't lowercase translated strings
			$unit_string = $unit_strings['days'] === 'Days' && $unit_strings['weeks'] === 'Weeks'
				? strtolower( $unit_strings[ $units ] ) : $unit_strings[ $units ];

			$output .= sprintf( ' %s', $unit_string );
		}

		return $output;
	}

	/**
	 * Get the global or individual lead time for the product.
	 * If the format is dynamic, the date is returned as timestamp.
	 *
	 * If the lead time is retrieved from the category and the format is "dynamic".
	 * It will return an array instead of a string.
	 *
	 * @param WC_Product $product
	 * @return string|array
	 */
	public static function get_lead_time_raw( $product ) {
		$per_stock_enabled = Util::per_stock_enabled();

		// 1st Priority: Individual
		if ( $product->is_type( 'variation' ) ) {
			$format = Util::get_product_lead_time_format( $product );

			if ( $per_stock_enabled ) {
				if ( $format === 'dynamic' ) {
					$lead_time = Util::get_lead_time_date_by_stock_status( $product );
				} else {
					$lead_time = Util::get_lead_time_text_by_stock_status( $product );
				}
			} else {
				$variation_key = Util::get_variation_key();
				$lead_time     = $product->get_meta( $variation_key );

				if ( $format === 'dynamic' ) {
					$lead_time = $product->get_meta( '_wclt_lead_time_date' );
				}
			}

			// if variation doesn't have lead time then use parent $product
			if ( $lead_time == '' ) {
				$product = wc_get_product( $product->get_parent_id() );
				$format  = Util::get_product_lead_time_format( $product );

				if ( $per_stock_enabled ) {
					if ( $format === 'dynamic' ) {
						$lead_time = Util::get_lead_time_date_by_stock_status( $product );
					} else {
						$lead_time = Util::get_lead_time_text_by_stock_status( $product );
					}
				} else {
					$lead_time = $product->get_meta( '_wclt_lead_time' );
					if ( $format === 'dynamic' ) {
						$lead_time = $product->get_meta( '_wclt_lead_time_date' );
					}
				}
			}
		} else {
			$format = Util::get_product_lead_time_format( $product );

			if ( $per_stock_enabled ) {
				if ( $format === 'dynamic' ) {
					$lead_time = Util::get_lead_time_date_by_stock_status( $product );
				} else {
					$lead_time = Util::get_lead_time_text_by_stock_status( $product );
				}
			} else {
				$lead_time = $product->get_meta( '_wclt_lead_time' );

				if ( $format === 'dynamic' ) {
					$lead_time = $product->get_meta( '_wclt_lead_time_date' );
				}
			}
		}

		// 2nd Priority: Category
		if ( empty( $lead_time ) ) {
			$categories = get_the_terms( $product->get_id(), 'product_cat' );

			if ( is_array( $categories ) ) {
				foreach ( $categories as $category ) {
					if ( Util::per_stock_enabled() ) {
						$details = Util::get_lead_time_by_term_with_stock_status( $category, $product );

						if ( is_array( $details ) && ! empty( $details ) ) {
							$lead_time_format = $details['format'];
							$lead_time_date   = $details['date'];
							$lead_time        = $details['text'];
						}
					} else {
						$lead_time_format = get_term_meta( $category->term_id, 'wclt_lead_time_format', true );
						$lead_time_date   = get_term_meta( $category->term_id, 'wclt_lead_time_date', true );
						$lead_time        = get_term_meta( $category->term_id, 'wclt_lead_time', true );
					}

					if ( $lead_time_format === 'dynamic' ) {
						$lead_time = [
							'term_id'   => $category->term_id,
							'lead_time' => $lead_time_date
						];
					}

					if ( ! empty( $lead_time ) ) {
						break;
					}
				}
			}
		}

		// 3rd Priority: Global
		if ( empty( $lead_time ) ) {
			if ( Util::per_stock_enabled() ) {
				$lead_time = Util::get_global_lead_time_by_stock_status( $product );
			} else {
				$lead_time = Util::get_global_lead_time();
			}
		}

		return $lead_time;
	}

	/**
	 * Display the stock with lead time on the product pages.
	 *
	 * @return void
	 */
	public function maybe_add_lead_time_to_shop_pages() {

		global $product;

		$display = get_option( 'wclt_display_on_shop', 'no' );

		if ( $display !== 'yes' ) {
			return;
		}

		if ( ! is_shop() && ! is_product_category() && ! is_product_tag() ) {
			return;
		}

		echo wc_get_stock_html( $product );

	}

}
