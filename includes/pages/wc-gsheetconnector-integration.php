<div class="card-wp">
         <div class="gs-woo-in-fields">

            <h2><span class="title1"><?php echo __(' WooCommerce - '); ?></span><span class="title"><?php echo __(' Google Sheet Integration'); ?></span></h2>
            <hr>
            <p class="wpform-gs-alert-kk"> <?php echo __('Click "Get code" to retrieve your code from Google Drive to allow us to access your spreadsheets. And paste the code in the below textbox. ', 'wc-gsheetconnector'); ?></p>
            <p>
               <label><?php echo __('Google Access Code', 'wc-gsheetconnector'); ?></label>

               <?php if (!empty(get_option('gs_woo_token')) && get_option('gs_woo_token') !== "") { ?>
                  <input type="text" name="gs-woo-code" id="gs-woo-code" value="" disabled placeholder="<?php echo __('Currently Active', 'wc-gsheetconnector'); ?>"/>
                  <input type="button" name="gs-woo-deactivate-log" id="gs-woo-deactivate-log" value="<?php echo __('Deactivate', 'wc-gsheetconnector'); ?>" class="button button-primary" />
                  <span class="tooltip"> <img src="<?php echo WC_GSHEETCONNECTOR_URL; ?>assets/img/help.png" class="help-icon"> <span class="tooltiptext tooltip-right"><?php _e('On deactivation, all your data saved with authentication will be removed and you need to reauthenticate with your google account and configure sheet name and tab.', 'wc-gsheetconnector'); ?></span></span>                 
                  <span class="loading-sign-deactive">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
               <?php } else { ?>
                  <input type="text" name="gs-woo-code" id="gs-woo-code" value="" placeholder="<?php echo __('Enter Code', 'wc-gsheetconnector'); ?>"/>
                  <!--<a href="https://accounts.google.com/o/oauth2/auth?access_type=offline&approval_prompt=force&client_id=1075324102277-drjc21uouvq2d0l7hlgv3bmm67er90mc.apps.googleusercontent.com&redirect_uri=urn:ietf:wg:oauth:2.0:oob&response_type=code&scope=https%3A%2F%2Fspreadsheets.google.com%2Ffeeds%2F+https://www.googleapis.com/auth/userinfo.email+https://www.googleapis.com/auth/drive.metadata.readonly" target="_blank" class="wpforms-btn wpforms-btn-md wpforms-btn-light-grey"><?php echo __('Get Code', 'wc-gsheetconnector'); ?></a>-->
				  
				  <a href="https://accounts.google.com/o/oauth2/auth?access_type=offline&approval_prompt=force&client_id=343006833383-c73msaal124n0psi2fqb5q30u8j2p495.apps.googleusercontent.com&redirect_uri=urn:ietf:wg:oauth:2.0:oob&response_type=code&scope=https%3A%2F%2Fspreadsheets.google.com%2Ffeeds%2F+https://www.googleapis.com/auth/userinfo.email+https://www.googleapis.com/auth/drive.metadata.readonly" target="_blank" class="wpforms-btn wpforms-btn-md wpforms-btn-light-grey"><?php echo __('Get Code', 'wc-gsheetconnector'); ?></a>
				  
               <?php } ?>
                  </br>
               <?php if (empty(get_option('gs_woo_token'))) { ?>
                  <input type="button" name="save-gs-woo-code" id="save-gs-woo-code" value="Save & Authenticate">
               <?php } ?>
               <span class="loading-sign">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>

               <p>
                  <label><?php echo __('Debug Log ->', 'wc-gsheetconnector'); ?></label>
                  <label><a href="<?php echo plugins_url('../../logs/log.txt', __FILE__); ?>" target="_blank" class="gs-woo-debug-view" ><?php echo __('View', 'wc-gsheetconnector'); ?></a></label>
                  <label><a class="debug-clear" ><?php echo __('Clear', 'wc-gsheetconnector'); ?></a></label>
                  <span class="clear-loading-sign">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
               </p>
                  <p id="gs-woo-validation-message"></p>
                  <span id="deactivate-msg"></span>
               <p class="gs-woo-sync-row"><?php echo __('<a id="gs-woo-sync" data-init="yes">Click here </a>  to fetch Sheet details to be set at WooCommerce settings. ', 'wc-gsheetconnector'); ?><span class="loading-sign">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></p>

               <input type="hidden" name="gs-ajax-nonce" id="gs-ajax-nonce" value="<?php echo wp_create_nonce( 'gs-ajax-nonce' ); ?>" />

            </p>

            <!-- Connected Email Account -->
            <?php 
                    if (!empty(get_option('gs_woo_token')) && get_option('gs_woo_token') !== "") {
                    $google_sheet = new GSCWOO_googlesheet();
                    $email_account = $google_sheet->gsheet_print_google_account_email(); 
                    if( $email_account ) { ?>
                      <p class="connected-account"><?php printf( __( 'Connected Email Account:   <u>%s </u>', 'wc-gsheetconnector' ), $email_account ); ?><p>
                    <?php }else{?>
                      <p style="color:red" ><?php echo esc_html(__('Something wrong ! Your Auth code may be wrong or expired Please Deactivate and Do Re-Auth Code ', 'gsconnector')); ?></p>
                    <?php 
                      } 
                    }         
                   ?>
               </div>
      </div>

     