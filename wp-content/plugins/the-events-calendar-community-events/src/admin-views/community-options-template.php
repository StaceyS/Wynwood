<?php

// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Tribe Community Events settings
 *
 * @package Tribe__Events__Community__Main
 * @since  1.0
 * @author Modern Tribe Inc.
 */

$tce = Tribe__Events__Community__Main::instance();

$blockRolesList = $tce->getOption( 'blockRolesList' );

if ( empty( $blockRolesList ) ) {
	$blockRolesList = array();
}

$redirectRoles = array();

foreach ( get_editable_roles() as $role => $atts ) {
	//don't let them lock admins out
	if ( $role == 'administrator' )
		continue;

	$redirectRoles[ $role ] = $atts['name'];
}

$statuses = array(
	'draft' => 'Draft',
	'pending' => 'Pending Review',
	'publish' => 'Published',
);
$statuses = apply_filters( 'tribe_community_events_default_status_options', $statuses );

$trash_vs_delete_options = array(
	'1' => __( 'Placed in the Trash', 'tribe-events-community' ),
	'0' => __( 'Permanently Deleted', 'tribe-events-community' ),
);

$venue_options = array();
$organizer_options = array();
$organizers = Tribe__Events__Main::instance()->get_organizer_info();
if ( is_array( $organizers ) && ! empty( $organizers ) ) {
	$organizer_options[0] = __( 'No Default', 'tribe-events-community' );
	foreach ( $organizers as $organizer ) {
		$organizer_options[ $organizer->ID ] = $organizer->post_title;
	}
}

$venues = Tribe__Events__Main::instance()->get_venue_info();
if ( is_array( $venues ) && ! empty( $venues ) ) {
	$venue_options[0] = __( 'Use New Venue/No Default', 'tribe-events-community' );
	foreach ( $venues as $venue ) {
		$venue_options[ $venue->ID ] = $venue->post_title;
	}
}

$communityTab = array(
	'priority' => 36,
	'fields' => array(
		'info-start' => array(
			'type' => 'html',
			'html' => '<div id="modern-tribe-info">',
		),
		'info-box-title' => array(
			'type' => 'html',
			'html' => '<h3>' . __( 'Community Events Settings', 'tribe-events-community' ) . '</h3>',
		),
		'info-box-description' => array(
			'type' => 'html',
			'html' => sprintf(
				__( '<p>Community Events enables users to submit events through a form on your site. Whether soliciting contributions from anonymous users or registered members, you have complete editorial control over what makes it onto the calendar.</p><p>Check out our <a href="%s">Community Events New User Primer</a> for information on configuring and using the plugin.', 'tribe-events-community' ),
				( isset( Tribe__Events__Main::$tecUrl ) ? Tribe__Events__Main::$tecUrl : Tribe__Events__Main::$tribeUrl ) . 'knowledgebase/new-user-primer-community-events/?utm_campaign=in-app&utm_medium=plugin-community&utm_source=communitytab'
			),
		),
		'info-end' => array(
			'type' => 'html',
			'html' => '</div><div class="tribe-settings-form-wrap">',
		),
		'general-heading' => array(
		'type' => 'heading',
		'label' => __( 'General', 'tribe-events-community' ),
		'parent_option' => Tribe__Events__Community__Main::OPTIONNAME,
		),

		'allowAnonymousSubmissions' => array(
			'type' => 'checkbox_bool',
			'label' => __( 'Allow anonymous submissions', 'tribe-events-community' ),
			'tooltip' => __( 'Check this box to allow users to submit events without having a WordPress account', 'tribe-events-community', 'tribe-events-community' ),
			'default' => false,
			'validation_type' => 'boolean',
			'parent_option' => Tribe__Events__Community__Main::OPTIONNAME,
		),

		'useVisualEditor' => array(
			'type' => 'checkbox_bool',
			'label' => __( 'Use visual editor for event descriptions', 'tribe-events-community' ),
			'default' => false,
			'validation_type' => 'boolean',
			'parent_option' => Tribe__Events__Community__Main::OPTIONNAME,
		),

		'defaultStatus' => array(
			'type' => 'dropdown',
		 	'label' => __( 'Default status for submitted events', 'tribe-events-community' ),
			'validation_type' => 'options',
			'size' => 'medium',
			'default' => 'draft',
			'options' => $statuses,
			'parent_option' => Tribe__Events__Community__Main::OPTIONNAME,
		),

		'communityRewriteSlug' => array(
			'type' => 'text',
		 	'label' => __( 'Community rewrite slug', 'tribe-events-community' ),
			'validation_type' => 'slug',
			'size' => 'medium',
			'default' => 'community',
			'tooltip' => __( 'The slug used for building the community events URL - it is appended to the main events slug. Your current community events URLs are', 'tribe-events-community' ),
			'parent_option' => Tribe__Events__Community__Main::OPTIONNAME,
		),

		'current-community-events-slug' => array(
			'type' => 'html',
			'display_callback' => 'tribe_display_current_community_events_slug',
			'conditional' => ( '' != get_option( 'permalink_structure' ) ),
		),


	'alerts-heading' => array(
		'type' => 'heading',
		'label' => __( 'Alerts', 'tribe-events-community' ),
	),

		'emailAlertsEnabled' => array(
			'type' => 'checkbox_bool',
			'label' => __( 'Send an email alert when a new event is submitted', 'tribe-events-community' ),
			'default' => false,
			'validation_type' => 'boolean',
			'parent_option' => Tribe__Events__Community__Main::OPTIONNAME,
		),

		'emailAlertsList' => array(
			'type' => 'textarea',
			'label' => __( 'Email addresses to be notified', 'tribe-events-community' ),
			'default' => get_option( 'admin_email' ),
			'validation_type' => 'html',
			'tooltip' => __( 'One address per line', 'tribe-events-community' ),
			'parent_option' => Tribe__Events__Community__Main::OPTIONNAME,
		),

		'member-heading' => array(
			'type' => 'heading',
			'label' => __( 'Members', 'tribe-events-community' ),
		),

		'member-info' => array(
			'type' => 'html',
			'html' => '<p>'. __( 'Control the permissions for your logged in users. Allow them to:', 'tribe-events-community' ) .'</p>',
		),

		'allowUsersToEditSubmissions' => array(
			'type' => 'checkbox_bool',
			'label' => __( 'Edit their submissions', 'tribe-events-community' ),
			'default' => false,
			'validation_type' => 'boolean',
			'parent_option' => Tribe__Events__Community__Main::OPTIONNAME,
		),

		'allowUsersToDeleteSubmissions' => array(
			'type' => 'checkbox_bool',
			'label' => __( 'Remove their submissions', 'tribe-events-community' ),
			'default' => false,
			'validation_type' => 'boolean',
			'parent_option' => Tribe__Events__Community__Main::OPTIONNAME,
		),

		'trashItemsVsDelete' => array(
			'type' => 'radio',
			'label' => __( 'Deleted events should be:', 'tribe-events-community' ),
			'options' => $trash_vs_delete_options,
			'default' => '1',
			'validation_type' => 'options',
			'parent_option' => Tribe__Events__Community__Main::OPTIONNAME,
		),

		'myevents-heading' => array(
			'type' => 'heading',
			'label' => __( 'My Events', 'tribe-events-community' ),
		),

		'eventsPerPage' => array(
			'type' => 'text',
			'label' => __( 'Events per page', 'tribe-events-community' ),
			'tooltip' => __( 'This is the number of events displayed per page', 'tribe-events-community' ),
			'default' => 10,
			'validation_type' => 'positive_int',
			'parent_option' => Tribe__Events__Community__Main::OPTIONNAME,
		 ),

		'eventListDateFormat' => array(
			'type' => 'text',
			'label' => __( 'Date/time format', 'tribe-events-community' ),
			'tooltip' => '<a href="http://codex.wordpress.org/Formatting_Date_and_Time" target="_blank">' . __( 'Help with date &amp; time formatting', 'tribe-events-community' ) . '</a>',
			'default' => 'M j, Y',
			'validation_type' => 'html',
			'parent_option' => Tribe__Events__Community__Main::OPTIONNAME,
		 ),

		'access-heading' => array(
			'type' => 'heading',
			'label' => __( 'Access Control', 'tribe-events-community' ),
		),

		'blockRolesFromAdmin' => array(
			'type' => 'checkbox_bool',
			'label' => __( 'Block access to WordPress dashboard', 'tribe-events-community' ),
			'tooltip' => __( 'Also disables the admin bar', 'tribe-events-community' ),
			'default' => false,
			'validation_type' => 'boolean',
			'parent_option' => Tribe__Events__Community__Main::OPTIONNAME,
		),

		'blockRolesList' => array(
			'type' => 'checkbox_list',
			'label' => __( 'Roles to block', 'tribe-events-community' ),
			'default' => array(),
			'options' => $redirectRoles,
			'validation_type' => 'options_multi',
			'can_be_empty' => true,
			'parent_option' => Tribe__Events__Community__Main::OPTIONNAME,
		),

		'blockRolesRedirect' => array(
			'type' => 'text',
			'label' => __( 'Redirect URL', 'tribe-events-community' ),
			'tooltip' => __( 'Redirect for users who attempt to access the admin without permissions<br>Enter a full url to a post or page on your site<br>Leave blank for homepage', 'tribe-events-community' ),
			'default' => '',
			'placeholder' => site_url( '/' ),
			'validation_type' => 'url',
			'can_be_empty' => true,
			'parent_option' => Tribe__Events__Community__Main::OPTIONNAME,
		 ),

		'defaults-heading' => array(
			'type' => 'html',
			'html' => '<h3>' . __( 'Form Defaults', 'tribe-events-community' ) . '</h3>',
			'conditional' => ( $venue_options || $organizer_options ),
		),

		'defaultCommunityVenueID' => array(
			'type' => 'dropdown_chosen',
		 	'label' => __( 'Default venue for submitted events', 'tribe-events-community' ),
			'validation_type' => 'options',
			'default' => 0,
			'options' => $venue_options,
			'parent_option' => Tribe__Events__Community__Main::OPTIONNAME,
			'can_be_empty' => true,
			'conditional' => $venue_options,
		),

		'defaultCommunityOrganizerID' => array(
			'type' => 'dropdown_chosen',
		 	'label' => __( 'Default organizer for submitted events', 'tribe-events-community' ),
			'validation_type' => 'options',
			'default' => 0,
			'options' => $organizer_options,
			'parent_option' => Tribe__Events__Community__Main::OPTIONNAME,
			'can_be_empty' => true,
			'conditional' => $organizer_options,
		),

		'single_geography_mode' => array(
			'type' => 'checkbox_bool',
			'label' => __( 'Single geography mode', 'tribe-events-community' ),
			'tooltip' => __( 'Removes the country, state/province and timezone selectors from the submission form', 'tribe-events-community' ),
			'default' => false,
			'validation_type' => 'boolean',
			'parent_option' => Tribe__Events__Community__Main::OPTIONNAME,
		),

		'tribe_community_events_wrapper_closer' => array(
			'type' => 'html',
			'html' => '</div>',
		),


	),
);

$communityTab = apply_filters( 'tribe_community_settings_tab', $communityTab );

function tribe_display_current_community_events_slug() {
	$base = tribe_get_events_link() . Tribe__Events__Community__Main::instance()->getOption( 'communityRewriteSlug', 'community', true );
	?>
	<p class="tribe-field-indent tribe-field-description description">
		<code><?php echo esc_url( $base . '/' . sanitize_title( __( 'add', 'tribe-events-community' ) ) ); ?></code>
		<br />
		<code><?php echo esc_url( $base . '/' . sanitize_title( __( 'list', 'tribe-events-community' ) ) ); ?></code>
	</p>
	<?php
}
