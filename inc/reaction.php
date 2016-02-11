<?php

class CEDRP_Reaction{
  public $reaction_id = 0;
  public $reaction_type = '';
  public $reaction_date = '0000-00-00 00:00:00';
  public $reaction_modified = '0000-00-00 00:00:00';
  public $reaction_weight = 1;
  public $object_id = 0;
  public $subject_id = 0;

  /**
   * Constructor.
   *
   * @param CEDRP_Reaction|object $reaction  Reaction object.
   */
  public function __construct( $reaction ) {
    foreach ( get_object_vars( $reaction ) as $key => $value )
      $this->$key = $value;

    $this->reaction_type = CEDRP_Reaction_Type::get_instance( $this->reaction_type );
  }

  public function update( $weight = 1 ) {
    global $wpdb;
    $weight = $this->reaction_type->validate_weight( $weight );
    $modified = current_time( 'mysql' );
    $wpdb->update( $wpdb->reactions,
      array( 'reaction_weight' => $weight, 'reaction_modified' => $modified ),
      array( 'reaction_id' => $this->reaction_id ),
      array( '%d', '%s' ),
      array( '%d' ) );
  }

  public function delete(){
    global $wpdb;

    //Delete cache
    wp_cache_delete( $this->reaction_id, 'post_reactions' );

    //Delete meta
    $reaction_meta_ids = $wpdb->get_col( $wpdb->prepare( "SELECT meta_id FROM $wpdb->reactionmeta WHERE reaction_id = %d ", $this->reaction_id ) );
    foreach ( $reaction_meta_ids as $mid )
      delete_metadata_by_mid( 'reaction', $mid );

    //Delete db row
    return (bool) $wpdb->delete( $wpdb->reactions, array( 'reaction_id' => $this->reaction_id ) );
  }

  public static function get_instance( $reaction_id ) {
    global $wpdb;
    $reaction_id = (int) $reaction_id;

    if( ! $reaction_id ) {
      return false;
    }

    $_reaction = wp_cache_get( $reaction_id, 'reactions' );

    if( ! $_reaction ) {
      $_reaction = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->reactions WHERE reaction_id = %d", $reaction_id ) );

      if( ! $_reaction )
        return false;

      wp_cache_set( $reaction_id, $_reaction, 'reactions' );
    }
    return new CEDRP_Reaction( $_reaction );
  }
}
