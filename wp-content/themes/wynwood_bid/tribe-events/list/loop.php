<?php
/**
 * List View Loop
 * This file sets up the structure for the list loop
 *
 * CUSTOMIZED FOR WYNWOOD:
 * This template overrides plugins/tribe-events/list/loop.php
 *
 * @package TribeEventsCalendar
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
} ?>

<?php
global $post;
global $more;
$more = false;
?>

<h1>Events in Wynwood</h1>

<aside class="filter-sidebar">

	<ul class="tribe-events-event-categories">
		<li><a href="#" class="active">View All Events</a></li>

	<?php while ( have_posts() ) : the_post(); ?>

		<?php
		// See article for hack to remove colon after category headers
		// https://theeventscalendar.com/support/forums/topic/category-tag-keeps-colon-when-label-is-removed/
		echo str_replace( ':', '', tribe_get_event_categories( null,array(
	    'before' => '',
	    'sep' => ', ',
	    'after' => '',
	    'label' => '',
	    'label_before' => '',
	    'label_after' => '',
	    'wrap_before' => '<li>',
	    'wrap_after' => '</li>'
		) ) );
		?>

	<?php endwhile; ?>

	</ul>

	<?php //echo tribe_meta_event_tags( sprintf( esc_html__( '%s Tags:', 'the-events-calendar' ), tribe_get_event_label_singular() ), ', ', false ) ?>
</aside>

<div class="tribe-events-loop results clearfix">

	<?php while ( have_posts() ) : the_post(); ?>
		<?php do_action( 'tribe_events_inside_before_loop' ); ?>

		<!-- Month / Year Headers -->
		<?php //tribe_events_list_the_date_headers(); ?>

		<!-- Event  -->
		<?php
		$post_parent = '';
		if ( $post->post_parent ) {
			$post_parent = ' data-parent-post-id="' . absint( $post->post_parent ) . '"';
		}
		?>
		<div id="post-<?php the_ID() ?>" class="<?php tribe_events_event_classes() ?>" <?php echo $post_parent; ?>>
			<?php tribe_get_template_part( 'list/single', 'event' ) ?>
		</div>


		<?php do_action( 'tribe_events_inside_after_loop' ); ?>
	<?php endwhile; ?>

</div><!-- .tribe-events-loop -->
