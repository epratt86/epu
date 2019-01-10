<?php 
  get_header();
  pageBanner(array(
    'title' => 'Search Results',
    'subtitle'  => 'You searched for &ldquo;' . esc_html(get_search_query(false)) . '&rdquo;'
  ));
  ?>

  <div class="container container--narrow page-section">
  <?php 

    if(have_posts()) {
      while(have_posts()) {
        the_post();
        //look inside template-parts. files will look like: template-parts/content-professor.php
        get_template_part('template-parts/content', get_post_type());
      }
      echo paginate_links();
    } else {
      echo '<h2 class="headline headline--small-plus">No results match that search.</h2>';
    }

    get_search_form();
  ?>
  </div>

<?php
  get_footer();
?>