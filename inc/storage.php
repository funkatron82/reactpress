<?php

class CEDPR_Storage {
  static $version = 1;
  static function init() {
    self::create_tables();
    add_action( 'deleted_post', array( __CLASS__, 'deleted_post' ) );
    add_action( 'deleted_user', array( __CLASS__, 'deleted_user' ) );
  }

  static function create_tables() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    //Reaction
    $table_name = $wpdb->prefix . "reactions";
    $sql = "CREATE TABLE $table_name (
      reaction_id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
      reaction_date datetime DEFAULT NOW NOT NULL,
      reaction_modified datetime DEFAULT NOW NOT NULL,
      reaction_type varchar(44) DEFAULT '' NOT NULL,
      reaction_weight int DEFAULT '1' NOT NULL,
      post_id bigint(20) UNSIGNED NOT NULL,
      user_id bigint(20) UNSIGNED NOT NULL,
      PRIMARY KEY id (id),
      UNIQUE KEY(reaction_type, post_id, user_id)
    ) $charset_collate;";
    dbDelta( $sql );

    //Meta
    $table_name = $wpdb->prefix . "reactionmeta";
    $sql = "CREATE TABLE $table_name (
      meta_id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
      reaction_id bigint(20) UNSIGNED NOT NULL DEFAULT '0',
      meta_key varchar(255) DEFAULT NULL,
      meta_value longtext,
      PRIMARY KEY  (meta_id),
      KEY reaction_id (p2p_id),
      KEY meta_key (meta_key)
    ) $charset_collate;";
    dbDelta( $sql );


  }

  static function deleted_post( $post_id ){}

  static function deleted_user( $user_id ){}
}
