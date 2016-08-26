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

	$begin_date = new DateTime(date('Y-m-d'));
	$end_date = new DateTime(date('Y-m-d'));
	$end_date->modify('+10 day');
	$interval = DateInterval::createFromDateString('1 day');
	$period = new DatePeriod($begin_date, $interval, $end_date);

	$php_date_array = array();

	foreach ( $period as $dt ) {
		$thisDateFormat = $dt->format('Ymd');

		// Create links for the ten dates
		echo '<a href=javascript:showDivs(';
		echo $thisDateFormat;
		echo ')>';
		echo $dt->format('l, m/d');
		echo '</a> ';

		// Create array of the ten dates 
		array_push($php_date_array, $thisDateFormat);
	}

	?>

	<br/>
	<div id='date_header'></div>

	<script type="text/javascript">
		<?php
			$js_date_array = json_encode($php_date_array);
			echo "var js_date_array = ". $js_date_array . ";\n";
		?>

		function showDivs(dateId) {

			var weekday = new Array(7);
			weekday[0]=  "Sunday";
			weekday[1] = "Monday";
			weekday[2] = "Tuesday";
			weekday[3] = "Wednesday";
			weekday[4] = "Thursday";
			weekday[5] = "Friday";
			weekday[6] = "Saturday";

			var dateStr = dateId.toString();
			var dateObjStr = dateStr[0] + dateStr[1] + dateStr[2] + dateStr[3] + '-' + dateStr[4] + dateStr[5] + '-' + dateStr[6] + dateStr[7];
			var d = new Date(dateObjStr);
			var dateHeaderHTML = "<strong><h2>" + weekday[d.getDay()] + ", " + d.getMonth() + "/" + d.getDate() + "</h2></strong>";
			var dateHeaderDiv = document.getElementById('date_header');
			dateHeaderDiv.innerHTML = dateHeaderHTML;

			for (var x = 0; x < js_date_array.length; x++) {
				var divsToMod = document.getElementsByClassName(js_date_array[x]);
				if (js_date_array[x] == dateId) {
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
			echo "var today_id = ". $begin_date->format('Ymd') . ";\n";
		?>
		showDivs(today_id);
	</script>

	<?php

	// Clean up
	wp_reset_query();
}
 
// Create a new shortcode to list upcoming events, optionally
// with pagination
add_shortcode( 'easy-event-list', 'do_easy_event_list' );
