<?php

class CEDRP_Reaction_Type {
  public static $reaction_types = array();
  public $name;
  public $object_options = array();
  public $weight_options = array();
  public $labels = array();

  function __construct( $name, $args = array() ) {
    $name = sanitize_key( $name );
    if( ! $name )
      return false;

    $this->name = $name;
    $args = wp_parse_args( $args, array(
      'object_options' => array(),
      'weight_options' => array(),
      'labels'         => array(),
    ) );

    //Object options
    $this->object_options = wp_parse_args( $args['object_options'], array(
      'type'    => 'post',
      'subtype' => array()
    ) );

    $this->object_options['type'] = in_array( $this->object_options['type'], array( 'post', 'user', 'comment' ) ) ? $this->object_options['type'] : 'post';

    //Weight options
    $this->weight_options = wp_parse_args( $args['weight_options'], array(
      'type'    => 'standard',
      'default' => 1,
      'min'     => 1,
      'max'     => 5
    ) );
    $this->weight_options['type']    = in_array( $this->weight_options['type'], array( 'standard', 'vote', 'rating' ) ) ? $this->weight_options['type'] : 'standard';
    $this->weight_options['default'] = (int) $this->weight_options['default'];
    $this->weight_options['min']     = (int) $this->weight_options['min'] > 0 ? (int) $this->weight_options['min'] :   $this->weight_options['default'];
    $this->weight_options['max']     = (int) $this->weight_options['max'] > $this->weight_options['min']  ? (int) $this->weight_options['max'] : $this->weight_options['min'] + 5;

    //labels
    $this->labels = wp_parse_args( $args['labels'], array(
      'name'          => _x( 'Reactions', 'reaction general name', 'cedrp' ),
      'singular_name' => _x( 'Reaction', 'reaction general singular name', 'cedrp' ),
      'present'       => __( 'react', 'cedrp' ),
      'past'          => __( 'reacted', 'cedrp' ),
      'continuous'    => __( 'reacting', 'cedrp' ),
    ) );
  }

  function validate_weight( $weight ) {
    switch ( $this->weight_options['type'] ) {
      case 'vote':
        return in_array( (int) $weight, array( -1, 1 ) ) ? (int) $weight : 1;
        break;
      case 'rating':
        return filter_var( $weight, FILTER_VALIDATE_INT, array(
          'default'   => $this->$weight_options['default'],
          'min_range' => $this->$weight_options['min'],
          'max_range' => $this->$weight_options['max']
        ));
        break;
      case 'standard':
      default:
        return $this->weight_options['default'];
    }
  }

  function create_reaction( $object_id, $subject_id, $weight = null ) {
    global $wpdb;
    $object  = $this->get_object( (int) $object_id );
    $subject = get_userdata( (int) $subject_id );

    if( ! $object || ! $subject )
      return false;

    $weight = $this->validate_weight( $weight );
    $result = $wpdb->insert(
      $wpdb->reactions,
      array(
        'object_id' => $object_id,
        'subject_id' => $subject_id,
        'reaction_weight' => $weight ),
      array( '%d', '%d', '%d' ) );

    if( $result ) {
      return CEDRP_Reaction::get_instance( $wpdb->insert_id );
    }

    return false;
  }

  function get_object_reactions( $object ) {
    global $wpdb;
    if ( ! $object = $this->get_object_id( $object ) )
      return false;

    $reactions = array();

    $sql = $wpdb->prepare(
      "SELECT reaction_id
       FROM $wpdb->reactions
       WHERE object_id = %d
       AND reaction_type  = %s",
       $object,
       $this->name
    );

    $results = $wpdb->get_col( $sql );

    if( ! empty( $results ) ){
      foreach ( $results as $rid ){
        if ( $reaction = CEDRP_Reaction::get_instance( $rid ) )
          $reactions[] = $reaction;
      }
    }

    return $reactions;
  }

  function get_subject_reactions ( $subject ) {
    global $wpdb;
    if ( ! subject = get_userdata( $subject ) )
      return false;
    $subject = $subject->ID;

    $reactions = array();

    $sql = $wpdb->prepare(
      "SELECT reaction_id
       FROM $wpdb->reactions
       WHERE subject_id = %d
       AND reaction_type  = %s",
       $subject,
       $this->name
    );

    $results = $wpdb->get_col( $sql );

    if( ! empty( $results ) ){
      foreach ( $results as $rid ){
        if ( $reaction = CEDRP_Reaction::get_instance( $rid ) )
          $reactions[] = $reaction;
      }
    }

    return $reactions;
  }

  function get_object( $object_id ) {
    switch ( $this->object_options['type'] ) {
      case 'user':
        return get_userdata( $object_id );
        break;
      case 'comment':
        return get_comment( $object_id );
        break;
      case 'post':
      default:
        return get_post( $object_id );
    }
  }

  function get_object_id( $object ) {
    if ( ! $object = $this->get_object( $object ) )
      return false;

      switch ( $this->object_options['type'] ) {
        case 'comment':
          return $object->comment_ID;
          break;
        case 'post':
        case 'user':
        default:
          return $object->ID;
      }
  }

  static function register( $name, $args ) {
    self::$reaction_types[ $name ] = new CEDRP_Reaction_Type( $name, $args );
  }

  static get_instance( $name ) {
    return isset( self::$reaction_types[ $name ] ) ? self::$reaction_types[ $name ] : false;
  }
}
