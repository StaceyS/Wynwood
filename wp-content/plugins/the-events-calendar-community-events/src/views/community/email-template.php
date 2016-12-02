<?php
/**
 * Email Template
 * The template for the Event Submission Notification Email
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/email-template.php
 *
 * @package Tribe__Events__Community__Main
 * @since  3.6
 * @author Modern Tribe Inc.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$events_label_singular = tribe_get_event_label_singular();

?>
<html>
	<body>
		<h2><?php echo $post->post_title; ?></h2>
		<h4><?php echo tribe_get_start_date( $tribe_event_id ); ?> - <?php echo tribe_get_end_date( $tribe_event_id ); ?></h4>

		<hr />

		<h3><?php printf( __( '%s Organizer', 'tribe-events-community' ), $events_label_singular ); ?></h3>
		<p><?php echo tribe_get_organizer( tribe_get_event_meta( $post->ID, '_EventOrganizerID', true ) ); ?></p>

		<h3><?php printf( __( '%s Venue', 'tribe-events-community' ), $events_label_singular ); ?></h3>
		<p><?php echo tribe_get_venue( tribe_get_event_meta( $post->ID, '_EventVenueID', true ) ); ?></p>

		<h3><?php esc_html_e( 'Description', 'tribe-events-community' ); ?></h3>
		<?php echo $post->post_content; ?>

		<hr />

		<h4><?php
		$query = array(
			'action' => 'edit',
			'post' => $post->ID,
		);
		$edit_url = add_query_arg( $query, get_admin_url( null, 'post.php' ) );
		echo '<a href="' . esc_url( $edit_url ) .'">' . sprintf( esc_html__( 'Review %s', 'tribe-events-community' ), $events_label_singular ) . '</a>';
		if ( 'publish' == $post->post_status ) {
			?> | <a href="<?php echo esc_url( get_permalink( $tribe_event_id ) ); ?>"><?php printf( __( 'View %s', 'tribe-events-community' ), $events_label_singular ); ?></a><?php
		}
		?></h4>
	</body>
</html>
