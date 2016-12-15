<?php //Template Name: Account Dashboard ?> 
<?php get_header(); ?>

<div class="hero-img" style="background: url(<?php bloginfo(template_url); ?>/images/layout/hero/wy_hero_images_businesses.jpg) no-repeat center center; background-size: cover;"></div>

<div class="main-content account-dashboard clearfix">
	<a class="section-marker" href="<?php bloginfo('url'); ?>/community/my-account">My Account</a>

	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
		
			<h1>My Wynwood Account</h1>
	  		<?php //the_content(); ?>
	  		<div class="account-dashbaord-section">
	  			<img title="My Business" alt="My Business" src="<?php bloginfo('template_url'); ?>/images/icons/icon_business.svg" >
	  			<h2>My Business</h2>
	  			<ul>
	  				<li>Edit your buinsess details</li>
					<li>Add a new photo</li>
					<li>Add a Deal callout</li>
	  			</ul>
	  			<a class="biz-cta-button" href="<?php bloginfo('url'); ?>/businesses/my-test-business/">View/Edit Business</a>
	  		</div>

	  		<div class="account-dashbaord-section">
	  			<img title="My Events" alt="My Events" src="<?php bloginfo('template_url'); ?>/images/icons/icon_events.svg">
	  			<h2>My Events</h2>
	  			<ul>
					<li>Submit a new event</li>
					<li>Edit events</li>
					<li>Manage past events</li>
	  			</ul>
	  			<a class="biz-cta-button" href="<?php bloginfo('url'); ?>/events/community/list">Manage Events</a>
	  		</div>

	  		<div class="account-dashbaord-section my-account">
	  			<img title="My Account" alt="My Account" src="<?php bloginfo('template_url'); ?>/images/icons/icon_account.svg">
	  			<h2>My Account</h2>
	  			<p><a href="<?php bloginfo('url'); ?>/community/lostpassword/">Reset Password</a>
	  			<a href="#">Manage Email Preferences</a>
	  			<a href="<?php echo wp_logout_url(); ?>">Log Out</a></p>
	  			<a class="biz-cta-button" href="<?php bloginfo('url'); ?>/my-profile">Edit Profile</a>
	  		</div>

		<?php endwhile; ?>		
	<?php endif; ?>

</div> <!-- end .main-content -->

<?php get_footer(); ?>
