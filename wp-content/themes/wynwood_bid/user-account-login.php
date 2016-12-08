<?php //Template Name: Login/Account Flow ?> 
<?php get_header(); ?>

<div class="hero-img" style="background: url(<?php bloginfo(template_url); ?>/images/layout/hero/wy_hero_images_businesses.jpg) no-repeat center center; background-size: cover;"></div>

<div class="main-content user-login clearfix">

	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
		
	  		<!-- <h1><a href="<?php bloginfo('url'); ?>"><?php bloginfo('name'); ?></a></h1> -->
			<h1><?php bloginfo('name'); ?></h1>
	  		<?php the_content(); ?>


		<?php endwhile; ?>		
	<?php endif; ?>

</div> <!-- end .main-content -->

<?php get_footer(); ?>
