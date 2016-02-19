<?php

function get_reaction_by_id( $reaction = NULL ){
  if ( $reaction instanceof CEDRP_Reaction ) {
    $_reaction = $reaction;
  } elseif ( is_object( $reaction ) ) {
    $_reaction = new CEDRP_Reaction( $reaction );
  } else {
    $_reaction = CEDRP_Reaction::get_instance( $reaction );
  }

  return $_reaction ? $_reaction : null;
}

function get_reaction( $reaction_type, $object_id, $subject_id ) {
  $type = get_reaction_type( $reaction_type );
  return $type->get_reaction( $object_id, $subject_id );
}

function get_reaction_type( $reaction_type ) {
  return CEDRP_Reaction_Type::get_instance( $reaction_type );
}

function get_object_reactions( $reaction_type, $object_id ) {
  $type = get_reaction_type( $reaction_type );
  return $type->get_object_reactions( $object_id );
}

function get_object_reaction_subjects( $reaction_type, $object_id ) {
  $reactions = get_object_reactions( $reaction_type, $object_id );
  $subject_ids = wp_list_pluck( $reactions, 'subject_id' );
  return get_users( array( 'include' => $subject_ids ) );
}

function react( $reaction_type, $object_id, $subject_id, $weight = null ) {
  $type = get_reaction_type( $reaction_type );
  return $type->react( $object_id, $subject_id, $weight );
}

function delete_reaction( $reaction_type, $object_id, $subject_id ) {
  $type = get_reaction_type( $reaction_type );
  return $type->delete_reaction( $object_id );
}

function delete_reaction_by_id( $reaction_id ) {
  if( $reaction = get_reaction( $reaction_id ) )
    return $reaction->delete();

  return false;
}

function update_reaction( $reaction_type, $object_id, $subject_id, $weight ) {
  if ( $reaction = get_reaction( $reaction_type, $object_id, $subject_id ) ) {
    $reaction->update( $weight );
  }
}

/**
 * Meta
 */
function add_reaction_meta( $reaction_id, $key, $value, $unique = false ) {
  return add_metadata( 'reaction', $reaction_id, $key, $value, $unique );
}

function delete_reaction_meta( $reaction_id, $key, $value = '' ) {
  return delete_metadata( 'reaction', $reaction_id, $key, $value );
}

function get_reaction_meta( $reaction_id, $key = '', $single ) {
  return get_metadata( 'reaction', $reaction_id, $key, $single );
}

function update_reaction_meta( $reaction_id, $key, $value, $prev ) {
  return update_metadata( 'reaction', $reaction_id, $key, $value, $prev );
}
