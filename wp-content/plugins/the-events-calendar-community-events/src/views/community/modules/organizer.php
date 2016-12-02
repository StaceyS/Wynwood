<?php
/**
 * Event Submission Form Metabox For Organizers
 * This is used to add a metabox to the event submission form to allow for choosing or
 * creating an organizer for user submitted events.
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/modules/organizer.php
 *
 * @package Tribe__Events__Community__Main
 * @since  2.1
 * @version 4.2
 * @author Modern Tribe Inc.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( ! isset( $event ) ) {
	$event = Tribe__Events__Main::postIdHelper();
}
?>

<!-- Organizer -->
<div class="tribe-events-community-details eventForm bubble" id="event_tribe_organizer">

	<table class="tribe-community-event-info" cellspacing="0" cellpadding="0">

		<thead> <tr>
			<td colspan="2" class="tribe_sectionheader">
				<h4> <label class="<?php echo tribe_community_events_field_has_error( 'organizer' ) ? 'error' : ''; ?>"> <?php
					printf( __( '%s Details', 'tribe-events-community' ), tribe_get_organizer_label_singular() );
					echo tribe_community_required_field_marker( 'organizer' );
				?> </label> </h4>
			</td><!-- .tribe_sectionheader -->
		</tr> </thead>

		<?php
		// The organizer meta box will render everything within a <tbody>
		$organizer_meta_box = new Tribe__Events__Linked_Posts__Chooser_Meta_Box( $event, Tribe__Events__Organizer::POSTTYPE );
		$organizer_meta_box->render();
		?>

	</table> <!-- #event_organizer -->

</div>
