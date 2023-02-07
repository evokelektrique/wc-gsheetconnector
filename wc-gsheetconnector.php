<?php

/**
 * Plugin Name: WooCommerce GSheetConnector
 * Plugin URI: https://wordpress.org/plugins/wc-gsheetconnector/
 * Description: Send your WooCommerce data to your Google Sheets spreadsheet.
 * Author: GSheetConnector
 * Author URI: https://www.gsheetconnector.com/
 * Version: 1.2.6
 * Text Domain: wc-gsheetconnector
 * WooCommerce requires at least: 3.2.0
 * WC GSheetConnector tested up to: 6.0
 * PHP tested up to 8.0.3
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if(wc_gsheetconnector_Init::gscwoo_is_pugin_active('wc_gsheetconnector_Init_Pro')){
    return;
}


/*freemius*/
if ( ! function_exists( 'gs_woofree' ) ) {
    // Create a helper function for easy SDK access.
    function gs_woofree() {
        global $gs_woofree;

        if ( ! isset( $gs_woofree ) ) {
            // Activate multisite network integration.
            if ( ! defined( 'WP_FS__PRODUCT_9480_MULTISITE' ) ) {
                define( 'WP_FS__PRODUCT_9480_MULTISITE', true );
            }

            // Include Freemius SDK.
            require_once dirname(__FILE__) . '/freemius/start.php';

            $gs_woofree = fs_dynamic_init( array(
                'id'                  => '9480',
                'slug'                => 'wc-gsheetconnector',
                'type'                => 'plugin',
                'public_key'          => 'pk_487f703ba4a974974c9d344111193',
                'is_premium'          => false,
                'has_addons'          => false,
                'has_paid_plans'      => false,
                'menu'                => array(
                    'slug'           => 'wc-gsheetconnector-config',
                    'first-path'     => 'admin.php?page=wc-gsheetconnector-config',
                    'account'        => false,
                ),
            ) );
        }

        return $gs_woofree;
    }

    // Init Freemius.
    gs_woofree();
    // Signal that SDK was initiated.
    do_action( 'gs_woofree_loaded' );
}
/*freemius*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}


// Declare some global constants
define( 'WC_GSHEETCONNECTOR_VERSION', '1.2.6' );
define( 'WC_GSHEETCONNECTOR_DB_VERSION', '1.2.6' );
define( 'WC_GSHEETCONNECTOR_ROOT', dirname( __FILE__ ) );
define( 'WC_GSHEETCONNECTOR_URL', plugins_url( '/', __FILE__ ) );
define( 'WC_GSHEETCONNECTOR_BASE_FILE', basename( dirname( __FILE__ ) ) . '/wc-gsheetconnector.php' );
define( 'WC_GSHEETCONNECTOR_BASE_NAME', plugin_basename( __FILE__ ) );
define( 'WC_GSHEETCONNECTOR_PATH', plugin_dir_path( __FILE__ ) ); //use for include files to other files
define( 'WC_GSHEETCONNECTOR_CURRENT_THEME', get_stylesheet_directory() );

load_plugin_textdomain( 'wc-gsheetconnector', false, basename( dirname( __FILE__ ) ) . '/languages' );

/*
 * include utility classes
 */
if ( ! class_exists( 'wc_gsheetconnector_utility' ) ) {
    include(WC_GSHEETCONNECTOR_ROOT . '/includes/class-wc-gsheetconnector-utility.php' );
}
//Include Library Files
require_once WC_GSHEETCONNECTOR_ROOT . '/lib/vendor/autoload.php';

include_once( WC_GSHEETCONNECTOR_ROOT . '/lib/google-sheets.php');

if ( ! class_exists( 'wc_gsheetconnector_Service' ) ) {
    include_once( WC_GSHEETCONNECTOR_PATH . 'includes/class-wc-gsheetconnector-services.php' );
}

class wc_gsheetconnector_Init {

    /**
     *  Set things up.
     *  @since 1.0
     */
    public function __construct() {

	//run on activation of plugin
	register_activation_hook( __FILE__, array( $this, 'wc_gsheetconnector_activate' ) );

	//run on deactivation of plugin
	register_deactivation_hook( __FILE__, array( $this, 'wc_gsheetconnector_deactivate' ) );

	//run on uninstall
	register_uninstall_hook( __FILE__, array( 'wc_gsheetconnector_Init', 'gs_wocommerce_free_uninstall' ) );

	// validate is contact form 7 plugin exist
	add_action( 'admin_init', array( $this, 'validate_parent_plugin_exists' ) );

	// register admin menu under "Contact" > "Integration"
	add_action( 'admin_menu', array( $this, 'register_gs_menu_pages' ), 70 );

	// load the js and css files
	add_action( 'init', array( $this, 'load_css_and_js_files' ) );

	// load the classes
	add_action( 'init', array( $this, 'load_all_classes' ) );
    }


    public function gs_wocommerce_free_uninstall(){
        // Not like register_uninstall_hook(), you do NOT have to use a static function.
        gs_woofree()->add_action('after_uninstall', 'gs_woofree_uninstall_cleanup');
    }

    /**
     * Do things on plugin activation
     * @since 1.0
     */
    public function wc_gsheetconnector_activate( $network_wide ) {
	global $wpdb;
	$this->run_on_activation();
	if ( function_exists( 'is_multisite' ) && is_multisite() ) {
	    // check if it is a network activation - if so, run the activation function for each blog id
	    if ( $network_wide ) {
		// Get all blog ids
		$blogids = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->base_prefix}blogs" );
		foreach ( $blogids as $blog_id ) {
		    switch_to_blog( $blog_id );
		    $this->run_for_site();
		    restore_current_blog();
		}
		return;
	    }
	}

	// for non-network sites only
	$this->run_for_site();
    }

     /**
    * Called on activation.
    * Creates the site_options (required for all the sites in a multi-site setup)
    * If the current version doesn't match the new version, runs the upgrade
    * @since 1.0
    */
   private function run_on_activation() {
       try{
          $plugin_options = get_site_option('WC_GS_info');
          if (false === $plugin_options) {
             $njforms_GS_info = array(
                'version' => WC_GSHEETCONNECTOR_VERSION,
                'db_version' => WC_GSHEETCONNECTOR_DB_VERSION
             );
             update_site_option('WC_GS_info', $njforms_GS_info);
          } else if (WC_GSHEETCONNECTOR_DB_VERSION != $plugin_options['version']) {
             $this->run_on_upgrade();
          }
      //echo "activate";
      //exit;
        } catch (Exception $e) {
         wc_gsheetconnector_utility::gs_debug_log("Something Wrong : - " . $e->getMessage());
      }
   }

   /**
    * called on upgrade. 
    * checks the current version and applies the necessary upgrades from that version onwards
    * @since 1.0
    */
   public function run_on_upgrade() {
      $plugin_options = get_site_option('WC_GS_info');

      // update the version value
      $google_sheet_info = array(
         'version' => WC_GSHEETCONNECTOR_ROOT,
         'db_version' => WC_GSHEETCONNECTOR_DB_VERSION
      );
      update_site_option('WC_GS_info', $google_sheet_info);
   }


    /**
     * deactivate the plugin
     * @since 1.0
     */
    public function wc_gsheetconnector_deactivate( $network_wide ) {
	
    }

    /**
     *  Runs on plugin uninstall.
     *  a static class method or function can be used in an uninstall hook
     *
     *  @since 1.0
     */
    public static function gs_connector_free_uninstall() {
	global $wpdb;
	wc_gsheetconnector_Init::run_on_uninstall();
	if ( function_exists( 'is_multisite' ) && is_multisite() ) {
	    //Get all blog ids; foreach of them call the uninstall procedure
	    $blog_ids = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->base_prefix}blogs" );

	    //Get all blog ids; foreach them and call the install procedure on each of them if the plugin table is found
	    foreach ( $blog_ids as $blog_id ) {
		switch_to_blog( $blog_id );
		wc_gsheetconnector_Init::delete_for_site();
		restore_current_blog();
	    }
	    return;
	}
	wc_gsheetconnector_Init::delete_for_site();
    }

    /**
     * Called on uninstall - deletes site_options
     *
     * @since 1.5
     */
    private static function run_on_uninstall() {
	if ( ! defined( 'ABSPATH' ) && ! defined( 'WP_UNINSTALL_PLUGIN' ) )
	    exit();

	delete_site_option( 'google_sheet_info' );
    }

    /**
     * Called on uninstall - deletes site specific options
     *
     * @since 1.0
     */
    private static function delete_for_site() {

	delete_option( 'gs_woo_access_code' );
	delete_option( 'gs_woo_verify' );
	delete_option( 'gs_woo_token' );
	delete_option( 'gs_woo_feeds' );
	delete_option( 'gs_woo_sheetId' );
	delete_post_meta_by_key( 'gs_woo_settings' );
    }

    /**
     * Validate parent Plugin WooCommerce exist and activated
     * @access public
     * @since 1.0
     */
    public function validate_parent_plugin_exists() {

	$plugin = plugin_basename( __FILE__ );
	if ( ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) || ( ! file_exists( plugin_dir_path( __DIR__ ) . 'woocommerce/woocommerce.php' ) ) ) {
	    add_action( 'admin_notices', array( $this, 'wc_gsheet_missing_notice' ) );
	    add_action( 'network_admin_notices', array( $this, 'wc_gsheet_missing_notice' ) );
	    deactivate_plugins( $plugin );
	    if ( isset( $_GET[ 'activate' ] ) ) {
		// Do not sanitize it because we are destroying the variables from URL
		unset( $_GET[ 'activate' ] );
	    }
	}
    }

    /**
     * If WooCommerce plugin is not installed or activated then throw the error
     *
     * @access public
     * @return mixed error_message, an array containing the error message
     *
     * @since 1.0 initial version
     */
    public function wc_gsheet_missing_notice() {
	$plugin_error = wc_gsheetconnector_utility::instance()->admin_notice( array(
	    'type'		 => 'error',
	    'message'	 => __( 'GSheetConnector WooCommerce Add-on requires WooCommerce plugin to be installed and activated.', 'wc-gsheetconnector' )
	) );
		//echo esc_html($plugin_error,'wc-gsheetconnector');
		echo $plugin_error;
    }

    /**
     * Called on activation.
     * Creates the options and DB (required by per site)
     * @since 1.0
     */
    private function run_for_site() {
	if ( ! get_option( 'gs_woo_access_code' ) ) {
	    update_option( 'gs_woo_access_code', '' );
	}
	if ( ! get_option( 'gs_woo_verify' ) ) {
	    update_option( 'gs_woo_verify', 'invalid' );
	}
	if ( ! get_option( 'gs_woo_token' ) ) {
	    update_option( 'gs_woo_token', '' );
	}
	if ( ! get_option( 'gs_woo_feeds' ) ) {
	    update_option( 'gs_woo_feeds', '' );
	}
	if ( ! get_option( 'gs_woo_sheetId' ) ) {
	    update_option( 'gs_woo_sheetId', '' );
	}
	if ( ! get_option( 'gs_woo_settings' ) ) {
	    update_option( 'gs_woo_settings', '' );
	}
	if ( ! get_option( 'gs_woo_checkbox_settings' ) ) {
	    update_option( 'gs_woo_checkbox_settings', array() );
	}
    }

    public function load_css_and_js_files() {
	add_action( 'admin_print_styles', array( $this, 'add_css_files' ) );
	add_action( 'admin_print_scripts', array( $this, 'add_js_files' ) );
    }

    /**
     * enqueue CSS files
     * @since 1.0
     */
    public function add_css_files() {
	if ( is_admin() && ( isset( $_GET[ 'page' ] ) && ( $_GET[ 'page' ] == 'wc-gsheetconnector-config' ) ) ) {
	    wp_enqueue_style( 'gs-woocommerce-connector-css', WC_GSHEETCONNECTOR_URL . 'assets/css/gs-woocommerce-connector.css', WC_GSHEETCONNECTOR_VERSION, true );
	}
    }

    /**
     * enqueue JS files
     * @since 1.0
     */
    public function add_js_files() {
	if ( is_admin() && ( isset( $_GET[ 'page' ] ) && ( $_GET[ 'page' ] == 'wc-gsheetconnector-config' ) ) ) {
	    wp_enqueue_script( 'gs-connector-js', WC_GSHEETCONNECTOR_URL . 'assets/js/gs-connector.js', WC_GSHEETCONNECTOR_VERSION, true );
	}

	if ( is_admin() ) {
	    wp_enqueue_script( 'gs-connector-notice-css', WC_GSHEETCONNECTOR_URL . 'assets/js/gs-connector-notice.js', WC_GSHEETCONNECTOR_VERSION, true );
	}
    }

    /**
     * Create/Register menu items for the plugin.
     * @since 1.0
     */
    public function register_gs_menu_pages() {
	add_submenu_page( 'woocommerce', 'Google Sheets', 'Google Sheets', 'manage_options', 'wc-gsheetconnector-config', array( $this, 'google_sheet_configuration' ) );
    }

    /**
     * Google Sheets page action.
     * This method is called when the menu item "Google Sheets" is clicked.
     * @since 1.0
     */
    public function google_sheet_configuration() {
	include( WC_GSHEETCONNECTOR_PATH . "includes/pages/google-sheet-settings.php" );
    }

    /**
     * Load all the classes - as part of init action hook
     * @since 1.0
     */
    public function load_all_classes() {
		if ( ! class_exists( 'GS_Processes' ) ) {
		    include( WC_GSHEETCONNECTOR_PATH . 'includes/class-wc-gsheetconnector-processes.php' );
		}
    }

    /**
    * Add custom link for the plugin beside activate/deactivate links
    * @param array $links Array of links to display below our plugin listing.
    * @return array Amended array of links.    * 
    * @since 1.5
    */
   public function wc_gsheet_setting_link($links) {
      // We shouldn't encourage editing our plugin directly.
      unset($links['edit']);

      // Add our custom links to the returned array value.
      return array_merge(array(
          '<a href="' . admin_url('admin.php?page=wc-gsheetconnector-config') . '">' . __('Settings', 'gsconnector') . '</a>'
              ), $links);
   }

   /**
    * Add function to check plugins is Activate or not
    * @param string $class of plugins main class .
    * @return true/false    * 
    * @since 2.0.2
    */
   public static function gscwoo_is_pugin_active($class) {
        if ( class_exists( $class ) ) {
            return true;
        }
        return false;
    }

}

// Initialize the google sheet connector class
$init = new wc_gsheetconnector_Init();

add_filter('plugin_action_links_' . WC_GSHEETCONNECTOR_BASE_NAME, array($init, 'wc_gsheet_setting_link'));
