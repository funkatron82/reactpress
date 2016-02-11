<?php

class CEDRP_Storage {
  static $version = 1;
  static function init() {
    self::create_tables();
    add_action( 'deleted_post', array( __CLASS__, 'deleted_post_object' ) );
    add_action( 'deleted_comment', array( __CLASS__, 'deleted_comment_object' ) );
    add_action( 'deleted_user', array( __CLASS__, 'deleted_user_object' ) );
    add_action( 'deleted_user', array( __CLASS__, 'deleted_subject' ) );
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
      object_id bigint(20) UNSIGNED NOT NULL,
      subject_id bigint(20) UNSIGNED NOT NULL,
      PRIMARY KEY reaction_id (reaction_id),
      UNIQUE KEY(reaction_type, object_id, subject_id),
      KEY reaction_type (reaction_type),
      KEY object_id (object_id),
      KEY subject_id (subject_id)
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

  static function deleted_object( $object_type, $object_id ) {
    if( ! in_array( $object_type, array( 'post', 'user', 'comment' ) ) )
      return false;

    global $wpdb;
    $types = array();
    foreach( CEDRP_Reaction_Type::$reaction_types as $name => $type ) {
      if( $object_type === $type->object_options['type'] ) {
        $types[] = $name;
      }
    }

    $sql = $wpdb->prepare(
      "SELECT reaction_id
       FROM $wpdb->reactions
       WHERE object_id = %d
       AND reaction_type IN (" . implode( ', ', array_fill( 0, count( $types ), '%s' ) ) . ")",
       array_merge( (array) $object_id, $types )
    );

    $result = $wpdb->get_col( $sql );

    if( ! empty( $result ) ){
      foreach ( $result as $rid ){
        $reaction = CEDRP_Reaction::get_instance( $rid );
        if( $reaction )
          $reaction->delete();
      }
    }
  }

  static function deleted_post_object( $object_id ){
    self::deleted_object( 'post', $object_id );
  }

  static function deleted_comment_object( $object_id ){
    self::deleted_object( 'comment', $object_id );
  }

  static function deleted_user_object( $object_id ){
    self::deleted_object( 'user', $object_id );
  }

  static function deleted_subject( $subject_id ){
    global $wpdb;

    $sql = $wpdb->prepare(
      "SELECT reaction_id
       FROM $wpdb->reactions
       WHERE subject_id = %d ",
       $subject_id
    );
    $result = $wpdb->get_col( $sql );

    if( ! empty( $result ) ){
      foreach ( $result as $rid ) {
        $reaction = CEDRP_Reaction::get_instance( $rid );
        if( $reaction )
          $reaction->delete();
      }
    }
  }
}
