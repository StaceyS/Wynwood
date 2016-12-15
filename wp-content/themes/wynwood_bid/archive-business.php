<?php // Business Directory - List & Map View?>

<?php get_header(); ?>

	<div class="hero-img" style="background: url(<?php bloginfo(template_url); ?>/images/layout/hero/wy_hero_images_businesses.jpg) no-repeat center center; background-size: cover;"></div>

	<div class="main-content business-index clearfix">
		<a class="section-marker" href="<?php bloginfo('url'); ?>/businesses">Business Directory</a>
		<h1>Explore our 600&plus; businesses</h1>
		<div class="grid-map-view-toggle">
			<button class="grid-view active-view">Grid View</button>
			<button class="map-view">Map View</button>
		</div>

			<aside class="filter-sidebar">
				<!-- <h2>Business Type:</h2>   -->
			    <?php
				//list terms in a given taxonomy
				$taxonomy = 'business_type';
				$tax_terms = get_terms($taxonomy);
				?>
				<ul>
					<li><a class="active" href="<?php bloginfo('url'); ?>/businesses">View All</a></li>
					<?php
					foreach ($tax_terms as $tax_term) {
						echo '<li>' . '<a href="' . esc_attr(get_term_link($tax_term, $taxonomy)) . '" title="' . sprintf( __( "View all posts in %s" ), $tax_term->name ) . '" ' . '>' . $tax_term->name.'</a></li>';
						} ?>
				</ul>
			</aside>  

			<div class="results list-view">
				
				<?php // Display entries for custom post type 'business'

				$posts = get_posts(array(
					'posts_per_page'	=> -1,
					'post_type'			=> 'business'	
					));

				if( $posts ): ?>	
					<?php foreach( $posts as $post ): ?>
						<?php 

						// Wynwood Business Custom Field Group 
						// global vars
						$profile_image = get_field('profile_image');
						$biz_short_description = get_field('biz_short_description');
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
			
						<div class="single-listing">
							<?php setup_postdata( $post->ID ) ?>
							
							<div class="summary-card">
								<h5><?php 
									// custom posttaxonomy
									$custom_terms = get_the_terms( $post->ID , 'business_type' );
									foreach ( $custom_terms as $custom_term ) {
									echo $custom_term->name;
									}
								 ?></h5>
								<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>	
								<a class="biz-profile-img" style="background: #ccc url(<?php echo $profile_image['url']; ?>) no-repeat center center; background-size: cover;" href="<?php the_permalink(); ?>" >
									<!-- Display deal icon if one exists -->
									<?php if( have_rows('deals') ): ?>
										<?php while( have_rows('deals') ): the_row(); 
											// deals vars
											//$deal_title = get_sub_field('deal_title');
											$deal_start_date = get_sub_field('deal_start_date', false, false);
											$deal_end_date = get_sub_field('deal_end_date', false, false);
											// ** Make this deal display dynamically based on start/end date
											?>
											<div class="deal-icon-wrapper">
												<i class="fa fa-usd" aria-hidden="true"></i>
												<span>Deal</span>
												<img src="<?php bloginfo('template_url');?>/images/icons/icon_deal_flag_bottom.svg" />
											</div>
										<?php endwhile; ?>
									<?php endif; ?>
								</a>
								<a href="#" class="toggle-biz-details"><i class="fa fa-plus" aria-hidden="true"></i></a>
							</div>
							<div class="summary-card-details clearfix">
								<aside class="biz-detail-sidebar">
									<!-- Not sure how to pull link to google maps. Tried echo $business_address['url']; -->
									<p><a class="biz-full-address" href="#" target="blank"><?php echo $business_address['address']; ?></a></p>
									<p class="biz-website"><a href="<?php echo $biz_website; ?>" target="blank">Website</a></p>
									<p class="biz-email"><a href="<?php echo $biz_email; ?>"><?php echo $biz_email; ?></a></p>
									<p class="phone"><a href="<?php echo $biz_phone; ?>"><?php echo $biz_phone; ?></a></p>
									<!-- operating hours -->
									<?php if( have_rows('operating_hours') ): ?>
										<?php while( have_rows('operating_hours') ): the_row(); ?>

											<ul class="hours">
												<h3>Hours</h3>

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
									<!-- <a class="biz-cta-button" href="#" title="Add to Favorites">Favorite <i class="fa fa-heart" aria-hidden="true"></i></a> -->
								</aside>

								<div class="biz-summary">
									<h2><?php the_title(); ?></h2>
									<p><?php echo $biz_short_description;?></p>
									<a class="learn-more-link" href="<?php the_permalink(); ?>">Learn More <i class="fa fa-angle-right" aria-hidden="true"></i></a>
								</div>		
							</div>
						</div>
						<?php endforeach; ?>
								
					<?php wp_reset_postdata(); ?>
				<?php endif; ?>

			</div> <!-- end .results.list-view -->

	</div> <!-- end .main-content -->
<?php get_footer(); ?>