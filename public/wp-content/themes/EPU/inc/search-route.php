<?php

  // custom REST API to pull in all the search information about the university

  /* add_action is a WP function to load in specific files or run custom functions when called upon.
   the first arg is the hook. What do you want to hook the new function to? (naming matters)
   second arg is the name of the function you want to run (call it what you want)
  */
  add_action('rest_api_init', 'universityRegisterSearch');

  function universityRegisterSearch() {
    // register_rest_route is a WP function for building slug
    // this slug will be -> epu.uni/wp-json/university/v1/search
    register_rest_route('university/v1', 'search', array(
      // methods = CRUD. what type of request? WP_REST_SERVER::READABLE is a WP function for saying "i want a 'GET' request". You could also just write 'GET'
      'methods' =>  WP_REST_SERVER::READABLE,
      // function call
      'callback' => 'universitySearchResults'
    ));
  }
// function for callback above
  function universitySearchResults($data) {
    // start off by grabbing all information about post type in the WP database
    $mainQuery = new WP_Query(array(
      // where does the information you're looking for live?
      'post_type' => array('post', 'page', 'professor', 'campus', 'program', 'event'),
      //this is coming back from the search term being entered on UI
      's' => sanitize_text_field($data['term'])
    ));
    // set the results you want to an associative array. Values will be stored as $results.professors etc.
    $results = array(
      'generalInfo' => array(),
      'professors' => array(),
      'programs' => array(),
      'events' => array(),
      'campuses' => array()
    );

    //filter through all data from WP_Query and cherry pick the stuff you need.
    while($mainQuery->have_posts()) {
      // each post inside results collection
      $mainQuery->the_post();
      //if its a post or a page type put it in the generalInfo array
      if(get_post_type() == 'post' OR get_post_type() == 'page') {
        //push the new information onto the results['generalInfo'] array
        array_push($results['generalInfo'], array(
          //what is it we are looking for from that page type?
          'title' => get_the_title(),
          'permalink' => get_the_permalink(),
          'postType' => get_post_type(),
          'authorName' => get_the_author()
        ));
      }

      if(get_post_type() == 'professor') {
        array_push($results['professors'], array(
          'title' => get_the_title(),
          'permalink' => get_the_permalink(),
          'image' => get_the_post_thumbnail_url(get_the_ID(), 'professorLandscape')
        ));
      }

      if(get_post_type() == 'program') {
        // check to see if there are any related campuses to the program being searched
        $relatedCampuses = get_field('related_campus');
        // if there has been a designated relationship...
        if($relatedCampuses) {
          // what do you wanna loop through? what are you gonna call each one?
          foreach($relatedCampuses as $campus) {
            //push each campus onto the $results['campuses'] array
            array_push($results['campuses'], array(
              'title' => get_the_title($campus),
              'permalink' => get_the_permalink($campus)
            ));
          }
        }
        array_push($results['programs'], array(
          'title' => get_the_title(),
          'permalink' => get_the_permalink(),
          'id'  => get_the_ID()
        ));
      }

      if(get_post_type() == 'event') {
        $eventDate = new DateTime(get_field('event_date', false, false));
        // start off by setting description to NULL then update the value whether or not it has a custom excerpt filled out.
        $description = NULL;
        if (has_excerpt()) {
          $description = get_the_excerpt();
        } else {
          $description = wp_trim_words(get_the_content(), 18);
        }
        array_push($results['events'], array(
          'title' => get_the_title(),
          'permalink' => get_the_permalink(),
          'month' => $eventDate->format('M'),
          'day' => $eventDate->format('d'),
          'description' => $description
        ));
      }

      if(get_post_type() == 'campus') {
        array_push($results['campuses'], array(
          'title' => get_the_title(),
          'permalink' => get_the_permalink()
        ));
      }

    }

    // building a relationship between professors and programs below.
    //only if $results['programs'] exists, do this
    if($results['programs']) {
      // an array to be passed down into the programRelationshipQuery below
      $programsMetaQuery = array('relation' => 'OR');

      // what array do you want to look in? what do you wanna call each var
      foreach($results['programs'] as $item) {
        //add each item onto new array above, pick off the parts needed from ACF
        array_push($programsMetaQuery, array(
          // what ACF do you want to look in
          'key' => 'related_programs',
          'compare' => 'LIKE',
          'value' => '"' . $item['id'] . '"'
        ));
      }
      // second query to keep relationship between events/campuses/professors/etc...
      $programRelationshipQuery = new WP_Query(array(
        'post_type' => array('professor', 'event'),
        'meta_query' => $programsMetaQuery
      ));

      while($programRelationshipQuery->have_posts()) {
        $programRelationshipQuery->the_post();

        if(get_post_type() == 'professor') {
          array_push($results['professors'], array(
            'title' => get_the_title(),
            'permalink' => get_the_permalink(),
            'image' => get_the_post_thumbnail_url(0, 'professorLandscape')
          ));
        }

        if(get_post_type() == 'event') {
          $eventDate = new DateTime(get_field('event_date', false, false));
          // start off by setting description to NULL then update the value whether or not it has a custom excerpt filled out.
          $description = NULL;
          if (has_excerpt()) {
            $description = get_the_excerpt();
          } else {
            $description = wp_trim_words(get_the_content(), 18);
          }
          array_push($results['events'], array(
            'title' => get_the_title(),
            'permalink' => get_the_permalink(),
            'month' => $eventDate->format('M'),
            'day' => $eventDate->format('d'),
            'description' => $description
          ));
        }

      }

      //keep from returning duplicate results
      $results['professors'] = array_values(array_unique($results['professors'], SORT_REGULAR));
      $results['events'] = array_values(array_unique($results['events'], SORT_REGULAR));
    }

    return $results;
  }