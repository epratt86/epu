<?php

  // if user isn't logged in don't show them this page
  if (!is_user_logged_in()) {
    wp_redirect(esc_url(site_url('/')));
    exit;
  }

  get_header();

  while(have_posts()) {
    the_post();
    pageBanner();
    ?>
    

  <div class="container container--narrow page-section">
    <!-- Create New Note -->
    <div class="create-note">
      <h2 class="headline headline--medium">Create New Note</h2>
      <input class="new-note-title" placeholder="Title">
      <textarea class="new-note-body" placeholder="Your note here..."></textarea>
      <span class="submit-note">Create Note</span>
      <span class="note-limit-message">Note limit reached! Delete an existing note to make room for a new note.</span>
    </div>

    <!-- Notes coming back from WP Database -->
    <ul class="min-list link-list" id="my-notes">
      <!-- Query WP Database and ask for all the notes from logged in user -->
      <?php 
        $userNotes = new WP_Query(array(
          'post_type' => 'note',
          // show all notes
          'posts_per_page' => -1,
          // only show posts for logged in user
          'author' => get_current_user_id()
        ));

        // take userNotes query and loop through each one as long as posts exist
        while($userNotes->have_posts()) {
          // each post in collection
          $userNotes->the_post(); ?>
          <!-- for each post in collection, format into this HTML.
               data-id will receive note ID from WP DB to send off and use in MyNotes.js for CRUD-->
          <li data-id="<?php the_ID(); ?>">
            <!-- esc_attr when pulling from WP DB -->
            <input readonly class="note-title-field" type="text" value="<?php echo str_replace('Private: ', '', esc_attr(get_the_title()) ); ?>">
            <span class="edit-note"><i class="fa fa-pencil" aria-hidden="true"></i>Edit</span>
            <span class="delete-note"><i class="fa fa-trash-o" aria-hidden="true"></i>Delete</span>
            <textarea readonly class="note-body-field"><?php echo esc_textarea(get_the_content()); ?></textarea>
            <span class="update-note btn btn--blue btn--small"><i class="fa fa-arrow-right" aria-hidden="true"></i>Save</span>
          </li>

        <?php }
      ?>
    </ul>
  </div>
    
  <?php }

  get_footer();

?>