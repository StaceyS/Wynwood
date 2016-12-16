<?php //Template Name: Login/Account Flow ?> 
<?php get_header(); ?>

<div class="hero-img" style="background: url(<?php bloginfo(template_url); ?>/images/layout/hero/wy_hero_images_businesses.jpg) no-repeat center center; background-size: cover;"></div>

<div class="main-content user-login clearfix">

	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
		
			<h1><?php bloginfo('name'); ?></h1>
			<p class="login-instructions">Don't have an account yet? <a href="<?php bloginfo(url); ?>/community/register/">Sign up</a>
			<br />Already have an account? <a href="<?php bloginfo(url); ?>/community/login">Sign in</a></p>
	  		<?php the_content(); ?>
			

			<p class="login-instructions"><a href="<?php bloginfo(url); ?>/community/lostpassword/">Forgot your password?</a></p>
		
		<?php endwhile; ?>		
	<?php endif; ?>

</div> <!-- end .main-content -->

<?php get_footer(); ?>
