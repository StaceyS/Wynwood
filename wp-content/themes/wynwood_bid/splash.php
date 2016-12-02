<?php  /* Template Name: Splash */ ?>  

<?php get_header(); ?>

	<div class="splash-bg" style="background: url('<?php echo get_bloginfo('template_url'); ?>/images/layout/background/wy_fullwidth_bg_splash.jpg') no-repeat top center; background-size: cover;">
	</div>

	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>

		<div class="sidebar">
			<img class="logo" src="<?php echo get_bloginfo('template_url'); ?>/images/layout/WYNWOOD_Logo_4-Color.svg">

			<section class="intro">
				<h2>Be on the lookout for our new website launching soon.</h2>
			</section>

			<section class="contact">
				<h3>For inquiries email</h3>
				<p><a href="mailto:info@wynwoodbid.com" target="blank">info@wynwoodbid.com</a></p>
			</section>

			<section class="social">
				<h3>Connect with Us</h3>
			    <div class="social-icons">
		    		<a href="https://www.facebook.com/WynwoodMiami" target="_blank" title="Like Wynwood on Facebook"><i class="fa fa-facebook" aria-hidden="true"></i></a>
		        	<a href="https://twitter.com/WynwoodMiami" target="_blank" title="Follow Wynwood on Twitter"><i class="fa fa-twitter" aria-hidden="true"></i></a>
		       		<a href="https://www.instagram.com/WynwoodMiami/" target="_blank" title="Follow Wynwood on Instagram"><i class="fa fa-instagram" aria-hidden="true"></i></a>
			    </div>
			</section>

			<section class="email-signup">
				<h3>Get Email Updates</h3>
				<!-- Begin MailChimp Signup Form -->
				<div id="mc_embed_signup">
				<form action="//asgk.us4.list-manage.com/subscribe/post?u=24211ad86584d889f9f75b438&amp;id=0cddedc89a" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
				    <div id="mc_embed_signup_scroll">
					
					<div id="mce-responses" class="clear">
						<div class="response" id="mce-error-response" style="display:none"></div>
						<div class="response" id="mce-success-response" style="display:none"></div>
					</div>    <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
				    <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_24211ad86584d889f9f75b438_0cddedc89a" tabindex="-1" value=""></div>
				    <input type="email" value="" name="EMAIL" class="required email" id="mce-EMAIL">
				    <input type="submit" value="Submit" name="subscribe" id="mc-embedded-subscribe" class="button">
				    </div>
				</form>
				</div>

				<!--End mc_embed_signup-->
			</section>
		</div>
		<div class="slant-back"></div>

		<?php //the_content(); ?>

		<?php endwhile; ?>		
	<?php endif; ?>
		
<?php //get_sidebar(); ?>
<?php get_footer(); ?>
