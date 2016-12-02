<?php get_header(); ?>

<?php if (have_posts()) : ?>
	<?php while (have_posts()) : the_post(); ?>
	
  		<h1><a href="<?php bloginfo('url'); ?>"><?php bloginfo('name'); ?></a></h1>

  		<?php the_content(); ?>


	<?php endwhile; ?>		
<?php endif; ?>
	
<?php //get_sidebar(); ?>
<?php get_footer(); ?>
