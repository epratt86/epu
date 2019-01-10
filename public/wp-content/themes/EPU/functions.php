<?php
// get_theme_file_path is a built in WP function to find custom directories/files. search-route is our custom API
require get_theme_file_path('/inc/search-route.php');
require get_theme_file_path('inc/like-route.php');

// add fields to response coming back 
function university_custom_rest() {
  // look at 'post' post type, call the field 'authorName', set it equal to 'get_the_author'.
  register_rest_field('post', 'authorName', array(
    'get_callback' => function() {return get_the_author();}
  ));
  //look at 'note' post type, call it 'userNoteCount, set it equal to the WP count_user_post function
  register_rest_field('note', 'userNoteCount', array(
    'get_callback' => function() {return count_user_posts(get_current_user_id(), 'note');}
  ));
}

add_action('rest_api_init', 'university_custom_rest');

//page banner @ top of most posts/pages
  //setting args = NULL keeps function from breaking if no arguments are passed in
function pageBanner($args = NULL) {
  
  if(!$args['title']) {
    $args['title'] = get_the_title();
  }

  if(!$args['subtitle']) {
    $args['subtitle'] = get_field('page_banner_subtitle');
  }

  if(!$args['photo']) {
    if (get_field('page_banner_background_image')) {
      $args['photo']  = get_field('page_banner_background_image')['sizes']['pageBanner'];
    } else {
      $args['photo']  = get_theme_file_uri('/images/ocean.jpg');
    }
  }

  ?>
  <div class="page-banner">
    <div class="page-banner__bg-image" style="background-image: url(<?php echo $args['photo'] ?>);"></div>
    <div class="page-banner__content container container--narrow">
      <h1 class="page-banner__title"><?php echo $args['title']?></h1>
      <div class="page-banner__intro">
        <p><?php echo $args['subtitle'] ?></p>
      </div>
    </div>  
  </div>

<?php }
//function to load in external files
function university_files() {
  // note for adding js files:
    // what you wanna call it, where the file's located, does it have dependencies, what version is it(not important), would you like it loaded at bottom
  wp_enqueue_script('googleMaps', '//maps.googleapis.com/maps/api/js?key=AIzaSyCBElVnRjmM6anYyiXvkxwaUdT2L3NvyAo', NULL, '1.0', true);
  wp_enqueue_script('university_main_scripts', get_theme_file_uri('/js/scripts-bundled.js'), NULL, microtime(), true);
  wp_enqueue_style('google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
  wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
  wp_enqueue_style('university_main_styles', get_stylesheet_uri(), NULL, microtime());
  // when scripts are loaded up, create a variable named 'universityData' that contains an array of desired information
  wp_localize_script('university_main_scripts', 'universityData', array(
    'root_url' => get_site_url(), 'nonce' => wp_create_nonce('wp_rest')
  ));
};

  add_action('wp_enqueue_scripts', 'university_files');

  function university_theme_support() {
    add_theme_support('title-tag');
    // post-thumbnails is another word for 'Featured Image'
    add_theme_support('post-thumbnails');
    add_image_size('professorLandscape', 400, 260, true);
    add_image_size('professorPortrait', 480, 650, true);
    add_image_size('pageBanner', 1500, 350, true);
  };

  add_action('after_setup_theme', 'university_theme_support');


  //adjust how many/what kind of events are coming back from db
  function university_adjust_queries($query) {
    // PROGRAM Q's
    // if you're not in the dashboard and you are looking at campus page
    if (!is_admin() AND is_post_type_archive('campus') AND $query->is_main_query()) {
      //if you're on the campus page, bring in all campuses from DB, not limiting to 10.
      $query->set('post_per_page', -1);
    }
    // not in dashboard and there are 'Program' post types in the DB
    if (!is_admin() AND is_post_type_archive('program') AND $query->is_main_query()) {
      $query->set('orderby', 'title');
      $query->set('order', 'ASC');
      $query->set('post_per_page', -1);
    }
    //EVENT Q's
    //dont apply changes to admin section, make sure you're in events section, dont adjust any custom queries
    if (!is_admin() AND is_post_type_archive('event') AND $query->is_main_query()) {
      //now we can set what we want to return from db
      $today = date('Ymd');
      $query->set('meta_key', 'event_date');
      $query->set('orderby', 'meta_value_num');
      $query->set('order', 'ASC');
      $query->set('meta_query', array(
        // only show us posts that are coming from 'event_date' that are greater than or equal to today (no old events)
        array(
          'key' => 'event_date',
          'compare' => '>=',
          'value' => $today,
          'type'  => 'numeric'
        )
        ));
    }
  }
  add_action('pre_get_posts', 'university_adjust_queries');


  //Add Google Maps API to custom field "Map Location" that lives in "Campuses"
  function universityMapKey($api) {
    $api['key'] = 'AIzaSyCBElVnRjmM6anYyiXvkxwaUdT2L3NvyAo';
    return $api;
  }
  add_filter('acf/fields/google_map/api', 'universityMapKey');

  // redirect subscriber accounts out of admin and into homepage
  add_action('admin_init', 'redirectSubsToFrontend');

  function redirectSubsToFrontend() {
    $ourCurrentUser = wp_get_current_user();
    // if the logged in user has 1 role AND it is 'subscriber' only show them homepage when they log in (dont take to backend)
    if (count($ourCurrentUser->roles) == 1 AND $ourCurrentUser->roles[0] =='subscriber') {
      wp_redirect(site_url('/'));
      exit;
    }
  }

  // if only given subscriber permission dont show them the admin bar up top
  add_action('wp_loaded', 'noSubsAdminBar');

  function noSubsAdminBar() {
    $ourCurrentUser = wp_get_current_user();
    // if the logged in user has 1 role AND it is 'subscriber' don't show top admin bar
    if (count($ourCurrentUser->roles) == 1 AND $ourCurrentUser->roles[0] =='subscriber') {
      show_admin_bar(false);
    }
  }

  // customize login screen
  add_filter('login_headerurl', 'ourHeaderUrl');

  function ourHeaderUrl() {
    return esc_url(site_url('/'));
  }

  add_action('login_enqueue_scripts', 'ourLoginCSS');

  function ourLoginCSS() {
    wp_enqueue_style('google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    wp_enqueue_style('university_main_styles', get_stylesheet_uri());
  }

  add_filter('login_headertitle', 'ourLoginTitle');

  function ourLoginTitle() {
    return get_bloginfo('name');
  }

  // Force Note Posts to be Private. '10' is default priority level. '2' means we are working with 2 parameters - data & postarr - default is 1
  add_filter('wp_insert_post_data', 'makeNotePrivate', 10, 2);

  function makeNotePrivate($data, $postarr) {
    // make sure we are only affecting 'notes'
    if ($data['post_type'] == 'note') {
      // if user already has more than 4 posts don't let them create more
      if (count_user_posts(get_current_user_id(), 'note') > 4 AND !$postarr['ID']) {
        die("You have reached your note limit.");
      }

      $data['post_content'] = sanitize_textarea_field($data['post_content']);
      $data['post_title'] = sanitize_text_field($data['post_title']);
    }

    if ($data['post_type'] == 'note' AND $data['post_status'] != 'trash') {
      $data['post_status'] = 'private';
    }
    
    return $data;
  }

?>