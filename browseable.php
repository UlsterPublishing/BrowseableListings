<?php
/*

Plugin Name: Browseable
Description: Shows a ton of events on each page for those afraid to search

/**
 * Initialize and run setup options on after_theme_setup hook *
 */
 function do_easy_event_list() {
 	// Safety first! Bail in the event TEC is inactive/not loaded yet
 	if ( ! class_exists( 'Tribe__Events__Main' ) )
 		return;

 	// Has the user paged forward, ie are they on /page-slug/page/2/?
 	$paged = get_query_var( 'paged' )
 		? get_query_var( 'paged' )
 		: 1;
     $date = strtotime("+7 day");

 	// Build our query, adopt the default number of events to show per page
 	$upcoming = new WP_Query( array(
 		'post_type' => Tribe__Events__Main::POSTTYPE,
 		'paged'     => $paged,
     'posts_per_page'=> 100,
     'meta_key' => '_EventStartDate',
               'meta_query' => array(
                   array(
                       'key' => '_EventStartDate',
                      // 'value' => array((date('Y-m-d') . ' 00:00:00'),(date('Y-m-d', $date) . ' 00:00:00')),
                       'value' => date('Y-m-d'),date('Y-m-d', $date),
                       'compare' => 'BETWEEN'
       ))
 	) );


  //<?php echo tribe_get_start_date( $upcoming->ID, false, $format = 'D M j');

 	// If we got some results, let's list 'em
 	while ( $upcoming->have_posts() ) {
 		$upcoming->the_post();
     echo tribe_get_start_date( $upcoming->ID, false, $format = 'm d'); ?>

    <p><?php echo tribe_get_start_date( $upcoming->ID, false, $format = 'g:i a') . "-" . tribe_get_end_date( $upcoming->ID, false, $format = 'g:i a'); ?>
      <strong><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></strong> <?php the_content_rss(0, 45);?></p>

     <?php

 	}
 	// Is Pagenavi activated? Let's use it for pagination if so
 	if ( function_exists( 'wp_pagenavi' ) )
 		wp_pagenavi( array( 'query' => $upcoming ) );

 	// Clean up
 	wp_reset_query();
 }
 // Create a new shortcode to list upcoming events, optionally
 // with pagination
 add_shortcode( 'easy-event-list', 'do_easy_event_list' );
