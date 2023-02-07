<?php
// Get selected Sheet
$selected_sheet_key = get_option( 'gs_woo_settings' );

// Get all sheet details of the connected account
$sheet_data = get_option( 'gs_woo_sheet_feeds' );

// Get order states/ Tab names
$selected_order_states = get_option( 'gscwc_order_states' );

$woo_service = new wc_gsheetconnector_Service();

?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<form method="post" id="gsSettingFormFree">
	<div class="gs-woo-fields">
		<h2>
			<span class="title1"><?php echo esc_html( __( 'WooCommerce Google Sheet Settings', 'wc-gsheetconnector' ) ); ?></span>
		</h2>
		<hr>
		</br>
		
		<div class="gs-woo-in-fields">
			<div class="sheet-details <?php echo esc_html($class,'wc-gsheetconnector'); ?>">
				<p>
					<label><?php echo esc_html( __( 'Google Sheet Name', 'wc-gsheetconnector' ) ); ?></label>
					<select name="gs-woo-sheet-id" id="gs-woo-sheet-id"> 
						<option value=""><?php echo __( 'Select', 'wc-gsheetconnector' ); ?></option>         

						<?php
							if ( ! empty( $sheet_data ) ) {
								foreach ( $sheet_data as $key => $value ) {
									$selected = "";
									if ( $selected_sheet_key !== "" && $key == $selected_sheet_key ) {
										$selected = "selected";
									}
									?>
									<option value="<?php echo esc_html($key,'wc-gsheetconnector'); ?>" <?php echo esc_html($selected,'wc-gsheetconnector'); ?> ><?php echo esc_html($value['sheet_name'],'wc-gsheetconnector'); ?></option>
									<?php
								}
							}
						?>
					</select>
					<span class="error_msg" id="error_spread"></span>

					<span class="loading-sign">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
					<input type="hidden" name="gs-ajax-nonce" id="gs-ajax-nonce" value="<?php echo wp_create_nonce( 'gs-ajax-nonce' ); ?>" />

				</p>

				<p class="sheet-url" id="sheet-url">
					<?php $sheet_id	 = "";
					
					if ( ! empty( $selected_sheet_key ) ) {
						$sheet_id	 = $selected_sheet_key; ?>
						<label><?php echo __( 'Google Sheet URL', 'wc-gsheetconnector' ); ?></label> 
						<a href="https://docs.google.com/spreadsheets/d/<?php echo esc_html($sheet_id,'wc-gsheetconnector'); ?>" target="_blank" ><input type="button" id="viewsheet" name="viewsheet" value="View Spreadsheet"></a>
						<?php    
					}
					?>
				</p>
				
				<br/>
				
				<p class="gs-woo-sync-row"><?php echo __( 'Not showing Sheet Name ? <a id="gs-woo-sync" data-init="no">Click here </a>  to fetch it. ', 'wc-gsheetconnector' ); ?><span class="loading-sign">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></p>
			</div>
		</div>
    </div>
    
	
	</br>

	<div class="gs-woo-tabs-set">
	<h2><span class="title1"><?php echo esc_html( __( 'Google Sheets/Tab Name ', 'wc-gsheetconnector' ) ); ?> </span>
    </h2>
    <hr>
    <span class="error_msg" id="error_gsTabName"></span>
	<br class="clear">
	
    <?php $order_state_list = $woo_service->status_and_sheets;
    
		foreach ( $order_state_list as $key => $state_name ) {
			$order_state_checked = "";
			if(!empty($selected_order_states)){
				if ( in_array( $key, $selected_order_states ) ) {
					$order_state_checked = "checked";
				}
			}
			?>
			<div class="gs-woo-cards" >
				<span class="woo-pointer">
					<input type="checkbox" class="wc_order_state check-toggle" name="wc_order_state[]" value="<?php echo esc_html($key,'wc-gsheetconnector'); ?>" <?php echo esc_html($order_state_checked,'wc-gsheetconnector'); ?> id="<?php echo $key; ?>"  style="display: none;"><?php echo esc_html($state_name,'wc-gsheetconnector'); ?>
					<label for="<?php echo $key; ?>" class="button-woo-toggle"></label>
				</span>
			</div>
	<?php } ?>
	</div>
    <br class="clear">
	<div class="woo-header">
    <h2>
    <span class="title1"><?php echo esc_html( __( 'Headers ', 'wc-gsheetconnector' ) ); ?> </span>
	</h2>
	<hr>
	<br class="clear">
    <ul>
		<?php 
		$header_list = $woo_service->sheet_headers;
		foreach( $header_list as $header => $data ) { ?>
			<!-- <li class="li-woo-header"><label><?php echo esc_html($header,'wc-gsheetconnector'); ?></label></li> -->

			<li class="li-woo-header">
					<i class="fa fa-sort sort-icon"></i>
					<div class="switch-label">
						<label>
							<span class='label'>
								<div class='label_text'><?php echo esc_html($header,'wc-gsheetconnector'); ?></div>
								<div class="edit_col_name"><span class="tooltip1"><span class="tooltiptext1"><?php _e('Upgrade To Pro', 'wc-gsheetconnector'); ?></span><i class="fa fa-pencil"></i></div>
						</label>
					</div>
					
					<div class="switch-field">
						<span class="tooltip1"><span class="tooltiptext1"><?php _e('Upgrade To Pro', 'wc-gsheetconnector'); ?></span>
						<label for="<?php echo $header ?>-one" class="switch-label-yes"></label>
						<label for="<?php echo $header ?>-two" class="switch-label-no"></label>
					</div>
					</span>
					
				</li>
		<?php } ?>	
    </ul>
	</div>
    <input type="submit" value="Submit Data" id="woo-save-btn" class="woo-save-btn" name="woo-save-btn" >
</form>
