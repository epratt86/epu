<?php

  get_header();

  while(have_posts()) {
    the_post(); 
    pageBanner();
    ?>
    
  

  <div class="container container--narrow page-section">

    <div class="generic-content">
      <div class="row group">
        <!-- left side -->
        <div class="one-third">
          <?php the_post_thumbnail('professorLandscape'); ?>
        </div>
        <!-- right side -->
        <div class="two-thirds">
          <!-- 'like' box logic -->
          <?php 
          // check how many likes a professor has
          $likeCount = new WP_Query(array(
            'post_type' => 'like',
            // meta query asks as a filter to get the parts of the post type you are looking for
            'meta_query' => array(
              array(
                // key is the ACF
                'key' => 'liked_professor_id',
                // compare exact matches
                'compare' => '=',
                // compare the ACF to the ID 
                'value' => get_the_ID()
              )
            )
          ));
          //check if user has liked professor (make heart solid)
          // start with status as 'no'. a value of 'yes' will trigger CSS
          $existStatus = 'no';

          if (is_user_logged_in()) {
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
                  'value' => get_the_ID()
                )
              )
            ));
            // if a post exists inside of existQuery change status to yes.
            if ($existQuery->found_posts) {
              $existStatus = 'yes';
            }
          }

          
          ?>
          <!-- 'like' box html -->
          <span class="like-box" data-professor="<?php the_ID(); ?>" data-like="<?php echo $existQuery->posts[0]->ID; ?>" data-exists="<?php echo $existStatus; ?>">
            <i class="fa fa-heart-o" aria-hidden="true"></i>
            <i class="fa fa-heart" aria-hidden="true"></i>
            <span class="like-count"><?php echo $likeCount->found_posts; ?></span>
          </span>
          <!-- text content -->
          <?php the_content(); ?>
        </div>
      </div>
    </div>
    
    <!-- Add relationship between event and program type -->
    <?php 
    // get_field is an ACF function that returns value of specific field
      $relatedPrograms = get_field('related_programs');

      if ($relatedPrograms) {
        echo '<hr class="section-break">';
        echo '<h2 class="headline headline--medium">Subject(s) taught</h2>';
        echo '<ul class="link-list min-list">';
        foreach($relatedPrograms as $program) { ?>
          <li><a href="<?php echo get_the_permalink($program) ?>"><?php echo get_the_title($program); ?></a></li>
        <?php }
        echo '</ul>';
      }
      
    ?>
  </div>
    
  <?php }

  get_footer();

?>