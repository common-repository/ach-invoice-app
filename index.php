<?php
/*
Plugin Name: Ach Invoice App
Description: Join the financial revolution today and let Service Shogun fortify your small business against corporate greed.
Version: 1.0.1
Author: Service Shogun
Author URI: https://serviceshogun.com/
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define('plaidintegra_url',plugin_dir_url(__FILE__ ));
define('plaidintegra_path',plugin_dir_path(__FILE__ ));
define('ACHINVOICEAPP_PLUGIN_WELCOME_URL',"?page=plaidplugin&tab=welcome");

$plugin = plugin_basename(__FILE__);

/* Master Class  */
require_once ('loader.php');
register_activation_hook( __FILE__, 'plaidintegra_activation'); 

function  plaidintegra_activation( $network_wide ) {
	$plugin = "ach-invoice-app/index.php";
	$plugin_path = '';	
	
	if ( is_multisite() && $network_wide ) // See if being activated on the entire network or one blog
	{ 
		activate_plugin($plugin_path,NULL,true);			
		
	} else { // Running on a single blog		   	
			
		activate_plugin($plugin_path,NULL,false);		
		
	}
}

function plaidintegra_load_textdomain(){     	  
	$locale = apply_filters( 'plugin_locale', get_locale(), 'ach-invoice-app' );	   
	$mofile = plaidintegra_path . "languages/ach-invoice-app-$locale.mo";
		 
	 // Global + Frontend Locale
	 load_textdomain( 'ach-invoice-app', $mofile );
	 load_plugin_textdomain( 'ach-invoice-app', false, dirname(plugin_basename(__FILE__)).'/languages/' );
}

/* Load plugin text domain (localization) */
add_action('init', 'plaidintegra_load_textdomain');	

global $plaidplugin;
$plaidplugin = new PlaidIntPlugin();

register_activation_hook(__FILE__, 'achinvoice_my_plugin_activate');
add_action('admin_init', 'achinvoice_my_plugin_redirect');

function achinvoice_my_plugin_activate(){
    add_option('achinvoice_plugin_do_activation_redirect', true);
}


function achinvoice_my_plugin_redirect(){
    if (get_option('achinvoice_plugin_do_activation_redirect', false)) {
        delete_option('achinvoice_plugin_do_activation_redirect');
		
		if (! get_option('achinvoice_ini_setup')){
			wp_redirect(ACHINVOICEAPP_PLUGIN_WELCOME_URL);
		}else{
			wp_redirect(ACHINVOICEAPP_PLUGIN_WELCOME_URL);
		}
    }
}

