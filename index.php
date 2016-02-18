<?php
/*
Plugin Name: ReactPress
Plugin URI:
Description: Create social user reactions for posts and post types
Version: 1.0
Author: Manny "Funkatron" Fleurmond
Author URI: http://www.crosseyedeveloper.com
License: GPL2
*/

require_once( 'config.php' );

function cedrp_plugin_load() {
	//Core
	require_once( 'functions.php' );
	require_once( CEDRP_INC_DIR . 'loader.php' );
	new CEDRP_Loader;
	//Admin
	if( is_admin() ) {
	}

	//Front end
	else {
	}
}
add_action( 'plugins_loaded', 'cedrp_plugin_load' );

//Activate
function cedrp_activate() {
}
register_activation_hook( __FILE__, 'cedrp_activate' );

//Deactivate
function cedrp_deactivate() {
}
register_deactivation_hook( __FILE__, 'cedrp_deactivate' );

//Common scripts and styles
function cedrp_enqueue_scripts( $hook ) {
}
add_action( 'admin_enqueue_scripts', 'cedrp_enqueue_scripts' );
