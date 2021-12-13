<?php
class WC_Correoargentino extends WC_Shipping_Method {

	public function __construct($instance_id = 0){
	  
		$this->id = 'correo_argentino';
		$this->instance_id = absint( $instance_id );
		$this->method_title = __( 'Correo Argentino', 'woocommerce' );
 		$this->method_description   = __( 'Obtener costos de envió de forma dinámica para Correo Argentino.', 'woocommerce' );

		$this->supports  = array(
			'shipping-zones',
			'instance-settings',
		);

		$this->init_form_fields();
		$this->init_settings();

		$this->wanderlust_api	= $this->get_option( 'wanderlust_api' );
		$this->enabled	= $this->get_option( 'enabled' );
		$this->title 		= 'Correo Argentino';
        $this->envio_gratis = $this->get_option( 'envio_gratis' );
        $this->redondear = $this->get_option( 'redondear' );
        $this->agregar_exta = $this->get_option( 'agregar_exta' );
        $this->services = $this->get_option( 'services', array( ));
    
		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
	}
  
	public function init_form_fields(){

		$this->instance_form_fields = array(

      'packing'           => array(
      'title'           => __( 'Todos los campos son OBLIGATORIOS', 'woocommerce-shipping-oca' ),
      'type'            => 'title',
      'description'     => __( 'Si tiene alguna duda o consulta no dude en contactarnos. <a href="https://wanderlust-webdesign.com/contact/" target="_blank">Contacto </a> </br> Recordar que las etiquetas se generan directamente desde la web de <a href="https://www.correoargentino.com.ar/MiCorreo/public/login" target="_blank">Mi Correo</a>', 'woocommerce-shipping-oca' ),
      ),      
      'wanderlust_api' => array(
        'title' 		=> __( 'Wanderlust API KEY', 'woocommerce' ),
        'type' 			=> 'text',
        'description' 	=> __( 'Ingresar API KEY provista por Wanderlust.', 'woocommerce' ),
			),  
			'enabled' => array(
        'title' 		=> __( 'Activar/Desactivar', 'woocommerce' ),
        'type' 			=> 'checkbox',
        'label' 		=> __( 'Correo Argentino', 'woocommerce' ),
        'default' 		=> 'yes'
			),
      'origin_afip' 	=> array(
        'title'           => __( 'Condicion frente al IVA', 'woocommerce-shipping-oca' ),
        'type'            => 'select',
        'description'     => __( 'Obligatorio', 'woocommerce-shipping-oca' ),
        'default'         => '',
        'class'           => 'packing_method',
        'options'         => array(
          'M'       => __( 'Monotributista / Consumidor Final', 'woocommerce-shipping-oca' ),
          'R'       => __( 'Responsable Inscripto', 'woocommerce-shipping-oca' ),
        ),		
        'desc_tip'        => true
      ),	
      'cp_origen' => array(
        'title' 		=> __( 'Código postal de origen', 'woocommerce' ),
        'type' 			=> 'text',
        'description' 	=> __( 'Ingresar CP de origen.', 'woocommerce' ),
        'default'		=> __( '', 'woocommerce' )
			),     
      'origin_prov' 	=> array(
        'title'           => __( 'Provincia de origen', 'woocommerce-shipping-oca' ),
        'type'            => 'select',
        'description'     => __( 'Obligatorio', 'woocommerce-shipping-oca' ),
        'default'         => '',
        'options'         => array(
          'C' => "Ciudad Autónoma de Buenos Aires",
          'B' => "Buenos Aires",
          'K' => "Catamarca",
          'H' => "Chaco",
          'U' => "Chubut",
          'X' => "Córdoba",
          'W' => "Corrientes",
          'E' => "Entre Ríos",
          'P' => "Formosa",
          'Y' => "Jujuy",
          'L' => "La Pampa",
          'F' => "La Rioja",
          'M' => "Mendoza",
          'N' => "Misiones",
          'Q' => "Neuquén",
          'R' => "Río Negro",
          'A' => "Salta",
          'J' => "San Juan",
          'D' => "San Luis",
          'Z' => "Santa Cruz",
          'S' => "Santa Fe",
          'G' => "Santiago del Estero",
          'V' => "Tierra del Fuego",
          'T' => "Tucumán"
        ),		
        'desc_tip'        => true
      ),      
      'envio_gratis' => array(
        'title' 		=> __( 'Envío Gratis', 'woocommerce' ),
        'type' 			=> 'text',
        'description' 	=> __( 'Envíos gratis para montos mayores a X.', 'woocommerce' ),
        'default'		=> __( '99999999999999999', 'woocommerce' )
			), 
			'agregar_exta' => array(
        'title' 		=> __( 'Agregar % extra', 'woocommerce' ),
        'type' 			=> 'text',
        'label' 		=> __( 'Agregar un % extra al costo de envío', 'woocommerce' ),
        'default'		=> __( '1', 'woocommerce' )
			),        
			'redondear' => array(
        'title' 		=> __( 'Ajustar totales', 'woocommerce' ),
        'type' 			=> 'checkbox',
        'label' 		=> __( 'EJ: $240.59 a $241', 'woocommerce' ),
        'default' 		=> 'yes'
			),      
      'services'  => array(
        'type'            => 'service'
      ),
		);
	}
  
	public function is_available( $package ){ 

		return true;
	}
 
	public function generate_service_html() {
		ob_start();
		include( 'data/services.php' );
		return ob_get_clean();
	}
 
  public function validate_service_field( $key ) {
						
 		$service_name     = isset( $_POST['service_name'] ) ? $_POST['service_name'] : array();
		$service_sucursal    = isset( $_POST['woocommerce_ca_wanderlust_modalidad'] ) ? $_POST['woocommerce_ca_wanderlust_modalidad'] : array();
		$service_enabled    = isset( $_POST['service_enabled'] ) ? $_POST['service_enabled'] : array();
			  	
		$services = array();

		if ( ! empty( $service_name ) && sizeof( $service_name ) > 0 ) {
			for ( $i = 0; $i <= max( array_keys( $service_name ) ); $i ++ ) {

				if ( ! isset( $service_name[ $i ] ) )
					continue;
		
				if ( $service_name[ $i ] ) {
  					$services[] = array(
 						'service_name'     =>  $service_name[ $i ],
						'woocommerce_ca_wanderlust_modalidad' =>  $service_sucursal[ $i ] ,  
						'enabled'    => isset( $service_enabled[ $i ] ) ? true : false
					);
				}
			}
 
		}
			
		return $services;
	}

  
	public function calculate_shipping($package = array()){
	
		//Calcular peso y dimensiones
		$peso = 0;
		$dimensiones = 0;
		$diferencia = 0;
    $weight_unit = esc_attr( get_option('woocommerce_weight_unit' ));
 		$weight_multi = 0;
 		if ($weight_unit == 'kg') { $weight_multi =  1;}
 		if ($weight_unit == 'g') {  $weight_multi =  0.001;}
		
		foreach ( $package['contents'] as $item_id => $values ) {
			$_product  = $values['data'];

			if ($_product->get_weight()){
				$peso =	$peso + $_product->get_weight() * $values['quantity'];
        $peso = $peso * $weight_multi;
			}

			if ($_product->get_length() && $_product->get_width() && $_product->get_height()){
				$dimensiones = $dimensiones + (($_product->get_length() * $values['quantity']) * $_product->get_width() * $_product->get_height());
				$pesovolumetrico = $dimensiones / 6000;
			}
		}
 
    foreach($this->services as $key => $services) {
						
			if($services['enabled'] == 1){
				
				$params = array(
						"method" => array(
								 "get_tarifas" => array(
												'api_key' => $this->instance_settings['wanderlust_api'],
												'peso_total' => $peso,
												'volumen_total' => $pesovolumetrico,  
												'ca_afip' => $this->instance_settings['origin_afip'],
												'cp_origen' => $this->instance_settings['cp_origen'],
												'provincia_origen' => $this->instance_settings['origin_prov'],
												'provincia_destino' => $package['destination']['state'],   
												'cp_destino' => $package['destination']['postcode'],   
								 )
						)
				);
        
        $total = WC()->cart->get_displayed_subtotal();
        $envioGratis = $this->instance_settings['envio_gratis'];
        
        if ( $total >= $envioGratis ){ 
          $costo = 0;
          $this->add_rate( array(
              'id' 	=> $this->id .'-'. $services['woocommerce_ca_wanderlust_modalidad'],
              'label' => $services['service_name'] . ' ¡GRATIS!',
              'cost' 	=> $ca_response[$key]
            ));
        } else {
          $ca_response = wp_remote_post( 'https://wanderlust.codes/apica/', array(
            'body'    => $params,
          ) );
                    
          if ( !is_wp_error( $ca_response ) ) {
            $ca_response = json_decode($ca_response['body']);	
            $valor_envio = $ca_response[$key];
            if($this->agregar_exta){
                $porc = $this->agregar_exta / 100;
                $calcporc = $valor_envio * $porc;
                $valor_envio = $valor_envio + $calcporc; 
            }
            
            if($this->redondear == 'yes'){
              $valor_envio = ceil($valor_envio);
            }  
            
            $this->add_rate( array(
              'id' 	=> $this->id .'-'. $services['woocommerce_ca_wanderlust_modalidad'],
              'label' => $services['service_name'],
              'cost' 	=> $valor_envio
            ));
            
          }          
        }
			}
    }
	}
}