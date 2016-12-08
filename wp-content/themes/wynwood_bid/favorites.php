
<?php  /* Template Name: Favorites */ ?>  

<?php get_header(); ?>

<div class="hero-img" style="background: url(<?php bloginfo(template_url); ?>/images/layout/hero/wy_hero_images_businesses.jpg) no-repeat center center; background-size: cover;"></div>

<div class="main-content clearfix">

	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
		
	  		<h1>My Favorites</h1>

	  		<?php //the_content(); ?>

	  		<h2>Businesses</h2>
	  		<?php echo do_shortcode('[user_favorites user_id="" include_links="true" site_id="" post_types="business" include_buttons="false"]'); ?>

	  		<h2>Events</h2>
	  		<?php echo do_shortcode('[user_favorites user_id="" include_links="true" site_id="" post_types="tribe_events" include_buttons="false"]'); ?>

	  		<h2>Resources</h2>

	  		<h2>News</h2>


		<?php endwhile; ?>		
	<?php endif; ?>

</div> <!-- end .main-content -->

<?php get_footer(); ?>
