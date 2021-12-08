<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YWSON_Manager' ) ) {

	class YWSON_Manager {

		protected static $instance;

		protected $plugin_meta;

		public function __construct() {

			$this->plugin_meta = array(
				'basic'        => '_ywson_custom_number_order_complete',
				'free'         => '_ywson_custom_number_order_complete',
				'quote'        => 'ywson_custom_quote_number_order',
				'subscription' => 'ywson_custom_subscription_number_order',
			);

			//create the sequential order number from admin
			add_action( 'woocommerce_process_shop_order_meta', array(
				$this,
				'save_sequential_order_number',
			), 50, 2 );

			add_action( 'woocommerce_checkout_update_order_meta', array(
				$this,
				'save_sequential_order_number_on_checkout',
			), 10, 1 );

			/**REST API*/
			add_action( 'woocommerce_api_create_order', array( $this, 'save_sequential_order_number' ), 10, 1 );

			if ( ! defined( 'YITH_WPV_PREMIUM' ) ) {
				add_action( 'woocommerce_new_order', array( $this, 'save_sequential_order_number' ), 10, 1 );
			}
			/*order tracking page*/
			add_filter( 'woocommerce_shortcode_order_tracking_order_id', array(
				$this,
				'get_order_by_custom_order_number',
			) );
			/*print custom order number*/
			add_filter( 'woocommerce_order_number', array( $this, 'get_custom_order_number' ), 10, 1 );

			/**YITH WooCommerce Request a Quote Integration*/
			add_action( 'ywraq_after_create_order', array( $this, 'save_sequential_order_number' ), 10, 1 );
			add_action( 'ywraq_after_create_order_from_checkout', array(
				$this,
				'create_sequential_order_number_from_checkout',
			), 10, 2 );
			add_filter( 'ywraq_quote_number', array( $this, 'get_custom_order_number' ), 10, 1 );

			/**YITH WooCommerce Multi Vendor compatibility*/
			add_action( 'yith_wcmv_suborder_created', array( $this, 'save_sequential_order_number' ), 10, 1 );

			add_action( 'woocommerce_order_status_changed', array(
				$this,
				'create_sequential_order_number_from_accepted_quote',
			), 20, 3 );

			//YITH Subscription integration
			add_action( 'ywsbs_renew_subscription', array( $this, 'save_sequential_order_number' ), 20, 1 );
			add_action( 'ywsbs_subscription_created', array(
				$this,
				'save_subscription_sequential_order_number'
			), 20, 1 );
			add_filter( 'ywsbs_get_number', array( $this, 'get_subscription_sequential_order_number' ), 20, 2 );

		}

		/**
		 * @return YWSON_Manager
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * check if current module is active
		 * @return bool
		 * @since  1.1.0
		 * @author Salvatore Strano
		 */
		public function is_basic_module_active() {

			$is_active = get_option( 'ywson_base_module_settings', array() );
			$is_active = isset( $is_active['enabled'] ) && 'yes' == $is_active['enabled'];

			return $is_active;
		}

		/**
		 * check if current module is active
		 * @return bool
		 * @since  1.1.0
		 * @author Salvatore Strano
		 */
		public function is_free_module_active() {

			$is_active = get_option( 'ywson_free_module_settings', array() );
			$is_active = isset( $is_active['enabled'] ) && 'yes' == $is_active['enabled'];

			return $is_active;
		}

		/**
		 * check if current module is active
		 * @return bool
		 * @since  1.1.0
		 * @author Salvatore Strano
		 */
		public function is_quote_module_active() {

			$is_active = get_option( 'ywson_quote_module_settings', array() );
			$is_active = isset( $is_active['enabled'] ) && 'yes' == $is_active['enabled'];

			return 'yes' == $is_active && ywson_is_raq_active();
		}

		/**
		 * get the right prefix
		 *
		 * @param string $type ( basic|free|quote )
		 *
		 * @return string
		 * @author Salvatore Strano
		 * @since  1.1.0
		 *
		 */
		public function get_prefix( $type = 'basic' ) {

			switch ( $type ) {
				case 'free':
					$option_name = 'ywson_free_module_settings';
					break;
				case 'quote':
					$option_name = 'ywson_quote_module_settings';
					break;
				case 'subscription':
					$option_name = 'ywson_subscription_module_settings';
					break;
				default:
					$option_name = 'ywson_base_module_settings';
					break;
			}

			$prefix = get_option( $option_name, '' );
			$prefix = isset( $prefix['order_prefix'] ) ? $prefix['order_prefix'] : '';

			return $prefix;
		}

		/**
		 * get the right suffix
		 *
		 * @param string $type ( basic|free|quote )
		 *
		 * @return string
		 * @author Salvatore Strano
		 * @since  1.1.0
		 *
		 */
		public function get_suffix( $type = 'basic' ) {

			switch ( $type ) {
				case 'free':
					$option_name = 'ywson_free_module_settings';
					break;
				case 'quote':
					$option_name = 'ywson_quote_module_settings';
					break;
				case 'subscription':
					$option_name = 'ywson_subscription_module_settings';
					break;
				default:
					$option_name = 'ywson_base_module_settings';
					break;
			}

			$suffix = get_option( $option_name, '' );
			$suffix = isset( $suffix['order_suffix'] ) ? $suffix['order_suffix'] : '';

			return $suffix;
		}

		/**
		 * get the next order number
		 *
		 * @param string $type
		 *
		 * @return int
		 */
		public function get_next_number( $type = 'basic' ) {
			switch ( $type ) {
				case 'free':
					$option_name = 'ywson_free_module_settings';
					break;
				case 'quote':
					$option_name = 'ywson_quote_module_settings';
					break;
				case 'subscription':
					$option_name = 'ywson_subscription_module_settings';
					break;
				default:
					$option_name = 'ywson_base_module_settings';
					break;
			}

			global $wpdb;
			wp_cache_delete( $option_name, 'options' );
			$query = $wpdb->prepare( "SELECT option_value AS next_number FROM {$wpdb->options} WHERE option_name = %s ", $option_name );

			$value = maybe_unserialize( $wpdb->get_var( $query ) );

			$this->update_next_number( $value, $option_name );

			return $value['order_number'];
		}

		/**
		 * update the next number option
		 *
		 * @param int $current_number
		 * @param string $option_name
		 *
		 * @return false|int
		 */
		public function update_next_number( $current_number, $option_name ) {

			if ( is_array( $current_number ) ) {
				$current_number['order_number'] += 1;
			} else {
				$current_number += 1;
			}
			global $wpdb;

			$update_args = array(
				'option_value' => maybe_serialize( $current_number ),
			);

			$success = false;


			for ( $i = 0; $i < 3 && ! $success; $i ++ ) {
				$success = $wpdb->update( $wpdb->options, $update_args, array( 'option_name' => $option_name ) );
			}

			return $success;
		}

		/**
		 * return the sequential order number formatted
		 *
		 * @param WC_Order|YWSBS_Subscription $order
		 * @param string $type
		 *
		 * @return string
		 * @since  1.1.0
		 *
		 * @author Salvatore Strano
		 */
		public function get_formatted_sequential_order_number( $order, $type = 'basic' ) {

			$order_date = apply_filters( 'ywson_order_date', current_time( 'timestamp', 0 ), $order->get_id() );

			$prefix = apply_filters( 'yith_sequential_order_number_prefix', $this->format_string_with_date( $this->get_prefix( $type ), $order_date ), $this->get_prefix( $type ), $order_date );
			$suffix = apply_filters( 'yith_sequential_order_number_suffix', $this->format_string_with_date( $this->get_suffix( $type ), $order_date ), $this->get_suffix( $type ), $order_date );
			$number = apply_filters( 'ywson_next_number_order', $this->get_next_number( $type ) );

			$order_number = $prefix . $number . $suffix;

			return apply_filters( 'ywson_get_formatted_sequential_order_number', $order_number, $order, $type );
		}

		/**
		 * replace in prefix or suffix the placeholder
		 *
		 * @param string $string
		 * @param int $date ( timestamp )
		 *
		 * @return string
		 * @since  1.1.0
		 *
		 * @author Salvatore Strano
		 */
		public function format_string_with_date( $string, $date ) {

			$string = str_replace(
				array( '[D]', '[DD]', '[M]', '[MM]', '[YY]', '[YYYY]', '[h]', '[hh]', '[m]', '[s]' ),
				array(
					date( 'j', $date ),
					date( 'd', $date ),
					date( 'n', $date ),
					date( 'm', $date ),
					date( 'y', $date ),
					date( 'Y', $date ),
					date( 'G', $date ),
					date( 'H', $date ),
					date( 'i', $date ),
					date( 's', $date ),
				),
				$string
			);

			return $string;
		}

		/**check if this order is free
		 *
		 * @param WC_Order $order
		 *
		 * @return bool
		 * @author YITH
		 * @since  1.1.0
		 *
		 */
		private function is_order_free( $order ) {

			$module    = get_option( 'ywson_free_module_settings', [] );
			$type_free = isset( $module['order_type'] ) ? $module['order_type'] : 'order_tot';
			$free      = false;

			switch ( $type_free ) {

				case 'order_tot' :
					$total = floatval( $order->get_total() );
					$free  = floatval( 0 ) === $total;
					break;

				case 'product_ord' :

					$product_in_order = $order->get_items();

					if ( count( $product_in_order ) > 0 ) {
						$free = true;
						foreach ( $product_in_order as $product ) {
							/**
							 * @var $product WC_Order_Item_Product
							 */

							if ( $product->get_subtotal( 'edit' ) > 0 ) {
								$free = false;
								break;
							}
						}
					}

					break;
			}

			return $free;
		}

		/**
		 * generate a new sequential when create a order from backend
		 *
		 * @param int $post_id
		 * @param WP_Post|array $post
		 *
		 * @see    woocommerce_process_shop_order_meta
		 *
		 * @author Salvatore Strano
		 * @since  1.1.0
		 */
		public function save_sequential_order_number( $post_id, $post = array() ) {

			$order = wc_get_order( $post_id );

			$this->generate_sequential_order_number( $order );

		}

		/**
		 * generate a new sequential on checkout page
		 *
		 * @param int $order_id
		 *
		 * @since  1.1.0
		 * @see    woocommerce_checkout_update_order_meta
		 *
		 * @author Salvatore Strano
		 */
		public function save_sequential_order_number_on_checkout( $order_id ) {

			$order = wc_get_order( $order_id );

			$this->generate_sequential_order_number( $order );
		}

		/**
		 * generate a new sequential order number
		 *
		 * @param WC_Order $order
		 *
		 * @since  1.1.0
		 *
		 * @author Salvatore Strano
		 */
		public function generate_sequential_order_number( $order ) {
			$order_status = $order->get_status();

			if ( ! apply_filters( 'ywson_generate_sequential_order_number', true, $order ) ) {
				return;
			}


			if ( ( ( 'draft' == $order_status && isset( $_REQUEST['yit_metaboxes']['ywraq_raq'] ) ) || ( 'ywraq-new' == $order_status ) || ( isset( $_REQUEST['payment_method'] ) && 'yith-request-a-quote' == $_REQUEST['payment_method'] ) ) && $this->is_quote_module_active() ) {
				$this->create_order_number( $order, 'quote' );
			} elseif ( $this->is_free_module_active() && $this->is_order_free( $order ) ) {
				$this->create_order_number( $order, 'free' );
			} elseif ( $this->is_basic_module_active() ) {
				$this->create_order_number( $order );
			}

		}


		/**
		 * @param WC_Order $order
		 * @param string $type
		 */
		public function create_order_number( $order, $type = 'basic' ) {

			$number_meta_key = $this->plugin_meta[ $type ];

			$number_meta = $order->get_meta( $number_meta_key );

			if ( empty( $number_meta ) ) {

				$number_meta = $this->get_formatted_sequential_order_number( $order, $type );

				$order->update_meta_data( $number_meta_key, $number_meta );
				$order->save();
			}
		}

		/**
		 * @param int $order_id
		 * return string
		 */
		public function get_custom_order_number( $order_id ) {

			$order = wc_get_order( $order_id );

			if ( ! $order ) {
				return $order_id;
			}

			$order_status = $order->get_status();
			$quote_status = function_exists( 'YITH_YWRAQ_Order_Request' ) ? YITH_YWRAQ_Order_Request()->get_quote_order_status() : array();
			$quote_status = array_keys( $quote_status );

			$type = 'basic';

			if ( 'ywraq_quote_number' === current_filter() || ( count( $quote_status ) > 0 && in_array( 'wc-' . $order_status, $quote_status ) ) ) {
				$type = 'quote';

			}

			$number_meta_key     = $this->plugin_meta[ $type ];
			$number_meta         = $order->get_meta( $number_meta_key );
			$custom_order_number = empty( $number_meta ) ? $order_id : $number_meta;

			return apply_filters( 'yith_son_get_order_number', $custom_order_number, $order_id, $number_meta );
		}

		/**
		 * return the right order id
		 *
		 * @param int $order_id
		 *
		 * @return int
		 * @author Salvatore Strano
		 * @since  1.1.0
		 *
		 */
		public function get_order_by_custom_order_number( $order_id ) {

			$order_number = ywson_get_order_id_by_order_number( $_REQUEST['orderid'] );

			return $order_number ? $order_number : $order_id;
		}


		/**
		 * @param array $raq
		 * @param WC_Order $order
		 */
		public function create_sequential_order_number_from_checkout( $raq, $order ) {

			if ( $this->is_quote_module_active() ) {
				$this->create_order_number( $order, 'quote' );
			}
		}

		public function create_sequential_order_number_from_accepted_quote( $order_id, $from, $to ) {

			if ( in_array( $from, array( 'ywraq-accepted', 'ywraq-pending' ) ) && 'pending' == $to ) {
				$order = wc_get_order( $order_id );
				$this->generate_sequential_order_number( $order );
			}
		}


		/**
		 * Check if current module is active
		 *
		 * @return bool
		 * @author Emanuela Castorina
		 * @since  1.2.7
		 */
		public function is_subscription_module_active() {
			$is_active = get_option( 'ywson_subscription_module_settings', array() );

			$is_active = isset( $is_active['enabled'] ) && 'yes' == $is_active['enabled'];

			return $is_active && ywson_is_subscription_active();
		}


		/**
		 * Generate a new sequential when create a order from backend
		 *
		 * @param int $subscription_id Subscription ID
		 *
		 * @see    ywsbs_subscription_created
		 *
		 * @author Emanuela Castorina
		 * @since  1.2.7
		 */
		public function save_subscription_sequential_order_number( $subscription_id ) {

			$subscription = ywsbs_get_subscription( $subscription_id );

			if ( ! apply_filters( 'ywson_generate_subscription_sequential_order_number', true, $subscription ) || ! $this->is_subscription_module_active() ) {
				return;
			}

			$number_meta_key = $this->plugin_meta['subscription'];

			$number_meta = $subscription->get( $number_meta_key );

			if ( empty( $number_meta ) ) {
				$number_meta = $this->get_formatted_sequential_order_number( $subscription, 'subscription' );
				$subscription->set( $number_meta_key, $number_meta );
			}

		}

		/**
		 * Filter the subscription number
		 *
		 * @param string $number Current Subscription Number.
		 * @param YWSBS_Subscription $subscription Subscription.
		 *
		 * @return string
		 * @see    ywsbs_get_number
		 *
		 * @author Emanuela Castorina
		 * @since  1.2.7
		 */
		public function get_subscription_sequential_order_number( $number, $subscription ) {

			if ( ! $this->is_subscription_module_active() ) {
				return $number;
			}

			$number_meta_key = $this->plugin_meta['subscription'];
			$number_meta     = $subscription->get( $number_meta_key );

			return empty( $number_meta ) ? $number : $number_meta;
		}
	}
}

/**
 * @return YWSON_Manager
 */
function YWSON_Manager() {
	return YWSON_Manager::get_instance();
}
