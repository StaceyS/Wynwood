<!doctype html>
<!--[if IE 9 ]>    <html <?php language_attributes(); ?> class="no-js ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html <?php language_attributes(); ?> class="no-js"> <!--<![endif]-->
<head>

  <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame 
       Remove this if you use the .htaccess -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  
  <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

  <title><?php wp_title('&laquo;', true, 'right'); ?> <?php bloginfo('name'); ?></title>
  <meta name="description" content="The Wynwood Arts District located in Miami, Florida is home to a community of Art Galleries, Retail Stores, Antique Shops, Eclectic Bars, and one of the largest open-air street-art installations in the world.">
  <meta name="author" content="Wynwood Business Improvement District">

  <!-- Twitter Card data -->
  <!-- Documentation at https://dev.twitter.com/cards/types/summary-large-image -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:site" content="@WynwoodMiami">
  <meta name="twitter:title" content="Wynwood Business Improvement District - Miami, Florida">
  <meta name="twitter:description" content="The Wynwood Arts District located in Miami, Florida is home to a community of Art Galleries, Retail Stores, Antique Shops, Eclectic Bars, and one of the largest open-air street-art installations in the world.">
  <meta name="twitter:creator" content="@WynwoodMiami">
  <meta name="twitter:image" content="<?php bloginfo('template_url'); ?>/images/theme/wynwood_tw_imagecard.jpg">

  <!-- Facebook Open Graph data -->
  <!-- Documentation at https://developers.facebook.com/docs/sharing/best-practices -->
  <meta property="og:title" content="Wynwood Business Improvement District - Miami, Florida" />
  <meta property="og:type" content="website" />
  <meta property="og:url" content="http://www.wynwoodmiami.com/" />
  <meta property="og:image" content="<?php bloginfo('template_url'); ?>/images/theme/wynwood_fb_opengraph_image.jpg" />
  <meta property="og:description" content="The Wynwood Arts District located in Miami, Florida is home to a community of Art Galleries, Retail Stores, Antique Shops, Eclectic Bars, and one of the largest open-air street-art installations in the world." />

  <!--  Mobile viewport optimized: j.mp/bplateviewport -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
  <link rel="shortcut icon" href="<?php bloginfo('url'); ?>/favicon.png">
  <link rel="apple-touch-icon" href="<?php bloginfo('template_url'); ?>/images/theme/wynwood_apple_touch_icon.png">

  <!-- Fonts: Google, Font Awesome -->
  <link href="https://fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:400,400i,700,700i" rel="stylesheet">
  <script src="https://use.fontawesome.com/b15e86bc43.js"></script>

  <!-- Pingback url -->
  <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

  <?php wp_head(); ?>

  <!-- GOOGLE MAPS API + ADVANCED CUSTOM FIELDS PLUGIN -->
  <!-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB1h91LTtrATtXoLYLAwmUP1Sz3g6pheQ0"></script> -->

</head>

<body class="<?php echo $post->post_name; ?>">

    <?php include 'modals.php'; ?>

    <header class="header clearfix">
  		<a class="header-logo" href="<?php bloginfo('url'); ?>" title="<?php bloginfo('name'); ?>"><img src="<?php bloginfo('template_url'); ?>/images/layout/wynwood_logo_knockedout.svg"></a>
  			<?php wp_nav_menu( array( 
  				'theme_location' => 'menu-1',
  				'sort_column' => 'menu_order',
  				'container' => 'false' 
  			) ); ?>

      <!-- Current user info -->
      <?php $user_info = get_userdata($current_user->ID); 
        //$user_info = get_userdata($current_user->ID);
        // echo 'Username: ' . $user_info->user_login . "\n";
        // echo 'User roles: ' . implode(', ', $user_info->roles) . "\n";
        // echo 'User ID: ' . $user_info->ID . "\n";
      ?>

      <!-- Utility Nav -->
      <div class="utility-nav">
        <?php if ( !is_user_logged_in() ) { ?>
           <a class="login-link" title="User Log In" href="http://localhost:8888/community/login/"><img src="<?php bloginfo('template_url'); ?>/images/icons/icon_user.svg"></a>
        <?php } else { ?>
            <a class="logout-link" title="User Log Out" href="<?php echo wp_logout_url(); ?>"><img src="<?php bloginfo('template_url'); ?>/images/icons/icon_user.svg"><span>Sign Out</span></a>
            <a class="favorites-link" href="<?php bloginfo('url'); ?>/my-favorites" title="My Favorites"><span><i class="fa fa-heart" aria-hidden="true"></i><?php echo do_shortcode('[user_favorite_count user_id="'.$user_info.'" site_id=""]'); ?></span></a>
            <a class="my-account-link" href="<?php bloginfo('url'); ?>/my-account" title="My Account"><span><i class="fa fa-cog" aria-hidden="true"></i></span></a>
        <?php  } ?>
          <span class="v-rule"></span><a class="search-icon" title="Search the Wynwood website" href="#"><img src="<?php bloginfo('template_url'); ?>/images/icons/icon_search.svg"></a>       
 
      </div>
      <?php dynamic_sidebar('Utility Nav'); ?>

    </header>
        
