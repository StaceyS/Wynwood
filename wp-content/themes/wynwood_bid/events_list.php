<?php  /* Template Name: Event List */ ?>  

<?php get_header(); ?>
<div class="hero-img" style="background: url(<?php bloginfo(template_url); ?>/images/layout/hero/wy_hero_images_events.jpg) no-repeat center center; background-size: cover;"></div>

<div class="main-content events-index clearfix">
	<span class="section-marker">Events Calendar</span>
	<h1>Events in Wynwood</h1>

	<!-- Remove this if we can style the native controls 
	<div class="grid-map-view-toggle">
		<button class="grid-view active-view">Grid</button>
		<button class="map-view">Calendar</button>
	</div> -->

<?php if (have_posts()) : ?>
	<?php while (have_posts()) : the_post(); ?>
		<!-- Display event list -->
  		<?php the_content(); ?>
	<?php endwhile; ?>		
<?php endif; ?>

</div> <!-- end .main-content -->

	
<?php get_footer(); ?>
