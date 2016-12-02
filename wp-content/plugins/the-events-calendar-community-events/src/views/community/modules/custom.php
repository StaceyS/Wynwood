<?php
/**
 * Event Submission Form Metabox For Custom Fields
 * This is used to add a metabox to the event submission form to allow for custom
 * field input for user submitted events.
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/modules/custom.php
 *
 * @package Tribe__Events__Community__Main
 * @since  2.1
 * @author Modern Tribe Inc.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$customFields = tribe_get_option( 'custom-fields' );

if ( empty( $customFields ) || ! is_array( $customFields ) ) {
	return;
}
?>

<!-- Custom -->
<div class="tribe-events-community-details eventForm bubble" id="event_custom">
	<table id="event-meta" class="tribe-community-event-info">

		<tbody>

			<tr>
				<td colspan="2" class="tribe_sectionheader">
					<h4><?php esc_html_e( 'Additional Fields', 'tribe-events-community' ); ?></h4>
				</td>
			</tr><!-- .snp-sectionheader -->

			<?php foreach ( $customFields as $customField ) :

				$val = '';
				global $post;
				if ( isset( $post->ID ) && get_post_meta( get_the_ID(), $customField['name'], true ) ) {
					$val = get_post_meta( get_the_ID(), $customField['name'], true );
				}
				$val = apply_filters( 'tribe_community_custom_field_value', $val, $customField['name'], get_the_ID() );

				$field_id = 'tribe_custom_'.sanitize_title( $customField['label'] );
				?>
				<tr>
					<td>
						<?php tribe_community_events_field_label( $customField['name'], sprintf( _x( '%s:', 'custom field label', 'tribe-events-community' ), $customField['label'] ) ); ?>
					</td>
					<td>
						<?php
						$options = explode( "\n", $customField['values'] );
						if ( $customField['type'] == 'text' ) {
							?>
							<input type="text" id="<?php echo esc_attr( $field_id ); ?>" name="<?php echo esc_attr( $customField['name'] ); ?>" value="<?php echo esc_attr( $val ); ?>"/>
							<?php
						} elseif ( $customField['type'] == 'url' ) {
							?>
							<input type="url" id="<?php echo esc_attr( $field_id ); ?>" name="<?php echo esc_attr( $customField['name'] ); ?>" value="<?php echo esc_attr( $val ); ?>"/>
							<?php
						} elseif ( 'radio' === $customField['type'] ) {
							?>
							<div>
								<label>
									<input type="radio" name="<?php echo esc_attr( $customField['name'] ) ?>" value="" <?php checked( trim( $val ), '' ) ?>/>
									<?php esc_html_e( 'None', 'tribe-events-community' ); ?>
								</label>
							</div>
							<?php
							foreach ( $options as $option ) {
								?>
								<div>
									<label>
										<input type="radio" name="<?php echo esc_attr( stripslashes( $customField['name'] ) ); ?>" value="<?php echo esc_attr( trim( $option ) ); ?>" <?php checked( esc_attr( trim( $val ) ), esc_attr( trim( $option ) ) ); ?>/>
										<?php echo esc_html( stripslashes( $option ) ); ?>
									</label>
								</div>
								<?php
							}
						} elseif ( $customField['type'] == 'checkbox' ) {
							foreach ( $options as $option ) {
								$values = ! is_array( $val ) ? explode( '|', $val ) : $val;
								?>
								<div>
									<label>
										<input type="checkbox" value="<?php echo esc_attr( trim( $option ) ); ?>" <?php checked( in_array( esc_attr( trim( $option ) ), $values ) ) ?> name="<?php echo esc_html( stripslashes( $customField['name'] ) ); ?>[]"/>
										<?php echo esc_html( stripslashes( $option ) ); ?>
									</label>
								</div>
								<?php
							}
						} elseif ( $customField['type'] == 'dropdown' ) {
							?>
							<select name="<?php echo esc_attr( $customField['name'] ); ?>">
								<option value="" <?php selected( trim( $val ), '' ) ?>><?php esc_html_e( 'None', 'tribe-events-community' ); ?></option>
								<?php
								$options = explode( "\n", $customField['values'] );
								foreach ( $options as $option ) {
									?>
									<option value="<?php echo esc_attr( trim( $option ) ); ?>" <?php selected( esc_attr( trim( $val ) ), esc_attr( trim( $option ) ) ); ?>><?php echo esc_html( stripslashes( $option ) ); ?></option>
									<?php
								}
								?>
							</select>
							<?php
						} elseif ( $customField['type'] == 'textarea' ) {
							?>
							<textarea id="<?php echo esc_attr( $field_id ); ?>" name="<?php echo esc_attr( $customField['name'] ); ?>"><?php echo esc_textarea( stripslashes( $val ) ); ?></textarea>
							<?php
						}
						?>
					</td>
				</tr>
			<?php endforeach; ?>

		</tbody>

	</table>
</div><!-- #event-meta -->
