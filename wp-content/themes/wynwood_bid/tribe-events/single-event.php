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

	<div class="event-cta-buttons-wrapper">
		<?php if ( tribe_address_exists() ) : ?>
			<?php //echo tribe_get_full_address(); ?>
			<?php echo tribe_get_map_link_html(); ?>
		<?php endif; ?>

		<!-- <a class="biz-cta-button" href="https://www.google.com/maps/place/?q=<?php echo $business_address['address']; ?>" target="blank">Get Directions <i class="fa fa-compass" aria-hidden="true"></i></a> -->

				
		<?php // Not sure why this won't work ?>
		<?php //include 'favorite-button.php'; ?>
		
		<!-- If a user is not logged in, force them to create an account to unlock favorite functionality. See 'Favorite Posts' plugin site for details https://favoriteposts.com/ -->

		<?php if ( is_user_logged_in() ) { ?>
			<?php echo do_shortcode('[favorite_button post_id="" site_id=""]'); ?>
		<?php } else { ?>
			<button id="favorite-create-account" class="biz-cta-button">Favorite  <i class="fa fa-heart-o" aria-hidden="true"></i></button>
		<?php  } ?>

	</div>


	<?php do_action( 'tribe_events_single_event_after_the_meta' ) ?>

</section>

