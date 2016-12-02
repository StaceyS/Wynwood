<?php

/**
 * Class Tribe__Events__Community__Submission_Scrubber
 *
 * Scrubs inappropriate data out of a submitted event
 */
class Tribe__Events__Community__Submission_Scrubber {
	protected $submission = array();
	protected $allowed_fields = array( // filter these with 'tribe_events_community_allowed_event_fields'
		'ID',
		'post_content',
		'post_title',
		'tax_input',
		'EventAllDay',
		'EventStartDate',
		'EventStartHour',
		'EventStartMinute',
		'EventStartMeridian',
		'EventEndDate',
		'EventEndHour',
		'EventEndMinute',
		'EventEndMeridian',
		'EventTimezone',
		'EventShowMapLink',
		'EventShowMap',
		'EventURL',
		'EventCurrencySymbol',
		'EventCurrencyPosition',
		'EventCost',
		'Venue',
		'Organizer',
		'is_recurring',
		'recurrence',
		'render_timestamp',
	);

	protected $allowed_venue_fields = array( // filter these with 'tribe_events_community_allowed_venue_fields'
		'VenueID',
		'Venue',
		'Address',
		'City',
		'Country',
		'Province',
		'State',
		'Zip',
		'Phone',
		'URL',
	);

	protected $allowed_organizer_fields = array( // filter these with 'tribe_events_community_allowed_organizer_fields'
		'OrganizerID',
		'Organizer',
		'Phone',
		'Website',
		'Email',
	);

	protected $filters = null;

	public function __construct( array $submission ) {
		$this->submission = $submission;
	}

	/**
	 * Remove data from the submission that shouldn't be there
	 *
	 * @return array The cleaned submission
	 */
	public function scrub() {
		add_filter( 'wp_kses_allowed_html', array( $this, 'filter_allowed_html_tags' ), 10, 2 );

		$this->fix_post_content_key();
		$this->set_venue();
		$this->set_organizer();

		$this->remove_unexpected_fields();
		$this->filter_field_contents();
		$this->filter_custom_urls();

		// These should not be user-submitted
		$this->set_post_type();
		$this->set_post_author();
		$this->set_post_status();

		remove_filter( 'wp_kses_allowed_html', array( $this, 'filter_allowed_html_tags' ), 10, 2 );


		$this->submission = apply_filters( 'tribe_events_community_sanitize_submission', $this->submission );
		return $this->submission;
	}

	protected function fix_post_content_key() {
		$this->submission['post_content'] = isset( $this->submission['tcepostcontent'] ) ? $this->submission['tcepostcontent'] : '';
		unset( $this->submission['tcepostcontent'] );
	}

	public function filter_allowed_html_tags( $tags, $context ) {
		unset( $tags['form'] );
		unset( $tags['button'] );
		unset( $tags['img'] );
		$tags = apply_filters( 'tribe_events_community_allowed_tags', $tags );
		return $tags;
	}

	protected function set_post_type() {
		$this->submission['post_type'] = Tribe__Events__Main::POSTTYPE;
	}

	protected function set_post_status() {
		if ( empty( $this->submission['ID'] ) ) {
			$this->submission['post_status'] = Tribe__Events__Community__Main::instance()->defaultStatus;
		} else {
			$this->submission['post_status'] = get_post_status( $this->submission['ID'] );
		}
	}

	protected function set_post_author() {
		$this->submission['post_author'] = get_current_user_id();
	}

	protected function set_venue() {
		if ( ! isset( $this->submission['venue'] ) ) {
			$this->submission['Venue'] = array();
			return;
		}
		$this->submission['Venue'] = stripslashes_deep( $this->submission['venue'] );
		$this->submission['Venue'] = $this->filter_venue_data( $this->submission['Venue'] );
		unset( $this->submission['venue'] );
	}

	protected function filter_venue_data( $venue_data ) {
		if ( ! empty( $venue_data['VenueID'] ) ) {
			$venue_data['VenueID'] = array_map( 'intval', $venue_data['VenueID'] );
		}

		$fields = array(
			'Venue',
			'Address',
			'City',
			'Country',
			'Province',
			'State',
			'Zip',
			'Phone',
		);

		foreach ( $fields as $field ) {
			if ( isset( $venue_data[ $field ] ) ) {
				$venue_data[ $field ] = $this->filter_string( $venue_data[ $field ] );
			}
		}

		return $venue_data;
	}

	protected function set_organizer() {
		if ( ! isset( $this->submission['organizer'] ) ) {
			$this->submission['Organizer'] = array();
			return;
		}
		$this->submission['Organizer'] = stripslashes_deep( $this->submission['organizer'] );
		unset( $this->submission['organizer'] );
	}

	protected function remove_unexpected_fields() {
		$allowed_fields = $this->get_allowed_event_fields();

		foreach ( $this->submission as $key => $value ) {
			if ( ! in_array( $key, $allowed_fields ) ) {
				unset( $this->submission[ $key ] );
			}
		}

		if ( ! empty( $this->submission['Venue'] ) ) {
			$allowed_venue_fields = $this->get_allowed_venue_fields();
			foreach ( $this->submission['Venue'] as $key => $value ) {
				if ( ! in_array( $key, $allowed_venue_fields ) ) {
					unset( $this->submission['Venue'][ $key ] );
				}
			}
		}

		if ( ! empty( $this->submission['Organizer'] ) && is_array( $this->submission['Organizer'] ) ) {
			$allowed_organizer_fields = $this->get_allowed_organzer_fields();
			foreach ( $this->submission['Organizer'] as $key => $value ) {
				if ( ! in_array( $key, $allowed_organizer_fields ) ) {
					unset( $organizer_data[ $key ] );
				}
			}
		}
	}

	protected function get_allowed_event_fields() {
		$allowed_fields = array_merge( $this->allowed_fields, $this->get_custom_field_keys() );
		return apply_filters( 'tribe_events_community_allowed_event_fields', $allowed_fields );
	}

	protected function get_allowed_venue_fields() {
		return apply_filters( 'tribe_events_community_allowed_venue_fields', $this->allowed_venue_fields );
	}

	protected function get_allowed_organzer_fields() {
		return apply_filters( 'tribe_events_community_allowed_organizer_fields', $this->allowed_organizer_fields );
	}

	protected function get_custom_field_keys() {
		$customFields = tribe_get_option( 'custom-fields' );
		if ( empty( $customFields ) || ! is_array( $customFields ) ) {
			return array();
		}
		$keys = array();
		foreach ( $customFields as $field ) {
			$keys[] = $field['name'];
		}
		return $keys;
	}

	protected function filter_field_contents() {
		foreach ( array( 'post_content', 'post_title', 'EventURL', 'EventCurrencySymbol', 'EventCost' ) as $field ) {
			if ( isset( $this->submission[ $field ] ) ) {
				$this->submission[ $field ] = $this->filter_string( $this->submission[ $field ] );
			}
		}
	}

	/**
	 * Filters custom field URLs, adding http:// if no protocol is detected and adding one would make it a valid URL
	 */
	protected function filter_custom_urls() {
		$custom_fields = tribe_get_option( 'custom-fields' );

		if ( ! $custom_fields ) {
			return;
		}

		foreach ( $custom_fields as $field ) {
			if ( 'url' !== $field['type'] ) {
				continue;
			}

			if ( empty( $this->submission[ $field['name'] ] ) ) {
				continue;
			}

			if ( filter_var( $this->submission[ $field['name'] ], FILTER_VALIDATE_URL ) ) {
				continue;
			}

			// if the field STILL can't be validated if we append http://, then it is a lost cause
			if ( ! filter_var( 'http://' . $this->submission[ $field['name'] ], FILTER_VALIDATE_URL ) ) {
				continue;
			}

			$this->submission[ $field['name'] ] = 'http://' . $this->submission[ $field['name'] ];
		}
	}

	protected function get_content_filters() {
		if ( ! isset( $this->filters ) ) {
			$this->filters = array();
			$user_id = is_user_logged_in() ? wp_get_current_user() : false;
			// These filters are a booleans to determine whether to strip bad stuff. The added arguments are the current user's id and the event id (false for new events, obviously).
			if ( apply_filters( 'tribe_events_community_submission_should_strip_html', true, $user_id, $this->submission['ID'] ) ) {
				$this->filters[] = 'wp_kses_post';
			}
			if ( apply_filters( 'tribe_events_community_submission_should_strip_shortcodes', false, $user_id, $this->submission['ID'] ) ) {
				$this->filters[] = 'strip_shortcodes';
			}
			$this->filters[] = 'stripslashes_deep';
		}
		return $this->filters;
	}

	protected function filter_string( $string ) {
		foreach ( $this->get_content_filters() as $callback ) {
			$string = call_user_func( $callback, $string );
		}
		return $string;
	}
}
