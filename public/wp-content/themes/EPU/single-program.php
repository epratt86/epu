<?php

  get_header();

  while(have_posts()) {
    the_post(); 
    pageBanner();
    ?>
    

  <div class="container container--narrow page-section">
    <!-- Link Box back to All Programs -->
    <div class="metabox metabox--position-up metabox--with-home-link">
      <p>
        <a class="metabox__blog-home-link" href="<?php echo get_post_type_archive_link('program'); ?>">
        <i class="fa fa-home" aria-hidden="true"></i>All Programs</a> 
        <span class="metabox__main"><?php the_title(); ?></span>
      </p>
    </div>
    
    <!-- Text coming back from DB -->
    <div class="generic-content">
      <?php the_field('main_body_content'); ?>
    </div>

    <?php 
//Custom Query to to associate professors to programs 
      $relatedProfessors = new WP_Query(array(
            'posts_per_page' => -1,
            'post_type' => 'professor',
            'orderby'   => 'title',
            'order'     => 'ASC',
            'meta_query'  => array(
              //connect professor to 'related_programs'
              array(
                'key' => 'related_programs',
                'compare' => 'LIKE',
                'value'   => '"' . get_the_ID() . '"'
              )
            )

          ));

          if ($relatedProfessors->have_posts()) {
            echo '<hr class="section-break">';
            echo '<h2 class="headline headline--medium">' . get_the_title() . ' Professors</h2>';
          
            // look in the newly created array of events. while events exists, loop through each and display to DOM 
            echo '<ul class="professor-cards">';
            while ($relatedProfessors->have_posts()) {
            $relatedProfessors->the_post(); ?>
            
            <li class="professor-card__list-item">
              <a class="professor-card" href="<?php the_permalink(); ?>">
                <img class="professor-card__image"src="<?php the_post_thumbnail_url('professorLandscap'); ?>">
                <span class="professor-card__name"><?php the_title(); ?></span>
              </a>
            </li>

          <?php }
          echo '</ul>';
          }

          wp_reset_postdata();
// Custom Query to associate Events to the Program
          $today = date('Ymd');
          $homepageEvents = new WP_Query(array(
            //show us 2 events from Custom Fields 'event_date' in ascending order
            'posts_per_page' => 2,
            'post_type' => 'event',
            'meta_key'  => 'event_date',
            'orderby'   => 'meta_value_num',
            'order'     => 'ASC',
            'meta_query'  => array(
              // only show us posts that are coming from 'event_date' that are greater than or equal to today (no old events)
              array(
                'key' => 'event_date',
                'compare' => '>=',
                'value' => $today,
                'type'  => 'numeric'
              ),
              //connect events coming in to 'related_programs'
              array(
                'key' => 'related_programs',
                'compare' => 'LIKE',
                'value'   => '"' . get_the_ID() . '"'
              )
            )

          ));

          if ($homepageEvents->have_posts()) {
            echo '<hr class="section-break">';
          echo '<h2 class="headline headline--medium">Upcoming ' . get_the_title() . ' Events</h2>';
        
          // look in the newly created array of events. while events exists, loop through each and display to DOM 
           while ($homepageEvents->have_posts()) {
            $homepageEvents->the_post();
            get_template_part('template-parts/content', 'event');
            }
          }
          wp_reset_postdata();

          // display any related campuses to the program being taught
          $relatedCampuses = get_field('related_campus');
          if ($relatedCampuses) {
            echo '<hr class="section-break">';
            echo '<h2 class="headline headline--medium">' . get_the_title() . ' is Available At These Campuses:</h2>';

            echo '<ul class="min-list link-list">';
            foreach($relatedCampuses as $campus) {
              ?><li><a href="<?php echo get_the_permalink($campus); ?>"><?php echo get_the_title($campus); ?></a></li><?php
            }
            echo '</ul>';
          }

        ?>

  </div>
    
  <?php }

  get_footer();

?>