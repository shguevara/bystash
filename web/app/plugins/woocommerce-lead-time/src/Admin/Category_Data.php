<?php

namespace Barn2\Plugin\WC_Lead_Time\Admin;

use Barn2\Plugin\WC_Lead_Time\Util;
use Barn2\WLT_Lib\Registerable,
	Barn2\WLT_Lib\Service;

defined( 'ABSPATH' ) || exit;

/**
 * Handles fields and data on the Product Category Add/Edit screen
 *
 * @package   Barn2/woocommerce-lead-time
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Category_Data implements Registerable, Service {
	public function register() {
		add_action( 'product_cat_add_form_fields', [ $this, 'add_lead_time_field' ], 11 );
		add_action( 'product_cat_edit_form_fields', [ $this, 'edit_lead_time_field' ], 11 );

		add_action( 'created_product_cat', [ $this, 'save_lead_time' ], 10, 2 );
		add_action( 'edit_product_cat', [ $this, 'save_lead_time' ], 10, 2 );
	}

	/**
	 * Add lead time field to 'add product category' screen.
	 *
	 * @return  void
	 */
	public function add_lead_time_field() {
		echo '
			<div id="wclt-category-root"></div>
			<input type="hidden" name="wclt_category_react" value="">
			<input type="hidden" name="wclt_lead_time_format" value="">
			<input type="hidden" name="wclt_lead_time" value="">
			<input type="hidden" name="wclt_lead_time_date" value="">
			';
	}

	/**
	 * Add lead time field to 'edit product category' screen
	 *
	 * @param mixed $term The product category being edited
	 * @return void
	 */
	public function edit_lead_time_field( $term ) {
		echo '
		<tbody id="wclt-category-root"></tbody>
		<input type="hidden" name="wclt_category_react" value="">
		<input type="hidden" name="wclt_lead_time_format" value="">
		<input type="hidden" name="wclt_lead_time" value="">
		<input type="hidden" name="wclt_lead_time_date" value="">
		';
	}

	/**
	 * Save lead time
	 *
	 * @param mixed $term_id Term ID being saved
	 * @param mixed $tt_id The term taxonomy ID
	 */
	public function save_lead_time( $term_id, $tt_id = '' ) {
		if ( Util::per_stock_enabled() ) {
			$react_data = json_decode( stripslashes( $_POST['wclt_category_react'] ), true );
			if ( ! empty( $react_data ) && is_array( $react_data ) ) {
				foreach ( $react_data as $stock_status_id => $settings ) {
					$format_id    = "wclt_lead_time_format_{$stock_status_id}";
					$lead_text_id = "wclt_lead_time_{$stock_status_id}";
					$lead_date_id = "wclt_lead_time_date_{$stock_status_id}";

					$format = isset( $settings['format'] ) && ! empty( $settings['format'] ) ? sanitize_text_field( $settings['format'] ) : '';
					$text   = isset( $settings['text'] ) && ! empty( $settings['text'] ) ? sanitize_text_field( $settings['text'] ) : '';
					$date   = isset( $settings['date'] ) && ! empty( $settings['date'] ) ? sanitize_text_field( $settings['date'] ) : '';

					if ( ! empty( $text ) ) {
						update_term_meta( $term_id, $lead_text_id, $text );
					}

					if ( ! empty( $format ) ) {
						update_term_meta( $term_id, $format_id, $format );
					}

					if ( ! empty( $date ) ) {
						update_term_meta( $term_id, $lead_date_id, $date );
					}
				}
			}
		} else {
			$lead_time_format = isset( $_POST['wclt_lead_time_format'] ) && in_array( sanitize_text_field( $_POST['wclt_lead_time_format'] ), [ 'static', 'dynamic' ] ) ? sanitize_text_field( $_POST['wclt_lead_time_format'] ) : 'static';
			$lead_time_date   = sanitize_text_field( $_POST['wclt_lead_time_date'] );
			$lead_time        = filter_input( \INPUT_POST, 'wclt_lead_time', \FILTER_SANITIZE_STRING );

			if ( ! empty( $lead_time ) ) {
				update_term_meta( $term_id, 'wclt_lead_time', $lead_time );
			}

			if ( ! empty( $lead_time_format ) ) {
				update_term_meta( $term_id, 'wclt_lead_time_format', $lead_time_format );
			}

			if ( ! empty( $lead_time_date ) ) {
				update_term_meta( $term_id, 'wclt_lead_time_date', $lead_time_date );
			}
		}
	}
}
