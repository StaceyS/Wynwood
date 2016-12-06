<?php
/**
 * List View Single Event
 * This file contains one event in the list view
 *
 * CUSTOMIZED FOR WYNWOOD:
 * This template overrides plugins/tribe-events/list/single-event.php
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

<!-- Event Cost -->
<!-- Removed: See original template file for details -->

<!-- Event Title -->
<?php do_action( 'tribe_events_before_the_event_title' ) ?>
<?php echo tribe_events_event_schedule_details() ?>
<h2 class="tribe-events-list-event-title">
	<a class="tribe-event-url" href="<?php echo esc_url( tribe_get_event_link() ); ?>" title="<?php the_title_attribute() ?>" rel="bookmark">
		<?php the_title() ?>
	</a>
</h2>
<?php do_action( 'tribe_events_after_the_event_title' ) ?>

<!-- Event Meta -->
<!-- Removed: See original template file for details -->

<!-- Event Image -->
<?php echo tribe_event_featured_image( null, 'medium' ) ?>

<!-- Event Content -->
<!-- Removed: See original template file for details -->

