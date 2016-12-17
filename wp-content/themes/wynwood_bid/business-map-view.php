<?php // Template Name: Business Directory - Map View ?>

<!-- GOOGLE MAPS API + ADVANCED CUSTOM FIELDS PLUGIN -->

	<!-- ACF Front End forms: Register the necessary assets (CSS/JS), process the saved data, and redirect the url
	https://www.advancedcustomfields.com/resources/create-a-front-end-form/ 
	Not sure why, but there is a conflict between ACF + The Events Calendar plugin
	When Google Maps API is registered in header.php or functions.php the plugin will not recognize it
	and when it is regesterd only via The Events Calendar plugin, ACF does not recognize it.
	Working as is, but there might be a better way. -->

	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB1h91LTtrATtXoLYLAwmUP1Sz3g6pheQ0"></script>

<?php get_header(); ?>

	<div class="hero-img" style="background: url(<?php bloginfo(template_url); ?>/images/layout/hero/wy_hero_images_businesses.jpg) no-repeat center center; background-size: cover;"></div>

	<div class="main-content business-index map-view clearfix">
		<a class="section-marker" href="<?php bloginfo('url'); ?>/businesses">Business Directory</a>
		<h1>Explore our 600&plus; businesses</h1>
		<div class="grid-map-view-toggle clearfix">
			<a href="<?php bloginfo('url'); ?>/explore/business-directory-grid-view/" class="grid-view">Grid View</a>
			<a href="<?php bloginfo('url'); ?>/explore/business-directory-map-view/" class="map-view active-view">Map View</a>
		</div>

			<aside class="filter-sidebar">
				<nav class="cat-list-toggle">
					<a class="active" href="<?php bloginfo('url'); ?>/explore/business-directory-map-view/">Categories</a>
					<a href="#">Results</a>
				</nav>
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

			<div class="results map-view">

				<div class="acf-map">

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
							$business_address = get_field('business_address');
							 ?>

							<!-- MAP STUFF!!!!! -->

							<?php if( !empty($business_address) ): ?>
								<div class="marker" data-lat="<?php echo $business_address['lat']; ?>" data-lng="<?php echo $business_address['lng']; ?>">
									<h5><?php 
										// custom posttaxonomy
										$custom_terms = get_the_terms( $post->ID , 'business_type' );
										foreach ( $custom_terms as $custom_term ) {
										echo $custom_term->name;
										}
									 ?></h5>
									<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
									<p class="address"><?php echo $business_address['address']; ?></p>
								</div>
							<?php endif; ?>
							
							<!-- end MAP STUFF!!!!! -->
							<div class="single-listing">
								<?php setup_postdata( $post->ID ) ?>
								<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>	
							</div>
							<?php endforeach; ?>
									
						<?php wp_reset_postdata(); ?>
					<?php endif; ?>

					</div> <!-- end map container -->

			</div> <!-- end .results.list-view -->

	</div> <!-- end .main-content -->
<?php get_footer(); ?>