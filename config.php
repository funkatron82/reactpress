<?php
//vVrsion
if (!defined('CEDPR_VERSION'))
	define("CEDPR_VERSION", "1.0" ); 
	
//Plugin dir
if (!defined('CEDPR_DIR'))
	define('CEDPR_DIR', plugin_dir_path( __FILE__ ) );
define('CEDPR_INC_DIR', trailingslashit( CEDPR_DIR . 'inc' ) );


//Plugin url
if (!defined('CEDPR_URL'))
	define('CEDPR_URL',  plugin_dir_url( __FILE__ ));
define('CEDPR_INC_URL', trailingslashit( CEDPR_URL . 'inc' ) );
