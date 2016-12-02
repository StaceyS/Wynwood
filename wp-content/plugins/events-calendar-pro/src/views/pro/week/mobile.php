<?php

/**
 *
 * Please see single-event.php in this directory for detailed instructions on how to use and modify these templates.
 * @version 4.3.3
 *
 */

?>

<script type="text/html" id="tribe_tmpl_week_mobile">
	<div class="tribe-events-mobile tribe-clearfix tribe-events-mobile-event-[[=eventId]][[ if(categoryClasses.length) { ]] [[= categoryClasses]][[ } ]]">
		<h4 class="summary">
			<a class="tribe-event-url" href="[[=permalink]]" title="[[=title]]" rel="bookmark">[[=raw title]]</a>
		</h4>

		<div class="tribe-events-event-body">
			<div class="tribe-event-schedule-details">
				<span class="tribe-event-date-start">[[=dateDisplay]] </span>
			</div>
			<a href="[[=permalink]]" class="tribe-events-read-more" rel="bookmark">
				<?php esc_html_e( 'Find out more', 'tribe-events-calendar-pro' ); ?> »
            </a>
		</div>
	</div>
</script>
