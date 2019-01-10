<?php 
get_header(); 
pageBanner(array(
  'title' => 'Past Events',
  'subtitle'  => 'See what you might have missed out on.'
));
?>

  <div class="container container--narrow page-section">
    <?php 
      $today = date('Ymd');
      $pastEvents = new WP_Query(array(
        //show us events from Custom Fields 'event_date' in ascending order
        'paged'     => get_query_var('paged', 1),
        'post_type' => 'event',
        'meta_key'  => 'event_date',
        'orderby'   => 'meta_value_num',
        'order'     => 'ASC',
        'meta_query'  => array(
          // only show us posts that are coming from 'event_date' that are less than today (past events)
          array(
            'key' => 'event_date',
            'compare' => '<',
            'value' => $today,
            'type'  => 'numeric'
          )
        )
      ));

      while($pastEvents->have_posts()) {
        $pastEvents->the_post();
        get_template_part('template-parts/content', 'event');
      }
      echo paginate_links(array(
        'total' => $pastEvents->max_num_pages
      ));
    ?>
  </div>

<?php get_footer(); ?>