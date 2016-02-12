<?php

function get_reaction( $reaction = NULL ){
  if ( $reaction instanceof CEDRP_Reaction ) {
    $_reaction = $reaction;
  } elseif ( is_object( $reaction ) ) {
    $_reaction = new CEDRP_Reaction( $reaction );
  } else {
    $_reaction = CEDRP_Reaction::get_instance( $reaction );
  }

  return $_reaction ? $_reaction : null;
}


function get_reaction_type_object( $reaction_type ) {
  return CEDRP_Reaction_Type::get_instance( $reaction_type );
}

function get_object_reactions( $reaction_type, $object_id ) {
  $type = get_reaction_type_object( $reaction_type );
  return $type->get_object_reactions( $object_id );
}

function react( $reaction_type, $object_id, $subject_id, $weight = null ) {
  $type = get_reaction_type_object( $reaction_type );
  $type->react( $object_id, $subject_id, $weight );
}

function delete_reaction( $reaction_type, $object_id, $subject_id ) {
  $type = get_reaction_type_object( $reaction_type );
  return $type->delete_reaction( $object_id, $subject_id );
}

function delete_reaction_by_id( $reaction_id ) {
  $reaction = get_reaction( $reaction_id );
  if( $reaction )
    $reaction->delete();
}

function get_reaction_id( $reaction_type, $object_id, $subject_id ) {
  $type = get_reaction_type_object( $reaction_type );
  return $type->get_reaction_id( $object_id, $subject_id );
}

function update_reaction( $reaction_type, $object_id, $subject_id, $weight ) {
  $reaction_id = get_reaction_id( $reaction_type, $object_id, $subject_id );
  if ( $reaction = get_reaction( $reaction_id ) ) {
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
