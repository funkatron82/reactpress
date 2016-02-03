<?php

function get_reaction( $reaction = NULL ){
  if ( $reaction instanceof CEDPR_Reaction ) {
    $_reaction = $reaction;
  } elseif ( is_object( $reaction ) ) {
    $_reaction = new CEDPR_Reaction( $reaction );
  } else {
    $_reaction = CEDPR_Reaction::get_instance( $reaction );
  }

  if ( ! $_reaction ) {
    return null;
  }

  return $_reaction;
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
