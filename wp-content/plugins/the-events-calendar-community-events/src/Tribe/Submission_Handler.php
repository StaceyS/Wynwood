<?php


class Tribe__Events__Community__Submission_Handler {
	/**
	 * @var Tribe__Events__Community__Main
	 */
	protected $community = null;
	protected $submission = array();
	protected $original_submission = array();
	protected $event_id = 0;
	protected $valid = null;
	protected $messages = array();
	protected $invalid_fields = array();

	public function __construct( $submission, $event_id ) {
		$this->community = Tribe__Events__Community__Main::instance();
		$this->original_submission = $submission;
		$submission[ 'ID' ] = $event_id;
		$this->submission = $submission;
		$scrubber = new Tribe__Events__Community__Submission_Scrubber( $this->submission );
		$this->submission = $scrubber->scrub();
		$this->event_id = $event_id;
	}

	public function validate() {
		if ( isset( $this->valid ) ) {
			return $this->valid;
		}

		$this->valid = true;

		// show no sympathy for spammers
		if ( ! is_user_logged_in() ) {
			$this->community->spam_check( $this->submission ); // exits on failure
		}

		if ( ! $this->validate_submission_has_required_fields( $this->submission ) ) {
			$this->valid = false;
		}

		if ( ! $this->validate_field_contents( $this->submission ) ) {
			$this->valid = false;
		}

		$this->valid = apply_filters( 'tribe_community_events_validate_submission', $this->valid, $this->submission, $this );
		return $this->valid;
	}

	/**
	 * Get the sanitized submission array
	 *
	 * @return array
	 */
	public function get_submission() {
		return $this->submission;
	}

	public function save() {
		$events_label_singular = tribe_get_event_label_singular();
		$events_label_singular_lowercase = tribe_get_event_label_singular_lowercase();
		$event = get_post( $this->event_id );

		// if the post isn't an auto-draft, then we're updating a post. Otherwise, we'll consider it new
		if ( $this->event_id && 'auto-draft' !== $event->post_status ) {
			$saved = Tribe__Events__API::updateEvent( $this->event_id, $this->submission );
			if ( $saved ) {
				$this->add_message( sprintf( __( '%s updated. ', 'tribe-events-community' ), $events_label_singular ) . $this->community->get_view_edit_links( $this->event_id ) );
				$this->add_message( '<a href="' . esc_url( $this->community->getUrl( 'add' ) ) . '">' . sprintf( __( 'Submit another %s', 'tribe-events-community' ), $events_label_singular_lowercase ) . '</a>' );
				do_action( 'tribe_community_event_updated', $this->event_id );
			} else {
				$this->add_message( sprintf( __( 'There was a problem saving your %s, please try again.', 'tribe-events-community' ), $events_label_singular_lowercase ), 'error' );
			}
		} else {
			$this->submission['post_status'] = Tribe__Events__Community__Main::instance()->getOption( 'defaultStatus' );
			$this->submission['EventOrigin'] = 'community-events';

			// if we DO have an event ID, then it is an auto-draft, and thus a new post
			if ( $this->event_id ) {
				$saved = Tribe__Events__API::updateEvent( $this->event_id, $this->submission );
			} else {
				$saved = Tribe__Events__API::createEvent( $this->submission );
			}

			if ( $saved ) {
				$this->event_id = $saved;
				$this->add_message( sprintf( __( '%s submitted.', 'tribe-events-community' ), $events_label_singular ) . $this->community->get_view_edit_links( $this->event_id ) );
				$this->add_message( '<a href="' . esc_url( $this->community->getUrl( 'add' ) ) . '">' . sprintf( __( 'Submit another %s', 'tribe-events-community' ), $events_label_singular_lowercase ) . '</a>' );
				do_action( 'tribe_community_event_created', $this->event_id );
			} else {
				$this->add_message( sprintf( __( 'There was a problem submitting your %s, please try again.', 'tribe-events-community' ), $events_label_singular_lowercase ), 'error' );
			}
		}

		// Handles the Upload
		if ( isset( $_FILES['event_image']['name'] ) && ! empty( $_FILES['event_image']['name'] ) ) {
			$attachment_id = $this->insert_attachment( 'event_image', $this->event_id, true, $this );

			if ( false === $attachment_id ) {
				$this->event_id = false;
				return false;
			}
		}

		// Logged out or underprivileged users will not have terms automatically added during wp_insert_post
		if ( isset( $this->submission['tax_input'] ) ) {
			foreach ( (array) $this->submission['tax_input'] as $taxonomy => $terms ) {
				$taxonomy_obj = get_taxonomy( $taxonomy );
				if ( ! current_user_can( $taxonomy_obj->cap->assign_terms ) ) {
					wp_set_post_terms( $this->event_id, $terms, $taxonomy, true );
				}
			}
		}
		return $this->event_id;
	}

	/**
	 * Insert an attachment.
	 *
	 * @param string $file_handler The upload.
	 * @param int $post_id The post to attach the upload to.
	 * @param string $setthumb To set or not to set the thumb.
	 * @return int The attachment's ID.
	 * @since 4.0.4
	 */
	public function insert_attachment( $file_handler, $post_id, $set_post_thumbnail = false ) {
		$events_label_singular = tribe_get_event_label_singular();

		// check to make sure its a successful upload
		if ( $_FILES[ $file_handler ]['error'] !== UPLOAD_ERR_OK ) {
			return false;
		}
		$uploaded_file_type = wp_check_filetype( basename( $_FILES[ $file_handler ]['name'] ) );
		$attach_id = false;

		if ( ! function_exists( 'media_handle_upload' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			require_once( ABSPATH . 'wp-admin/includes/media.php' );
		}

		$allowed_file_types = array( 'image/jpg', 'image/jpeg', 'image/gif', 'image/png' );
		if ( in_array( $uploaded_file_type['type'], $allowed_file_types ) ) {
			$attach_id = media_handle_upload( $file_handler, $post_id );
		} else {
			$this->add_message( esc_attr__( 'The file is not an Image', 'tribe-events-community' ), 'error' );
			return false;
		}

		if ( false !== $attach_id ) {
			$image_path = get_attached_file( $attach_id );
			$editor = wp_get_image_editor( $image_path );
			$image = @getimagesize( $image_path );
			$status = true;

			if ( is_wp_error( $editor ) ) {
				$this->add_message( $editor->get_error_message(), 'error' );
				$status = false;
			} elseif ( false === $image ) {
				$this->add_message( esc_attr__( 'The file is not an Image', 'tribe-events-community' ), 'error' );
				$status = false;
			} elseif ( empty( $image[0] ) || ! is_numeric( $image[0] ) || empty( $image[1] ) || ! is_numeric( $image[1] ) ) {
				$this->add_message( esc_attr__( 'The image size is invalid', 'tribe-events-community' ), 'error' );
				$status = false;
			} elseif ( empty( $image[2] ) || ! in_array( $image[2], array( IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG ) ) ) {
				$this->add_message( esc_attr__( 'The file is not a valid image', 'tribe-events-community' ), 'error' );
				$status = false;
			}

			if ( false === $status ) {
				// Purge this weird file!
				wp_delete_attachment( $attach_id, true );
				return false;
			}

			if ( true === $set_post_thumbnail ) {
				update_post_meta( $post_id, '_thumbnail_id', $attach_id );
			}
		}

		return $attach_id;
	}

	protected function validate_submission_has_required_fields( $submission ) {
		$valid = true;
		foreach ( $this->community->required_fields_for_submission() as $key ) {

			$valid_field = $this->submission_has_value_for_key( $submission, $key );

			if ( is_array( $valid_field ) || ! $valid_field ) {
				$message = __( '%s is required', 'tribe-events-community' );
				$message = sprintf( $message, $this->get_field_label( $key ) );
				$this->add_message( $message, 'error' );
				$valid                  = false;
				$this->invalid_fields[] = $key;
			}
		}

		return $valid;
	}

	protected function submission_has_value_for_key( $submission, $key ) {

		switch ( $key ) {
			case 'venue':

				if ( empty( $submission['Venue'] ) ) {
					return false;
				}

				$valid_venue = $invalid_fields = false;

				//Check For Venue ID in Array
				foreach ( $submission['Venue']['VenueID'] as $key => $venue_id ) {
					//We have an ID for an existing Organizer
					if ( 0 < $venue_id ) {
						return true;
					}
				}

				/*
				 * Validate Individual Fields
				 * Default is only Venue Name on New Venues
				 */
				foreach ( $submission['Venue'] as $venue ) {

					/**
					 * Filter Community Events Required Venue Fields
					 *
					 * @parm array of fields to validate - Venue, Address, City, Country, Province, State, Zip, Phone, URL
					 */
					$required_fields = apply_filters( 'tribe_events_community_required_venue_fields', array( 'Venue' ) );

					foreach ( $required_fields as $field ) {
						if ( empty( $submission['Venue'][ $field ] ) ) {
							$invalid_fields[] = $field;
						}
						$valid_venue = true;
					}
				}

				if ( $valid_venue && ! $invalid_fields ) {
					return true;
				}

				$this->set_individual_field_msgs( $invalid_fields, 'venue' );

				return false;

			case 'organizer':

				if ( empty( $submission['Organizer'] ) ) {
					return false;
				}
				$valid_organizer      = $invalid_fields = false;
				$organizer_add_checks = array();

				//Check For Organizer ID
				foreach ( $submission['Organizer']['OrganizerID'] as $key => $organizer_id ) {
					//We have an ID for an existing Organizer
					if ( 0 < $organizer_id ) {
						$valid_organizer = true;

						//If 0 we need to do more checks
					} elseif ( 0 == $organizer_id ) {
						//Save Key For Additional Checks
						$organizer_add_checks[] = $key;
					}
				}

				/*
				 * Validate Individual Fields
				 * Default is only Organizer Name on New Organizers
				 */
				foreach ( $organizer_add_checks as $organizer ) {

					/**
					 * Filter Community Events Required Organizer Fields
					 *
					 * @parm array of fields to validate - Organizer, Phone, Website, Email
					 */
					$required_fields = apply_filters( 'tribe_events_community_required_organizer_fields', array( 'Organizer' ) );

					foreach ( $required_fields as $field ) {
						if ( empty( $submission['Organizer'][ $field ][ $organizer ] ) ) {
							$invalid_fields[] = $field;
						}
						$valid_organizer = true;
					}
				}

				if ( $valid_organizer && ! $invalid_fields ) {
					return true;
				}

				$this->set_individual_field_msgs( $invalid_fields, 'organizer' );

				return false;

			case 'event_image':

				if ( $this->event_id && has_post_thumbnail( $this->event_id ) ) {
					return true;
				}
				$attachment = $this->get_attachment_array();

				return ! empty( $attachment['name'] );

			default:

				return ! empty( $submission[ $key ] );

		}
	}

	/**
	 * Set Messages for Individual Organizer and Venue Fields
	 *
	 * @param array $invalid_fields
	 * @param null  $key
	 */
	protected function set_individual_field_msgs( $invalid_fields = array(), $key = null ) {

		if ( ! is_array( $invalid_fields ) ) {
			return;
		}

		//merge duplicates
		$invalid_fields = array_flip( array_flip( $invalid_fields ) );
		$label          = $this->get_field_label( $key );

		foreach ( $invalid_fields as $field ) {
			$message = __( '%s %s is required', 'tribe-events-community' );
			$message = sprintf( $message, $label, $this->get_venue_organizer_field_label( $field ) );
			$this->add_message( $message, 'error' );
		}

	}

	protected function get_field_label( $field ) {
		$events_label_singular = tribe_get_event_label_singular();

		switch ( $field ) {
			case 'post_title':
				$label = sprintf( __( '%s Title', 'tribe-events-community' ), $events_label_singular );
				break;
			case 'post_content':
				$label = sprintf( __( '%s Description', 'tribe-events-community' ), $events_label_singular );
				break;
			case 'venue':
				$label = tribe_get_venue_label_singular();
				break;
			case 'organizer':
				$label = tribe_get_organizer_label_singular();
				break;
			default:
				if ( strpos( $field, '_ecp_custom_' ) === 0 ) {
					$label = $this->get_custom_field_label( $field );
				} else {
					$label = $this->format_field_name_as_label( $field );
				}
				break;
		}
		return apply_filters( 'tribe_community_form_field_label', $label, $field );
	}

	/**
	 * Get Translated Labels for Error Message
	 *
	 * @param $field
	 *
	 * @return string
	 */
	protected function get_venue_organizer_field_label( $field ) {

		switch ( $field ) {

			case 'Venue':

				return esc_html__( 'Name', 'tribe-events-community' );

			case 'Address':

				return esc_html__( 'Address', 'tribe-events-community' );

			case 'City':

				return esc_html__( 'City', 'tribe-events-community' );

			case 'Province':

				return esc_html__( 'Province', 'tribe-events-community' );

			case 'State':

				return esc_html__( 'State', 'tribe-events-community' );

			case 'Zip':

				return esc_html__( 'Zip', 'tribe-events-community' );

			case 'URL':

				return esc_html__( 'URL', 'tribe-events-community' );

			case 'Phone':

				return esc_html__( 'Phone', 'tribe-events-community' );

			case 'Organizer':

				return esc_html__( 'Name', 'tribe-events-community' );

			case 'Website':

				return esc_html__( 'Website', 'tribe-events-community' );

			case 'Email':

				return esc_html__( 'Email', 'tribe-events-community' );

			default:
				return $field;
		}
	}

	protected function format_field_name_as_label( $field ) {
		$regex = '/(?#! splitCamelCase Rev:20140412)
    # Split camelCase "words". Two global alternatives. Either g1of2:
      (?<=[a-z])      # Position is after a lowercase,
      (?=[A-Z])       # and before an uppercase letter.
    | (?<=[A-Z])      # Or g2of2; Position is after uppercase,
      (?=[A-Z][a-z])  # and before upper-then-lower case.
    /x';
		$parts = preg_split( $regex, $field );
		$label = implode( ' ', $parts );
		$label = str_replace( '_', ' ', $label );
		$label = ucwords( $label );
		return $label;
	}

	protected function get_custom_field_label( $name ) {
		$fields = tribe_get_option( 'custom-fields' );
		foreach ( $fields as $field ) {
			if ( $field['name'] == $name ) {
				return $field['label'];
			}
		}
		return $name;
	}

	protected function validate_field_contents( $submission ) {
		$valid = true;
		foreach ( $submission as $key => $value ) {
			if ( ! $this->is_field_valid( $key, $value ) ) {
				$message = __( 'Invalid value for %s', 'tribe-events-community' );
				$message = sprintf( $message, $this->get_field_label( $key ) );
				$this->add_message( $message, 'error' );
				$valid = false;
			}
		}
		if ( $attachment = $this->get_attachment_array() ) {
			if ( $attachment && isset( $attachment['error'] ) && $attachment['error'] ) {
				$this->add_message( $this->get_img_upload_error_msg( $attachment['error'] ), 'error' );
				$valid = false;
			} elseif ( $attachment && ! in_array( $attachment['type'], $this->image_mime_types() ) ) {
				$message = esc_html__( 'Images must be png, jpg, or gif', 'tribe-events-community' );
				$this->add_message( $message, 'error' );
				$valid = false;
			}
		}

		if ( $custom_fields = tribe_get_option( 'custom-fields' ) ) {
			foreach ( $custom_fields as $field ) {
				if ( 'url' !== $field['type'] ) {
					continue;
				}

				if ( empty( $submission[ $field['name'] ] ) ) {
					continue;
				}

				if ( filter_var( $submission[ $field['name'] ], FILTER_VALIDATE_URL ) ) {
					continue;
				}

				$message = esc_html__( 'Invalid URL provided for %s', 'tribe-events-community' );
				$message = sprintf( $message, $this->get_field_label( $field['name'] ) );
				$this->add_message( $message, 'error' );
				$valid = false;
			}
		}

		return $valid;
	}

	protected function image_mime_types() {
		return array(
			'image/png',
			'image/jpeg',
			'image/gif',
		);
	}

	protected function get_img_upload_error_msg( $upload_error_code ) {
		switch ( $upload_error_code ) {
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				return __( 'Image exceeded the allowed file size', 'tribe-events-community' );
				break;

			case UPLOAD_ERR_PARTIAL:
			case UPLOAD_ERR_NO_FILE:
				return __( 'The image failed to upload successfully', 'tribe-events-community' );
				break;

			default:
				return __( 'The uploaded image could not be processed', 'tribe-events-community' );
				break;
		}
	}

	protected function get_attachment_array() {
		// TODO: we still have to use the global here for now
		if ( empty( $_FILES ) || empty( $_FILES['event_image'] ) || empty( $_FILES['event_image']['name'] ) ) {
			return array();
		}
		return $_FILES['event_image'];
	}

	protected function is_field_valid( $key, $value ) {
		$valid = true;
		$valid = apply_filters( 'tribe_community_is_field_valid', $valid, $key, $value );
		return $valid;
	}

	public function get_messages() {
		return $this->messages;
	}

	public function add_message( $message, $type = 'update' ) {
		$message = apply_filters( 'tribe_events_community_submission_message', $message, $type );
		$this->messages[] = (object) array( 'message' => $message, 'type' => $type );
	}

	public function get_invalid_fields() {
		return array_unique( $this->invalid_fields );
	}
}
