<div id="ca_header" class="wrap">
  
  <h2>Exportar Pedidos</h2>
  
    <h4>Seleccionar rango de fechas</h4>
  
      <div class="table">
        <small>Desde</small><input id="desde" type="date">
      
        <small>Hasta</small><input id="hasta" type="date">
        
        <div id="expotar" class="button">EXPORTAR</div>
        <div id="resultado" ></div>
      </div>    

      <script>
   jQuery(document).ready(function(){
	  console.log('ca export loaded');

    jQuery('body').on('click', '#expotar',function(e){ 
      e.preventDefault();
      var desde = jQuery('#desde').val();
      var hasta = jQuery('#hasta').val();
           
      jQuery('#expotar').text('ESPERE...');
      jQuery.ajax({
        type: 'POST',
        cache: false,
        url: ajaxurl,
        data: {
          action: 'wanderlust_ca_export',
          desde: desde,
          hasta: hasta,
        },
        success: function(data, textStatus, XMLHttpRequest){ 
          jQuery("#expotar").hide();
          jQuery("#resultado").append(data);
        },
        error: function(MLHttpRequest, textStatus, errorThrown){ jQuery("#expotar").hide(); }
      });
    });
    
  });

  </script>
  
<?php
 
  
?>
 
</div>

<style> 
  .table {
    display: table-caption;
  }
  #expotar {
    margin:20px 0px;
  }
</style>