<?php
/**
 * Event Submission Form Metabox For Datepickers
 * This is used to add a metabox to the event submission form to allow for choosing the
 * event time and day.

 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/modules/datepickers.php
 *
 * @package Tribe__Events__Community__Main
 * @since  3.1
 * @version 4.1.2
 * @author Modern Tribe Inc.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$has_post = get_post();

if ( $has_post && 0 !== get_the_ID() && 'auto-draft' !== get_post_status( $has_post ) ) {
	$all_day = tribe_community_events_is_all_day();
	$start_date = tribe_community_events_get_start_date();
	$end_date = tribe_community_events_get_end_date();
} else {
	$all_day = ! empty( $_POST['EventAllDay'] );
	$start_date = isset( $_POST['EventStartDate'] ) ? $_POST['EventStartDate'] : tribe_community_events_get_start_date();
	$end_date = isset( $_POST['EventEndDate'] ) ? $_POST['EventEndDate'] : tribe_community_events_get_end_date();
}

$events_label_singular = tribe_get_event_label_singular();
$events_label_plural = tribe_get_event_label_plural();
$events_label_singular_lowercase = tribe_get_event_label_singular_lowercase();
$events_label_plural_lowercase = tribe_get_event_label_plural_lowercase();

?>
<!-- Event Date Selection -->
<?php do_action( 'tribe_events_community_before_the_datepickers' ); ?>

<div class="tribe-events-community-details eventForm bubble" id="event_datepickers">

	<table class="tribe-community-event-info" cellspacing="0" cellpadding="0">

		<tr>
			<td colspan="2" class="tribe_sectionheader">
				<h4 class="event-time"><?php printf( __( '%s Time &amp; Date', 'tribe-events-community' ), $events_label_singular ); ?></h4>
			</td><!-- .tribe_sectionheader -->
		</tr>

		<tr id="recurrence-changed-row">
			<td colspan="2">
				<?php printf( __( 'You have changed the recurrence rules of this %1$s. Saving the %1$s will update all future %2$s.  If you did not mean to change all %2$s, then please refresh the page.', 'tribe-events-community' ), $events_label_singular_lowercase, $events_label_plural_lowercase ); ?>
			</td>
		</tr><!-- #recurrence-changed-row -->

		<tr>
			<td><?php printf( __( 'All day %s?', 'tribe-events-community' ), $events_label_singular_lowercase ); ?></td>
			<td>
				<input type="checkbox" id="allDayCheckbox" name="EventAllDay" value="yes" <?php echo ( $all_day ) ? 'checked' : ''; ?> />
			</td>
		</tr>

		<tr id="tribe-event-datepickers" data-startofweek="<?php echo esc_attr( get_option( 'start_of_week' ) ); ?>">
			<td>
				<?php tribe_community_events_field_label( 'EventStartDate', __( 'Start Date / Time:', 'tribe-events-community' ) ); ?>
			</td>
			<td>
				<input autocomplete="off" type="text" id="EventStartDate" class="tribe-datepicker" name="EventStartDate"  value="<?php echo esc_attr( $start_date ); ?>" />
				<span class="helper-text hide-if-js"><?php esc_html_e( 'YYYY-MM-DD', 'tribe-events-community' ); ?></span>
			<span class="timeofdayoptions">
				@ <?php echo tribe_community_events_form_start_time_selector(); ?>
			</span><!-- .timeofdayoptions -->
			</td>
		</tr>

		<tr>
			<td>
				<?php tribe_community_events_field_label( 'EventEndDate', __( 'End Date / Time:', 'tribe-events-community' ) ); ?>
			</td>
			<td>
				<input autocomplete="off" type="text" id="EventEndDate" class="tribe-datepicker" name="EventEndDate" value="<?php echo esc_attr( $end_date ); ?>" />
				<span class="helper-text hide-if-js"><?php esc_html_e( 'YYYY-MM-DD', 'tribe-events-community' ); ?></span>
			<span class="timeofdayoptions">
				@ <?php echo tribe_community_events_form_end_time_selector(); ?>
			</span><!-- .timeofdayoptions -->
			</td>
		</tr>

		<?php if ( class_exists( 'Tribe__Events__Timezones' ) && ! tribe_community_events_single_geo_mode() ): ?>
			<tr>
				<td>
					<?php tribe_community_events_field_label( 'EventTimezone', __( 'Timezone:', 'tribe-events-community' ) ); ?>
				</td>
				<td>
					<select tabindex="<?php tribe_events_tab_index(); ?>" name="EventTimezone" id="event-timezone" class="chosen">
						<?php echo wp_timezone_choice( Tribe__Events__Timezones::get_event_timezone_string() ); ?>
					</select>
				</td>
			</tr>
		<?php endif ?>

		<?php do_action( 'tribe_events_date_display', null, true ); ?>

	</table><!-- .tribe-community-event-info -->

</div>

<?php
do_action( 'tribe_events_community_after_the_datepickers' );
