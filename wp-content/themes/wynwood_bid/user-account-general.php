<?php //Template Name: Account Pages (General) ?> 
<?php get_header(); ?>

<div class="hero-img" style="background: url(<?php bloginfo(template_url); ?>/images/layout/hero/wy_hero_images_businesses.jpg) no-repeat center center; background-size: cover;"></div>

<div class="main-content user-account-general clearfix">
	<a class="section-marker" href="<?php bloginfo('url'); ?>/community/my-account">My Account</a>

	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
		
			<h1><?php the_title(); ?></h1>
	  		<?php the_content(); ?>

		<?php endwhile; ?>		
	<?php endif; ?>

</div> <!-- end .main-content -->

<?php get_footer(); ?>
