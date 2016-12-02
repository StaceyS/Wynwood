<?php
if ( ! function_exists( 'tribe_is_community_my_events_page' ) ) {
	/**
	 * Tests if the current page is the My Events page
	 *
	 * @return bool whether it is the My Events page.
	 * @author Paul Hughes
	 * @since 1.0.1
	 */
	function tribe_is_community_my_events_page() {
		return Tribe__Events__Community__Main::instance()->isMyEvents;
	}
}

if ( ! function_exists( 'tribe_is_community_edit_event_page' ) ) {
	/**
	 * Tests if the current page is the Edit Event page
	 *
	 * @return bool whether it is the Edit Event page.
	 * @author Paul Hughes
	 * @since 1.0.1
	 */
	function tribe_is_community_edit_event_page() {
		return Tribe__Events__Community__Main::instance()->isEditPage;
	}
}

/**
 * Test if the current user can edit posts
 *
 * @param int|null $post_id
 * @param string|null $post_type (tribe_events, tribe_venue, or tribe_organizer)
 * @return bool whether the user can edit
 * @author Peter Chester
 * @since 3.1
 * @deprecated since version 3.1
 */
function tribe_community_events_user_can_edit( $post_id = null, $post_type = null ) {
	return Tribe__Events__Community__Main::instance()->userCanEdit( $post_id, $post_type );
}

/**
 * Echo the community events form title field
 *
 * @author Peter Chester
 * @since 3.1
 */
function tribe_community_events_form_title() {
	Tribe__Events__Community__Main::instance()->formTitle();
}

/**
 * Echo the community events form content editor
 *
 * @author Peter Chester
 * @since 3.1
 */
function tribe_community_events_form_content() {
	Tribe__Events__Community__Main::instance()->formContentEditor();
}

/**
 * Echo the community events form image delete button
 *
 * @author Peter Chester
 * @since 3.1
 */
function tribe_community_events_form_image_delete() {
	echo Tribe__Events__Community__Main::instance()->getDeleteFeaturedImageButton();
}

/**
 * Echo the community events form image preview
 *
 * @author Peter Chester
 * @since 3.1
 */
function tribe_community_events_form_image_preview() {
	echo Tribe__Events__Community__Main::instance()->getDeleteFeaturedImageButton();
}

/**
 * Echo the community events form currency symbol
 *
 * @author Peter Chester
 * @since 3.1
 */
function tribe_community_events_form_currency_symbol() {
	if ( get_post() ) {
		$EventCurrencySymbol = get_post_meta( get_the_ID(), '_EventCurrencySymbol', true );
	}

	if ( ! isset( $EventCurrencySymbol ) || ! $EventCurrencySymbol ) {
		$EventCurrencySymbol = isset( $_POST['EventCurrencySymbol'] ) ? $_POST['EventCurrencySymbol'] : tribe_get_option( 'defaultCurrencySymbol', '$' );
	}

	echo esc_attr( $EventCurrencySymbol );
}

/**
 * Return URL for adding a new event.
 */
function tribe_community_events_add_event_link() {
	$url = Tribe__Events__Community__Main::instance()->getUrl( 'add' );
	return apply_filters( 'tribe-community-events-add-event-link', $url );
}

/**
 * Return URL for listing events.
 */
function tribe_community_events_list_events_link() {
	$url = Tribe__Events__Community__Main::instance()->getUrl( 'list' );
	return apply_filters( 'tribe-community-events-list-events-link', $url );
}

/**
 * Return URL for editing an event.
 */
function tribe_community_events_edit_event_link( $event_id = null ) {
	$url = Tribe__Events__Community__Main::instance()->getUrl( 'edit', $event_id );
	return apply_filters( 'tribe-community-events-edit-event-link', $url, $event_id );
}

/**
 * Return URL for deleting an event.
 */
function tribe_community_events_delete_event_link( $event_id = null ) {
	$url = Tribe__Events__Community__Main::instance()->getUrl( 'delete', $event_id );
	return apply_filters( 'tribe-community-events-delete-event-link', $url, $event_id );
}

/**
 * Return the event start date string with a default of today.
 *
 * @param null|int $event_id
 * @return string event date
 * @author Peter Chester
 * @since 3.1
 */
function tribe_community_events_get_start_date( $event_id = null ) {
	$event_id = Tribe__Events__Main::postIdHelper( $event_id );
	$event = ( $event_id ) ? get_post( $event_id ) : null;
	$date = tribe_get_start_date( $event, true, 'Y-m-d' );
	$date = ( $date ) ? Tribe__Date_Utils::date_only( $date ) : date_i18n( 'Y-m-d' );
	return apply_filters( 'tribe_community_events_get_start_date', $date, $event_id );
}

/**
 * Return the event end date string with a default of today.
 *
 * @param null|int $event_id
 * @return string event date
 * @author Peter Chester
 * @since 3.1
 */
function tribe_community_events_get_end_date( $event_id = null ) {
	$event_id = Tribe__Events__Main::postIdHelper( $event_id );
	$event = ( $event_id ) ? get_post( $event_id ) : null;
	$date = tribe_get_end_date( $event, true, 'Y-m-d' );
	$date = ( $date ) ? Tribe__Date_Utils::date_only( $date ) : date_i18n( 'Y-m-d' );
	return apply_filters( 'tribe_community_events_get_end_date', $date, $event_id );
}

/**
 * Return true if event is an all day event.
 *
 * @param null|int $event_id
 * @return bool event date
 * @author Peter Chester
 * @since 3.1
 */
function tribe_community_events_is_all_day( $event_id = null ) {
	$event_id = Tribe__Events__Main::postIdHelper( $event_id );
	$is_all_day = tribe_event_is_all_day( $event_id );
	$is_all_day = ( $is_all_day == 'Yes' || $is_all_day == true );
	return apply_filters( 'tribe_community_events_is_all_day', $is_all_day, $event_id );
}

/**
 * Return form select fields for event start time.
 *
 * @param null|int $event_id
 * @return string time select HTML
 * @author Peter Chester
 * @since 3.1
 */
function tribe_community_events_form_start_time_selector( $event_id = null ) {

	$event_id = Tribe__Events__Main::postIdHelper( $event_id );
	$is_all_day = tribe_event_is_all_day( $event_id );

	$start_date = null;

	if ( $event_id ) {
		$start_date = tribe_get_start_date( $event_id, true, Tribe__Date_Utils::DBDATETIMEFORMAT );
	}

	$start_minutes 	= Tribe__View_Helpers::getMinuteOptions( $start_date, true );
	$start_hours = Tribe__View_Helpers::getHourOptions( $is_all_day == 'yes' ? null : $start_date, true );
	$start_meridian = Tribe__View_Helpers::getMeridianOptions( $start_date, true );

	$output = '';
	$output .= sprintf( '<select name="EventStartHour">%s</select>', $start_hours );
	$output .= sprintf( '<select name="EventStartMinute">%s</select>', $start_minutes );
	if ( ! tribe_community_events_use_24hr_format() ) {
		$output .= sprintf( '<select name="EventStartMeridian">%s</select>', $start_meridian );
	}
	return apply_filters( 'tribe_community_events_form_start_time_selector', $output, $event_id );
}

/**
 * Return form select fields for event end time.
 *
 * @param null|int $event_id
 * @return string time select HTML
 * @author Peter Chester
 * @since 3.1
 */
function tribe_community_events_form_end_time_selector( $event_id = null ) {

	$event_id = Tribe__Events__Main::postIdHelper( $event_id );
	$is_all_day = tribe_event_is_all_day( $event_id );
	$end_date = null;

	if ( $event_id ) {
		$end_date = tribe_get_end_date( $event_id, true, Tribe__Date_Utils::DBDATETIMEFORMAT );
	}

	$end_minutes = Tribe__View_Helpers::getMinuteOptions( $end_date );
	$end_hours = Tribe__View_Helpers::getHourOptions( $is_all_day == 'yes' ? null : $end_date );
	$end_meridian = Tribe__View_Helpers::getMeridianOptions( $end_date );

	$output = '';
	$output .= sprintf( '<select name="EventEndHour">%s</select>', $end_hours );
	$output .= sprintf( '<select name="EventEndMinute">%s</select>', $end_minutes );
	if ( ! tribe_community_events_use_24hr_format() ) {
		$output .= sprintf( '<select name="EventEndMeridian">%s</select>', $end_meridian );
	}
	return apply_filters( 'tribe_community_events_form_end_time_selector', $output, $event_id );
}

/**
 * Determines if the current time format is 24hrs or not.
 *
 * In future releases this function can be removed and Tribe__View_Helpers::is_24hr_format()
 * can be used directly from calling functions; this is simply an intermediate step/compatibility
 * measure to minimize problems with mismatched dependencies (ie, Community 3.8 and Core 3.7).
 *
 * @deprecated 3.8 - remove in 4.0 and update calling functions as above
 * @return bool
 */
function tribe_community_events_use_24hr_format() {
	if ( method_exists( 'Tribe__View_Helpers', 'is_24hr_format' ) ) {
		return Tribe__View_Helpers::is_24hr_format();
	}
	else {
		return strstr( get_option( 'time_format', Tribe__Date_Utils::TIMEFORMAT ), 'H' );
	}
}

/**
 * Get the error or notice messages for a given form result.
 *
 * @return string error/notice HTML
 * @author Peter Chester
 * @since 3.1
 */
function tribe_community_events_get_messages() {
	return Tribe__Events__Community__Main::instance()->outputMessage( null, false );
}

/********************** ORGANIZER TEMPLATE TAGS **********************/

/**
 * Echo Organizer edit form contents
 *
 * @param int|null $organizer_id (optional)
 * @author Peter Chester
 * @since 3.1
 */
function tribe_community_events_organizer_edit_form( $organizer_id = null ) {
	if ( $organizer_id ) {
		$post = get_post( $organizer_id );
		$saved = false;

		if ( isset( $post->post_type ) && $post->post_type == Tribe__Events__Main::ORGANIZER_POST_TYPE ) {

			$postId = $post->ID;

			$saved = ( ( is_admin() && isset( $_GET['post'] ) && $_GET['post'] ) || ( ! is_admin() && isset( $postId ) ) );

			// Generate all the inline variables that apply to Organizers
			$organizer_vars = Tribe__Events__Main::instance()->organizerTags;
			foreach ( $organizer_vars as $var ) {
				if ( $postId && $saved ) { //if there is a post AND the post has been saved at least once.
					$$var = get_post_meta( $postId, $var, true );
				}
			}
		}
		$meta_box_template = apply_filters( 'tribe_events_organizer_meta_box_template', '' );
		if ( ! empty( $meta_box_template ) ) {
			include( $meta_box_template );
		}
	}
}

/**
 * Echo Organizer select menu
 *
 * @param int|null $event_id (optional)
 * @author Peter Chester
 * @since 3.1
 */
function tribe_community_events_organizer_select_menu( $event_id = null ) {
	if ( ! $event_id ) {
		global $post;
		if ( isset( $post->post_type ) && $post->post_type == Tribe__Events__Main::POSTTYPE ) {
			$event_id = $post->ID;
		} elseif ( isset( $post->post_type ) && $post->post_type == Tribe__Events__Main::ORGANIZER_POST_TYPE ) {
			return;
		}
	}
	do_action( 'tribe_organizer_table_top', $event_id );
}

/**
 * Test to see if this is the Organizer edit screen
 *
 * @param int|null $organizer_id (optional)
 * @return bool
 * @author Peter Chester
 * @since 3.1
 */
function tribe_community_events_is_organizer_edit_screen( $organizer_id = null ) {
	$organizer_id = Tribe__Events__Main::postIdHelper( $organizer_id );
	$is_organizer = ( $organizer_id ) ? Tribe__Events__Main::instance()->isOrganizer( $organizer_id ) : false;
	return apply_filters( 'tribe_is_organizer', $is_organizer, $organizer_id );
}

/**
 * Return Organizer Description
 *
 * @param int|null $organizer_id (optional)
 * @return string
 * @author Peter Chester
 * @since 3.1
 */
function tribe_community_events_get_organizer_description( $organizer_id = null ) {
	$organizer_id = tribe_get_organizer_id( $organizer_id );
	$description = ( $organizer_id > 0 ) ? get_post( $organizer_id )->post_content : null;
	return apply_filters( 'tribe_get_organizer_description', $description );
}

/********************** VENUE TEMPLATE TAGS **********************/

/**
 * Echo Venue edit form contents
 *
 * @param int|null $venue_id (optional)
 * @author Peter Chester
 * @since 3.1
 */
function tribe_community_events_venue_edit_form( $venue_id = null ) {
	if ( $venue_id ) {
		$post = get_post( $venue_id );
		$saved = false;

		if ( isset( $post->post_type ) && $post->post_type == Tribe__Events__Main::VENUE_POST_TYPE ) {

			$postId = $post->ID;

			$saved = ( ( is_admin() && isset( $_GET['post'] ) && $_GET['post'] ) || ( ! is_admin() && isset( $postId ) ) );

			// Generate all the inline variables that apply to Venues
			$venue_vars = Tribe__Events__Main::instance()->venueTags;
			foreach ( $venue_vars as $var ) {
				if ( $postId && $saved ) { //if there is a post AND the post has been saved at least once.
					$$var = get_post_meta( $postId, $var, true );
				}
			}
		}

		$meta_box_template = apply_filters( 'tribe_events_venue_meta_box_template', '' );
		if ( ! empty( $meta_box_template ) ) {
			include( $meta_box_template );
		}
	}
}

/**
 * Echo Venue select menu
 *
 * @param int|null $event_id (optional)
 * @author Peter Chester
 * @since 3.1
 */
function tribe_community_events_venue_select_menu( $event_id = null ) {
	if ( ! $event_id ) {
		global $post;
		if ( isset( $post->post_type ) && $post->post_type == Tribe__Events__Main::POSTTYPE ) {
			$event_id = $post->ID;
		} elseif ( isset( $post->post_type ) && $post->post_type == Tribe__Events__Main::VENUE_POST_TYPE ) {
			return;
		}
	}
	do_action( 'tribe_venue_table_top', $event_id );
}

/**
 * Test to see if this is the Venue edit screen
 *
 * @param int|null $venue_id (optional)
 * @return bool
 * @author Peter Chester
 * @since 3.1
 */
function tribe_community_events_is_venue_edit_screen( $venue_id = null ) {
	$venue_id = Tribe__Events__Main::postIdHelper( $venue_id );
	return ( tribe_is_venue( $venue_id ) );
}

/**
 * Return Venue Description
 *
 * @param int|null $venue_id (optional)
 * @return string
 * @author Peter Chester
 * @since 3.1
 */
function tribe_community_events_get_venue_description( $venue_id = null ) {
	$venue_id = tribe_get_venue_id( $venue_id );
	$description = ( $venue_id > 0 ) ? get_post( $venue_id )->post_content : null;
	return apply_filters( 'tribe_get_venue_description', $description );
}


/**
 * Event Website URL
 *
 * @param null|object|int $event
 * @return string The event's website URL
 * @deprecated use tribe_get_event_website_url()
 *
 * This function was added for compatibility reasons. It can be removed once
 * tribe_get_event_website_url() is in the required version of core
 *  -- jbrinley (2013-09-16)
 */
function tribe_community_get_event_website_url( $event = null ) {
	if ( function_exists( 'tribe_get_event_website_url' ) ) {
		return tribe_get_event_website_url();
	}
	$post_id = ( is_object( $event ) && isset( $event->tribe_is_event ) && $event->tribe_is_event ) ? $event->ID : $event;
	$post_id = ( ! empty( $post_id ) || empty( $GLOBALS['post'] ) ) ? $post_id : get_the_ID();
	$url = tribe_get_event_meta( $post_id, '_EventURL', true );
	if ( ! empty( $url ) ) {
		$parseUrl = parse_url( $url );
		if ( empty( $parseUrl['scheme'] ) ) {
			$url = "http://$url";
		}
	}
	return apply_filters( 'tribe_get_event_website_url', $url, $post_id );
}

/**
 * Get the logout URL
 *
 * @return string The logout URL with appropriate redirect for the current user
 * @since 3.1
 */
function tribe_community_events_logout_url() {
	$community = Tribe__Events__Community__Main::instance();
	return $community->logout_url();
}

/**
 * @param string $field
 *
 * @return bool
 */
function tribe_community_is_field_required( $field ) {
	$community = Tribe__Events__Community__Main::instance();
	return in_array( $field, $community->required_fields_for_submission() );
}

/**
 * @param string $field
 *
 * @return string
 */
function tribe_community_required_field_marker( $field ) {
	if ( tribe_community_is_field_required( $field ) ) {
		$html = '<small class="req">' . __( '(required)', 'tribe-events-community' ) . '</small>';
		return apply_filters( 'tribe_community_required_field_marker', $html, $field );
	}
	return '';
}

function tribe_community_events_field_label( $field, $text ) {
	$label_text = apply_filters( 'tribe_community_events_field_label_text', $text, $field );
	$class = tribe_community_events_field_has_error( $field ) ? 'error' : '';
	$class = apply_filters( 'tribe_community_events_field_label_class', $class, $field );
	$html = sprintf(
		'<label for="%s" class="%s">%s %s</label>',
		$field,
		$class,
		$label_text,
		tribe_community_required_field_marker( $field )
	);
	$html = apply_filters( 'tribe_community_events_field_label', $html, $field, $text );
	echo $html;
}

function tribe_community_events_field_has_error( $field ) {
	return apply_filters( 'tribe_community_events_field_has_error', false, $field );
}

/**
 * Indicates if single geography mode is enabled (this typically implies there
 * is no need for country, state/province or timezone options).
 *
 * @return boolean
 */
function tribe_community_events_single_geo_mode() {
	return (bool) Tribe__Events__Community__Main::instance()->getOption( 'single_geography_mode' );
}

/**
 * Whether an event is one submitted via the community event submission or not.
 *
 * The check is made on the `_EventOrigin` custom field set when the event is
 * originally submitted; as such later modifications or deletions of that field can
 * cause different return values from this function.
 * Also note that this function will always return `false` for community events submitted
 * before version `4.3`; to have this function return the right value set the
 * `_EventOrigin` custom field to `community-events` on previously created community events.
 * Note that editing a pre `4.3` version community event through the community event
 * edit screen will mark it as a community event.
 *
 * @since 4.3
 *
 * @param WP_Post|int $event Either the `WP_Post` event object or the event post `ID`
 */
function tribe_community_events_is_community_event( $event ) {
	$event_id = Tribe__Main::post_id_helper( $event );

	return get_post_meta( $event_id, '_EventOrigin', true ) === 'community-events';
}
