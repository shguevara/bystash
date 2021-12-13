<?php
	global $wp_session;

  define( 'CA_EXPORTADOR_WANDERLUST_PATH', plugin_dir_path( __FILE__ ) );
  define( 'CA_EXPORTADOR_WANDERLUST_DIR',  plugin_dir_url( __FILE__ ) );

	if (isset($_COOKIE['ca_notice'])) {
			$wp_session['ca_notice'] = $_COOKIE['ca_notice'];
			add_action( 'admin_notices', 'ca_admin_notice' );
	}

  add_action( 'woocommerce_checkout_fields' , 'woo_ca_partido_checkout_field' );
	function woo_ca_partido_checkout_field( $checkout_fields ){
		 $checkout_fields['billing']['billing_ca_partido']  =  array(
       'label'          => __('Partido', 'woocommerce'),
       'placeholder'    => _x('SELECCIONAR PROVINCIA ', 'placeholder', 'wanderlust-ca'),
       'required'       => false,
       'clear'          => false,
       'type'           => 'text',
       'class'          => array('form-row-wide'),
       'priority'    => 80,
		 );
		 return $checkout_fields;
	}
 

  add_action('wp_ajax_get_localidades_ca', 'get_localidades_ca', 1);
	add_action('wp_ajax_nopriv_get_localidades_ca', 'get_localidades_ca', 1);

  function get_localidades_ca(){
    $client_state = $_POST['state'];
    if ($client_state == 'C') {$client_state = 'CAPITAL FEDERAL';}
    if ($client_state == 'B') {	$client_state = 'Buenos Aires';	}
    if ($client_state == 'K') {	$client_state = 'Catamarca';	}
    if ($client_state == 'H') {	$client_state = 'Chaco';	}
    if ($client_state == 'U') {	$client_state = 'Chubut';	}
    if ($client_state == 'X') {	$client_state = 'Cordoba';	}
    if ($client_state == 'W') {	$client_state = 'Corrientes';	}
    if ($client_state == 'E') {	$client_state = 'Entre Rios';	}
    if ($client_state == 'P') {	$client_state = 'Formosa';	}
    if ($client_state == 'Y') {	$client_state = 'Jujuy';	}
    if ($client_state == 'L') {	$client_state = 'La Pampa';	}
    if ($client_state == 'F') {	$client_state = 'La Rioja';	}
    if ($client_state == 'M') {	$client_state = 'Mendoza';	}
    if ($client_state == 'N') {	$client_state = 'Misiones';	}
    if ($client_state == 'Q') {	$client_state = 'Neuquen';	}
    if ($client_state == 'R') {	$client_state = 'Rio Negro';	}
    if ($client_state == 'A') {	$client_state = 'Salta';	}
    if ($client_state == 'J') {	$client_state = 'San Juan';	}
    if ($client_state == 'D') {	$client_state = 'San Luis';	}
    if ($client_state == 'Z') {	$client_state = 'Santa Cruz';	}
    if ($client_state == 'S') {	$client_state = 'Santa Fe';	}
    if ($client_state == 'G') {	$client_state = 'Santiago del Estero';	}
    if ($client_state == 'V') {	$client_state = 'Tierra del Fuego';	}
    if ($client_state == 'T') {	$client_state = 'Tucuman';	}	
    $delivery_zones = WC_Shipping_Zones::get_zones();

    foreach($delivery_zones as $zones){
      foreach($zones['shipping_methods'] as $methods){ 
        if($methods->id =='correo_argentino'){
          if($methods->enabled == 'yes'){
            $api_key = $methods->instance_settings['wanderlust_api'];
          }
        }
      }
    }      
    $params = array(
      "method" => array(
        "get_localidad" => array(
          'api_key' => $api_key,
          'provincia_destino' => $client_state,   
        )
      )
    );
									
    $ch = curl_init();
    curl_setopt_array($ch,	
      array(	
        CURLOPT_TIMEOUT	=> 30,
        CURLOPT_POST => TRUE,
        CURLOPT_POSTFIELDS => http_build_query($params),
        CURLOPT_URL => 'https://wanderlust.codes/apica/',
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_FOLLOWLOCATION	=> TRUE
      )
    );

    $ca_response = curl_exec ($ch);		
    $ca_response = json_decode($ca_response); 
    
    
 
    echo '<select id="listas" name="listas" style="border-radius: 5px;">';
			
				$listado_ca = array();
			
				foreach($ca_response as $sucursales){
 				 
 					echo '<option value="'. $sucursales.'">'. $sucursales . '</option>';
				}
			
				echo '</select>';
    die();
  }

	add_action('wp_ajax_check_sucursalesca', 'check_sucursalesca', 1);
	add_action('wp_ajax_nopriv_check_sucursalesca', 'check_sucursalesca', 1);
 	 
	function check_sucursalesca() {
		global $wp_session;
    $delivery_zones = WC_Shipping_Zones::get_zones();

    foreach($delivery_zones as $zones){
      foreach($zones['shipping_methods'] as $methods){ 
        if($methods->id =='correo_argentino'){
          if($methods->enabled == 'yes'){
            $api_key = $methods->instance_settings['wanderlust_api'];
          }
        }
      }
    }
    		
		if (isset($_POST['localidad'])) {
			
			$params = array(
						"method" => array(
								 "get_sucursal" => array(
												'api_key' => $api_key,
												'localidad' => $_POST['localidad'],   
												'provincia_destino' => $_POST['provincia'],   
								 )
						)
				);
									
			 $ch = curl_init();
			 curl_setopt_array($ch,	
														array(	
															CURLOPT_TIMEOUT	=> 30,
															CURLOPT_POST => TRUE,
															CURLOPT_POSTFIELDS => http_build_query($params),
															CURLOPT_URL => 'https://wanderlust.codes/apica/',
															CURLOPT_RETURNTRANSFER => TRUE,
															CURLOPT_FOLLOWLOCATION	=> TRUE
														)
													);

			 	$ca_response = curl_exec ($ch);			
        $ca_response = json_decode($ca_response); 
 
        echo '<select id="pv_centro_ca_estandar" name="pv_centro_ca_estandar">';

        foreach($ca_response as $sucursales){
          
          echo '<option value="'. $sucursales[1].'">'.$sucursales[4] . ' ' .  $sucursales[5]   . ' - ' .$sucursales[6] . ' - ' .$sucursales[8] . '</option>';

        }
        echo '</select>';
        
      
         
 
							 
 			die();
		} else {
      echo '<strong>* Seleccionar un partido</strong>';
      exit();
    }
	}
 
  add_action( 'wp_footer', 'only_numbers_cas');
	function only_numbers_cas(){ 
		if ( is_checkout() ) { ?>
 			<script type="text/javascript">
        
        jQuery(document).on('change','#listas',function () {

              var selectedMethod = jQuery('input:checked', '#shipping_method').attr('id');
							var selectedMethodb = jQuery( "#order_review .shipping .shipping_method option:selected" ).val();
							if (selectedMethod == null) {
									if(selectedMethodb != null){
										selectedMethod = selectedMethodb;
									}  
							}	 					
							var order_sucursal = 'ok';
              if (selectedMethod.indexOf("-sas") >= 0 ) {
							jQuery("#order_sucursal_mainca_result").fadeOut(100);
							jQuery("#order_sucursal_mainca_result_cargando").fadeIn(100);	
              var provincia = jQuery('#billing_state').val();
              var localidad = jQuery('#listas').val();
				    	jQuery.ajax({
				    		type: 'POST',
				    		cache: false,
				    		url: wc_checkout_params.ajax_url,
				    		data: {
 									action: 'check_sucursalesca',
									localidad: localidad,
									provincia: provincia,								
				    		},
				    		success: function(data, textStatus, XMLHttpRequest){
											jQuery("#order_sucursal_mainca_result").fadeIn(100);
 											jQuery("#order_sucursal_mainca_result_cargando").fadeOut(100);	
											jQuery("#order_sucursal_mainca_result").html('');
											jQuery("#order_sucursal_mainca_result").append(data);
									
 											var selectList = jQuery('#pv_centro_ca_estandar option');
											jQuery('#pv_centro_ca_estandar').html(selectList);
											jQuery("#pv_centro_ca_estandar").prepend("<option value='0' selected='selected'>Sucursales Disponibles</option>");
									
										},
										error: function(MLHttpRequest, textStatus, errorThrown){alert(errorThrown);}
									});
				    	    return false;	
              }
        });
        
        
 				jQuery(document).ready(function () {  
          jQuery('#order_sucursal_mainca').insertAfter( jQuery( '.woocommerce-checkout-review-order-table' ) );  
          jQuery('#calc_shipping_postcode').attr({ maxLength : 4 });
          jQuery('#billing_postcode').attr({ maxLength : 4 });
          jQuery('#shipping_postcode').attr({ maxLength : 4 });

		          jQuery("#calc_shipping_postcode").keypress(function (e) {
		          if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
		          	return false;
		          }
		          });
		          jQuery("#billing_postcode").keypress(function (e) { 
		          if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) { 
		          return false;
		          }
		          });
		          jQuery("#shipping_postcode").keypress(function (e) {  
		          if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
		          return false;
		          }
		          });
                     
						jQuery('#billing_state').change(function () {
				    	if (jQuery('#ship-to-different-address-checkbox').is(':checked')) {
				    		var state = jQuery('#shipping_state').val();
				    		var post_code = jQuery('#shipping_postcode').val();
				    	} else {
				    		var state = jQuery('#billing_state').val();
				    		var post_code = jQuery('#billing_postcode').val();
				    	}
              jQuery('#billing_ca_partido_field .woocommerce-input-wrapper').html('CARGANDO PARTIDOS...');  
				    	jQuery.ajax({
				    		type: 'POST',
				    		cache: false,
				    		url: wc_checkout_params.ajax_url,
				    		data: {
 									action: 'get_localidades_ca',
									state: state,
				    		},
				    		success: function(data, textStatus, XMLHttpRequest){
											jQuery('#billing_ca_partido_field .woocommerce-input-wrapper').html(data);
										  jQuery("#listas").prepend("<option value='0' selected='selected'>Seleccionar Partido</option>");
									
										},
										error: function(MLHttpRequest, textStatus, errorThrown){ }
									});
				    	  return false;			    	
						
				    });		
					
				});

				function toggleCustomBox() {
                var selectedMethod = jQuery('input:checked', '#shipping_method').val();
 				        //var selectedMethod = jQuery('input:checked', '#shipping_method').attr('id');
								var selectedMethodb = jQuery( '#order_review .shipping .shipping_method option:selected' ).val();
								if (selectedMethod == null) {
									if(selectedMethodb != null){
										selectedMethod = selectedMethodb;
									}  
								}	                 
 									//sas, sasp, pasp, pas
                if (selectedMethod == 'correo_argentino-sas') {
									
                  jQuery(document).on('updated_checkout', function(data) {
                    jQuery('#order_sucursal_mainca').show();
										jQuery('#order_sucursal_mainca').insertAfter( jQuery('.shop_table') );
									});
 
									if (jQuery('#ship-to-different-address-checkbox').is(':checked')) {
										var state = jQuery('#shipping_state').val();
										var post_code = jQuery('#shipping_postcode').val();
									} else {
										var state = jQuery('#billing_postcode').val();
										var post_code = jQuery('#billing_postcode').val();
									}
 									
									var order_sucursal = 'ok';
                  var provincia = jQuery('#billing_state').val();
                  var localidad = jQuery('#listas').val();
                  
									jQuery("#order_sucursal_mainca_result").fadeOut(100);
									jQuery("#order_sucursal_mainca_result_cargando").fadeIn(100);	
									jQuery.ajax({
										type: 'POST',
										cache: false,
										url: wc_checkout_params.ajax_url,
										data: {
											action: 'check_sucursalesca',
											localidad: localidad,
											provincia: provincia,
										},
										success: function(data, textStatus, XMLHttpRequest){
													jQuery("#order_sucursal_mainca_result").fadeIn(100);
													jQuery("#order_sucursal_mainca_result_cargando").fadeOut(100);	
													jQuery("#order_sucursal_mainca_result").html('');
													jQuery("#order_sucursal_mainca_result").append(data);
											
	 											var selectList = jQuery('#pv_centro_ca_estandar option');					 
												jQuery('#pv_centro_ca_estandar').html(selectList);
												jQuery("#pv_centro_ca_estandar").prepend("<option value='0' selected='selected'>Sucursales Disponibles</option>");										
											
												},
												error: function(MLHttpRequest, textStatus, errorThrown){alert(errorThrown);}
											});
									return false;					

                } else {
                  jQuery('#order_sucursal_mainca').hide();  
                }
				}; //ends toggleCustomBox

				jQuery(document).ready(toggleCustomBox);
				jQuery(document).on('change', '#shipping_method input:radio', toggleCustomBox);
 				jQuery(document).on('change', '#order_review .shipping .shipping_method', toggleCustomBox);
        
  				jQuery(document).on('updated_checkout', function() {
          var selectedMethod = jQuery('input:checked', '#shipping_method').val();
								var selectedMethodb = jQuery( '#order_review .shipping .shipping_method option:selected' ).val();
								if (selectedMethod == null) {
									if(selectedMethodb != null){
										selectedMethod = selectedMethodb;
									}  
								}	                  
									 
                 if (selectedMethod == 'correo_argentino-sas') {
                  jQuery('#order_sucursal_mainca_ca').show();
                  jQuery('#order_sucursal_mainca_ca').fadeIn();
                  jQuery('#order_sucursal_mainca').removeClass( "hideall" );
                  var partido = jQuery('#listas').val();
                  if (partido != null){
                    
                    if (partido == 0){
                      jQuery('#order_sucursal_mainca_ca').hide();
                      jQuery('#order_sucursal_mainca_ca').fadeOut(); 
                     
                    }  
                     

                  } else {
                     
                    jQuery('#order_sucursal_mainca_ca').hide();
                    jQuery('#order_sucursal_mainca_ca').fadeOut();                   
                  }
                    
                } else {
                  jQuery('#order_sucursal_mainca').hide();
                  jQuery('#order_sucursal_mainca').fadeOut();
                  jQuery('#order_sucursal_mainca').addClass( "hideall" );

                }
          
        }); 
			</script>

			<style type="text/css">
         #order_sucursal_mainca h3 {
            text-align: left;
            padding: 5px 0 5px 115px;
        }
				.ca-logo {
					position: absolute;
    			margin: 0px;
				}
        .hideall {display:none !important;}
			</style>
		<?php }
	}	//ends only_numbers_cas

  /**
	 * Add the field to the checkout
	 */
	add_action( 'woocommerce_after_order_notes', 'order_sucursal_mainca' );
	function order_sucursal_mainca( $checkout ) {
		global $woocommerce, $wp_session;
 

	  echo '<div id="order_sucursal_mainca" style="display:none; margin-bottom: 50px;"><img class="ca-logo" src="'. plugins_url( 'img/suc-ca.png', __FILE__ ) . '"><h3>' . __('Sucursal Correo Argentino') . '</h3>';
    	echo '<small>Si seleccionaste retirar por sucursal, elegí tu sucursal en el listado.</small>';
      echo '<div id="order_sucursal_mainca_result_cargando">Cargando Sucursales...';echo '</div>';
 			echo '<div id="order_sucursal_mainca_result" style="display:none;">Cargando Sucursales...';echo '</div>';
    echo '</div>';
	
 	}


	 /**
	 * Process the checkout
	 */
	add_action('woocommerce_checkout_process', 'checkout_field_ca_process');
	function checkout_field_ca_process() {
			global $woocommerce, $wp_session;
		
			$chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
			$chosen_shipping = $chosen_methods[0]; 
			$wp_session['chosen_shipping'] = $chosen_shipping;
			if (strpos($chosen_shipping, '-sas') !== false) {
				if (empty($_POST['pv_centro_ca_estandar']) )
									wc_add_notice( __( 'Por favor, seleccionar sucursal de retiro.' ), 'error' ); 
			}
	}

	 /**
	 * Update the order meta with field value
	 */
	add_action( 'woocommerce_checkout_update_order_meta', 'order_sucursal_mainca_update_order_meta_ca', 10);
	function order_sucursal_mainca_update_order_meta_ca( $order_id ) {
		global $wp_session;
	 
 	    if ( ! empty( $_POST['pv_centro_ca_estandar'] ) ) {
				
				update_post_meta( $order_id, '_sucursal_pv_centro_ca_estandar', $_POST['pv_centro_ca_estandar'] );
				update_post_meta( $order_id, 'sucursal_ca_c', $_POST['pv_centro_ca_estandar'] );
	    }
		
			$chosen_shipping = json_encode($wp_session['chosen_shipping'] );
			update_post_meta( $order_id, '_chosen_shipping', $chosen_shipping );
 
 	}

	function ca_admin_notice() {
		global $wp_session;
 
			?>
			<div class="notice error my-acf-notice is-dismissible" >
					<p><?php print_r($wp_session['ca_notice'] ); ?></p>
			</div>

			<?php
	} 

 	function wanderlust_ca_exportar_admin() {
		$my_page = add_submenu_page( 'woocommerce','Wanderlust Correo Argentino Exportador', 'Exportar a Correo Argentino', 'manage_woocommerce', 'wanderlust-ca-exportar', 'wanderlust_ca_exportar' );
 		add_action( 'load-' . $my_page, 'wanderlust_ca_exportar_js' );
 
  }
	add_action( 'admin_menu', 'wanderlust_ca_exportar_admin' );

	function wanderlust_ca_exportar_js(){
		add_action( 'admin_enqueue_scripts', 'wanderlust_ca_exportar_admin_js' );
	}

	function wanderlust_ca_exportar_admin_js(){	
		wp_enqueue_script( 'easypost-label-admin-script', plugins_url('includes/cajs.js',__FILE__), array( 'jquery' ) );
	}

	function wanderlust_ca_exportar() {
		if ( !current_user_can( 'manage_woocommerce' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		
    require_once(dirname(__FILE__) . '/admin.php');
	}

  add_action( 'wp_ajax_wanderlust_ca_export', 'wanderlust_ca_export' );
	add_action( 'wp_ajax_nopriv_wanderlust_ca_export', 'wanderlust_ca_export' );

  function wanderlust_ca_export() {    
    global $wpdb;
    
    $post_status = implode("','", array('wc-processing', 'wc-completed') );

    $result = $wpdb->get_results( "SELECT * FROM $wpdb->posts 
                WHERE post_type = 'shop_order'
                AND post_status IN ('{$post_status}')
                AND post_date BETWEEN '{$_POST['desde']}  00:00:00' AND '{$_POST['hasta']} 23:59:59'
            ");
    
    $pedidos = array();
    foreach($result as $orders){
      $order = wc_get_order($orders->ID);
      $shipping_method = @array_shift($order->get_shipping_methods());
      $shipping_method_id = $shipping_method['method_id'];

      if($shipping_method_id == 'correo_argentino' ){
        
        $peso = null;
        $precio = null;
        $items = $order->get_items();
        foreach( $items as $item ) {    				 
          if ( $item['product_id'] > 0 ) {
            $product = wc_get_product( $item['product_id'] );
            $peso += $product->get_weight();
            $precio += $product->get_price();   
          }
        }
        
        $calle = preg_replace('/[0-9]+/', '', $order->get_shipping_address_1());
        preg_match_all('!\d+!', $order->get_shipping_address_1(), $numero);
	      $unwanted_array = array(    'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
					'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
					'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
					'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
					'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', '°'=>'', 'º'=>'' );
        
        $ciudad =  strtr( $order->get_shipping_city(), $unwanted_array );
        $destino_nombre = strtr( $order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name(), $unwanted_array );
        $piso =  strtr(  $order->get_shipping_address_2(), $unwanted_array );
        $phone = $order->get_billing_phone();
        $phone = preg_replace('/[^0-9.]+/', '', $phone);
        
        $sucursal_ca_c = get_post_meta($orders->ID, 'sucursal_ca_c', true);
        if($sucursal_ca_c){
    
          $pedidos[] = array(
            'tipo_producto' => 'CP',
            'largo' => '70',
            'ancho' => '70',
            'altura' => '70',
            'peso' => $peso,
            'valor_del_contenido' => $precio,
            'provincia_destino' => $order->get_shipping_state(),
            'sucursal_destino' => $sucursal_ca_c,
            'localidad_destino' => ' ',
            'calle_destino' => ' ',
            'altura_destino' => ' ',
            'piso' => ' ',
            'dpto' => ' ',
            'codpostal_destino' => ' ',
            'destino_nombre' => $destino_nombre,
            'destino_email' => $order->get_billing_email(),
            'cod_area_tel' => '054',
            'tel' => $phone,
            'cod_area_cel' => ' ',
            'cel' => ' ',
          );   
          
        } else {
          $pedidos[] = array(
            'tipo_producto' => 'CP',
            'largo' => '70',
            'ancho' => '70',
            'altura' => '70',
            'peso' => $peso,
            'valor_del_contenido' => $precio,
            'provincia_destino' => $order->get_shipping_state(),
            'sucursal_destino' => ' ',
            'localidad_destino' => $ciudad,
            'calle_destino' => $calle,
            'altura_destino' => $numero[0][0],
            'piso' => $piso,
            'dpto' => ' ',
            'codpostal_destino' => $order->get_shipping_postcode(),
            'destino_nombre' => $destino_nombre,
            'destino_email' => $order->get_billing_email(),
            'cod_area_tel' => '054',
            'tel' => $phone,
            'cod_area_cel' => ' ',
            'cel' => ' ',
          );          
        }
      }
    }
  
   
    if(!empty($pedidos)){
       $delivery_zones = WC_Shipping_Zones::get_zones();

        foreach($delivery_zones as $zones){
          foreach($zones['shipping_methods'] as $methods){ 
            if($methods->id =='correo_argentino'){
              if($methods->enabled == 'yes'){
                $api_key = $methods->instance_settings['wanderlust_api'];
              }
            }
          }
        }      

        $params = array(
          "method" => array(
            "generar_csv" => array(
              'api_key' => $api_key,
              'pedidos' => json_encode($pedidos),   
            )
          )
        );

        $ch = curl_init();
        curl_setopt_array($ch,	
          array(	
            CURLOPT_TIMEOUT	=> 30,
            CURLOPT_POST => TRUE,
            CURLOPT_POSTFIELDS => http_build_query($params),
            CURLOPT_URL => 'https://wanderlust.codes/apica/',
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_FOLLOWLOCATION	=> TRUE
          )
        );

        $ca_response = curl_exec ($ch);		
        $ca_response = base64_decode($ca_response); 

        $save_path = CA_EXPORTADOR_WANDERLUST_PATH . 'csv/';
        $save_url = CA_EXPORTADOR_WANDERLUST_DIR . 'csv/';

        $file_handle = fopen($save_path .'correo_argentino.csv', 'w');
        fwrite($file_handle,  $ca_response );
        fclose($file_handle);

        $archivo_cbte = plugins_url('/csv/correo_argentino.csv',__FILE__);
        echo '<a class="button" href="'.$archivo_cbte.'" download="correo_argentino.csv">DESCARGAR</a>';
    } else {
      echo 'NO SE ENCONTRARON RESULTADOS';
    }
 
    die();
	}
?>