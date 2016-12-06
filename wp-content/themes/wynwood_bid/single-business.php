<?php  /* Template Name: Business Detail */ ?>  

<!-- ACF Front End forms: Register the necessary assets (CSS/JS), process the saved data, and redirect the url
	 https://www.advancedcustomfields.com/resources/create-a-front-end-form/ 
	 
	 *** This is currently causing issues with the Google Maps display in the front end form/post editor
	 *** Google maps display fine w/o this on both front end & default WP editor (dashboard)

	 Maybe this plugin can help? https://wordpress.org/plugins/api-key-for-google-maps/screenshots/
	 -->
<?php acf_form_head(); ?>

<?php get_header(); ?>
	<div class="hero-img" style="background: url(<?php bloginfo(template_url); ?>/images/layout/hero/wy_hero_images_businesses.jpg) no-repeat center center; background-size: cover;"></div>

	<div class="main-content biz-detail-pg clearfix">
		<span class="section-marker">Business Directory</span>

		<?php
			// Check to see if the current site visitor is a registered WP user
		    $current_user = wp_get_current_user();

		    // Safe usage:https://developer.wordpress.org/reference/functions/wp_get_current_user/ 
		    if ( !($current_user instanceof WP_User) ) {
			      return;
		    }

		    else {
			    // echo 'Username: ' . $current_user->user_login . '<br />';
			    // echo 'User email: ' . $current_user->user_email . '<br />';
			    // echo 'User first name: ' . $current_user->user_firstname . '<br />';
			    // echo 'User last name: ' . $current_user->user_lastname . '<br />';
			    // echo 'User display name: ' . $current_user->display_name . '<br />';
			    // echo 'User ID: ' . $current_user->ID . '<br />';
			}
			 
			 $user_info = get_userdata($current_user->ID);
		      // echo 'Username: ' . $user_info->user_login . "\n";
		      // echo 'User roles: ' . implode(', ', $user_info->roles) . "\n";
		      // echo 'User ID: ' . $user_info->ID . "\n";
		?>
		
		<?php 
			// If not logged in, hide preview/edit options
			// **** This needs some troubleshooting
		    if ( !($current_user instanceof WP_User) ) {
			      return false;
		    } 
		    // Otherwise, if logged in, show listing preview/edit options
		    else {
		    	
		    	if ( is_user_logged_in() ) {
					//echo 'Username: ' . $current_user->user_login . '<br />';
			    	echo "<div class='toggle-profile-edit'>";
					echo "<h3>Hi " . $current_user->user_login . ", Youre Logged In.</h3>";
					echo "<a class='edit-profile' href='#'>Edit Business Details</a>";
					echo "</div>"; 
					}
				else {
					//echo 'Visitor is not logged in.';
					}

		    	} 
		?>


		<div class="biz-details-wrapper">

			<div class="biz-details clearfix">

			<?php if (have_posts()) : ?>
				<?php while (have_posts()) : the_post(); ?>

					<?php 
						// Wynwood Business Custom Field Group 
						// global vars
						$profile_image = get_field('profile_image');
						$business_description = get_field('business_description');
						$business_address = get_field('business_address');
						$biz_website = get_field('website');
						$biz_email = get_field('email_address');
						$biz_phone = get_field('phone');

						// display phone number in format XXX.XXX.XXXX
						$biz_phone = substr($biz_phone, 0, 3).".".substr($biz_phone, 3, 3).".".substr($biz_phone,6);

						// repeaters 
						$deals = get_field('deals');
						$operating_hours = get_field('operating_hours');
						$social_media = get_field('social_media');
					 ?>

					<!-- Display deal if it exists -->
					<?php if( have_rows('deals') ): ?>
						<?php while( have_rows('deals') ): the_row(); 

							// deals vars
							$deal_title = get_sub_field('deal_title');
							$deal_description = get_sub_field('deal_description');
							$deal_start_date = get_sub_field('deal_start_date', false, false);
							$deal_end_date = get_sub_field('deal_end_date', false, false);

							// convert date data to text display
							//$deal_start_date = mktime($deal_start_date);

							?>

							<div class="deal-wrapper">
								<?php
									// only display a deal if today's date is on or after the deal start date, 
									// and on or before the deal end date
									//echo $deal_start_date; ?>
								<?php //echo date("M", $deal_start_date); ?>
								<?php //echo "Today is " . date("M/d/Y"); ?>
								<?php //echo $deal_end_date; ?>
								<h4>Deal</h4>
								<h2><?php echo $deal_title; ?></h2>
								<div class="deal-description"><?php echo $deal_description; ?></div>
							</div>
						<?php endwhile; ?>
					<?php endif; ?>

						<div class="biz-summary">
							<img class="biz-profile-img" src="<?php echo $profile_image['url']; ?>" alt="<?php echo $profile_image['alt']; ?>" />
							<h1><?php the_title(); ?></h1>
							<?php echo $business_description; ?>
						</div>
					</div>

					<aside class="biz-detail-sidebar">
						<section class="sidebar-map">

							<?php if( !empty($business_address) ): ?>
								<div class="acf-map">
									<div class="marker" data-lat="<?php echo $business_address['lat']; ?>" data-lng="<?php echo $business_address['lng']; ?>"></div>
								</div>
								<?php endif; ?>

							<!-- Not sure how to pull link to google maps. Tried echo $business_address['url']; -->
							<p><a class="biz-full-address" href="#" target="blank"><?php echo $business_address['address']; ?></a></p>
						</section>
						<section class="operating-hrs">
							<h3>Hours</h3>

							<?php if( have_rows('operating_hours') ): ?>
								<?php while( have_rows('operating_hours') ): the_row(); ?>

									<ul class="hours">

									<?php if( have_rows('time_blocks') ): ?>
									<?php while( have_rows('time_blocks') ): the_row(); 

										// time_blocks vars
										$start_day = get_sub_field('start_day');
										$end_day = get_sub_field('end_day');
										$start_time = get_sub_field('start_time');
										$start_time_mins = get_sub_field('start_time_mins');
										$am_pm_start = get_sub_field('am_pm_start');
										$end_time = get_sub_field('end_time');
										$stop_time_mins = get_sub_field('stop_time_mins');
										$am_pm_stop = get_sub_field('am_pm_stop');

										// Fix ACF from defaulting to changing "00" to "0"
										if ($start_time_mins <= 0 ) {
											$start_time_mins = "00";
											}

										if ($stop_time_mins <= 0 ) {
											$stop_time_mins = "00";
											}

										?>

										<li class="time_block">
										   	<?php echo $start_day;
											
											// If start & end day are the same, only show start day
											if ($start_day != $end_day ) { echo " - " . $end_day . ": "; }
										   	else { echo ": "; }
										   	
										   	?>

										   	<?php echo $start_time; ?>:<?php echo $start_time_mins . " " . $am_pm_start; ?> - <?php echo $end_time; ?>:<?php echo $stop_time_mins . " " . $am_pm_stop; ?>
										</li>

										<?php endwhile; ?>
									<?php endif; ?>

									</ul>

								<?php endwhile; ?>
							<?php endif; ?>
						</section>

						<section class="biz-contact">
							<h3>Contact</h3>
							<p class="biz-website"><a href="<?php echo $biz_website; ?>" target="blank">Website</a></p>
							<p class="biz-email"><a href="<?php echo $biz_email; ?>"><?php echo $biz_email; ?></a></p>
							<p class="phone"><a href="<?php echo $biz_phone; ?>"><?php echo $biz_phone; ?></a></p>
						</section>

						<!-- Display social links section only when at least one social account exists -->
						<?php if( have_rows('social_media') ): ?>
							<?php while( have_rows('social_media') ): the_row(); 

								// $social_media vars
								$facebook = get_sub_field('facebook');
								$twitter = get_sub_field('twitter');
								$instagram = get_sub_field('instagram');
								$yelp = get_sub_field('yelp');
							?>
								<section class="social-links">
									<h3>Follow Us</h3>
									<div class="social-icons">
										
										<!-- Only show icons for existing social accounts -->
										<?php if ($facebook): ?>
											<a href="<?php echo $facebook; ?>" target="_blank" title="Like <?php the_title(); ?> on Facebook"><i class="fa fa-facebook" aria-hidden="true"></i></a>
										<?php endif ?>

										<?php if ($twitter): ?>
											<a href="<?php echo $twitter; ?>" target="_blank" title="Follow <?php the_title(); ?> on Twitter"><i class="fa fa-twitter" aria-hidden="true"></i></a>
										<?php endif ?>

										<?php if ($instagram): ?>
											<a href="<?php echo $instagram; ?>" target="_blank" title="Follow <?php the_title(); ?> on Instagram"><i class="fa fa-instagram" aria-hidden="true"></i></a>
										<?php endif ?>

										<?php if ($yelp): ?>
											<a href="<?php echo $yelp; ?>" target="_blank" title="Review <?php the_title(); ?> on Yelp"><i class="fa fa-yelp" aria-hidden="true"></i></a>
										<?php endif ?>

								    </div>
								</section>

								<section>
									<a class="biz-cta-button" href="#">Get Directions <i class="fa fa-compass" aria-hidden="true"></i></a>
									<!-- See 'Favorite Posts' plugin site for details https://favoriteposts.com/ -->
									<?php echo do_shortcode('[favorite_button post_id="" site_id=""]'); ?>
								</section>
								

							<?php endwhile; ?>
						<?php endif; ?>	
						</aside>
					</div> <!-- end .biz-details-wrapper -->

				<!-- Front End Edit Business Details -->
				<div class="edit-biz-details-form">

					<h2>Editing: <?php the_title(); ?></h2>
					<!-- *** See 'acf_form_head();' on line 9 for Google Maps API/display issues
						 *** Also having issues with page redirect after form submission. Although URL looks -->
					<?php //acf_form(); ?>

					<?php $options = array( 
						/* (string) The text displayed on the submit button */
						'submit_value' => __("Update Profile", 'acf')
						/* (string) The URL to be redirected to after the form is submit. Defaults to the current URL with a GET parameter '?updated=true'. A special placeholder '%post_url%' will be converted to post's permalink (handy if creating a new post) */
						//'return' => '%post_url%'
						);?>

					<?php acf_form( $options ); ?>

				</div>
				
	
			<?php endwhile; ?>
		<?php endif; ?>

	</div> <!--  end .main-content -->

<?php //get_sidebar(); ?>
<?php get_footer(); ?>
