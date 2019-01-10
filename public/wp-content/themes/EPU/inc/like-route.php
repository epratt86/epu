<?php

add_action('rest_api_init', 'universityLikeRoutes');

function universityLikeRoutes() {
  // new rest api url
  register_rest_route('university/v1', 'manageLike', array(
    'methods' => 'POST',
    'callback' => 'createLike'
  ));
  // same url, different method
  register_rest_route('university/v1', 'manageLike', array(
    'methods' => 'DELETE',
    'callback' => 'deleteLike'
  ));
}

// $data is whats coming back from Like.js
function createLike($data) {

  // lets check to see if user is logged in so user can 'like' professor
  // always use NONCE to check if user is who user says they are. NONCE is coming from Like.js
  if (is_user_logged_in()) {
    // always good practice to santize anything going into our DB
    $professor = sanitize_text_field($data['professorId']);

    $existQuery = new WP_Query(array(
      'author' => get_current_user_id(),
      'post_type' => 'like',
      // meta query asks as a filter to get the parts of the post type you are looking for
      'meta_query' => array(
        array(
          // key is the ACF
          'key' => 'liked_professor_id',
          // compare exact matches
          'compare' => '=',
          // compare the ACF to the ID 
          'value' => $professor
        )
      )
    ));

    //if 'like' does not already exist and its definitely a professor post
    if ($existQuery->found_posts == 0 AND get_post_type($professor) == 'professor') {
      //create new like post
      return wp_insert_post(array(
        'post_type' => 'like',
        'post_status' => 'publish',
        'post_title' => 'PHP TEST',
        'meta_input' => array(
          'liked_professor_id' => $professor
        )
      ));
    } else {
      die('Invalid professor id');
    }

    
  } else {
    die("You must be logged in to do that.");
  }
  
}

function deleteLike($data) {
  $likeId = sanitize_text_field($data['like']);
  if (get_current_user_id() == get_post_field('post_author', $likeId) AND get_post_type($likeId) == 'like') {
    wp_delete_post($likeId, true);
    return 'Congrats, like deleted.';
  } else {
    die('You do not have permission to delete that.');
  }
}