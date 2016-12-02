    
   	<footer class="footer clearfix">
   		<section class="branding">
	   		<a class="header-logo" href="<?php bloginfo('url'); ?>" title="<?php bloginfo('name'); ?>"><img src="<?php bloginfo('template_url'); ?>/images/layout/wynwood_logo_knockedout_nobg.svg"></a>

	   		<p>&copy; <script>document.write(new Date().getFullYear())</script> <?php echo get_bloginfo('name'); ?></p>	
   		</section>
  		
		<?php //dynamic_sidebar("Footer"); ?>

		<?php wp_nav_menu( array( 
  				'theme_location' => 'menu-2',
  				'sort_column' => 'menu_order',
  				'container' => 'false' 
  			) ); ?>

  		<section>  			
	  		<div class="social-links">
	  			<h3>Connect with Us</h3>
	  			<a href="https://www.facebook.com/WynwoodMiami" target="blank"><i class="fa fa-facebook" aria-hidden="true"></i></a>
	  			<a href="https://twitter.com/WynwoodMiami" target="blank"><i class="fa fa-twitter" aria-hidden="true"></i></a>
	  			<a href="https://www.instagram.com/WynwoodMiami/" target="blank"><i class="fa fa-instagram" aria-hidden="true"></i></a>
	  			<a href="#" target="blank"><i class="fa fa-snapchat-ghost" aria-hidden="true"></i></a>
	  		</div>

	  		<div class="email-signup">
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
			</div>
  		</section>

	</footer>

	</div> <!-- end of #container -->
  
	<!-- Add GOOGLE ANALYTICS CODE HERE	-->

	<?php wp_footer(); ?>
	<!-- ^^ Do not remove! ^^ Required for login bar & plugins which generally use this hook to reference JavaScript files. -->
	
</body>
</html>
