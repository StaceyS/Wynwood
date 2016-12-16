<!-- If a user is not logged in, force them to create an account to unlock favorite functionality. See 'Favorite Posts' plugin site for details https://favoriteposts.com/ -->

<?php if ( is_user_logged_in() ) { ?>
	<?php echo do_shortcode('[favorite_button post_id="" site_id=""]'); ?>
<?php } else { ?>
	<button id="favorite-create-account" class="biz-cta-button">Favorite  <i class="fa fa-heart-o" aria-hidden="true"></i></button>
<?php  } ?>