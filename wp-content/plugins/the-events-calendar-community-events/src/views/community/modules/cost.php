<?php
/**
 * Event Submission Form Price Block
 * Renders the pricing fields in the submission form.
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/modules/cost.php
 *
 * @package Tribe__Events__Community__Main
 * @since  3.1
 * @version 4.1.1
 * @author Modern Tribe Inc.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$events_label_singular = tribe_get_event_label_singular();
$events_label_plural = tribe_get_event_label_plural();
$events_label_plural_lowercase = tribe_get_event_label_plural_lowercase();

global $post;

if ( $post instanceof WP_Post ) {
	$_EventCurrencyPosition = get_post_meta( $post->ID, '_EventCurrencyPosition', true );
}
?>

<!-- Event Cost -->
<?php
do_action( 'tribe_events_community_before_the_cost' );

if ( apply_filters( 'tribe_events_community_display_cost_section', true ) ) {
	?>
	<div class="tribe-events-community-details eventForm bubble" id="event_cost">
		<table class="tribe-community-event-info" cellspacing="0" cellpadding="0">
			<tr>
				<td colspan="2" class="tribe_sectionheader">
					<h4><?php printf( esc_html__( '%s Cost', 'tribe-events-community' ), $events_label_singular ); ?></h4>
				</td><!-- .tribe_sectionheader -->
			</tr>
			<tr>
				<td>
					<?php tribe_community_events_field_label( 'EventCurrencySymbol', __( 'Currency Symbol:', 'tribe-events-community' ) ); ?>
				</td>
				<td>
					<input type="text" id="EventCurrencySymbol" name="EventCurrencySymbol" size="2" value="<?php echo esc_attr( isset( $_POST['EventCurrencySymbol'] ) ? $_POST['EventCurrencySymbol'] : tribe_community_events_form_currency_symbol() ); ?>" />
					<select id="EventCurrencyPosition" name="EventCurrencyPosition">
						<?php
						if ( isset( $_EventCurrencyPosition ) && 'suffix' === $_EventCurrencyPosition ) {
							$suffix = true;
						} elseif ( isset( $_EventCurrencyPosition ) && 'prefix' === $_EventCurrencyPosition ) {
							$suffix = false;
						} elseif ( true === tribe_get_option( 'reverseCurrencyPosition', false ) ) {
							$suffix = true;
						} else {
							$suffix = false;
						}
						?>
						<option value="prefix"> <?php _ex( 'Before cost', 'Currency symbol position', 'tribe-events-community' ) ?> </option>
						<option value="suffix"<?php if ( $suffix ) {
							echo ' selected="selected"';
						} ?>><?php _ex( 'After cost', 'Currency symbol position', 'tribe-events-community' ) ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<?php tribe_community_events_field_label( 'EventCost', __( 'Cost:', 'tribe-events-community' ) ); ?>
				</td>
				<td><input type="text" id="EventCost" name="EventCost" size="6" value="<?php echo esc_attr( isset( $_POST['EventCost'] ) ? $_POST['EventCost'] : tribe_get_cost() ); ?>" /></td>
			</tr>
			<tr>
				<td></td>
				<td><small><?php printf( __( 'Leave blank to hide the field. Enter a 0 for %s that are free.', 'tribe-events-community' ), $events_label_plural_lowercase ); ?></small></td>
			</tr>
		</table><!-- #event_cost -->
	</div><!-- .tribe-events-community-details -->
	<?php
}//end if
do_action( 'tribe_events_community_after_the_cost' );
