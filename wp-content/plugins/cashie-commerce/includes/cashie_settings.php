<?php
if (!class_exists('cashie_settings'))
{

	class cashie_settings
	{
		var $post_vars;
		var $option_values;
		var $update;
		var $custom_roles;
		var $cashie_url;
		var $cashie_url_vars;
		var $cashie_s3;
				
		function __construct($post_vars)
		{
			global $cashie_url, $cashie_url_vars, $cashie_s3, $cashie_partner_options_handle;
			$this->post_vars = $post_vars;
			$this->cashie_url = $cashie_url;
			$this->cashie_url_vars = $cashie_url_vars . get_option($cashie_partner_options_handle);
			$this->cashie_s3 = $cashie_s3;
		}
			
		function cashie_admin_init()
		{
			//Registers options page stylesheet
			wp_register_style('cashie_stylesheet', plugins_url('/css/settings.css', dirname(__FILE__)));
			wp_register_script( 'cashie_js_utils', plugins_url('/js/utils.js', dirname(__FILE__)));
			wp_register_script( 'cashie_js_dashboard', plugins_url('/js/cashie_settings_dashboard.js', dirname(__FILE__)));
			wp_register_script( 'cashie_js_profile', plugins_url('/js/cashie_settings_profile.js', dirname(__FILE__)));
			wp_register_script( 'cashie_js_products', plugins_url('/js/cashie_settings_products.js', dirname(__FILE__)));
			wp_register_script( 'cashie_js_store', plugins_url('/js/cashie_settings_store.js', dirname(__FILE__)));
		}
				
		function create_menu()
		{
							//creates new top-level menu
							add_menu_page('Cashie Commerce', 'Cashie Commerce', 'administrator', __FILE__, null, plugins_url('/images/icon_cashie.png', dirname(__FILE__)));
							
							// First submenu has same slug as top level menu so that it is the default selected when top level menu is clicked
							$page = add_submenu_page( __FILE__, 'Cashie Commerce', 'Dashboard', 'administrator', __FILE__, array($this, 'settings_dashboard'));
							add_action( 'admin_print_styles-' . $page, array($this, 'cashie_admin_styles') );
							add_action('admin_print_scripts-' . $page, array($this, 'cashie_dashboard_js') );
							
							$page = add_submenu_page( __FILE__, 'Cashie Commerce', 'Products', 'administrator', __FILE__.'_products', array($this, 'settings_products'));
							add_action( 'admin_print_styles-' . $page, array($this, 'cashie_admin_styles') );
							add_action('admin_print_scripts-' . $page, array($this, 'cashie_products_js') );
							
							//$page = add_submenu_page( __FILE__, 'Cashie Commerce', 'Categories', 'administrator', __FILE__.'_categories', array($this, 'settings_categories'));
							//add_action( 'admin_print_styles-' . $page, array($this, 'cashie_admin_styles') );
							
							$page = add_submenu_page( __FILE__, 'Cashie Commerce', 'Design', 'administrator', __FILE__.'_pages', array($this, 'settings_pages'));
							add_action( 'admin_print_styles-' . $page, array($this, 'cashie_admin_styles') );
							
							$page = add_submenu_page( __FILE__, 'Cashie Commerce', 'Settings', 'administrator', __FILE__.'_store', array($this, 'settings_store'));
							add_action( 'admin_print_styles-' . $page, array($this, 'cashie_admin_styles') );
							add_action('admin_print_scripts-' . $page, array($this, 'cashie_store_js') );
							
							$page = add_submenu_page( __FILE__, 'Cashie Commerce', 'Orders', 'administrator', __FILE__.'_transactions', array($this, 'settings_transactions'));
							add_action( 'admin_print_styles-' . $page, array($this, 'cashie_admin_styles') );
							
							$page = add_submenu_page( __FILE__, 'Cashie Commerce', 'Billing', 'administrator', __FILE__.'_billing', array($this, 'settings_billing'));
							add_action( 'admin_print_styles-' . $page, array($this, 'cashie_admin_styles') );
							
							$page = add_submenu_page( __FILE__, 'Cashie Commerce', 'Account', 'administrator', __FILE__.'_profile', array($this, 'settings_profile'));
							add_action( 'admin_print_styles-' . $page, array($this, 'cashie_admin_styles') );
							add_action('admin_print_scripts-' . $page, array($this, 'cashie_profile_js') );
							
							$page = add_submenu_page( __FILE__, 'Cashie Commerce', 'Help', 'administrator', __FILE__.'_help', array($this, 'settings_help'));
							add_action( 'admin_print_styles-' . $page, array($this, 'cashie_admin_styles') );
				
							//Triggers instantiation of psb_Options class
							$this->init_options();
							
							//add_action('admin_enqueue_scripts','cashie_admin_js',10,1);
							//add_action( 'admin_enqueue_scripts', 'cashie_admin_js' );
							
		}
			
		function cashie_admin_styles()
		{
							//It will be called only on your plugin admin page, enqueue our stylesheet here
							wp_enqueue_style('cashie_stylesheet');
		}
		
		function cashie_dashboard_js()
		{
			wp_enqueue_script( 'cashie_js_utils' );
			wp_enqueue_script( 'cashie_js_dashboard');
		}
		
		function cashie_products_js()
		{
			wp_enqueue_script( 'cashie_js_utils' );
			wp_enqueue_script( 'cashie_js_products');
		}
		
		function cashie_store_js()
		{
			wp_enqueue_script( 'cashie_js_utils' );
			wp_enqueue_script( 'cashie_js_store');
		}
		
		function cashie_profile_js()
		{
			wp_enqueue_script( 'cashie_js_utils' );
			wp_enqueue_script( 'cashie_js_profile');
		}
			
		function init_options()
		{
			if (class_exists('cashie_options'))
			{
					//only instantiates options when form is submitted
					if (!empty($this->post_vars['update']))
					{
							//instantiates psb_Options class and pass post_vars to it
							$cashie_options = new cashie_options($this->post_vars);
							$this->update = true;
					}
			}

			if (isset($cashie_options))
			{
					//updates options and retrieves the options array
				//only do that when form is submitted
				$this->option_values = $cashie_options->get_cashie_options();
			} 
			else
			{
					//gets options array from the the db directly when coming from other admin menus-- no form is submitted.
					$this->option_values = get_option('cashie_admin_options');
			}
			
		}
		
		function settings_dashboard()
		{ 
			include_once('cashie_settings_dashboard.php');
		}
		
		function settings_store()
		{ 
		  if (empty($this->option_values['hash']))
				include_once('cashie_settings_link.php');
			else
		    include_once('cashie_settings_store.php');
		}
		
		function settings_categories()
		{ 
		  if (empty($this->option_values['hash']))
				include_once('cashie_settings_link.php');
			else
				include_once('cashie_settings_categories.php');
		}
		
		function settings_pages()
		{ 
		  if (empty($this->option_values['hash']))
				include_once('cashie_settings_link.php');
			else
				include_once('cashie_settings_pages.php');
		}
		
		function settings_products()
		{ 
		  if (empty($this->option_values['hash']))
				include_once('cashie_settings_link.php');
			else
				include_once('cashie_settings_products.php');
		}
		
		function settings_transactions()
		{ 
			if (empty($this->option_values['hash']))
				include_once('cashie_settings_link.php');
			else
		  	include_once('cashie_settings_transactions.php');
		}
		
		function settings_billing()
		{ 
			if (empty($this->option_values['hash']))
				include_once('cashie_settings_link.php');
			else
		  	include_once('cashie_settings_billing.php');
		}
		
		function settings_profile()
		{ 
			if (empty($this->option_values['hash']))
				include_once('cashie_settings_link.php');
			else
				include_once('cashie_settings_profile.php');  
		}
		
		function settings_help()
		{ 
			include_once('cashie_settings_help.php');  
		}
		
	} // class cashie_settings
	
} // if (!class_exists('cashie_settings'))


if (class_exists('cashie_settings'))
{
		//instantiates this class
		$cashie_settings = new cashie_settings($_POST);
	
		if (isset($cashie_settings))
		{
			//loads settings page css script
			add_action('admin_init', array(&$cashie_settings, 'cashie_admin_init'));
			//initializes display of settings page
			add_action('admin_menu', array(&$cashie_settings, 'create_menu'));
		}
}	