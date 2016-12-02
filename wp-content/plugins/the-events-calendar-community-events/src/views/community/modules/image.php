<?php
/**
 * Event Submission Form Image Uploader Block
 * Renders the image upload field in the submission form.
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/modules/image.php
 *
 * @package Tribe__Events__Community__Main
 * @since  3.1
 * @author Modern Tribe Inc.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$upload_error = Tribe__Events__Community__Main::instance()->max_file_size_exceeded();
$size_format = size_format( wp_max_upload_size() );

?>

<!-- Event Featured Image -->
<?php do_action( 'tribe_events_community_before_the_featured_image' ); ?>

	<div class="tribe-events-community-details eventForm bubble" id="event_image_uploader">
		<table class="tribe-community-event-info" cellspacing="0" cellpadding="0">
			<tr>
				<td colspan="2" class="tribe_sectionheader">
					<h4 class="event-time"><?php printf( esc_html__( '%s Image', 'tribe-events-community' ), tribe_get_event_label_singular() ); ?></h4>
				</td>
			</tr>
			<tr>
				<td>
					<label for="EventImage" class="<?php echo esc_attr( $upload_error ? 'error' : '' ); ?>">
						<?php tribe_community_events_field_label( 'event_image', __( 'Upload:', 'tribe-events-community' ) ); ?>
					</label>
				</td>
				<td>
					<?php if ( get_post() && has_post_thumbnail() ) { ?>
						<div class="tribe-community-events-preview-image">
							<?php the_post_thumbnail( 'medium' ); ?>
							<?php tribe_community_events_form_image_delete(); ?>
						</div>
					<?php }	?>

					<input type="file" name="event_image" id="EventImage">
					<small class="note"><?php echo esc_html( sprintf( __( 'Images that are not png, jpg, or gif will not be uploaded. Images may not exceed %1$s in size.', 'tribe-events-community' ), $size_format ) ); ?></small>
				</td>
			</tr>
		</table><!-- .tribe-community-event-info -->
	</div><!-- .tribe-events-community-details -->

<?php
do_action( 'tribe_events_community_after_the_featured_image' );
