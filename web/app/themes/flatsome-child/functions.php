<?php
// Add custom Theme Functions here
// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_locale_css' ) ):
    function chld_thm_cfg_locale_css( $uri ){
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );

// END ENQUEUE PARENT ACTION




/**
 * WOOCOMMERCE
 *
 * 
 * 
 */

// Add "Cantidad:" on Product page
function echo_qty_front_add_cart() {
    echo '<div class="qty">Cantidad: </div>'; 
}
add_action( 'woocommerce_before_add_to_cart_quantity', 'echo_qty_front_add_cart' );


// Breadcrumbs formatted
function ts_woocommerce_breadcrumbs_change() {
    return array(
        'delimiter' => ' <i class="icon-angle-right"></i> ',
        'wrap_before' => '<nav class="woocommerce-breadcrumb" itemprop="breadcrumb" style="margin-left:5%">',
        'wrap_after' => '</nav>',
        'before' => 'Category: ',
        'after' => '',
        'home' => _x( 'SuperStore', 'breadcrumb', 'woocommerce' ),
    );
}
add_filter( 'woocommerce_breadcrumb_defaults', 'ts_woocommerce_breadcrumbs_change' );

// Ya esta en tu carrito TEXT
// add_filter( 'woocommerce_product_single_add_to_cart_text', 'custom_add_cart_button_single_product', 9999 );
// function custom_add_cart_button_single_product( $label ) {
//    if ( WC()->cart && ! WC()->cart->is_empty() ) {
//       foreach( WC()->cart->get_cart() as $cart_item_key => $values ) {
//          $product = $values['data'];
//          if ( get_the_ID() == $product->get_id() ) {
//             $label = 'Ya esta en tu carrito';
//             break;
//          }
//    }
//    return $label;
// }
// }


// Pasar orden a PROCESSING en MP
if ( WP_ENV === 'development' ) {
    add_action( 'woocommerce_thankyou', 'letsgo_auto_processing_orders');
    function letsgo_auto_processing_orders( $order_id ) {
        if ( ! $order_id )
            return;
            $order = wc_get_order( $order_id );
            //ID's de las pasarelas de pago a las que afecta
            $paymentMethods = array( 'woo-mercado-pago-basic' );
            if ( !in_array( $order->payment_method, $paymentMethods ) ) return;
            // If order is “pending” update status to “processing”
            if( $order->has_status( 'pending' ) ) {
                $order->update_status( 'completed' );
            } 
    }

    // DEACTIVATE Plugins in Local Env

    // echo( $_SERVER['DOCUMENT_ROOT']);
    // deactivate_plugins( $_SERVER['DOCUMENT_ROOT'] . '/app/litespeed-cache/litespeed-cache.php' );

}


// add_action( 'woocommerce_checkout_create_order', 'force_new_order_status', 20, 1 );
// function force_new_order_status( $order ) {

//     if( ! $order->has_status('on-hold') )
//         $order->set_status( 'on-hold', 'Forced status by a custom script' );
// }


// // MercadoPago svg icon
// add_filter( 'flatsome_payment_icons', function ( $icons ) {
// 	$icons['mercadopago'] = 'MercadoPago';
// 	return $icons;
// });



// COUNTDOWN - Read shortcode
function read_shortcode_for_notice($notice_with_dismiss_message, $notice_without_dismiss_message)
{
    echo(do_shortcode("$notice_without_dismiss_message"));
}
add_filter('woocommerce_demo_store', 'read_shortcode_for_notice', 2, 99);




/**
 * WPFORMS
 *
 * 
 * 
 */


/**
 * Enqueue your own stylesheet for conversational forms
 * 
 * @link  https://wpforms.com/developers/how-to-enqueue-a-stylesheet-for-conversational-forms/
 * 
 */
 
// Dequee and Deregister styles from plugin
add_action('wp_enqueue_scripts', function() {
    wp_dequeue_style('wpforms-conversational-forms');
    wp_deregister_style('wpforms-conversational-forms');
},99999);

// Enquee specfic stylehseet(s)
function enquee_spefic_css() {
    wp_enqueue_style('conversational-forms', get_stylesheet_directory_uri().'/wpforms-conversational-forms/conversational-forms.css');
    wp_enqueue_style('color-scheme-dark', get_stylesheet_directory_uri().'/wpforms-conversational-forms/color-scheme-dark.css');
}
add_action( 'wpforms_conversational_forms_enqueue_styles', 'enquee_spefic_css',999 );

/**
 * Preserve the query strings in the URL on form submit.
 *
 * @link   https://wpforms.com/developers/how-to-keep-the-query-strings-in-the-url-on-submit/
 * 
 */
 
function wpf_dev_process_redirect_url() {
    global $wp;
    $current_url = home_url( add_query_arg( array( $_GET), $wp->request ) );
    return $current_url;
}
add_filter( 'wpforms_process_redirect_url', 'wpf_dev_process_redirect_url' );


// WPFORMS - Process smart tags in HTML Code block
function wpf_dev_html_process_smarttags( $properties, $field, $form_data ) {
    $properties['inputs']['primary']['code'] = apply_filters( 'wpforms_process_smart_tags', $properties['inputs']['primary']['code'], $form_data );
    return $properties;
}
add_filter( 'wpforms_field_properties_html', 'wpf_dev_html_process_smarttags', 10, 3 );


/**
 * Select specific form option based on query string
 *
 * 
 * 
 */
function wpf_preselect_dropdown( $field, $field_atts, $form_data ) {
	// Only continue of the form and field are the ones we are looking for
	if ( '2153' != $form_data['id'] || '3' != $field['id'] ) {
		return $field;
	}

	// Only continue if a prefered vehicle was provided
	if ( empty( $_GET['razon'] ) ) {
		return $field;
	}

	// Check to see if the vehicle provided exists in the dropdown, if it does
	// then set it to default.
	foreach ( $field['choices'] as $key => $choice ) {
		if ( $choice['label'] == $_GET['razon'] ) {
			$field['choices'][$key]['default'] = '1';
			break;
		}
	}
	return $field;
}
add_filter( 'wpforms_select_field_display', 'wpf_preselect_dropdown', 10 , 3 );




/**
 * WAITLIST
 *
 * 
 * 
 */

// Remove "My Waitlist" tab from MY ACCOUNT
add_filter( 'wcwl_enable_waitlist_account_tab', '__return_false' );




/**
 * AUTOMATEWOO
 *
 * 
 * 
 */

// Register a template by adding a slug and name to the $templates array
add_filter( 'automatewoo_email_templates', 'my_automatewoo_email_templates' );
function my_automatewoo_email_templates( $templates ) {
	$templates['customer-representative-1'] = 'Email Mkt - Customer Representative #1';
	$templates['text-only'] = 'Email Mkt - Text Only';
	$templates['full-banner'] = 'Email Mkt - Full Banner';
	return $templates;
}   

// Add custom variables to the list
add_filter( 'automatewoo/variables', 'my_automatewoo_variables' );
function my_automatewoo_variables( $variables ) {
	// Add var to display total savings ($)
	$variables['cart']['savings'] = dirname(__FILE__) . '/automatewoo/variables/cart-total-savings.php';
	return $variables;
}


// Add custom product template
add_filter( 'automatewoo/variables/product_templates', 'my_automatewoo_product_templates' );
function my_automatewoo_product_templates( $templates ) {
	$templates['product-rows-buy-button.php'] = 'Product Rows - "buy now"';
	$templates['product-rows-buy-button-counter.php'] = 'Product Rows - "buy now" + counter';
	return $templates;
}




/**
 * WORDPRESS
 *
 * 
 * 
 */

//  Create a new role
add_role( 'lead', __( 'Lead' ), array(
    'read' => true, 
  ));
    
//   Assign new role to 
add_filter( 'woocommerce_new_customer_data', 'wp_assign_custom_role' );
    function wp_assign_custom_role( $args ) {
    $args['role'] = 'lead';
    return $args;
}

// Disable XML-RPC
add_filter('xmlrpc_enabled', '__return_false');


function color_customizer($wp_customize){
    $wp_customize->add_section( 'theme_extra_styles', array(
        'title' => __( 'Extra styles', 'extra_styles' ),
        'priority' => 5,
      ) );
  
      $theme_colors = array();
  
      // Navigation Background Color
      $theme_colors[] = array(
        'slug'=>'color_top_seller',
        'default' => '#000000',
        'label' => __('Top seller', 'themeslug')
      );

      $theme_colors[] = array(
        'slug'=>'color_new_arrival',
        'default' => '#000000',
        'label' => __('New arrival', 'themeslug')
      );      
  
      foreach( $theme_colors as $color ) {
  
        $wp_customize->add_setting(
          $color['slug'], array(
            'default' => $color['default'],
            'sanitize_callback' => 'sanitize_hex_color',
            'type' => 'theme_mod',
            'capability' => 'edit_theme_options'
          )
        );
  
        $wp_customize->add_control(
          new WP_Customize_Color_Control(
            $wp_customize,
            $color['slug'],
            array('label' => $color['label'],
            'section' => 'theme_extra_styles',
            'settings' => $color['slug'])
          )
        );
      }
    }
  
    add_action( 'customize_register', 'color_customizer' );

    function themename_customize_register($wp_customize){
        $wp_customize->add_setting( 'test_setting', array(
            'default'        => 'value_xyz',
            'capability'     => 'edit_theme_options',
            'type'           => 'theme_mod',
        ));
        $wp_customize->add_control( 'test_control', array(
            'label'      => __('Text Test', 'themename'),
            'section'    =>  'spacious_slider_number_section1',
            'settings'   => 'test_setting',
        ));
}
add_action('customize_register', 'themename_customize_register');