<?php
/*
 * Google Sheet configuration and settings page
 * @since 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
   exit();
}

$active_tab = ( isset ( $_GET['tab'] ) && sanitize_text_field( $_GET["tab"] )) ?  sanitize_text_field( $_GET['tab'] ) : 'integration';

?>

<div class="wrap">
	<?php
       $tabs = array(  
        'integration' => __( 'Integration', 'wc-gsheetconnector' ),
        'settings'    => __( 'WooCommerce Data Settings', 'wc-gsheetconnector')
         
         );
       echo '<div id="icon-themes" class="icon32"><br></div>';
       echo '<h2 class="nav-tab-wrapper">';
       foreach( $tabs as $tab => $name ){
           $class = ( $tab == $active_tab ) ? ' nav-tab-active' : '';
           echo "<a class='nav-tab$class' href='?page=wc-gsheetconnector-config&tab=$tab'>".filter_var($name, FILTER_SANITIZE_STRING)."</a>";

       }
       echo '</h2>';
   	switch ( $active_tab ){
        case 'integration' :
            include( WC_GSHEETCONNECTOR_PATH . "includes/pages/wc-gsheetconnector-integration.php" ) ;
              break;
            case 'settings' :
                include( WC_GSHEETCONNECTOR_PATH . "includes/pages/wc-gsheetconnector-setting.php" ) ;
            break;            
		
	}
	?>
</div>

