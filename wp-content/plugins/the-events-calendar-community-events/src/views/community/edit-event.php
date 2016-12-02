<?php
/**
 * Event Submission Form
 * The wrapper template for the event submission form.
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/edit-event.php
 *
 * @package Tribe__Events__Community__Main
 * @since  3.1
 * @author Modern Tribe Inc.
 *
 * @var object $event
 * @var array $required
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$events_label_singular = tribe_get_event_label_singular();

?>

<?php tribe_get_template_part( 'community/modules/header-links' ); ?>

<?php do_action( 'tribe_events_community_form_before_template', isset( $tribe_event_id ) ? $tribe_event_id : null ); ?>

<form method="post" enctype="multipart/form-data" data-datepicker_format="<?php echo esc_attr( tribe_get_option( 'datepickerFormat', 0 ) ); ?>">
	<input type="hidden" name="post_ID" id="post_ID" value="<?php echo absint( $tribe_event_id ); ?>"/>
	<?php wp_nonce_field( 'ecp_event_submission' ); ?>

	<!-- Event Title -->
	<?php do_action( 'tribe_events_community_before_the_event_title' ) ?>

	<div class="events-community-post-title">
		<?php tribe_community_events_field_label( 'post_title', sprintf( __( '%s Title:', 'tribe-events-community' ), $events_label_singular ) ); ?>
		<?php tribe_community_events_form_title(); ?>
	</div><!-- .events-community-post-title -->

	<?php do_action( 'tribe_events_community_after_the_event_title' ) ?>


	<!-- Event Description -->
	<?php do_action( 'tribe_events_community_before_the_content' ); ?>

	<div class="events-community-post-content">
		<?php tribe_community_events_field_label( 'post_content', sprintf( __( '%s Description:', 'tribe-events-community' ), $events_label_singular ) ); ?>
		<?php tribe_community_events_form_content(); ?>
	</div><!-- .tribe-events-community-post-content -->

	<?php do_action( 'tribe_events_community_after_the_content' ); ?>


	<?php tribe_get_template_part( 'community/modules/taxonomy' ); ?>

	<?php tribe_get_template_part( 'community/modules/image' ); ?>

	<?php tribe_get_template_part( 'community/modules/datepickers' ); ?>

	<?php tribe_get_template_part( 'community/modules/venue' ); ?>

	<?php tribe_get_template_part( 'community/modules/organizer' ); ?>

	<?php tribe_get_template_part( 'community/modules/website' ); ?>

	<?php tribe_get_template_part( 'community/modules/custom' ); ?>

	<?php tribe_get_template_part( 'community/modules/cost' ); ?>

	<!-- Spam Control -->
	<?php Tribe__Events__Community__Main::instance()->formSpamControl(); ?>

	<!-- Form Submit -->
	<?php do_action( 'tribe_events_community_before_form_submit' ); ?>
	<div class="tribe-events-community-footer">
		<input type="submit" id="post" class="button submit events-community-submit" value="<?php

			if ( isset( $post_id ) && $post_id ) {
				echo apply_filters( 'tribe_ce_event_update_button_text', sprintf( __( 'Update %s', 'tribe-events-community' ), $events_label_singular ) );
			} else {
				echo apply_filters( 'tribe_ce_event_submit_button_text', sprintf( __( 'Submit %s', 'tribe-events-community' ), $events_label_singular ) );
			}

			?>" name="community-event" />
	</div><!-- .tribe-events-community-footer -->
	<?php do_action( 'tribe_events_community_after_form_submit' ); ?>

</form>
<?php
do_action( 'tribe_events_community_form_after_template' );
