<?php
/*

Plugin Name: Browseable
Description: Shows a ton of events on each page for those afraid to search

/**
 * Initialize and run setup options on after_theme_setup hook *
 */
function do_easy_event_list() {
	// Safety first! Bail in the event TEC is inactive/not loaded yet
	if (!class_exists( 'Tribe__Events__Main'))
		return;

	// Build our query, get all events for the next ten days
	$events = new WP_Query( 
		array(
			'post_type' => Tribe__Events__Main::POSTTYPE,
			'nopaging' => true,
			'meta_key' => '_EventStartDate',
			'meta_query' => array(
				array(
					'key' => '_EventStartDate',
					'value' => array(date('Y-m-d'),date('Y-m-d', strtotime("+9 day"))),
					'compare' => 'BETWEEN',
					'type' => 'DATE'
				)
			)
		)
	);

	$begin_date = new DateTime();
	$end_date = new DateTime();
	$end_date->modify('+10 day');
	$interval = DateInterval::createFromDateString('1 day');
	$period = new DatePeriod($begin_date, $interval, $end_date);

	$event_map = array();

	foreach ( $period as $dt ) {
		$thisDateFormat = $dt->format('Ymd');

		// Create links for the ten dates
		echo '<a href=javascript:showDivs(';
		echo $thisDateFormat;
		echo ')>';
		echo $dt->format('l, m/d');
		echo '</a> ';

		// Create associative array for the ten dates 
		$event_map[$thisDateFormat] = array();
	}

	?>

	<script type="text/javascript">
		<?php
			$php_date_array = array();
			foreach($event_map as $date_str => $date_value) {
				array_push($php_date_array, $date_str);
			}
			$js_date_array = json_encode($php_date_array);
			echo "var js_date_array = ". $js_date_array . ";\n";
			echo "var today_str = ". $begin_date->format('Ymd') . ";\n";
		?>

		function showDivs(dateStr) {
			for (var x = 0; x < js_date_array.length; x++) {
				var divsToMod = document.getElementsByClassName(js_date_array[x]);
				if (js_date_array[x] == dateStr) {
					for(var i = 0; i < divsToMod.length; i++) {
						divsToMod[i].style.visibility="visible";
						divsToMod[i].style.display="block";
					}
				} else {
					for(var i = 0; i < divsToMod.length; i++) {
						divsToMod[i].style.visibility="hidden";
						divsToMod[i].style.display="none";
					}
				}
			}
		}

	</script>

	<?php

	// Add events to divs with class corresponding to date
	while ($events->have_posts()) {
		$events->the_post();

		echo '<div class=';
		echo tribe_get_start_date($events->ID, false, $format = 'Ymd');
		echo '>';
		echo '<p>';
		echo tribe_get_start_date( $events->ID, false, $format = 'g:i a') . "-" . tribe_get_end_date( $events->ID, false, $format = 'g:i a');
		echo '<strong><a href="';
		echo the_permalink();
		echo '">';
		echo the_title();
		echo '</a></strong>';
		echo the_content_rss(0, 45);
		echo '</p>';
		echo '</div>';
	}

	?>

	<script type="text/javascript">
		<?php
			echo "var today_str = ". $begin_date->format('Ymd') . ";\n";
		?>
		showDivs(today_str);
	</script>

	<?php

	// Clean up
	wp_reset_query();
}
 
// Create a new shortcode to list upcoming events, optionally
// with pagination
add_shortcode( 'easy-event-list', 'do_easy_event_list' );
