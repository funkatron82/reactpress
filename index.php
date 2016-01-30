<?php
/*
Plugin Name: Post Reactr
Plugin URI:
Description: Create social user reactions for posts and post types
Version: 1.0
Author: Manny "Funkatron" Fleurmond
Author URI: http://www.crosseyedeveloper.com
License: GPL2
*/

require_once( 'config.php' );

function cedpr_plugin_load() {
	//Core

	//Admin
	if( is_admin() ){
	}

	//Front end
	else{
	}
}
add_action( 'plugins_loaded', 'cedpr_plugin_load' );

//Activate
function cedpr_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'cedpr_activate' );

//Deactivate
function cedpr_deactivate() {
}
register_deactivation_hook( __FILE__, 'cedpr_deactivate' );

//Common scripts and styles
function cedpr_enqueue_scripts( $hook ) {
}
add_action( 'admin_enqueue_scripts', 'cedpr_enqueue_scripts' );
