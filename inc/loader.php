<?php

class CEDRP_Loader {
  function __construct() {
    $this->define_constants();
    spl_autoload_register( array( $this, 'autoload' ) );
  }

  function define_constants() {
  	define("CEDRP_VERSION", "1.0" );
  	define('CEDRP_DIR', plugin_dir_path( __FILE__ ) );
    define('CEDRP_INC_DIR', trailingslashit( CEDRP_DIR . 'inc' ) );
  	define('CEDRP_URL',  plugin_dir_url( __FILE__ ));
    define('CEDRP_INC_URL', trailingslashit( CEDRP_URL . 'inc' ) );
  }

  function autoload( $class ) {
    if( 0 !== strpos( $class, 'CEDRP_' ) ) {
      return;
    }

    $file = substr( $class, 6 );
    $file = strtolower( str_replace( '_', '-', $file ) ) . '.php';
    if( file_exists( CEDRP_INC_DIR . $file ) )
      require_once( CEDRP_INC_DIR . $file );
  }
}
