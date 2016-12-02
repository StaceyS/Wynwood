<?php
/**
 * My Events List Template
 * The template for a list of a users events.
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/event-list.php
 *
 * @package Tribe__Events__Community__Main
 * @since  2.1
 * @version 4.1.1
 * @author Modern Tribe Inc.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$organizer_label_singular = tribe_get_organizer_label_singular();
$venue_label_singular = tribe_get_venue_label_singular();
$events_label_plural = tribe_get_event_label_plural();
$events_label_plural_lowercase = tribe_get_event_label_plural_lowercase();

// List "Add New" Button
do_action( 'tribe_ce_before_event_list_top_buttons' ); ?>

<div id="add-new"><a href="<?php echo esc_url( tribe_community_events_add_event_link() ); ?>" class="button"><?php echo apply_filters( 'tribe_ce_add_event_button_text', __( 'Add New', 'tribe-events-community' ) ); ?></a></div>

<div class="table-menu-wrapper">

	<?php if ( $events->have_posts() ) { ?>
	<a href="#" class="table-menu-btn button"><?php echo apply_filters( 'tribe_ce_event_list_display_button_text', __( 'Display', 'tribe-events-community' ) ); ?></a><!-- table-menu-btn -->
	<?php } ?>

	<?php do_action( 'tribe_ce_after_event_list_top_buttons' ); ?>

	<div class="table-menu table-menu-hidden">
		<ul></ul>
	</div><!-- .table-menu -->

</div><!-- .table-menu-wrapper -->


<?php // list admin link
$current_user = wp_get_current_user(); ?>
<div id="not-user">
	<?php esc_html_e( 'Not', 'tribe-events-community' ); ?>
	<i><?php echo $current_user->display_name; ?></i> ?
	<a href="<?php echo esc_url( tribe_community_events_logout_url() ); ?>">
		<?php esc_html_e( 'Log Out', 'tribe-events-community' ); ?>
	</a>
</div>

<div style="clear:both"></div>

<?php // list pagination
if ( ! $events->have_posts() ) {
	$this->enqueueOutputMessage( sprintf( __( 'There are no upcoming %s in your queue.', 'tribe-events-community' ), $events_label_plural_lowercase ) );
}
echo tribe_community_events_get_messages();
$tbody = '';

?>
<div class="my-events-display-options">
	<?php
	add_filter( 'get_pagenum_link', array( Tribe__Events__Community__Main::instance(), 'fix_pagenum_link' ) );
	$link = get_pagenum_link( 1 );
	$link = remove_query_arg( 'eventDisplay', $link );

	if ( empty( $_GET['eventDisplay'] ) || 'past' !== $_GET['eventDisplay'] ) {
		?>
		<a href="<?php echo esc_url( $link . '?eventDisplay=past' ); ?>"><?php echo esc_html__( 'View past events', 'tribe-events-community' ); ?></a>
		<?php
	} else {
		?>
		<a href="<?php echo esc_url( $link . '?eventDisplay=list' ); ?>"><?php echo esc_html__( 'View upcoming events', 'tribe-events-community' ); ?></a>
		<?php
	}
	?>
</div>
<?php
echo $this->pagination( $events, '', $this->paginationRange );

do_action( 'tribe_ce_before_event_list_table' );
if ( $events->have_posts() ) {
	?>
	<div class="my-events-table-wrapper">
		<table class="events-community my-events" cellspacing="0" cellpadding="4">
			<thead id="my-events-display-headers">
				<tr>
					<th class="essential persist"><?php esc_html_e( 'Status', 'tribe-events-community' ); ?></th>
					<th class="essential persist"><?php esc_html_e( 'Title', 'tribe-events-community' ); ?></th>
					<th class="essential"><?php _e( $organizer_label_singular, 'tribe-events-community' ); ?></th>
					<th class="essential"><?php _e( $venue_label_singular, 'tribe-events-community' ); ?></th>
					<th class="optional1"><?php esc_html_e( 'Category', 'tribe-events-community' ); ?></th>
					<?php
					if ( class_exists( 'Tribe__Events__Pro__Main' ) ) {
						echo '<th class="optional2">' . esc_html__( 'Recurring?', 'tribe-events-community' ) . '</th>';
					}
					?>
					<th class="essential"><?php esc_html_e( 'Start Date', 'tribe-events-community' ); ?></th>
					<th class="essential"><?php esc_html_e( 'End Date', 'tribe-events-community' ); ?></th>
				</tr>
			</thead><!-- #my-events-display-headers -->

			<tbody id="the-list"><tr>
				<?php $rewriteSlugSingular = Tribe__Settings_Manager::get_option( 'singleEventSlug', 'event' );
				global $post;
				$old_post = $post;
				while ( $events->have_posts() ) {
					$e = $events->next_post();
					$post = $e; ?>

					<tr>

						<td><?php echo Tribe__Events__Community__Main::instance()->getEventStatusIcon( $post->post_status ); ?></td>
						<td>
						<?php
						$canView = ( get_post_status( $post->ID ) == 'publish' || current_user_can( 'edit_post', $post->ID ) );
						$canEdit = current_user_can( 'edit_post', $post->ID );
						$canDelete = current_user_can( 'delete_post', $post->ID );
						if ( $canEdit ) {
							?>
							<span class="title">
								<a href="<?php echo esc_url( tribe_community_events_edit_event_link( $post->ID ) ); ?>"><?php echo $post->post_title; ?></a>
							</span>
							<?php
						} else {
							echo $post->post_title;
						}
						?>
						<div class="row-actions">
							<?php
							if ( $canView ) {
								?>
								<span class="view">
									<a href="<?php echo esc_url( tribe_get_event_link( $post ) ); ?>"><?php esc_html_e( 'View', 'tribe-events-community' ); ?></a>
								</span>
								<?php
							}

							if ( $canEdit ) {
								echo Tribe__Events__Community__Main::instance()->getEditButton( $post, __( 'Edit', 'tribe-events-community' ), '<span class="edit wp-admin events-cal"> |', '</span> ' );
							}

							if ( $canDelete ) {
								echo Tribe__Events__Community__Main::instance()->getDeleteButton( $post );
							}
							do_action( 'tribe_ce_event_list_table_row_actions', $post );
							?>
						</div><!-- .row-actions -->
						</td>

						<td>
							<?php
							if ( tribe_has_organizer( $post->ID ) ) {
								$organizer_id = tribe_get_organizer_id( $post->ID );
								if ( current_user_can( 'edit_post', $organizer_id ) ) {
									echo '<a href="'. esc_url( Tribe__Events__Community__Main::instance()->getUrl( 'edit', $organizer_id, null, Tribe__Events__Main::ORGANIZER_POST_TYPE ) ) .'">'. tribe_get_organizer( $post->ID ) .'</a>';
								} else {
									echo tribe_get_organizer( $post->ID );
								}
							}
							?>
						</td>

						<td>
							<?php
							if ( tribe_has_venue( $post->ID ) ) {
								$venue_id = tribe_get_venue_id( $post->ID );
								if ( current_user_can( 'edit_post', $venue_id ) ) {
									echo '<a href="' . esc_url( Tribe__Events__Community__Main::instance()->getUrl( 'edit', $venue_id, null, Tribe__Events__Main::VENUE_POST_TYPE ) ) . '">'. tribe_get_venue( $post->ID ) .'</a>';
								} else {
									echo tribe_get_venue( $post->ID );
								}
							}
							?>
						</td>

						<td><?php echo Tribe__Events__Admin_List::custom_columns( 'events-cats', $post->ID, false ); ?></td>

						<?php
						if ( function_exists( 'tribe_is_recurring_event' ) ) {
							?>
							<td>
								<?php
								if ( tribe_is_recurring_event( $post->ID ) ) {
									esc_html_e( 'Yes', 'tribe-events-community' );
								} else {
									esc_html_e( 'No', 'tribe-events-community' );
								}
								?>
							</td>
							<?php
						} ?>

						<td>
							<?php echo esc_html( tribe_get_start_date( $post->ID, Tribe__Events__Community__Main::instance()->eventListDateFormat ) ) ?>
						</td>

						<td>
							<?php echo esc_html( tribe_get_end_date( $post->ID, Tribe__Events__Community__Main::instance()->eventListDateFormat ) ) ?>
						</td>

					</tr>

				<?php } // end list loop
				$post = $old_post; ?>

			</tbody><!-- #the-list -->

			<?php do_action( 'tribe_ce_after_event_list_table' ); ?>

		</table><!-- .events-community -->

	</div><!-- .my-events-table-wrapper -->

	<?php // list pagination
	echo $this->pagination( $events, '', $this->paginationRange );

} // if ( $events->have_posts() )
