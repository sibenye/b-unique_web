<?php
/*
Plugin Name: Cashie Commerce
Description: Cashie Commerce is an easy to use shopping cart that allows you to quickly start selling on your WordPress blog. Cashie Commerce hosts all of the code and functionality for you so there is no additional software to download, install, or maintain. Just use this handy plugin to complete a few easy steps and you're on your way to making money.
Version: 2.2.0
Author: Cashie Commerce, Inc.
*/

// Global variables
global $cashie_url, $cashie_url_vars, $cashie_s3, $cashie_plugin_url, $cashie_partner, $cashie_partner_options_handle, $cashie_options_handle;

$cashie_url = "https://cashiecommerce.com";
// IE8 throws security errors if WP is non-ssl and you try to connect to SSL server
if (empty($_SERVER['HTTPS']) && isset($_SERVER['HTTP_USER_AGENT']))
{
	preg_match('/MSIE ([0-9]\.[0-9])/',$_SERVER['HTTP_USER_AGENT'],$matches);
	if(isset($matches[1]) && floatval($matches[1])<9) {
		$cashie_url = "http://cashiecommerce.com";
	}
}

$cashie_url_vars = "headless=true&utm_campaign=plugin_admin&utm_medium=WordPress_Plugin&utm_source=";
$cashie_s3 = "cashie";
$cashie_plugin_url = plugin_dir_url(__FILE__);
$cashie_partner_options_handle = "cashie_partner";
$cashie_options_handle = "cashie_admin_options";

include_once(dirname (__FILE__) . '/includes/cashie_partner.php'); // make sure this is first include to set global partner variable
include_once(dirname (__FILE__) . '/includes/cashie_install.php');
include_once(dirname (__FILE__) . '/includes/cashie_uninstall.php');
include_once(dirname (__FILE__) . '/includes/cashie_options.php');
include_once(dirname (__FILE__) . '/includes/cashie_settings.php');

if (!class_exists('cashie'))
{
	class cashie {
		var $post_vars;
		var $option_values;
		var $wp_user_obj;
		var $psb_mp_types;
		var $psb_query;
		var $psb_ipn;
		var $paypal_email;
		var $currency;
		
		
		function __construct() {		
			add_action( 'admin_init', array( $this, 'action_admin_init' ) );
		}
		
		function action_admin_init() {
			// only hook up these filters if we're in the admin panel, and the current user has permission
			// to edit posts and pages
			if ( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) {
				add_filter('tiny_mce_before_init', array($this, 'filter_mce_init') );
				add_filter( 'mce_buttons', array( $this, 'filter_mce_button' ) );
				add_filter( 'mce_external_plugins', array( $this, 'filter_mce_plugin' ) );
			}
		}
	
		function filter_mce_init($initArray) {
			global $cashie_url, $cashie_url_vars, $cashie_s3, $cashie_partner_options_handle;
			
			$initArray['cashie_url'] = $cashie_url;
			$initArray['cashie_url_vars'] = $cashie_url_vars . get_option($cashie_partner_options_handle);
			$initArray['cashie_s3'] = $cashie_s3;
			$initArray['extended_valid_elements'] .= ",script[*]";
			return $initArray;
		}
		
		function filter_mce_button( $buttons ) {
			// add a separation before our button
			array_push( $buttons, '|', 'cashie_atc' );
			return $buttons;
		}
		
		function filter_mce_plugin( $plugins ) {
			// this plugin file will work the magic of our button
			$plugins['cashie_atc'] = plugin_dir_url( __FILE__ ) . 'cashie_mce_plugin.js';
			$plugins['cashie_image_translation'] = plugin_dir_url( __FILE__ ) . 'cashie_mce_plugin.js';
			return $plugins;
		}
		
	
	} // end class cashie
	
} // end if (!class_exists('cashie'))

if (class_exists('cashie'))
{
    $cashie = new cashie();

    if (isset($cashie))
    {        
      // Any custom install actions
			register_activation_hook(__FILE__,'cashie_install');
			// Any custom uninstall actions
			register_deactivation_hook(__FILE__, 'cashie_uninstall');
			//load listener into wordpress
			//add_action('wp_footer', array(&$psb, 'listener'));
    }
}

function cashie_get_version() {
	$plugin_data = get_plugin_data( __FILE__ );
	$plugin_version = $plugin_data['Version'];
	return $plugin_version;
}