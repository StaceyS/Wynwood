<?php
/**
 * Event Submission Form Captcha Block
 * Renders the captcha field in the submission form.
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/modules/captcha.php
 *
 * @package Tribe__Events__Community__Main
 * @author Modern Tribe Inc.
 *
 *
 * @var string $captcha The captcha form from the currently loaded captcha module
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

?>

<!-- Event Captcha -->
<?php do_action( 'tribe_events_community_before_the_captcha' ); ?>

	<div class="tribe-events-community-details eventForm bubble" id="event_captcha">

		<table class="tribe-community-event-info" cellspacing="0" cellpadding="0">

			<tr>
				<td colspan="2" class="tribe_sectionheader">
					<h4><?php tribe_community_events_field_label( 'EventCaptcha', __( 'Anti-Spam Check', 'tribe-events-community' ) ); ?></h4>
				</td><!-- .tribe_sectionheader -->
			</tr>
			<tr>
				<td colspan="2"><?php echo $captcha; ?></td>
			</tr>

		</table><!-- #event_cost -->

	</div><!-- .tribe-events-community-details -->

<?php
do_action( 'tribe_events_community_after_the_captcha' );