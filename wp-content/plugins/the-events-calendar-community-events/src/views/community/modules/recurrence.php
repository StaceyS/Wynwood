<?php
/**
 * Event Submission Form Metabox For Recurrence
 * This is used to add a metabox to the event submission form to allow for choosing or
 * creating recurrences of user submitted events.
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/modules/recurrence.php
 *
 * @version 4.1
 * @package Tribe__Events__Community__Main
 * @since  2.1
 * @author Modern Tribe Inc.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

global $post;
$post_id = isset( $post->ID ) ? $post->ID : null;


if ( ! empty( $_POST['recurrence'] ) ) {
	Tribe__Events__Pro__Recurrence__Meta::output_recurrence_json_data( $post_id, $_POST['recurrence'] );
}

include Tribe__Events__Pro__Main::instance()->pluginPath . '/src/admin-views/event-recurrence.php';
