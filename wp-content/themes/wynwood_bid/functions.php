<?php

//update_option('siteurl','http://example.com/blog');
//update_option('home','http://example.com/blog');

// ======================== SIDEBARS ======================== 

// Registers a widgetized sidebar and replaces default WordPress HTML code with a better HTML
register_sidebar(array(
    'name' => 'Sidebar',
    'before_widget' => '<section>',
    'after_widget' => '</section>',
    'before_title' => '<h2>',
    'after_title' => '</h2>',
));

// Registers a widgetized sidebar and replaces default WordPress HTML code with a better HTML
register_sidebar(array(
    'name' => 'Footer',
    'before_widget' => '<section>',
    'after_widget' => '</section>',
    'before_title' => '<h2>',
    'after_title' => '</h2>',
));


// ======================== MENUS ======================== 
add_theme_support('menus');

// This theme uses wp_nav_menu()
function register_my_menus() {
	register_nav_menus(
		array(
			'menu-1' => __( 'Primary Nav' ),
			'menu-2' => __( 'Footer Nav' )
		)
	);
}
add_action( 'init', 'register_my_menus' );


// ======================== THUMBNAILS ======================== 

// Enables post-thumbnail support
add_theme_support('post-thumbnails');
set_post_thumbnail_size( 50, 50, true ); // Size of normal post thumbnails


// ======================== RICH TEXT EDITOR (TinyMCE) ======================== 

/* Change TinyMCE options to include HTML headers & reduce ability to change colors, etc. */

function change_mce_options( $init ) {

 /* Load our stylesheet */
 $init['content_css'] = get_bloginfo('template_directory') . "/style.css" . "," . get_bloginfo('template_directory') . "/editor-style.css";
 
 /* Customize TinyMCE buttons 
    Complete list: http://tinymce.moxiecode.com/wiki.php/Buttons/controls 
	
	Wordpress defaults, FYI:
	
	 $init["theme_advanced_buttons1"] = "bold,italic,strikethrough,|,bullist,numlist,blockquote,|,justifyleft,justifycenter,justifyright,|,link,unlink,wp_more,|,spellchecker,fullscreen,wp_adv";
	 $init["theme_advanced_buttons2"] = "formatselect,underline,justifyfull,forecolor,|,pastetext,pasteword,removeformat,|,charmap,|,outdent,indent,|,undo,redo,wp_help";
	 $init["theme_advanced_buttons3"]="";
 */
 
 $init['theme_advanced_buttons1'] = "formatselect,|,bold,italic,|,bullist,numlist,blockquote,|,outdent,indent,|,justifyleft,justifycenter,justifyright,|,link,unlink,|,spellchecker";
 $init["theme_advanced_buttons2"] = "";
 $init['theme_advanced_blockformats'] = 'p,h1,h2,h3,h4';
 $init['theme_advanced_disable'] = 'forecolor';
 $init['oninit'] = 'myCustomOnInit';
 
 if (isset( $init['extended_valid_elements'] )) { 
 $init['extended_valid_elements'] .= ",";
 };
 $init ['extended_valid_elements'] .= "canvas[id|width|height],script[src|type]," .
"object[classid|codebase|width|height|align|name|id],param[name|value],embed[quality|type|pluginspage|width|height|src|align]," .
"iframe[src|frameborder|width|height|scrolling|name|id]," .
"video[src|audio|autoplay|controls|width|height|loop|preload|poster],audio[src|autoplay|loop|controls|preload],source[id|src|type],";

return $init;
 }
add_filter('tiny_mce_before_init', 'change_mce_options');

function mce_plugins($plugins){

	$plugins[] = get_bloginfo('template_directory') ."/js/tinymce.js";
	return $plugins;
}

// ======================== COMMENT FORM ======================== 

// Removes inline CSS style for Recent Comments widget
function html5boilerplate_remove_recent_comments_style() {
	global $wp_widget_factory;
	remove_action( 'wp_head', array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ) );
}
add_action( 'widgets_init', 'html5boilerplate_remove_recent_comments_style' );

// Custom commments HTML
// Used in comments.php: wp_list_comments('callback=html5boilerplate_comment')
function html5boilerplate_comment($comment, $args, $depth) {
   $GLOBALS['comment'] = $comment; ?>
   <dt <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>-author">
      <?php /* echo get_avatar($comment,$size='54');*/ ?>
      <?php comment_author_link(); ?> <time datetime="">on <?php comment_date() ?> at <?php comment_time() ?></time> 
      <a href="#comment-<?php comment_ID() ?>" title="Permanent Link for this comment">#permalink</a>
   </dt>
   <dd <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>-body">
	<?php if ($comment->comment_approved == '0') : ?>
		<p><strong>Your comment is awaiting moderation.</strong></p>
	<?php endif; ?>
	<?php comment_text(); ?>
	<?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?> <?php edit_comment_link('Edit this comment', ' ', ''); ?>
	</dd>
	<?php 
}


// ======================== JAVASCRIPT ======================== 

// Put jQuery and Selectivizr at the footer; Modernizr in the header.
// Use our, more recent, version of jQuery

function boilerplate_scripts() {

    if (!is_admin()) {
        wp_deregister_script( 'jquery' );
        wp_register_script( 'jquery', get_bloginfo('template_directory') . '/js/jquery-1.12.1.min.js',array(),"1.12.1",true);
        wp_enqueue_script( 'jquery' );
	
		wp_register_script('modernizr',
		   get_bloginfo('template_directory') . '/js/modernizr-2.custom.cssonly.js',
		   array(),
		   '2.1',false);
		wp_enqueue_script('modernizr');
	
		wp_register_script('selectivizr',
		   get_bloginfo('template_directory') . '/js/selectivizr-1.0.2-min.js',
		   array('jquery'),
		   '1.0.2',true);
		wp_enqueue_script('selectivizr');

		wp_register_script('boilerplate_plugins',
		   get_bloginfo('template_directory') . '/js/plugins.js',
		   array(),
		   '1',true);
		wp_enqueue_script('boilerplate_plugins');

		wp_register_script('boilerplate_scripts',
		   get_bloginfo('template_directory') . '/js/script.js',
		   array(),
		   '1',true);
		wp_enqueue_script('boilerplate_scripts');

		// Add our CSS. Do it here instead of in the header.php file so WordPress or plugins can combine & minify CSS.

		wp_register_style(
        'boilerplate_style',
        get_bloginfo( 'stylesheet_directory' ) . '/style.css',
        array(), 1,"all");
        wp_enqueue_style( 'boilerplate_style' );

	}

}

add_action("init","boilerplate_scripts");



// ======================== UTILITY FUNCTIONS ======================== 


// Returns TRUE if more than one page exists. Useful for not echoing .post-navigation HTML when there aren't posts to page
function show_posts_nav() {
	global $wp_query;
	return ($wp_query->max_num_pages > 1);
}

// Add default posts and comments RSS feed links to head
add_theme_support('automatic-feed-links');

// Removes inline CSS style for Recent Comments widget
function html5boilerplate_post_thumbnail_html( $html ) {

	// strip out width and height, let css handle it.
	$html = preg_replace('/(width|height)="[0-9]+"[ ]*/', '', $html);

global $id;
$attachment_id = get_post_thumbnail_id( $id );
$attachment =& get_post($attachment_id);

	return '<figure title="'.$attachment->post_excerpt.'">' 
		. $html 
		. ( ( strlen($attachment->post_content) > 0 ) ? '<figcaption>'.apply_filters( 'the_content', $attachment->post_excerpt ).'</figcaption>' : '' )
		. '</figure>';
}
add_filter( 'post_thumbnail_html', 'html5boilerplate_post_thumbnail_html' );


// Adds a handy 'tag-cloud' class to the Tag Cloud Widget for better styling
function html5boilerplate_tag_cloud($tags) {
	$tag_cloud = '<aside class="tag-cloud">' . $tags . '</aside>';
	return $tag_cloud;
}
add_action('wp_tag_cloud', 'html5boilerplate_tag_cloud');


// ======================== GOOGLE MAPS API ======================== 

function my_acf_google_map_api( $api ){
	
	$api['key'] = 'AIzaSyB1h91LTtrATtXoLYLAwmUP1Sz3g6pheQ0';
	
	return $api;
	
}

add_filter('acf/fields/google_map/api', 'my_acf_google_map_api');
