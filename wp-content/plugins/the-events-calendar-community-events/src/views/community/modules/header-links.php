<?php
/**
 * Header links for edit forms.
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/modules/header-links.php
 *
 * @package Tribe__Events__Community__Main
 * @since  3.1
 * @author Modern Tribe Inc.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$current_user = wp_get_current_user();

if ( is_user_logged_in() ) {
	echo '<div id="my-events"><a href="' . esc_url( tribe_community_events_list_events_link() ) . '" class="button">' . sprintf( esc_html__( 'My %s', 'tribe-events-community' ), tribe_get_event_label_plural() ) . '</a></div>';
	echo '<div id="not-user">' . esc_html__( 'Not', 'tribe-events-community' ) . ' <i>'. esc_html( $current_user->display_name ) . '</i>? <a href="' . esc_url( tribe_community_events_logout_url() ) . '">' . esc_html__( 'Log Out', 'tribe-events-community' ) . '</a></div>';
	echo '<div style="clear:both"></div>';
}

echo tribe_community_events_get_messages();

