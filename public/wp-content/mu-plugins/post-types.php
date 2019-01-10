<?php
// Add Different Post Types to Admin sidebar
  function university_post_types() {
    // Campuses
    register_post_type('campus', array(
      // look at 'event' for description
      'capability_type' => 'campus',
      'map_meta_cap'  => true,
      'supports' => array('title', 'editor', 'excerpt'),
      'rewrite' => array('slug' => 'campuses'),
      'public' => true,
      'has_archive' => true,
      'labels' => array(
        'name' => 'Campuses',
        'add_new_item'  => 'Add New Campus',
        'edit_item' => 'Edit Campus',
        'all_items' => 'All Campuses',
        'singular_name' => 'Campus'
      ),
      'menu_icon' => 'dashicons-location-alt'
    ));
    // Events
    register_post_type('event', array(
      // capability_type is setting up event coordinator in admin instead of giving ability to edit all post types (default behavior if unspecified)
      'capability_type' => 'event',
      // map_meta_cap is asking WP to require a admin to be explicitly given permission to edit events
      'map_meta_cap' => true,
      'supports' => array('title', 'editor', 'excerpt'),
      'rewrite' => array('slug' => 'events'),
      'public' => true,
      'has_archive' => true,
      'labels' => array(
        'name' => 'Events',
        'add_new_item'  => 'Add New Event',
        'edit_item' => 'Edit Event',
        'all_items' => 'All Events',
        'singular_name' => 'Event'
      ),
      'menu_icon' => 'dashicons-calendar'
    ));

    // Programs
    register_post_type('program', array(
      'supports' => array('title'),
      'rewrite' => array('slug' => 'programs'),
      'public' => true,
      'has_archive' => true,
      'labels' => array(
        'name' => 'Programs',
        'add_new_item'  => 'Add New Program',
        'edit_item' => 'Edit Program',
        'all_items' => 'All Programs',
        'singular_name' => 'Program'
      ),
      'menu_icon' => 'dashicons-awards'
    ));

    // Professor
    register_post_type('professor', array(
      // show_in_rest allows us to work with custom post type with our REST API
     // example: http://epu.uni/wp-json/wp/v2/professor
      'show_in_rest'  => true,
      //'thumbnail' gives ability to add "Featured Image" to custom post type
      'supports' => array('title', 'editor', 'thumbnail'),
      'public' => true,
      'labels' => array(
        'name' => 'Professors',
        'add_new_item'  => 'Add New Professor',
        'edit_item' => 'Edit Professor',
        'all_items' => 'All Professors',
        'singular_name' => 'Professor'
      ),
      'menu_icon' => 'dashicons-welcome-learn-more'
    ));

    // Notes
    register_post_type('note', array(
      // this line creates a new permission type
      'capability_type' => 'note',
      //map meta cap enforces the above permision at the right time
      'map_meta_cap' => true,
      'show_in_rest' => true,
      'supports' => array('title', 'editor'),
      // public -> false means keep this private to user
      'public' => false,
      //show_ui means show in admin dashboard. explicitly say 'true' when public is false
      'show_ui' => true,
      'labels' => array(
        'name' => 'Notes',
        'add_new_item'  => 'Add New Note',
        'edit_item' => 'Edit Note',
        'all_items' => 'All Notes',
        'singular_name' => 'Note'
      ),
      'menu_icon' => 'dashicons-welcome-write-blog'
    ));

    // 'Like' post type for hearting professors
    register_post_type('like', array(
      'supports' => array('title'),
      // public -> false means keep this private to user
      'public' => false,
      //show_ui means show in admin dashboard. explicitly say 'true' when public is false
      'show_ui' => true,
      'labels' => array(
        'name' => 'Likes',
        'add_new_item'  => 'Add New Like',
        'edit_item' => 'Edit Like',
        'all_items' => 'All Likes',
        'singular_name' => 'Like'
      ),
      'menu_icon' => 'dashicons-heart'
    ));
  };

  add_action('init', 'university_post_types');