<?php

class CEDPR_Storage {
  static $version = 1;
  static function init() {
    self::create_tables();
    add_action( 'deleted_post', array( __CLASS__, 'deleted_reaction_to' ) );
    add_action( 'deleted_user', array( __CLASS__, 'deleted_reaction_from' ) );
  }

  static function create_tables() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    //Reaction
    $wpdb->reactions = $wpdb->prefix . "reactions";
    $sql = "CREATE TABLE $wpdb->reactions (
      reaction_id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
      reaction_date datetime DEFAULT NOW NOT NULL,
      reaction_modified datetime DEFAULT NOW NOT NULL,
      reaction_type varchar(44) DEFAULT '' NOT NULL,
      reaction_weight int DEFAULT '1' NOT NULL,
      reaction_to bigint(20) UNSIGNED NOT NULL,
      reaction_from bigint(20) UNSIGNED NOT NULL,
      PRIMARY KEY reaction_id (reaction_id),
      UNIQUE KEY(reaction_type, reaction_to, reaction_from),
      KEY reaction_type (reaction_type),
      KEY reaction_to (reaction_to),
      KEY reaction_from (reaction_from)
    ) $charset_collate;";
    dbDelta( $sql );

    //Meta
    $wpdb->reactionmeta = $wpdb->prefix . "reactionmeta";
    $sql = "CREATE TABLE $wpdb->reactionmeta (
      meta_id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
      reaction_id bigint(20) UNSIGNED NOT NULL DEFAULT '0',
      meta_key varchar(255) DEFAULT NULL,
      meta_value longtext,
      PRIMARY KEY  (meta_id),
      KEY reaction_id (reaction_id),
      KEY meta_key (meta_key)
    ) $charset_collate;";
    dbDelta( $sql );
  }

  static function deleted_reaction_to( $post_id ){
    global $wpdb;
    $post_id = intval( $post_id );
    $result = $wpdb->get_col(
      "SELECT reaction_id
       FROM $wpdb->reactions
       wHERE reaction_to = $post_id;"
    );

    if( ! empty( $result ) ){
      foreach ( $result as $rid ){
        $reaction = CEDPR_Reaction::get_instance( $rid );
        if( $reaction )
          $reaction->delete();
      }
    }
  }

  static function deleted_reaction_from( $user_id ){
    global $wpdb;
    $user_id = intval( $user_id );
    $result = $wpdb->get_col(
      "SELECT reaction_id
       FROM $wpdb->reactions
       wHERE reaction_from = $user_id;"
    );

    if( ! empty( $result ) ){
      foreach ( $result as $rid ) {
        $reaction = CEDPR_Reaction::get_instance( $rid );
        if( $reaction )
          $reaction->delete();
      }
    }
  }
}
