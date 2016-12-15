
<?php  /* Template Name: Favorites */ ?>  

<?php get_header(); ?>

<div class="hero-img" style="background: url(<?php bloginfo(template_url); ?>/images/layout/hero/wy_hero_images_businesses.jpg) no-repeat center center; background-size: cover;"></div>

<div class="main-content clearfix">
	<a class="section-marker" href="<?php bloginfo('url'); ?>/community/my-account">My Account</a>

	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
		
	  		<h1>My Favorites</h1>

	  		<?php //the_content(); ?>

	  		<section class="faves-section fave-businesses">
	  			<h2>Businesses</h2>
		  		<?php echo do_shortcode('[user_favorites user_id="" include_links="true" site_id="" post_types="business" include_buttons="false"]'); ?>
	  		</section>

	  		<section class="faves-section fave-events">
		  		<h2>Events</h2>
		  		<?php echo do_shortcode('[user_favorites user_id="" include_links="true" site_id="" post_types="tribe_events" include_buttons="false"]'); ?>
	  		</section>

	  		<section class="faves-section fave-resources">
		  		<h2>Resources</h2>
	  		</section>
	  		
	  		<section class="faves-section fave-news">
		  		<h2>News</h2>
	  		</section>




		<?php endwhile; ?>		
	<?php endif; ?>

</div> <!-- end .main-content -->

<?php get_footer(); ?>
