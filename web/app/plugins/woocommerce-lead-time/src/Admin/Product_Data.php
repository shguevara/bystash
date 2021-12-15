<?php
namespace Barn2\Plugin\WC_Lead_Time\Admin;

use Barn2\WLT_Lib\Registerable,
	Barn2\WLT_Lib\Service,
	Barn2\Plugin\WC_Lead_Time\Display,
	Barn2\Plugin\WC_Lead_Time\Util;

/**
 * Handles the lead time settings on the Edit Product screen, in the Product Data metabox.
 *
 * @package   Barn2/woocommerce-lead-time
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Product_Data implements Registerable, Service {

	/**
	 * Register hooks and filters.
	 */
	public function register() {
		add_action( 'woocommerce_product_options_stock_status', [ $this, 'add_field' ] );
		add_action( 'woocommerce_process_product_meta', [ $this, 'save_field' ] );

		add_action( 'woocommerce_variation_options_dimensions', [ $this, 'add_field_to_variations' ], 10, 3 );
		add_action( 'woocommerce_save_product_variation', [ $this, 'save_variation_fields' ], 10, 2 );
	}

	/**
	 * Add the lead time field to Product Data.
	 */
	public function add_field() {

		global $product_object;

		$per_stock_enabled  = Util::per_stock_enabled();
		$existing_lead_time = Display::get_lead_time_raw( $product_object );

		if ( is_array( $existing_lead_time ) && isset( $existing_lead_time['lead_time'] ) ) {
			$existing_lead_time = Util::timestamp_to_date( $existing_lead_time['lead_time'] );
		}

		echo '<div class="lead_time_field">';

		if ( $per_stock_enabled ) {

			echo '<div id="wclt-product-perstock-root"></div><input type="hidden" name="wclt-product-perstock-data">';

		} else {

			woocommerce_wp_select(
				[
					'id'      => '_wclt_lead_time_format',
					'label'   => __( 'Lead time', 'woocommerce-lead-time' ),
					'options' => [
						'static'  => __( 'Static', 'woocommerce-lead-time' ),
						'dynamic' => __( 'Dynamic', 'woocommerce-lead-time' ),
					]
				]
			);

			woocommerce_wp_text_input(
				[
					'id'          => '_wclt_lead_time',
					'label'       => __( 'Lead time text', 'woocommerce-lead-time' ),
					'desc_tip'    => true,
					'description' => __( 'Enter a lead time to be displayed on the product page', 'woocommerce-lead-time' ),
					'placeholder' => ! empty( $existing_lead_time ) ? $existing_lead_time : '',
				]
			);

			woocommerce_wp_hidden_input(
				[
					'id' => '_wclt_lead_time_date',
				]
			);

			echo '<div id="wclt-product-root"></div>';

		}

		echo '</div>';
	}

	/**
	 * Save the lead time field.
	 */
	public function save_field( $post_id ) {

		$per_stock_enabled = Util::per_stock_enabled();

		if ( ! $per_stock_enabled && ! isset( $_POST['_wclt_lead_time'] ) ) {
			return;
		}

		$product = wc_get_product( $post_id );

		if ( $per_stock_enabled ) {
			$stock_states         = wc_get_product_stock_status_options();
			$submitted_react_data = isset( $_POST['wclt-product-perstock-data'] ) && ! empty( $_POST['wclt-product-perstock-data'] ) ? json_decode( stripslashes( $_POST['wclt-product-perstock-data'] ), true ) : [];

			foreach ( $stock_states as $state => $label ) {
				if ( ! isset( $submitted_react_data[ $state ] ) ) {
					continue;
				}

				$lead_time_format = isset( $submitted_react_data[ $state ]['format'] ) && in_array( sanitize_text_field( $submitted_react_data[ $state ]['format'] ), [ 'static', 'dynamic' ] ) ? sanitize_text_field( $submitted_react_data[ $state ]['format'] ) : 'static';
				$lead_time        = sanitize_text_field( $submitted_react_data[ $state ]['text'] );
				$lead_time_date   = sanitize_text_field( $submitted_react_data[ $state ]['date'] );

				$product->update_meta_data( "_wclt_lead_time_{$state}", $lead_time );
				$product->update_meta_data( "_wclt_lead_time_format_{$state}", $lead_time_format );
				$product->update_meta_data( "_wclt_lead_time_date_{$state}", $lead_time_date );
			}
		} else {
			$lead_time_format = isset( $_POST['_wclt_lead_time_format'] ) && in_array( sanitize_text_field( $_POST['_wclt_lead_time_format'] ), [ 'static', 'dynamic' ] ) ? sanitize_text_field( $_POST['_wclt_lead_time_format'] ) : 'static';
			$lead_time        = sanitize_text_field( $_POST['_wclt_lead_time'] );
			$lead_time_date   = sanitize_text_field( $_POST['_wclt_lead_time_date'] );

			$product->update_meta_data( '_wclt_lead_time', $lead_time );
			$product->update_meta_data( '_wclt_lead_time_format', $lead_time_format );
			$product->update_meta_data( '_wclt_lead_time_date', $lead_time_date );
		}

		$product->save();
	}

	/**
	 * Add the lead time field to product variations.
	 */
	public function add_field_to_variations( $loop, $variation_data, $variation ) {
		$product_object     = wc_get_product( $variation->ID );
		$existing_lead_time = Display::get_lead_time_raw( $product_object );
		$post_key           = Util::get_variation_key();
		$per_stock_enabled  = Util::per_stock_enabled();

		if ( is_array( $existing_lead_time ) && isset( $existing_lead_time['lead_time'] ) ) {
			$existing_lead_time = Util::timestamp_to_date( $existing_lead_time['lead_time'] );
		}

		echo '<div class="form-row form-row-full">';

		if ( $per_stock_enabled ) {

			echo '<div class="wclt-product-variable-perstock-root" data-loop-id="' . esc_attr( $loop ) . '"></div>';
			echo '<input type="hidden" id="wclt_variation_react_data_' . esc_attr( $loop ) . '" name="wclt_variation_react_data[' . esc_attr( $loop ) . ']" value="' . esc_attr( json_encode( $this->get_variation_json( $variation->ID ) ) ) . '" />';

		} else {

			woocommerce_wp_select(
				[
					'id'                => "_wclt_lead_time_format[{$loop}]",
					'label'             => __( 'Lead time', 'woocommerce-lead-time' ),
					'options'           => [
						'static'  => __( 'Static', 'woocommerce-lead-time' ),
						'dynamic' => __( 'Dynamic', 'woocommerce-lead-time' ),
					],
					'value'             => $product_object->get_meta( '_wclt_lead_time_format' ),
					'custom_attributes' => [
						'data-lead-time-format' => true,
						'data-variation-id'     => absint( $loop )
					]
				]
			);

			woocommerce_wp_text_input(
				[
					'id'                => "{$post_key}[{$loop}]",
					'label'             => __( 'Lead time text', 'woocommerce-lead-time' ),
					'desc_tip'          => true,
					'description'       => __( 'Enter a lead time to be displayed on the product page', 'woocommerce-lead-time' ),
					'placeholder'       => ! empty( $existing_lead_time ) ? $existing_lead_time : '',
					'value'             => ! empty( $variation_data[ $post_key ][0] ) ? $variation_data[ $post_key ][0] : '',
					'custom_attributes' => [
						'data-lead-time-text' => true,
					]
				]
			);

			$date = $product_object->get_meta( '_wclt_lead_time_date' );

			woocommerce_wp_hidden_input(
				[
					'id'    => "_wclt_lead_time_date[{$loop}]",
					'value' => $date,
				]
			);

			echo '<div class="wclt-product-variable-root" data-loop-id="' . esc_attr( $loop ) . '"></div>';

		}

		echo '</div>';
	}

	/**
	 * Get the json configuration of the per stock data of a product.
	 *
	 * @param boolean $post_id
	 * @return array
	 */
	private function get_variation_json( $post_id = false ) {

		$data = [];

		$statuses = wc_get_product_stock_status_options();

		foreach ( $statuses as $stock_status_id => $label ) {

			$format = $post_id ? get_post_meta( $post_id, "_wclt_lead_time_format_{$stock_status_id}", true ) : '';

			if ( empty( $format ) ) {
				$format = 'static';
			}

			$data[ $stock_status_id ] = [
				'format' => $format,
				'text'   => $post_id ? get_post_meta( $post_id, "_wclt_lead_time_{$stock_status_id}", true ) : '',
				'date'   => $post_id ? get_post_meta( $post_id, "_wclt_lead_time_date_{$stock_status_id}", true ) : '',
			];
		}

		return [
			'variation_id' => $post_id,
			'data'         => $data
		];

	}

	/**
	 * Save the variation lead time fields.
	 */
	public function save_variation_fields( $variation_id, $i ) {
		$post_key = Util::get_variation_key();
		$product  = wc_get_product( $variation_id );

		if ( Util::per_stock_enabled() ) {
			$stock_states = wc_get_product_stock_status_options();
			$react_data   = isset( $_POST['wclt_variation_react_data'][ $i ] ) && ! empty( $_POST['wclt_variation_react_data'][ $i ] ) ? json_decode( stripslashes( $_POST['wclt_variation_react_data'][ $i ] ), true ) : [];

			foreach ( $stock_states as $state => $label ) {
				if ( ! isset( $react_data[ $state ] ) ) {
					continue;
				}

				$lead_time_format = isset( $react_data[ $state ]['format'] ) && in_array( sanitize_text_field( $react_data[ $state ]['format'] ), [ 'static', 'dynamic' ] ) ? sanitize_text_field( $react_data[ $state ]['format'] ) : 'static';
				$lead_time        = sanitize_text_field( wc_clean( wp_unslash( $react_data[ $state ]['text'] ) ) );
				$lead_time_date   = sanitize_text_field( $react_data[ $state ]['date'] );

				$product->update_meta_data( "_wclt_lead_time_{$state}", $lead_time );
				$product->update_meta_data( "_wclt_lead_time_format_{$state}", $lead_time_format );
				$product->update_meta_data( "_wclt_lead_time_date_{$state}", $lead_time_date );
			}
		} else {

			if ( ! isset( $_POST[ $post_key ][ $i ] ) ) {
				return;
			}

			if ( ! isset( $_POST['_wclt_lead_time_date'][ $i ] ) ) {
				return;
			}

			if ( ! isset( $_POST['_wclt_lead_time_format'][ $i ] ) ) {
				return;
			}

			$lead_time_format = isset( $_POST['_wclt_lead_time_format'][ $i ] ) && in_array( sanitize_text_field( $_POST['_wclt_lead_time_format'][ $i ] ), [ 'static', 'dynamic' ] ) ? sanitize_text_field( $_POST['_wclt_lead_time_format'][ $i ] ) : 'static';
			$lead_time_date   = sanitize_text_field( $_POST['_wclt_lead_time_date'][ $i ] );

			$product->update_meta_data( '_wclt_lead_time_format', wc_clean( wp_unslash( $lead_time_format ) ) );
			$product->update_meta_data( $post_key, wc_clean( wp_unslash( $_POST[ $post_key ][ $i ] ) ) );
			$product->update_meta_data( '_wclt_lead_time_date', $lead_time_date );

		}

		$product->save();
	}
}
