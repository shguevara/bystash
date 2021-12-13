  jQuery(document).ready(function(){
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
