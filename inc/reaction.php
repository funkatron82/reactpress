<?php

class CEDPR_Reaction{
  public static $reaction_types = array();
  public $reaction_id = 0;
  public $reaction_type = '';
  public $reaction_date = '0000-00-00 00:00:00';
  public $reaction_modified = '0000-00-00 00:00:00';
  public $reaction_weight = 1;
  public $reaction_to = 0;
  public $reaction_from = 0;

  /**
   * Constructor.
   *
   * @param CEDPR_Reaction|object $reaction  Reaction object.
   */
  public function __construct( $reaction ) {
    foreach ( get_object_vars( $reaction ) as $key => $value )
      $this->$key = $value;

    $this->reaction_type_object = self::$reaction_types[ $this->reaction_type ];
  }

  public function update( $weight ) {
    global $wpdb;
    $weight = in_array( $weight, $this->reaction_type_object['to_types'] ) ? $weight : $this->reaction_type_object['default_weight'];
    $modified = current_time( 'mysql' );
    $wpdb->update( $wpdb->reactions, array( 'reaction_weight' => $weight, 'reaction_modified' => $modified ), array( 'reaction_id' => $this->reaction_id ), array( '%d', '%s' ), array( '%d' ) );
  }

  public function delete(){
    global $wpdb;

    //Delete cache
    wp_cache_delete( $this->reaction_id, 'post_reactions' );

    //Delete meta
    $reaction_meta_ids = $wpdb->get_col( $wpdb->prepare( "SELECT meta_id FROM $wpdb->reactionmeta WHERE reaction_id = %d ", $$this->reaction_id ) );
    foreach ( $reaction_meta_ids as $mid )
      delete_metadata_by_mid( 'reaction', $mid );

    //Delete db row
    if( ! $result = $wpdb->delete( $wpdb->reactions, array( 'reaction_id' => $this->reaction_id ) ) )
      return false;

    return true;
  }

  public static function register_reaction_type( $reaction_type, $args = array() ) {
    $reaction_type = sanitize_key( $reaction_type );
    if( ! $reaction_type )
      return false;

    $args = wp_parse_args( $args, array(
      'to_types'       => array( 'post' ),
      'weight_options' => attay( 1 ),
      'default_weight' => 1,
      'labels'         => array()
    ) );

    if( empty( $args['to_types'] ) )
      return false;

    $args['default_weight'] =  empty( $args['default_weight'] ) || in_array( $args['default_weight'], $args['weight_options'] ) ? 1 : $args['default_weight'];

    $args['labels'] = wp_parse_args( $args['labels'], array(
      'name' => _x( 'Reactions', 'reaction general name' ),
      'singularname' => _x( 'Reaction', 'reaction general singular name' ),
    ) );

    $args['name'] = $reaction_type;

    self::$reaction_types[$reaction_type] = $args;
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

      wp_cache_set( $reaction_id, 'reactions' );
    }
    return new CEDPR_Reaction( $_reaction );
  }
}
