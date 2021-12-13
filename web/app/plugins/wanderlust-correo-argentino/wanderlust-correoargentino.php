<?php
/*
  Plugin Name: Wanderlust Correo Argentino
  Plugin URI: https://wanderlust-webdesign.com/
  Description: Costos de envio por Correo Argentino.
  Version: 11.0.2
  Author: Wanderlust Web Design
  Author URI: https://wanderlust-webdesign.com
  WC tested up to: 5.4.2
  Copyright: 2007-2021 wanderlust-webdesign.com.
*/

 
if ( ! defined( 'WPINC' ) ) {die;}
if ( ! defined( 'ABSPATH' ) ) {exit;}
 
$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );

if ( in_array( 'woocommerce/woocommerce.php',  $active_plugins) ) {
  require_once( 'includes/functions.php' );
 
	add_filter( 'woocommerce_shipping_methods', 'tipos_de_envio' );
		
	function tipos_de_envio( $methods ) {

		$methods['correo_argentino'] = 'WC_Correoargentino';

	return $methods;
	}

	add_action( 'woocommerce_shipping_init', 'tipos_de_envio_init' );

	function tipos_de_envio_init(){

		require_once plugin_dir_path(__FILE__) . 'clases/class-correoargentino.php';
    
	}

}