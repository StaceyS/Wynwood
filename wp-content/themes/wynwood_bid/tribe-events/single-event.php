<?php
/**
 * Single Event Template
 * A single event. This displays the event title, description, meta, and
 * optionally, the Google map for the event.
 *
 * CUSTOMIZED FOR WYNWOOD:
 * This template overrides plugins/tribe-events/single-event.php
 *
 * @package TribeEventsCalendar
 * @version  4.3
 *
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

// Setup an array of venue details for use later in the template
$venue_details = tribe_get_venue_details();

// Venue
$has_venue_address = ( ! empty( $venue_details['address'] ) ) ? ' location' : '';

// Organizer
$organizer = tribe_get_organizer();

?>

<!-- Event Title -->
<?php do_action( 'tribe_events_before_the_event_title' ) ?>
<h1 class="tribe-events-list-event-title"><?php the_title() ?></h1>
<?php do_action( 'tribe_events_after_the_event_title' ) ?>

<!-- Event Header/Summary -->
<section class="event-summary-section">
	<!-- Event featured image, but exclude link -->
	<?php echo tribe_event_featured_image( $event_id, 'large', false ); ?>

	<div class="event-summary clearfix">
		<!-- Event Content -->
		<?php do_action( 'tribe_events_single_event_before_the_content' ) ?>
		<div class="tribe-events-single-event-description tribe-events-content">
			<!-- Event content -->
			<?php the_content(); ?>
		</div><!-- .tribe-events-list-event-description -->
		<?php do_action( 'tribe_events_single_event_after_the_content' ) ?>			
	</div>
</section>

<section class="event-details-section clearfix">
	<!-- Event meta -->
	<?php do_action( 'tribe_events_single_event_before_the_meta' ) ?>
	<?php tribe_get_template_part( 'modules/meta' ); ?>
	<?php do_action( 'tribe_events_single_event_after_the_meta' ) ?>
</section>

