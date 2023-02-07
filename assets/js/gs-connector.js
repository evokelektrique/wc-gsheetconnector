jQuery(document).ready(function () {
  
   /**
   * verify the api code
   * @since 1.0
   */
   jQuery(document).on('click', '#save-gs-woo-code', function (event) {
      event.preventDefault();
         jQuery( ".loading-sign" ).addClass( "loading" );
         var data = {
         action: 'verify_gs_woo_integation',
         code: jQuery('#gs-woo-code').val(),
         security: jQuery('#gs-ajax-nonce').val()
         };
         jQuery.post(ajaxurl, data, function (response ) {
            if( ! response.success ) { 
               jQuery( ".loading-sign" ).removeClass( "loading" );
               jQuery( "#gs-woo-validation-message" ).empty();
               jQuery("<span class='error-message'>Access code Can't be blank.</span>").appendTo('#gs-woo-validation-message');
            } else {
               jQuery( ".loading-sign" ).removeClass( "loading" );
               jQuery( "#gs-woo-validation-message" ).empty();
               jQuery("<span class='woo-valid-message'>Your Google Access Code is Authorized and Saved.</span> ").appendTo('#gs-woo-validation-message');
            setTimeout(function () { location.reload(); }, 1000);
           }
         });
         
   });  

   /**
    * deactivate the api code
    * @since 1.0
    */
   jQuery(document).on('click', '#gs-woo-deactivate-log', function () {
      jQuery(".loading-sign-deactive").addClass( "loading" );
    var txt;
    var r = confirm("Are You sure you want to deactivate Google Integration ?");
    if (r == true) {
       var data = {
          action: 'deactivate_gs_woo_integation',
          security: jQuery('#gs-ajax-nonce').val()
       };
       jQuery.post(ajaxurl, data, function (response ) {
          if ( response == -1 ) {
             return false; // Invalid nonce
          }
        
          if( ! response.success ) {
             alert('Error while deactivation');
             jQuery( ".loading-sign-deactive" ).removeClass( "loading" );
             jQuery( "#deactivate-msg" ).empty();
             
          } else {
             jQuery( ".loading-sign-deactive" ).removeClass( "loading" );
             jQuery( "#deactivate-msg" ).empty();
             jQuery("<span class='woo-valid-message'>Your account is removed. Reauthenticate again to integrate WooCommerce with Google Sheet.</span>").appendTo('#deactivate-msg');
             setTimeout(function () { location.reload(); }, 1000);
          }
       });
    } else {
       jQuery( ".loading-sign-deactive" ).removeClass( "loading" );
    }
         
  }); 

  function html_decode(input) {
      var doc = new DOMParser().parseFromString(input, "text/html");
      return doc.documentElement.textContent;
   }

   jQuery(document).on('click', '#gs-woo-sync', function () {
      jQuery(this).parent().children(".loading-sign").addClass("loading");
      var integration = jQuery(this).data("init");
      var data = {
         action: 'sync_woo_google_account',
         isajax: 'yes',
         isinit: integration,
         security: jQuery('#gs-ajax-nonce').val()
      };

      jQuery.post(ajaxurl, data, function (response) {
         if (response == -1) {
            return false; // Invalid nonce
         }

         if (response.data.success == "yes") {
            jQuery(".loading-sign").removeClass("loading");
            jQuery("#gs-woo-validation-message").empty();
            jQuery("<span class='woo-valid-message'>Fetched latest sheet names.</span>").appendTo('#gs-woo-validation-message');
            setTimeout(function () { location.reload(); }, 1000);
         } else {
            jQuery(this).parent().children(".loading-sign").removeClass( "loading" );
          location.reload(); // simply reload the page
         }
      });
   });

   /**
    * Clear debug
    */
   jQuery(document).on('click', '.debug-clear', function () {
      jQuery(".clear-loading-sign").addClass("loading");
      var data = {
         action: 'gs_woo_clear_log',
         security: jQuery('#gs-ajax-nonce').val()
      };
      jQuery.post(ajaxurl, data, function (response) {
         if (response.success) {
            jQuery(".clear-loading-sign").removeClass("loading");
            jQuery("#gs-woo-validation-message").empty();
            jQuery("<span class='woo-valid-message'>Logs are cleared.</span>").appendTo('#gs-woo-validation-message');
         }
      });
   });

   jQuery(document).on('submit', '#gsSettingFormFree', function (event) {
      console.log('prevent the subitting the form');
      jQuery('#error_spread').html('');
      jQuery('#error_gsTabName').html('');
      
      var submit = true;
      var spreadsheetsName = jQuery('#gs-woo-sheet-id').val();
      var gsTabName = jQuery('input.wc_order_state:checked').length;
      

      if(spreadsheetsName == ""){
         jQuery('#error_spread').html('* Please Select Spreadsheet Name !');
         submit = false;
      }
      if(gsTabName <= 0){
         jQuery('#error_gsTabName').html('* Please select atleast one Tabs !');
         submit = false;
      }
      
      if(submit == false){
         event.preventDefault();
         window.scrollTo({ top: 0, behavior: 'smooth' });
         // jQuery([document.documentElement, document.body]).animate({
         //    scrollTop: jQuery(".gs-woo-tabs-set").offset().top
         // }, 2000);
      }
   });
   
});