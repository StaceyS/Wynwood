<?php /* Template Name: Event List */ 
	
	// This is the outer-most wrapper for all events pages
	// AJAX loading (filtering) causes some duplication for separate template pages
	// so elements for index (list/cal) & single event page are controlled here
?>  

<?php get_header(); ?>

<?php 
	// Hack to add css class to show/hide specific elements on list/calendar vs. single event page
	$isArchiveEventPage =is_post_type_archive();
	$eventPageType;

	if ($isArchiveEventPage == 1) { $eventPageType = 'events-index-page'; }
	else { $eventPageType = 'event-detail-page'; }
 ?>

<div class="hero-img" style="background: url(<?php bloginfo('template_url'); ?>/images/layout/hero/wy_hero_images_events.jpg) no-repeat center center; background-size: cover;"></div>

<?php if (have_posts()) : ?>
	<?php while (have_posts()) : the_post(); ?>

	<div class="main-content <?php echo $eventPageType; ?> clearfix">
		<a class="section-marker" href="<?php bloginfo('url'); ?>/events">Events Calendar</a>

		<!-- <a class="events-index-link" href="<?php bloginfo(url); ?>/events" title="events"><i class="fa fa-caret-left" aria-hidden="true"></i> All Wynwood Events</a>		 -->	

		<?php 
			// If this is a category view, append that to title
			//http://www.wpbeginner.com/wp-themes/how-to-show-the-current-taxonomy-title-url-and-more-in-wordpress/
			$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) ); 
			$event_category = $term->name; // the category name
			$event_cat_slug = $term->slug; // the category slug
			$event_cat_nav_wrapper = '<span class="' . $event_cat_slug . '">';
		?>

		<h1 class="event-index-title">Events in Wynwood<?php if ($event_category) {echo ': ' . $event_category;}  ?>	</h1>

		<?php
			// Relocated from original locaiton in tribe-events/list/list.php 
			tribe_get_template_part( 'modules/bar' ); ?>

		<aside class="filter-sidebar">
			<ul id="tribe-events-event-categories" class="<?php echo $event_cat_slug; ?>">
				<li class="view-all-events"><a href="/events">View All Events</a></li>
	  			<?php wp_nav_menu( array( 
	  				'theme_location' => 'menu-3',
	  				'sort_column' => 'menu_order',
	  				'container' => 'false',
	  				'items_wrap' => '%3$s',
	  				'before' => $event_cat_nav_wrapper,
	  				'after' => '</span'
	  			) ); ?>
			</ul>
		</aside>

		<!-- Display event list -->
  		<?php the_content(); ?>

		</div> <!-- end .main-content -->
	<?php endwhile; ?>		
<?php endif; ?>

	
<?php get_footer(); ?>
