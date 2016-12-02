<?php

// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( ! class_exists( 'Tribe__Events__Community__Main' ) ) {
	/**
	 * Tribe Community Events main class
	 *
	 * @package Tribe__Events__Community__Main
	 * @author Modern Tribe Inc.
	 * @since  1.0
	 */
	class Tribe__Events__Community__Main {

		/**
		 * The current version of Community Events
		 */
		const VERSION = '4.3.1';

		/**
		 * required The Events Calendar Version
		 */
		const REQUIRED_TEC_VERSION = '4.3.1';

		/**
		 * Singleton instance variable
		 * @var object
		 */
		private static $instance;

		/**
		 * Whether before and after event HTML should be printed on the page.
		 * @var bool
		 */
		protected $should_print_before_after_html = true;

		/**
		 * Loadscripts or not
		 * @var bool
		 */
		private $loadScripts = false;

		/**
		 * plugin options
		 * @var array
		 */
		protected static $options;

		/**
		 * this plugin's directory
		 * @var string
		 */
		public $pluginDir;

		/**
		 * this plugin's path
		 * @var string
		 */
		public $pluginPath;

		/**
		 * this plugin's url
		 * @var string
		 */
		public $pluginUrl;

		/**
		 * this plugin's slug
		 * @var string
		 */
		public $pluginSlug;

		/**
		 * tribe url (used for calling the mothership)
		 * @var string
		 */
		public static $tribeUrl = 'http://tri.be/';

		/**
		 * default event status
		 * @var string
		 */
		public $defaultStatus;

		/**
		 * setting to allow anonymous submissions
		 * @var bool
		 */
		public $allowAnonymousSubmissions;

		/**
		 * setting to allow editing of submisisons
		 * @var bool
		 */
		public $allowUsersToEditSubmissions;

		/**
		 * setting to allow deletion of submisisons
		 * @var bool
		 */
		public $allowUsersToDeleteSubmissions;

		/**
		 * setting to trash items instead of permanent delete
		 * @var bool
		 */
		public $trashItemsVsDelete;

		/**
		 * setting to use visual editor
		 * @var bool
		 */
		public $useVisualEditor;

		/**
		 * setting to control # of events per page
		 * @var int
		 */
		public $eventsPerPage;

		/**
		 * setting to control format for dates
		 * @var string
		 */
		public $eventListDateFormat;

		/**
		 * setting for pagination range
		 * @var string
		 */
		public $paginationRange;

		/**
		 * setting for default organizer (requires ECP)
		 * @var int
		 */
		public $defaultCommunityOrganizerID;

		/**
		 * setting for default venue (requires ECP)
		 * @var int
		 */
		public $defaultCommunityVenueID;

		/**
		 * message to be displayed to the user
		 * @var array
		 */
		public $messages;

		/**
		 * the type of the message (error, notice, etc.)
		 * @var string
		 */
		public $messageType;

		/**
		 * the rewrite slug to use
		 * @var string
		 */
		public $communityRewriteSlug;

		/**
		 * the main rewrite slug to use
		 * @var string
		 */
		public $rewriteSlugs;

		/**
		 * rewrite slugs for different components
		 * @var array
		 */
		public $context;

		/**
		 * is the current page the my events list?
		 * @var bool
		 */
		public $isMyEvents;

		/**
		 * is the current page the event edit page?
		 * @var bool
		 */
		public $isEditPage;

		/**
		 * should the permalinks be flushed upon plugin load?
		 * @var bool
		 */
		 public $maybeFlushRewrite;

		/**
		 * @var Tribe__Events__Community__Anonymous_Users
		 */
		public $anonymous_users;

		/**
		 * @var int The ID of a page with the community shortcode on it
		 */
		private $tcePageId = null;

		/** @var Tribe__Events__Community__Captcha__Abstract_Captcha */
		private $captcha = null;

		/** @var Tribe__Events__Community__Event_Form */
		public $form;

		/**
		 * Holds the multisite default options values for CE.
		 * @var array
		 */
		public static $tribeCommunityEventsMuDefaults;

		/**
		 * option name to save all plugin options under
		 * as a serialized array
		 */
		const OPTIONNAME = 'tribe_community_events_options';

		/**
		 * Class constructor
		 * Sets all the class vars up and such
		 *
		 * @author Nick Ciske
		 * @since 1.0
		 */
		private function __construct() {
			$tec = Tribe__Events__Main::instance();


			// Load multisite defaults
			if ( is_multisite() ) {
				$tribe_community_events_mu_defaults = array();
				if ( file_exists( WP_CONTENT_DIR . '/tribe-events-mu-defaults.php' ) )
					include( WP_CONTENT_DIR . '/tribe-events-mu-defaults.php' );
				self::$tribeCommunityEventsMuDefaults = apply_filters( 'tribe_community_events_mu_defaults', $tribe_community_events_mu_defaults );
			}

			// get options
			$this->defaultStatus                 = $this->getOption( 'defaultStatus' );
			$this->allowAnonymousSubmissions     = $this->getOption( 'allowAnonymousSubmissions' );
			$this->allowUsersToEditSubmissions   = $this->getOption( 'allowUsersToEditSubmissions' );
			$this->allowUsersToDeleteSubmissions = $this->getOption( 'allowUsersToDeleteSubmissions' );
			$this->trashItemsVsDelete            = $this->getOption( 'trashItemsVsDelete' );
			$this->useVisualEditor               = $this->getOption( 'useVisualEditor' );
			$this->eventsPerPage                 = $this->getOption( 'eventsPerPage', 10 );
			$this->eventListDateFormat           = $this->getOption( 'eventListDateFormat' );
			$this->paginationRange               = 3;
			$this->defaultStatus                 = $this->getOption( 'defaultStatus' );

			$this->emailAlertsEnabled            = $this->getOption( 'emailAlertsEnabled' );
			$emailAlertsList                     = $this->getOption( 'emailAlertsList' );

			$this->emailAlertsList = explode( "\n", $emailAlertsList );

			$this->blockRolesFromAdmin = $this->getOption( 'blockRolesFromAdmin' );
			$this->blockRolesList      = $this->getOption( 'blockRolesList' );
			$this->blockRolesRedirect  = $this->getOption( 'blockRolesRedirect', get_home_url() ) == '' ? get_home_url() : $this->getOption( 'blockRolesRedirect', get_home_url() );

			$this->maybeFlushRewrite   = $this->getOption( 'maybeFlushRewrite' );

			if ( $this->blockRolesFromAdmin )
				add_action( 'init', array( $this, 'blockRolesFromAdmin' ) );

			$this->pluginPath = trailingslashit( dirname( dirname( dirname( __FILE__ ) ) ) );
			$this->pluginDir  = trailingslashit( basename( $this->pluginPath ) );
			$this->pluginUrl = plugins_url() . '/' . $this->pluginDir;
			$this->pluginSlug = 'events-community';

			$this->register_active_plugin();

			$this->isMyEvents = false;
			$this->isEditPage = false;

			$this->anonymous_users = new Tribe__Events__Community__Anonymous_Users( $this );

			add_shortcode( 'tribe_community_events', array( $this, 'doShortCode' ) );
			add_shortcode( 'tribe_community_events_title', array( $this, 'doShortCodeTitle' ) );

			//allow shortcodes for dynamic titles
			add_filter( 'the_title', 'do_shortcode' );
			add_filter( 'wp_title', 'do_shortcode' );
			add_filter( 'document_title_parts', array( $this, 'support_shortcodes_in_post_title' ) );

			if ( '' == get_option( 'permalink_structure' ) ) {
				add_action( 'template_redirect', array( $this, 'maybeRedirectMyEvents' ) );
			} else {
				add_action( 'template_redirect', array( $this, 'redirectUglyUrls' ) );
			}

			// in 3.5 this is causing an error moved self::maybeLoadAssets(); into function init()...
			add_action( 'wp', array( $this, 'maybeLoadAssets' ) );

			add_action( 'init', array( $this, 'loadTextDomain' ), 1 );

			add_action( 'init', array( $this, 'init' ) );

			add_action( 'init', array( $this, 'load_captcha_plugin' ), 11 );

			add_action( 'admin_init', array( $this, 'maybeFlushRewriteRules' ) );

			add_action( 'wp_before_admin_bar_render', array( $this, 'addCommunityToolbarItems' ), 20 );

			// Tribe common resources
			TribeCommonLibraries::register( 'wp-router', '0.3', $this->pluginPath . 'vendor/wp-router/wp-router.php' );

			add_action( 'tribe_settings_save_field_allowAnonymousSubmissions', array( $this, 'flushRewriteOnAnonymous' ), 10, 2 );

			add_filter( 'query_vars', array( $this, 'communityEventQueryVars' ) );

			add_filter( 'body_class', array( $this, 'setBodyClasses' ) );

			// Hook into templates class and add theme body classes
			add_filter( 'body_class', array( 'Tribe__Events__Templates', 'theme_body_class' ) );

			// ensure that we don't include tabindexes in our form fields
			add_filter( 'tribe_events_tab_index', '__return_null' );

			add_filter( 'tribe_tec_addons', array( __CLASS__, 'init_addon' ) );

			// options page hook
			add_action( 'tribe_settings_do_tabs', array( $this, 'doSettings' ), 10, 1 );

			add_action( 'wp_router_generate_routes', array( $this, 'addRoutes' ) );

			add_action( 'plugin_action_links_' . trailingslashit( $this->pluginDir ) . 'Main.php', array( $this, 'addLinksToPluginActions' ) );

			add_filter( 'tribe-events-pro-support', array( $this, 'support_info' ) );

			add_action( 'tribe_community_before_event_page', array( $this, 'maybe_delete_featured_image' ), 10, 1 );
			add_filter( 'tribe_help_tab_forums_url', array( $this, 'helpTabForumsLink' ), 100 );

			add_action( 'save_post', array( $this, 'flushPageIdTransient' ), 10, 1 );

			add_filter( 'user_has_cap', array( $this, 'filter_user_caps' ), 10, 3 );

			if ( is_multisite() ) {
				add_action( 'tribe_settings_get_option_value_pre_display', array( $this, 'multisiteDefaultOverride' ), 10, 3 );
			}

			add_filter( 'tribe_events_multiple_organizer_template', array( $this, 'overwrite_multiple_organizers_template' ) );

			$this->register_resources();
		}


		/**
		 * Registers this plugin as being active for other tribe plugins and extensions
		 *
		 * @return bool Indicates if Tribe Common wants the plugin to run
		 */
		public function register_active_plugin() {
			if ( ! function_exists( 'tribe_register_plugin' ) ) {
				return true;
			}

			return tribe_register_plugin( EVENTS_COMMUNITY_FILE, __CLASS__, self::VERSION );
		}


		/**
		 * Method used to overwrite the admin template for multiple organizers
		 *
		 * @param  string $template The original template
		 * @return string
		 */
		public function overwrite_multiple_organizers_template( $template ) {
			if ( is_admin() ) {
				return $template;
			}

			$community_file = Tribe__Events__Templates::getTemplateHierarchy( 'community/modules/organizer-multiple.php' );

			ob_start();
			include $community_file;
			$community_html = trim( ob_get_clean() );

			// Only use this URL if the template is not empty
			if ( empty( $community_html ) ) {
				return $template;
			}

			return $community_file;
		}

		/**
		 * Object accessor method for the Event_Form object
		 *
		 * @return Tribe__Events__Community__Event_Form
		 */
		public function event_form() {
			if ( ! $this->form ) {
				$event = null;

				if ( ! empty( $_GET['event_id'] ) ) {
					$event = get_post( absint( $_GET['event_id'] ) );
				}

				$this->form = new Tribe__Events__Community__Event_Form( $event );
			}

			return $this->form;
		}//end event_form

		/**
		 * Determines what assets to load.
		 *
		 * @return void
		 * @author Nick Ciske
		 * @since 1.0
		 */
		public function maybeLoadAssets() {
			if ( tribe_is_community_my_events_page() || tribe_is_community_edit_event_page() ) {
				$tec = Tribe__Events__Main::instance();

				Tribe__Events__Template_Factory::asset_package( 'chosen' );
				Tribe__Events__Template_Factory::asset_package( 'select2' );
				Tribe__Events__Template_Factory::asset_package( 'dropdowns' );
				Tribe__Events__Template_Factory::asset_package( 'admin-ui' );
				Tribe__Events__Template_Factory::asset_package( 'datepicker' );
				Tribe__Events__Template_Factory::asset_package( 'dialogue' );
				Tribe__Events__Template_Factory::asset_package( 'ecp-plugins' );
				Tribe__Events__Template_Factory::asset_package( 'admin' );

				// This comes from Common Lib
				wp_enqueue_style( 'tribe-jquery-ui-datepicker' );

				// calling our own localization because wp_localize_scripts doesn't support arrays or objects for values, which we need.
				add_action( 'admin_footer', array( $tec, 'printLocalizedAdmin' ) );

				// hook for other plugins
				do_action( 'tribe_events_enqueue' );

				add_action( 'wp_footer', array( $tec, 'printLocalizedAdmin' ) );

				// disable comments on this page
				add_filter( 'comments_template', array( $this, 'disable_comments_on_page' ) );

				// load EC resources
				add_action( 'wp_enqueue_scripts', array( $this, 'addScriptsAndStyles' ) );
			}
		}//end maybeLoadAssets

		/**
		 * registers scripts and styles
		 */
		public function register_resources() {
			$stylesheet_url = 'tribe-events-community.css';
			$stylesheet_url = Tribe__Events__Templates::locate_stylesheet( 'tribe-events/community/tribe-events-community.css', $stylesheet_url );
			$stylesheet_url = apply_filters( 'tribe_events_community_stylesheet_url', $stylesheet_url );

			tribe_asset(
				$this,
				Tribe__Events__Main::POSTTYPE . '-community-styles',
				$stylesheet_url,
				array()
			);

			tribe_asset(
				$this,
				Tribe__Events__Main::POSTTYPE . '-community',
				'tribe-events-community.js',
				array( 'jquery' )
			);

			tribe_assets(
				Tribe__Main::instance(),
				array(
					array( 'tribe-community-jquery-ui-theme', 'vendor/jquery/ui.theme.css' ),
					array( 'tribe-community-jquery-ui-datepicker', 'vendor/jquery/ui.datepicker.css' ),
				),
				'wp_enqueue_scripts',
				array(
					'tribe_is_community_edit_event_page',
					'tribe_is_community_my_events_page',
				)
			);
		}//end register_resources

		/**
		 * Disable comments on community pages.
		 *
		 * @return null
		 * @author imaginesimplicity
		 * @since 1.0.3
		 */
		public function disable_comments_on_page() {
			return Tribe__Events__Templates::getTemplateHierarchy( 'community/blank-comments-template' );
		}

		/**
		 * Add wprouter and callbacks.
		 *
		 * @param object $router The router object.
		 * @return void
		 * @author Nick Ciske
		 * @since 1.0
		 */
		public function addRoutes( $router ) {

			$tec_template = tribe_get_option( 'tribeEventsTemplate' );

			switch ( $tec_template ) {
				case '' :
					$template_name = Tribe__Events__Templates::getTemplateHierarchy( 'default-template' );
					break;
				case 'default' :
					$template_name = 'page.php';
					break;
				default :
					$template_name = $tec_template;
			}

			$template_name = apply_filters( 'tribe_events_community_template', $template_name );

			// edit venue
			$router->add_route( 'ce-edit-venue-route', array(
				'path' => '^' . $this->getCommunityRewriteSlug() . '/' . $this->rewriteSlugs['edit'] . '/' . $this->rewriteSlugs['venue'].'/(\d+)$',
				'query_vars' => array(
					'tribe_event_id' => 1,
				),
				'page_callback' => array(
					get_class(),
					'editCallback',
				),
				'page_arguments' => array(
					'tribe_event_id'
				),
				'access_callback' => true,
				'title' => __( 'Edit a Venue', 'tribe-events-community' ),
				'template' => $template_name,
			) );


			// edit organizer
			$router->add_route( 'ce-edit-organizer-route', array(
				'path' => '^' . $this->getCommunityRewriteSlug() . '/' . $this->rewriteSlugs['edit'] . '/' . $this->rewriteSlugs['organizer'] . '/(\d+)$',
				'query_vars' => array(
					'tribe_event_id' => 1,
				),
				'page_callback' => array(
					get_class(),
					'editCallback',
				),
				'page_arguments' => array(
					'tribe_event_id'
				),
				'access_callback' => true,
				'title' => __( 'Edit an Organizer', 'tribe-events-community' ),
				'template' => $template_name,
			) );

			// edit event
			$router->add_route( 'ce-edit-route', array(
				'path' => '^' . $this->getCommunityRewriteSlug() . '/' . $this->rewriteSlugs['edit'] . '/' . $this->rewriteSlugs['event'] . '/(\d+/?)$',
				'query_vars' => array(
					'tribe_community_event_id' => 1,
				),
				'page_callback' => array(
					get_class(),
					'editCallback',
				),
				'page_arguments' => array(
					'tribe_community_event_id'
				),
				'access_callback' => true,
				'title' => apply_filters( 'tribe_ce_edit_event_page_title', __( 'Edit an Event', 'tribe-events-community' ) ),
				'template' => $template_name,
			) );


			// edit redirect
			$router->add_route( 'ce-edit-redirect-route', array(
				'path' => '^' . $this->getCommunityRewriteSlug() . '/' . $this->rewriteSlugs['edit'] . '/(\d+)$',
				'query_vars' => array(
					'tribe_id' => 1,
				),
				'page_callback' => array(
					get_class(),
					'redirectCallback',
				),
				'page_arguments' => array(
					'tribe_id'
				),
				'access_callback' => true,
				'title' => __( 'Redirect', 'tribe-events-community' ),
				'template' => $template_name,
			) );


			// add event
			$router->add_route( 'ce-add-route', array(
				'path' => '^' . $this->getCommunityRewriteSlug() . '/' . $this->rewriteSlugs['add'] . '$',
				'query_vars' => array(),
				'page_callback' => array(
					get_class(),
					'addCallback',
				),
				'page_arguments' => array(),
				'access_callback' => true,
				'title' => apply_filters( 'tribe_ce_submit_event_page_title', __( 'Submit an Event', 'tribe-events-community' ) ),
				'template' => $template_name,
			) );

			$router->add_route( 'ce-redirect-to-add-route', array(
				'path' => $this->getCommunityRewriteSlug() . '/?$',
				'page_callback' => 'wp_redirect',
				'page_arguments' => array( home_url( $this->getCommunityRewriteSlug() . '/' . $this->rewriteSlugs['add'] ), 301 ),
				'template' => false,
				'access_callback' => true,
			) );

			// delete event
			$router->add_route( 'ce-delete-route', array(
				'path' => '^' . $this->getCommunityRewriteSlug() . '/' . $this->rewriteSlugs['delete'] . '/(\d+)$',
				'query_vars' => array(
					'tribe_event_id' => 1,
				),
				'page_callback' => array(
					get_class(),
					'deleteCallback',
				),
				'page_arguments' => array(
					'tribe_event_id'
				),
				'access_callback' => true,
				'title' => apply_filters( 'tribe_ce_remove_event_page_title', __( 'Remove an Event', 'tribe-events-community' ) ),
				'template' => $template_name,
			) );

			// list events
			$router->add_route( 'ce-list-route', array(
				'path' => '^' . $this->getCommunityRewriteSlug() . '/' . $this->rewriteSlugs['list'] . '(/page/(\d+))?/?$',
				'query_vars' => array(
					'page' => 2,
				),
				'page_callback' => array(
					get_class(),
					'listCallback',
				),
				'page_arguments' => array( 'page' ),
				'access_callback' => true,
				'title' => apply_filters( 'tribe_ce_event_list_page_title', __( 'My Events', 'tribe-events-community' ) ),
				'template' => $template_name,
			) );

		}

		/**
         * Used to ensure that CE views function when the Default Events Template is in use.
		 *
		 * We could consider using a template class at some future point, right now this provides
		 * a light functional means of letting users choose the Default Events Template for CE
		 * views.
		 *
		 * @param bool $print_before_after_override Whether before and after HTML should be printed
		 *                                          on the page in any case (`true`) or that should be
		 *                                          instead a consequence of the context.
		 */
		protected function default_template_compatibility( $print_before_after_override = false ) {
			add_filter( 'tribe_events_current_view_template', array( $this, 'default_template_placeholder' ) );
			Tribe__Events__Template_Factory::asset_package( 'events-css' );

			if ( false === $print_before_after_override && '' === tribe_get_option( 'tribeEventsTemplate', '' ) ) {
				$this->should_print_before_after_html = false;
			} else {
				$this->should_print_before_after_html = true;
		}
		}

		/**
		 * We need to provide an "inner" template if community views are being displayed using the
		 * default template.
		 *
		 * @param $unused_template
		 * @return string
		 */
		public function default_template_placeholder( $unused_template ) {
			return Tribe__Events__Templates::getTemplateHierarchy( 'community/default-placeholder.php' );
		}

		/**
		 * Redirect user to the right place.
		 *
		 * @param string $tribe_id The page being viewed.
		 * @return void
		 * @author Nick Ciske
		 * @since 1.0
		 */
		public static function redirectCallback( $tribe_id ) {

			$tce = self::instance();

			if ( $tribe_id != $tce->rewriteSlugs['event'] && $tribe_id != $tce->rewriteSlugs['venue'] && $tribe_id != $tce->rewriteSlugs['organizer'] ) {
				// valid route
				$context = $tce->getContext( 'edit', $tribe_id );
				$url = $tce->getUrl( 'edit', $tribe_id, null, $context['post_type'] );
				wp_safe_redirect( esc_url_raw( $url ) ); exit;
			} else {
				// invalid route, redirect to My Events
				wp_safe_redirect( esc_url_raw( $tce->getUrl( 'list' ) ) ); exit;
			}

		}

		/**
		 * Display event editing.
		 *
		 * @param $tribe_id The event being viewed.
		 * @return string The form to display.
		 * @since 1.0
		 * @author Nick Ciske
		 */
		public static function editCallback( $tribe_id ) {

			$tce = self::instance();

			$tce->isEditPage = true;
			add_filter( 'edit_post_link', array( $tce, 'removeEditPostLink' ) );

			$tce->removeFilters();

			$context = $tce->getContext( 'edit', $tribe_id );
			$tce->default_template_compatibility();

			if ( ! isset( $context['post_type'] ) ) {
				return __( 'Not found.', 'tribe-events-community' );
			}

			if ( $context['post_type'] == Tribe__Events__Main::VENUE_POST_TYPE ) {
				return $tce->doVenueForm( $tribe_id );
			}

			if ( $context['post_type'] == Tribe__Events__Main::ORGANIZER_POST_TYPE ) {
				return $tce->doOrganizerForm( $tribe_id );
			}

			if ( $context['post_type'] == Tribe__Events__Main::POSTTYPE ) {
				return $tce->doEventForm( $tribe_id );
			}

		}

		/**
		 * Display event deletion.
		 *
		 * @param int $tribe_event_id The event id.
		 * @return void
		 * @author Nick Ciske
		 * @since 1.0
		 */
		public static function deleteCallback( $tribe_event_id ) {

			$tce = self::instance();
			$tce->removeFilters();
			echo $tce->doDelete( $tribe_event_id );

		}


		/**
		 * Display event adding.
		 *
		 * @return void
		 * @author Nick Ciske
		 * @since 1.0
		 */
		public static function addCallback() {

			$tce = self::instance();

			$tce->isEditPage = true;
			add_filter( 'edit_post_link', array( $tce, 'removeEditPostLink' ) );

			$tce->removeFilters();
			$tce->default_template_compatibility();
			echo $tce->doEventForm();
		}

		/**
		 * Display event listings.
		 *
		 * @param string $page
		 * @return void
		 * @author Nick Ciske
		 * @since 1.0
		 */
		public static function listCallback( $page = 1 ) {

			$tce = self::instance();

			$tce->isMyEvents = true;
			add_filter( 'edit_post_link', array( $tce, 'removeEditPostLink' ) );
			$tce->removeFilters();
			echo $tce->doMyEvents( $page );
		}


		/**
		 * Determine whether to redirect a user back to his events.
		 *
		 * @return void
		 * @author Nick Ciske
		 * @since 1.0
		 */
		public function maybeRedirectMyEvents() {

			if ( ! is_admin() ) {
				//redirect my events with no args to todays page
				global $paged;
				if ( empty( $paged ) && isset( $_GET['tribe_action'] ) && $_GET['tribe_action'] == 'list' ) {
					$paged = 1;
					wp_safe_redirect( esc_url_raw( $this->getUrl( 'list', null, $paged ) ) ); exit;
				}
			}
		}

		/**
		 * Check if we're on the page specified with [tribe_community_events].
		 *
		 * @return bool
		 * @author Nick Ciske
		 * @since 1.0
		 */
		public function isTcePage() {

			if ( is_404() )
				return false;

			$page_id = $this->get_community_page_id();
			if ( empty ( $page_id ) )
				return false;

			return get_the_ID() == $page_id;
		}

		/**
		 * Take care of ugly URLs.
		 *
		 * @since 1.0
		 * @author Nick Ciske
		 * @return void
		 */
		public function redirectUglyUrls() {

			if ( ! is_admin() ) {
				// disable title shortcode
				add_shortcode( 'tribe_community_events', '__return_null' );
				add_shortcode( 'tribe_community_events_title', create_function( '', 'return apply_filters( "tribe_ce_submit_event_page_title", __( "Submit an Event", "tribe-events-community" ) );' ) );
				add_filter( 'the_title', 'do_shortcode' );

				if ( $this->isTcePage() ) {
					$url = $this->getUrl( 'add' );
				}

				// redirect ugly link URLs to pretty permalinks
				if ( isset( $_GET['tribe_action'] ) ) {
					if ( isset( $_GET['paged'] ) ) {
						$url = $this->getUrl( $_GET['tribe_action'], null, $_GET['paged'] );
					} elseif ( isset( $_GET['tribe_id'] ) ) {
						$url = $this->getUrl( $_GET['tribe_action'], $_GET['tribe_id'] );
					} else {
						$url = $this->getUrl( $_GET['tribe_action'] );
					}
				}

				if ( isset( $url ) ) {
					wp_safe_redirect( esc_url_raw( $url ) ); exit;
				}
			}

		}

		public function notice_permalinks() {
			?>
			<div class="error"><p>
				<?php esc_html_e( 'Community Events requires non-default (pretty) permalinks to be enabled or the [tribe_community_events] shortcode to exist on a page.', 'tribe-events-community' ); ?>
			</p></div>
			<?php
		}

		/**
		 * Get the URL for a specific action.
		 *
		 * @param string $action The action being performed.
		 * @param int $id The id of whatever is being done, if applicable.
		 * @param string $page The page being used.
		 * @param string $post_type The post type being used.
		 * @return string The url.
		 * @author Nick Ciske
		 * @since 1.0
		 * @todo move recurrence related tasks to pro
		 */
		public function getUrl( $action, $id = null, $page = null, $post_type = null ) {

			if ( ! empty( $id ) && $action == 'edit' && function_exists( 'tribe_is_recurring_event' ) && tribe_is_recurring_event( $id ) ) {

				if ( $parent = wp_get_post_parent_id( $id ) ) {
					$id = $parent;
				}
			}

			if ( '' == get_option( 'permalink_structure' ) ) {
				// pretty permalinks off
				if ( ! $this->get_community_page_id() ) {
					add_action( 'admin_notices', array( $this, 'notice_permalinks' ) );
					return '';
				}

				$args = array( 'tribe_action' => $action );
				if ( $id )
					$args['tribe_id'] = $id;
				if ( $page )
					$args['paged'] = $page;

				return add_query_arg( $args, get_permalink( $this->get_community_page_id() ) );
			} else {
				if ( $id ) {
					if ( $post_type ) {
						if ( $post_type == Tribe__Events__Main::POSTTYPE )
							return home_url() . '/' . $this->getCommunityRewriteSlug() . '/' . $this->rewriteSlugs[ $action ] . '/' . $this->rewriteSlugs['event'] . '/' . $id . '/';

						if ( $post_type == Tribe__Events__Main::ORGANIZER_POST_TYPE )
							return home_url() . '/' . $this->getCommunityRewriteSlug() . '/' . $this->rewriteSlugs[ $action ] . '/' . $this->rewriteSlugs['organizer'] . '/' . $id . '/';

						if ( $post_type == Tribe__Events__Main::VENUE_POST_TYPE )
							return home_url() . '/' . $this->getCommunityRewriteSlug() . '/' . $this->rewriteSlugs[ $action ] . '/' . $this->rewriteSlugs['venue'] . '/' . $id . '/';
					} else {
						return home_url() . '/' . $this->getCommunityRewriteSlug() . '/' . $this->rewriteSlugs[ $action ] . '/' . $id . '/';
					}
				} else {
					if ( $page ) {
						return home_url() . '/' . $this->getCommunityRewriteSlug() . '/' . $this->rewriteSlugs[ $action ] . '/page/' . $page . '/';
					} else {
						return home_url() . '/' . $this->getCommunityRewriteSlug() . '/' . $this->rewriteSlugs[ $action ];
					}
				}
			}
		}

		public function getCommunityRewriteSlug() {
			$tec = Tribe__Events__Main::instance();
			$events_slug = $tec->getRewriteSlug();
			return $events_slug.'/'.$this->communityRewriteSlug;
		}

		/**
		 * Get delete button for an event.
		 *
		 * @param object $event The event to get the button for.
		 * @return string The button's output.
		 * @author Nick Ciske
		 * @since 1.0
		 */
		public function getDeleteButton( $event ) {

			if ( ! $this->allowUsersToDeleteSubmissions ) {
				$output = '';
				return $output;
			}
			$label = __( 'Delete', 'tribe-events-community' );
			$message = __( 'Are you sure?', 'tribe-events-community' );
			if ( class_exists( 'Tribe__Events__Pro__Main' ) && tribe_is_recurring_event( $event->ID ) ) {
				if ( empty( $event->post_parent ) ) {
					$label = __( 'Delete All', 'tribe-events-community' );
					$message = __( 'Are you sure you want to permanently delete all instances of this recurring event?', 'tribe-events-community' );
				} else {
					$message = __( 'Are you sure you want to permanently delete this instance of a recurring event?', 'tribe-events-community' );
				}
			}

			$output  = ' <span class="delete wp-admin events-cal">| <a rel="nofollow" class="submitdelete" href="';
			$output .= esc_url( wp_nonce_url( $this->getUrl( 'delete', $event->ID ), 'tribe_community_events_delete' ) );
			$output .= '" onclick="return confirm(\'' . $message . '\')">' . $label . '</a></span>';
			return $output;
		}

		/**
		 * Get edit button for an event.
		 *
		 * @param object $event The event object.
		 * @param string $label The label for the button.
		 * @param string $before What comes before the button.
		 * @param string $after What comes after the button.
		 * @return string $output The button's output.
		 * @author Nick Ciske
		 * @since 1.0
		 */
		public function getEditButton( $event, $label = 'Edit', $before = '', $after = '' ) {
			if ( ! isset( $event->EventStartDate ) ) {
				$event->EventStartDate = tribe_get_event_meta( $event->ID, '_EventStartDate', true );
			}

			$output  = $before . '<a rel="nofollow" href="';
			$output .= esc_url( $this->getUrl( 'edit', $event->ID, null, Tribe__Events__Main::POSTTYPE ) );
			$output .= '"> ' . $label . '</a>' . $after;
			return $output;

		}

		/**
		 * Get the featured image delete button.
		 *
		 * @param object $event The event id.
		 * @return string The button's output.
		 * @author Paul Hughes
		 * @since 1.0
		 */
		public function getDeleteFeaturedImageButton( $event = null ) {
			if ( ! isset( $event ) ) {
				$event = get_post();
			}

			if ( ! has_post_thumbnail( $event->ID ) ) {
				return '';
			}

			$url = add_query_arg( 'action', 'deleteFeaturedImage', wp_nonce_url( $this->getUrl( 'edit', $event->ID, null, Tribe__Events__Main::POSTTYPE ), 'tribe_community_events_featured_image_delete' ) );

			if ( class_exists( 'Tribe__Events__Pro__Main' ) && tribe_is_recurring_event( $event->ID ) ) {
				$url = add_query_arg( 'eventDate', date( 'Y-m-d', strtotime( $event->EventStartDate ) ), $url );
			}

			$output = '<a rel="nofollow" class="submitdelete" href="' . esc_url( $url ) . '">' . esc_html__( 'Delete Image', 'tribe-events-community' ) . '</a>';
			return $output;
		}

		/**
		 * Get title for a page.
		 *
		 * @param string $action The action being performed.
		 * @param string $post_type The post type being viewed.
		 * @return string The title.
		 * @since 1.0
		 */
		public function getTitle( $action, $post_type ) {
			$i18n['delete'] = array(
				Tribe__Events__Main::POSTTYPE => __( 'Remove an Event', 'tribe-events-community' ),
				Tribe__Events__Main::VENUE_POST_TYPE => __( 'Remove a Venue', 'tribe-events-community' ),
				Tribe__Events__Main::ORGANIZER_POST_TYPE => __( 'Remove an Organizer', 'tribe-events-community' ),
				'unknown' => __( 'Unknown Post Type', 'tribe-events-community' ),
			);

			$i18n['default'] = array(
				Tribe__Events__Main::POSTTYPE => __( 'Edit an Event', 'tribe-events-community' ),
				Tribe__Events__Main::VENUE_POST_TYPE => __( 'Edit a Venue', 'tribe-events-community' ),
				Tribe__Events__Main::ORGANIZER_POST_TYPE => __( 'Edit an Organizer', 'tribe-events-community' ),
				'unknown' => __( 'Unknown Post Type', 'tribe-events-community' ),
			);

			if ( empty( $action ) || 'delete' !== $action ) {
				$action = 'default';
			}

			/**
			 * Allow users to hook and change the Page Title for all the existing pages.
			 * Don't remove the 'unknown' key from the array
			 */
			$i18n = apply_filters( 'tribe_ce_i18n_page_titles', $i18n, $action, $post_type );

			if ( ! empty( $i18n[ $action ][ $post_type ] ) ){
				return $i18n[ $action ][ $post_type ];
			} else {
				return $i18n[ $action ]['unknown'];
			}
		}

		/**
		 * Set context for where we are.
		 *
		 * @param string $action The current action.
		 * @param string $post_type The current post type.
		 * @param int $id The current id.
		 * @return void
		 * @author Nick Ciske
		 * @since 1.0
		 */
		private function setContext( $action, $post_type, $id ) {

			$this->context = array(
				'title' => $this->getTitle( $action, $post_type ),
				'post_type' => $post_type,
				'action' => $action,
				'id' => $id,
			);

		}

		/**
		 * Get context for where we are.
		 *
		 * @param string $action The current action.
		 * @param int $tribe_id The current post id.
		 * @return string The current context.
		 * @author Nick Ciske
		 * @since 1.0
		 */
		public function getContext( $action = null, $tribe_id = null ) {

			// get context from query string
			if ( isset( $_GET['tribe_action'] ) )
			 $action = $_GET['tribe_action'];

			if ( isset( $_GET['tribe_id'] ) )
			 $tribe_id = intval( $_GET['tribe_id'] );

			$tribe_id = intval( $tribe_id );

			if ( isset( $this->context ) )
				return $this->context;

			switch ( $action ) {
				case 'edit':
					$context = array(
						'title' => 'Test',
						'action' => $action,
					);
					if ( $tribe_id ) {
						$post = get_post( $tribe_id );
						if ( is_object( $post ) ) {
							$context = array(
								'title' => $this->getTitle( $action, $post->post_type ),
								'action' => $action,
								'post_type' => $post->post_type,
								'id' => $tribe_id,
							);
						}
					}

				break;

				case 'list':
					$context = array(
						'title' => apply_filters( 'tribe_ce_event_list_page_title', __( 'My Events', 'tribe-events-community' ) ),
						'action' => $action,
						'id' => null,
					);
				break;

				case 'delete':

					if ( $tribe_id )
						$post = get_post( $tribe_id );

					$context = array(
						'title' => $this->getTitle( $action, $post->post_type ),
						'post_type' => $post->post_type,
						'action' => $action,
						'id' => $tribe_id,
					);

				break;

				default:
					$context = array(
						'title' => apply_filters( 'tribe_ce_submit_event_page_title', __( 'Submit an Event', 'tribe-events-community' ) ),
						'action' => 'add',
						'id' => null,
					);
			}

			$this->context = $context;
			return $context;

		}

		/**
		 * Set the title for the shortcode.
		 *
		 * @return string The title.
		 * @author Nick Ciske
		 * @since 1.0
		 */
		public function doShortCodeTitle() {

			$action = '';
			$tribe_id = '';

			$context = $this->getContext( $action, $tribe_id );

			return $context['title'];
		}

		/**
		 * Output the shortcode's content based on the content.
		 *
		 * @return string The shortcode's content.
		 * @author Nick Ciske
		 * @since 1.0
		 */
		public function doShortCode() {

			if ( ! is_page() || ! in_the_loop() || tribe_is_event() )
				return '<p>' . __( 'This shortcode can only be used in pages.', 'tribe-events-community' ) . '</p>';

			$action = '';
			$tribe_id = '';

			$context = $this->getContext( $action, $tribe_id );

			switch ( $context['action'] ) {

				case 'edit':

					if ( $context['post_type'] == Tribe__Events__Main::VENUE_POST_TYPE ) {
						return $this->doVenueForm( $context['id'] );
					}

					if ( $context['post_type'] == Tribe__Events__Main::ORGANIZER_POST_TYPE ) {
						return $this->doOrganizerForm( $context['id'] );
					}

					if ( $context['post_type'] == Tribe__Events__Main::POSTTYPE ) {
						return $this->doEventForm( $context['id'] );
					}

				break;

				case 'list':

					return $this->doMyEvents( null, true );

				break;

				case 'delete':

					return $this->doDelete( $context['id'] );

				break;

				case 'add':
				default:

					return $this->doEventForm();
			}
		}

		/**
		 * Unhook content filters from the content.
		 *
		 * @return void
		 * @author Nick Ciske
		 * @since 1.0
		 */
		public function removeFilters() {
			remove_filter( 'the_content', 'wpautop' );
			remove_filter( 'the_content', 'wptexturize' );
		}

		/**
		 * Set the body classes.
		 *
		 * @param array $classes The current array of body classes.
		 * @return array The body classes to add.
		 * @since 1.0.1
		 * @author Paul Hughes
		 */
		public function setBodyClasses( $classes ) {
			if ( tribe_is_community_my_events_page() ) {
				$classes[] = 'tribe_community_list';
			}

			if ( tribe_is_community_edit_event_page() ) {
				$classes[] = 'tribe_community_edit';
			}

			return $classes;
		}

		/**
		 * Upon page save, flush the transient for the page-id.
		 *
		 * @param int $post_id The current post id.
		 * @return void
		 * @author Paul Hughes
		 * @since 1.0.5
		 */
		public function flushPageIdTransient( $post_id ) {
			if ( get_post_type( $post_id ) == 'page' ) {
				delete_transient( 'tribe-community-events-page-id' );
			}
		}

		/**
		 * Enqueue scripts & styles.
		 *
		 * @return void
		 * @author Nick Ciske
		 * @since 1.0
		 */
		public function addScriptsAndStyles() {
			wp_enqueue_style( Tribe__Events__Main::POSTTYPE . '-community-styles' );
			wp_enqueue_script( Tribe__Events__Main::POSTTYPE . '-community' );

			do_action( 'tribe_community_events_enqueue_resources' );
		}

		/**
		 * Adds the event specific query vars to Wordpress.
		 *
		 * @param array $qvars Array of query variables.
		 * @return array Filtered array of query variables.
		 * @author Nick Ciske
		 * @link http://codex.wordpress.org/Custom_Queries#Permalinks_for_Custom_Archives
		 * @since 1.0
		 */
		public function communityEventQueryVars( $qvars ) {
			$qvars[] = 'tribe_event_id';
			$qvars[] = 'tribe_venue_id';
			$qvars[] = 'tribe_organizer_id';
			return $qvars;
		}

		protected function create_event_object_from_submission( $submission ) {
			return (object) $submission;
		}

		/**
		 * Send email alert to email list when an event is submitted.
		 *
		 * @param int $tribe_event_id The event ID.
		 * @return void
		 * @author Nick Ciske
		 * @since 1.0
		 */
		public function sendEmailAlerts( $tribe_event_id ) {
			$post = get_post( intval( $tribe_event_id ) );

			$subject = sprintf( '[%s] ' . __( 'Community Events Submission', 'tribe-events-community' ) . ':', wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) ) . ' "' . $post->post_title . '"';

			// Get Message HTML from Email Template
			ob_start();
			include Tribe__Events__Templates::getTemplateHierarchy( 'community/email-template' );
			$message = ob_get_clean();

			$headers = array( 'Content-Type: text/html' );
			$h = implode( "\r\n", $headers ) . "\r\n";

			if ( is_array( $this->emailAlertsList ) ) {
				foreach ( $this->emailAlertsList as $email ) {
					wp_mail( trim( $email ), $subject, $message, $h );
				}
			}

		}

		/**
		 * Searches current user's events for the event closest to
		 * today but not in the past, and returns the 'page' that event is on.
		 *
		 * @return object The page object.
		 * @author Nick Ciske
		 * @since 1.0
		 */
		public function findTodaysPage() {

			if ( WP_DEBUG ) delete_transient( 'tribe_community_events_today_page' );
			$todaysPage = get_transient( 'tribe_community_events_today_page' );

			$todaysPage = null;

			if ( ! $todaysPage ) {
				$current_user = wp_get_current_user();
				if ( is_object( $current_user ) && ! empty( $current_user->ID ) ) {
					$args = array(
						'posts_per_page' => -1,
						'paged' => 0,
						'nopaging' => true,
						'author' => $current_user->ID,
						'post_type' => Tribe__Events__Main::POSTTYPE,
						'post_status' => 'any',
						'order' => 'ASC',
						'orderby' => 'meta_value',
						'meta_key' => '_EventStartDate',
						'meta_query' => array(
							'key' => '_EventStartDate',
							'value' => date( 'Y-m-d 00:00:00' ),
							'compare' => '<=',
						),
					);

					$tp = new WP_Query( $args );

					$pc = $tp->post_count;

					unset( $tp );

					$todaysPage = floor( $pc / $this->eventsPerPage );

					//handle bounds
					if ( $todaysPage <= 0 )
						$todaysPage = 1;

					set_transient( 'tribe-community-events_today_page', $todaysPage, 60 * 60 * 1 ); //cache for an hour
				}
			}

			return $todaysPage;

		}


		/**
		 * Delete view for an event.
		 *
		 * @param int $tribe_event_id The event's ID.
		 * @return string The deletion view.
		 * @author Nick Ciske
		 * @since 1.0
		 */
		public function doDelete( $tribe_event_id ) {
			$this->default_template_compatibility();

			if ( wp_verify_nonce( $_GET['_wpnonce'], 'tribe_community_events_delete' ) && current_user_can( 'delete_post', $tribe_event_id ) ) {
				//does this event even exist?
				$event = get_post( $tribe_event_id );

				if ( isset( $event->ID ) ) {
					if ( $this->trashItemsVsDelete ) {
						wp_trash_post( $tribe_event_id );
						$this->enqueueOutputMessage( __( 'Trashed Event #', 'tribe-events-community' ) . $tribe_event_id );
					} else {
						wp_delete_post( $tribe_event_id, true );
						$this->enqueueOutputMessage( __( 'Deleted Event #', 'tribe-events-community' ) . $tribe_event_id );
					}
				} else {
					$this->enqueueOutputMessage( sprintf( __( 'This event (#%s) does not appear to exist.', 'tribe-events-community' ), $tribe_event_id ) );
				}
			} else {
				$this->enqueueOutputMessage( __( 'You do not have permission to delete this event.', 'tribe-events-community' ) );
			}

			$output = '<div id="tribe-community-events" class="delete">';

			ob_start();
			$this->addScriptsAndStyles();
			include Tribe__Events__Templates::getTemplateHierarchy( 'community/modules/delete' );
			$output .= ob_get_clean();

			$back_url = apply_filters( 'tribe_events_community_deleted_event_back_url', home_url( $this->getCommunityRewriteSlug() . '/list' ) );
			$output .= '<a href="' . esc_url( $back_url ) . '">&laquo; ' . _x( 'Back', 'As in "go back to previous page"', 'tribe-events-community' ) . '</a>';

			$output .= '</div>';

			return $output;

		}

		/**
		 * Event editing form.
		 *
		 * @param int $id the event's ID.
		 * @return string The editing view markup.
		 * @author Nick Ciske
		 * @since 1.0
		 */
		public function doEventForm( $id = null ) {
			do_action( 'tribe_community_before_event_page', $id );
			$events_label_singular = tribe_get_event_label_singular();
			$events_label_plural = tribe_get_event_label_plural();
			$events_label_singular_lowercase = tribe_get_event_label_singular_lowercase();

			$output    = '';
			$show_form = true;
			$event     = null;

			if ( $id ) {
				$edit = true;
				$tribe_event_id = $id = intval( $id );
			} else {
				$edit = false;
				$tribe_event_id = null;
			}

			if ( $tribe_event_id && class_exists( 'Tribe__Events__Pro__Main' ) && tribe_is_recurring_event( $tribe_event_id ) ) {
				$this->enqueueOutputMessage( sprintf( __( '%sWarning:%s You are editing a recurring %s. All changes will be applied to the entire series.', 'tribe-events-community' ), '<b>', '</b>', $events_label_singular_lowercase ), 'error' );
			}

			if ( $edit && $tribe_event_id ) {
				$event = get_post( intval( $tribe_event_id ) );
			}

			// TODO: Not entirely sure this check is necessary. -- jbrinley
			if ( $edit && ( ! $tribe_event_id || ! isset( $event->ID ) ) ) {
				$this->enqueueOutputMessage( sprintf( __( '%s not found.', 'tribe-events-community' ), $events_label_singular ), 'error' );
				$output = $this->outputMessage( null, false );
				$show_form = false;
			}

			// login check
			if ( ( ! $this->allowAnonymousSubmissions && ! is_user_logged_in() ) || ( $edit && $tribe_event_id && ! is_user_logged_in() ) ) {
				do_action( 'tribe_ce_event_submission_login_form' );
				$output .= $this->login_form( __( 'Please log in first.', 'tribe-events-community' ) );
				return $output;
			}

			// security check
			if ( $edit && $tribe_event_id && ! current_user_can( 'edit_post', $tribe_event_id ) ) {
				$output .= '<p>' . sprintf( __( 'You do not have permission to edit this %s.', 'tribe-events-community' ), $events_label_singular_lowercase ) . '</p>';
				return $output;
			}

			// file upload check
			if ( $this->max_file_size_exceeded() ) {
				$this->enqueueOutputMessage( sprintf( __( 'The file you attempted to upload exceeded the maximum file size of %1$s.', 'tribe-events-community' ), size_format( wp_max_upload_size() ) ), 'error' );
			}

			$this->loadScripts = true;
			do_action( 'tribe_ce_before_event_submission_page' );
			$output .= '<div id="tribe-community-events" class="form">';

			if ( $this->allowAnonymousSubmissions || is_user_logged_in() ) {
				$errors = array();
				$submission = $this->get_submitted_event();

				if ( ! empty( $submission ) ) {
					if ( isset( $submission['post_ID'] ) ) {
						$tribe_event_id = absint( $submission['post_ID'] );
					}//end if

					$handler = new Tribe__Events__Community__Submission_Handler( $submission, $tribe_event_id );

					if ( $handler->validate() ) {
						add_filter( 'tribe-post-origin', array( $this, 'filterPostOrigin' ) );
						$tribe_event_id = $handler->save();
						remove_filter( 'tribe-post-origin', array( $this, 'filterPostOrigin' ) );
						delete_transient( 'tribe_community_events_today_page' ); //clear cache

						if ( $tribe_event_id ) {
							// email alerts
							if ( $this->emailAlertsEnabled ) {
								$this->sendEmailAlerts( $tribe_event_id );
							}
						} else {
							// This is only to prevent bad images
							$event = $this->create_event_object_from_submission( $handler->get_submission() );
						}
					} else {
						$event = $this->create_event_object_from_submission( $handler->get_submission() );
						$errors = $handler->get_invalid_fields();
					}

					$messages = $handler->get_messages();
					$has_errors = in_array( 'error', wp_list_pluck( $messages, 'type' ) );

					foreach ( $messages as $m ) {
						if ( $has_errors && 'error' !== $m->type ) {
							continue;
						}
						$this->enqueueOutputMessage( $m->message, $m->type );
					}
				}

				if ( isset( $tribe_event_id ) && $edit ) {
					$event = get_post( intval( $tribe_event_id ) );
				} elseif ( empty( $event ) ) {
					$event = new stdClass();
				}

				$GLOBALS['post'] = $event;

				$show_form = apply_filters( 'tribe_community_events_show_form', $show_form );

				if ( $show_form ) {
					$tec_template = tribe_get_option( 'tribeEventsTemplate' );

					if ( ! empty( $tec_template ) ) {
						ob_start();
						tribe_events_before_html();
						$output .= ob_get_clean();
					}

					do_action( 'tribe_ce_before_event_submission_page_template' );

					if ( empty( $submission ) || $this->messageType == 'error' ) {
						$required = $this->required_fields_for_submission();
						$this->event_form()->set_event( $event );
						$this->event_form()->set_required_fields( $required );
						$this->event_form()->set_error_fields( $errors );
						$output .= $this->event_form()->render();
					} else {
						ob_start();
						include Tribe__Events__Templates::getTemplateHierarchy( 'community/modules/header-links' );
						$output .= ob_get_clean();
					}

					if ( ! empty( $tec_template ) ) {
						ob_start();
						tribe_events_after_html();
						$output .= ob_get_clean();
					}
				}
			}
			$output .= '</div>';

			wp_reset_query();

			return $output;
		}

		/**
		 * If a request comes in to delete a featured image,
		 * delete it and redirect back to the event page
		 *
		 * @see do_action('before_tribe_community_event_page')
		 * @see Tribe__Events__Community__Main::doEventForm()
		 * @param int $event_id
		 * @return void
		 */
		public function maybe_delete_featured_image( $event_id ) {
			// Delete the featured image, if there was a request to do so.
			if ( $event_id && isset( $_GET['action'] ) && $_GET['action'] == 'deleteFeaturedImage' && wp_verify_nonce( $_GET['_wpnonce'], 'tribe_community_events_featured_image_delete' ) && current_user_can( 'edit_post', $event_id ) ) {
				$featured_image_id = get_post_thumbnail_id( $event_id );
				if ( $featured_image_id ) {
					delete_post_meta( $event_id, '_thumbnail_id' );
					$image_parent = wp_get_post_parent_id( $featured_image_id );
					if ( $image_parent == $event_id ) {
						wp_delete_attachment( $featured_image_id, true );
					}
				}
				$redirect = $_SERVER['REQUEST_URI'];
				$redirect = remove_query_arg( '_wpnonce', $redirect );
				$redirect = remove_query_arg( 'action', $redirect );
				wp_safe_redirect( esc_url_raw( $redirect ), 302 );
				exit();
			}
		}

		public function get_view_edit_links( $event_id ) {
			$edit_link = $view_link = '';

			if ( get_post_status( $event_id ) == 'publish' ) {
				$view_link = sprintf( '<a href="%s" class="view-event">%s</a>',
					esc_url( get_permalink( $event_id ) ),
					__( 'View', 'tribe-events-community' ) );
			}

			if ( current_user_can( 'edit_post', $event_id ) ) {
				$edit_link = sprintf( '<a href="%s" class="edit-event">%s</a>',
					esc_url( tribe_community_events_edit_event_link( $event_id ) ),
					__( 'Edit', 'tribe-events-community' )
				);
			}

			// If the user isn't allowed to edit and the post wasn't published, return an empty string
			if ( empty( $edit_link ) && empty( $view_link ) ) {
				return '';
			}

			$separator = '<span class="sep"> | </span>';
			return '(' . tribe_separated_field( $view_link, $separator, $edit_link ) . ')';
		}

		private function get_submitted_event() {
			if ( empty( $_POST[ 'community-event' ] ) ) {
				return array();
			}

			if ( ! check_admin_referer( 'ecp_event_submission' ) ) {
				return array();
			}
			$submission = $_POST;
			return $submission;
		}

		public function required_fields_for_submission() {

			/**
			 * Required Community Event Fields
			 *
			 * @parm array of required fields (case sensitive) post_content, EventStartDate, EventStartHour, EventStartMinute, EventStartMeridian, EventEndDate, EventEndMinute, EventEndHour, EventEndMeridian, is_recurring, EventCurrencySymbol, tax_input (For Event Categories), venue, organizer, EventShowMapLink, EventURL
			 */
			return apply_filters( 'tribe_events_community_required_fields', array( 'post_content', 'post_title' ) );
		}

		public function login_form( $caption = '' ) {
			ob_start();
			echo '<p>' . $caption . '</p>';
			wp_login_form();
			wp_register( '<div class="register">', '</div>', true );
			return ob_get_clean();
		}

		/**
		 * Main form for events.
		 *
		 * @param int $tribe_venue_id The event's venue ID.
		 * @return string The form.
		 * @author Nick Ciske
		 * @since 1.0
		 */
		public function doVenueForm( $tribe_venue_id ) {
			$tribe_venue_id = intval( $tribe_venue_id );

			$output = '';

			add_filter( 'tribe-post-origin', array( $this, 'filterPostOrigin' ) );

			if ( empty( $tribe_venue_id ) ) {
				$output .= '<p>' . __( 'Venue not found.', 'tribe-events-community' ) . '</p>';
				return $output;
			}

			if ( ! is_user_logged_in() ) {
				return $this->login_form( __( 'Please log in to edit this venue', 'tribe-events-community' ) );
			}

			if ( ! current_user_can( 'edit_post', $tribe_venue_id ) ) {
				$output .= '<p>' . __( 'You do not have permission to edit this venue.', 'tribe-events-community' ) . '</p>';
				return $output;
			}

			$this->loadScripts = true;
			$output .= '<div id="tribe-community-events" class="form venue">';

			if ( ( isset( $_POST[ 'community-event' ] ) && $_POST[ 'community-event' ] ) && check_admin_referer( 'ecp_venue_submission' ) ) {
				if ( isset( $_POST[ 'post_title' ] ) && $_POST[ 'post_title' ] ) {
					$_POST['ID'] = $tribe_venue_id;
					$scrubber = new Tribe__Events__Community__Venue_Submission_Scrubber( $_POST );
					$_POST = $scrubber->scrub();

					remove_action( 'save_post_'.Tribe__Events__Main::VENUE_POST_TYPE, array( Tribe__Events__Main::instance(), 'save_venue_data' ), 16, 2 );

					wp_update_post( array(
						'post_title' => $_POST[ 'post_title' ],
						'ID' => $tribe_venue_id,
						'post_content' => $_POST[ 'post_content' ],
					) );

					Tribe__Events__API::updateVenue( $tribe_venue_id, $_POST['venue'] );

					$this->enqueueOutputMessage( __( 'Venue updated.', 'tribe-events-community' ) );
						/*
						// how it should work, but updateVenue does not return a boolean
						if ( Tribe__Events__API::updateVenue($tribe_venue_id, $_POST) ) {
						$this->enqueueOutputMessage( __("Venue updated.",'tribe-events-community') );
						}else{
						$this->enqueueOutputMessage( __("There was a problem saving your venue, please try again.",'tribe-events-community'), 'error' );
						}
						*/
				} else {
					$this->enqueueOutputMessage( __( 'Venue name cannot be blank.', 'tribe-events-community' ), 'error' );
				}
			} else {
				if ( isset( $_POST[ 'community-event' ] ) ) {
					$this->enqueueOutputMessage( __( 'There was a problem updating your venue, please try again.', 'tribe-events-community' ), 'error' );
				}
			}

			global $post;
			$post = get_post( intval( $tribe_venue_id ) );

			ob_start();
			include Tribe__Events__Templates::getTemplateHierarchy( 'community/edit-venue' );

			$output .= ob_get_clean();

			wp_reset_query();

			$output .= '</div>';

			remove_filter( 'tribe-post-origin', array( $this, 'filterPostOrigin' ) );
			return $output;

		}

		/**
		 * Organizer form for events.
		 *
		 * @param int $tribe_organizer_id The organizer's ID.
		 * @return string The form.
		 * @author Nick Ciske
		 * @since 1.0
		 */
		public function doOrganizerForm( $tribe_organizer_id ) {
			$tribe_organizer_id = intval( $tribe_organizer_id );

			add_filter( 'tribe-post-origin', array( $this, 'filterPostOrigin' ) );

			$output = '';

			if ( empty( $tribe_organizer_id ) ) {
				return '<p>' . __( 'Organizer not found.', 'tribe-events-community' ) . '</p>';
			}

			if ( ! is_user_logged_in() ) {
				return $this->login_form( __( 'Please log in to edit this organizer', 'tribe-events-community' ) );
			}

			if (  ! current_user_can( 'edit_post', $tribe_organizer_id ) ) {
				$output .= '<p>' . __( 'You do not have permission to edit this organizer.', 'tribe-events-community' ) . '</p>';
				return $output;
			}

			$this->loadScripts = true;
			$output .= '<div id="tribe-community-events" class="form organizer">';

			if ( ( isset( $_POST[ 'community-event' ] ) && $_POST[ 'community-event' ] ) && check_admin_referer( 'ecp_organizer_submission' ) ) {
				if ( isset( $_POST[ 'post_title' ] ) && $_POST[ 'post_title' ] ) {
					$_POST['ID'] = $tribe_organizer_id;
					$scrubber = new Tribe__Events__Community__Organizer_Submission_Scrubber( $_POST );
					$_POST = $scrubber->scrub();

					remove_action( 'save_post_'.Tribe__Events__Main::ORGANIZER_POST_TYPE, array( Tribe__Events__Main::instance(), 'save_organizer_data' ), 16, 2 );

					wp_update_post( array(
						'post_title' => $_POST[ 'post_title' ],
						'ID' => $tribe_organizer_id,
						'post_content' => $_POST[ 'post_content' ],
					) );

					Tribe__Events__API::updateOrganizer( $tribe_organizer_id, $_POST['organizer'] );
					$this->enqueueOutputMessage( __( 'Organizer updated.', 'tribe-events-community' ) );

						/*
						// how it should work, but updateOrganizer does not return a boolean
						if ( Tribe__Events__API::updateOrganizer($tribe_organizer_id, $_POST) ) {
							$this->enqueueOutputMessage( __("Organizer updated.",'tribe-events-community') );
						}else{
							$this->enqueueOutputMessage( __("There was a problem saving your organizer, please try again.",'tribe-events-community'), 'error' );
						}
						*/
				} else {
					$this->enqueueOutputMessage( __( 'Organizer name cannot be blank.', 'tribe-events-community' ), 'error' );
				}
			} else {
				if ( isset( $_POST[ 'community-event' ] ) ) {
					$this->enqueueOutputMessage( __( 'There was a problem updating this organizer, please try again.', 'tribe-events-community' ), 'error' );
				}
			}

			global $post;
			$post = get_post( intval( $tribe_organizer_id ) );

			ob_start();
			include Tribe__Events__Templates::getTemplateHierarchy( 'community/edit-organizer' );

			$output .= ob_get_clean();

			$output .= '</div>';

			remove_filter( 'tribe-post-origin', array( $this, 'filterPostOrigin' ) );

			return $output;

		}

		/**
		 * Show the current user's events.
		 *
		 * @param int  $page Pagination.
		 * @param bool $print_before_after_override
		 *
		 * @return string The page.
		 *
		 * @author Nick Ciske
		 * @since  1.0
		 */
		public function doMyEvents( $page = null, $print_before_after_override = false ) {
			$output = '';
			$this->default_template_compatibility( $print_before_after_override );

			$this->loadScripts = true;
			do_action( 'tribe_ce_before_event_list_page' );
			$output .= '<div id="tribe-community-events" class="list">';
			ob_start();

			if ( $this->should_print_before_after_html ) {
			tribe_events_before_html();
			}

			$output .= ob_get_clean();

			if ( is_user_logged_in() ) {

				$current_user = wp_get_current_user();

				global $paged;

				if ( empty( $paged ) && ! empty( $page ) ) {
					$paged = $page;
				}

				add_filter( 'tribe_query_can_inject_date_field', '__return_false' );

				$args = array(
					'posts_per_page' => $this->eventsPerPage,
					'paged' => $paged,
					'author' => $current_user->ID,
					'post_type' => Tribe__Events__Main::POSTTYPE,
					'post_status' => 'any',
					'eventDisplay' => empty( $_GET['eventDisplay'] ) ? 'list' : $_GET['eventDisplay'],
					'tribeHideRecurrence' => false,
					'orderby' => 'meta_value',
					'order' => 'DESC',
				);
				$args = apply_filters( 'tribe_ce_my_events_query', $args );
				$events = tribe_get_events( $args, true );

				remove_filter( 'tribe_query_can_inject_date_field', '__return_false' );

				do_action( 'tribe_ce_before_event_list_page_template' );
				ob_start();
				include Tribe__Events__Templates::getTemplateHierarchy( 'community/event-list' );
				$output .= ob_get_clean();

				wp_reset_query();
			} else {
				do_action( 'tribe_ce_event_list_login_form' );
				$output .= $this->login_form( __( 'Please log in to view your events', 'tribe-events-community' ) );
			}

			ob_start();

			if ( $this->should_print_before_after_html ) {
			tribe_events_after_html();
			}

			$output .= ob_get_clean();
			$output .= '</div>';

			return $output;

		}

		/**
		 * Indicates whether or not the image size was exceeded
		 *
		 * @return boolean
		 */
		public function max_file_size_exceeded() {
			return (
				isset( $_SERVER['CONTENT_LENGTH'] )
				&& (int) $_SERVER['CONTENT_LENGTH'] > wp_max_upload_size()
			);
		}

		/**
		 * Honeypot to prevent spam.
		 *
		 * @return void
		 * @author Nick Ciske
		 * @since 1.0
		 */
		public function formSpamControl() {

			$output = '';

			if ( ! is_user_logged_in() ) {
				// add honeypot for anon submissions
				$output .= sprintf( '<p class="aes"><input type="text" name="tribe-not-title" id="tribe-not-title" value=""> <label for="tribe-not-title">%s</label></p>', __( 'Fake Title', 'tribe-events-community' ) );
				$output .= sprintf( '<input type="hidden" name="render_timestamp" value="%d" />', time() );
			}

			echo apply_filters( 'tribe_community_events_form_spam_control', $output );
		}

		/**
		 * If we have a spam submission, just kick the user away
		 * @return void
		 */
		public function spam_check( $submission ) {
			$timestamp = empty( $submission['render_timestamp'] ) ? 0 : intval( $submission['render_timestamp'] );
			if ( ! empty( $submission['tribe-not-title'] ) || $timestamp == 0 || time() - $timestamp < 3 ) { // you can't possibly fill out this form in 3 seconds
				wp_safe_redirect( home_url(), 303 );
				exit();
			}
		}

		/**
		 * Display event details.
		 *
		 * @param object $event The event post
		 * @return void
		 * @author Nick Ciske
		 * @uses Tribe__Events__Main::EventsChooserBox()
		 * @since 1.0
		 * @TODO is this method used anywhere?
		 */
		public function formEventDetails( $event = null ) {
			global $post;
			$tec = Tribe__Events__Main::instance();

			// TEC doesn't like an empty $post object
			if ( ! $event ) {
				// error with php 5.4
				if ( ! is_object( $post ) ) {
					$post = new stdClass;
				}

				if ( isset( $post->ID ) ) {
					$old_post_id = $post->ID;
				}
				$post->ID = 0;
				$post->post_type = Tribe__Events__Main::POSTTYPE;
			}

			if ( isset( $event->ID ) && $event->ID ) {
				$tec->EventsChooserBox( $event );
			} else {
				$tec->EventsChooserBox();
			}

			if ( ! $event && isset( $old_post_id ) ) {
				$post->ID = $old_post_id;
			}
		}

		/**
		 * Form event title.
		 *
		 * @param object $event The event to display the tile for.
		 * @return void
		 * @author Nick Ciske
		 * @since 1.0
		 */
		public function formTitle( $event = null ) {
			$title = get_the_title( $event );
			if ( empty( $title ) && ! empty( $_POST['post_title'] ) ) {
				$title = stripslashes( $_POST['post_title'] );
			}
			?>
			<input type="text" name="post_title" value="<?php esc_attr_e( $title ); ?>"/>
			<?php
		}

		/**
		 * Form event content.
		 *
		 * @param object $event The event to display the tile for.
		 * @return void
		 * @author Nick Ciske
		 * @since 1.0
		 */
		public function formContentEditor( $event = null ) {
			if ( $event == null ) {
				$event = get_post();
			}
			if ( $event ) {
				$post_content = $event->post_content;
			} elseif ( ! empty( $_POST['post_content'] ) ) {
				$post_content = stripslashes( $_POST['post_content'] );
			} else {
				$post_content = '';
			}

			// if the admin wants the rich editor, and they are using WP 3.3, show the WYSIWYG, otherwise default to just a text box
			if ( $this->useVisualEditor && function_exists( 'wp_editor' ) ) {
				$settings = array(
					'wpautop' => true,
					'media_buttons' => false,
					'editor_class' => 'frontend',
					'textarea_rows' => 5,
				);

				wp_editor( $post_content, 'tcepostcontent', $settings );
			} else {
				?><textarea name="tcepostcontent"><?php
					echo esc_textarea( $post_content );
				?></textarea><?php
			}
		}

		/**
		* Form category dropdown.
		*
		* @param object $event The event to display the tile for.
		* @param array $currently_selected DEPRECATED Category ids that should start selected (theoretically passed from the $_POST variable).
		* @return void
		* @author Nick Ciske, Paul Hughes
		* @since 1.0
		*/
		public function formCategoryDropdown( $event = null, $currently_selected = array() ) {
			_deprecated_function(
				'Tribe__Events__Community__Main::formCategoryDropdown',
				'4.2',
				'Tribe__Events__Community__Modules__Taxonomy_Block::the_category_checklist'
			);
			Tribe__Events__Community__Modules__Taxonomy_Block::instance()->the_category_checklist( $event );
		}

		/**
		 * Display status icon.
		 *
		 * @param string $status The post status.
		 * @return string The status image element markup.
		 * @author Nick Ciske
		 * @since 1.0
		 */
		public function getEventStatusIcon( $status ) {
			$icon = str_replace( ' ', '-', $status ) . '.png';

			if ( $status == 'publish' ) {
				$status = 'Published';
			}
			if ( file_exists( get_stylesheet_directory() . '/events/community/' . $icon ) ) {
				return '<img width="16" height="16" src="' . get_stylesheet_directory_uri() . '/events/community/' . $icon . '" alt="' . ucwords( $status ) . ' icon" title="' . ucwords( $status ) . '" class="icon">';
			}elseif ( file_exists( get_template_directory() . '/events/community/icons/' . $icon ) ) {
				return '<img width="16" height="16" src="' . get_template_directory_uri() . '/events/community/' . $icon . '" alt="' . ucwords( $status ) . ' icon" title="' . ucwords( $status ) . '" class="icon">';
			} else {
				return '<img width="16" height="16" src="' . $this->pluginUrl . '/src/resources/images/' . $icon . '" alt="' . ucwords( $status ) . ' icon" title="' . ucwords( $status ) . '" class="icon">';
			}

		}

		/**
		 * Filter pagination.
		 *
		 * @param object $query The query to paginate.
		 * @param int $pages The pages.
		 * @param int $range The range.
		 * @return string The pagination links.
		 * @author Nick Ciske
		 * @link http://www.kriesi.at/archives/how-to-build-a-wordpress-post-pagination-without-plugin
		 * @since 1.0
		 */
		public function pagination( $query, $pages = 0, $range = 3 ) {
			$output    = '';
			$showitems = ( $range * 2 ) + 1;

			global $paged;
			if ( empty( $paged ) )
				$paged = 1;

			if ( $pages == 0 ) {
				//global $wp_query;
				$pages = ceil( $query->found_posts / $this->eventsPerPage );

				//echo $pages;

				if ( ! $pages ) {
					$pages = 1;
				}
			}

			if ( $paged > $pages ) {
				$this->enqueueOutputMessage( __( 'The requested page number was not found.', 'tribe-events-community' ) );
			}
			if ( 1 != $pages ) {
				add_filter( 'get_pagenum_link', array( $this, 'fix_pagenum_link' ) );

				$output .= "<div class='pagination'>";
				if ( $paged > 2 && $paged > $range + 1 && $showitems < $pages )
					$output .= "<a href='" . esc_url( get_pagenum_link( 1 ) ) . "'>&laquo;</a>";
				if ( $paged > 1 && $showitems < $pages )
					$output .= "<a href='" . esc_url( get_pagenum_link( $paged - 1 ) ) . "'>&lsaquo;</a>";

				for ( $i = 1; $i <= $pages; $i++ ) {
					if ( 1 != $pages && ( ! ( $i >= $paged + $range + 1 || $i <= $paged - $range - 1 ) || $pages <= $showitems ) ) {
						$output .= ( $paged == $i ) ? '<span class="current">' . $i . '</span>' : '<a href="' . esc_url( get_pagenum_link( $i ) ) . '" class="inactive">' . $i . '</a>';
					}
				}

				if ( $paged < $pages && $showitems < $pages )
					$output .= "<a href='" . esc_url( get_pagenum_link( $paged + 1 ) ) . "'>&rsaquo;</a>";
				if ( $paged < $pages - 1 && $paged + $range - 1 < $pages && $showitems < $pages )
					$output .= "<a href='" . esc_url( get_pagenum_link( $pages ) ) . "'>&raquo;</a>";
				$output .= "</div>\n";
			}

			return $output;

		}

		/**
		 * Get the template file with an output buffer.
		 *
		 * @param string $template_path The path.
		 * @param string $template_file The file.
		 * @return string The file's output.
		 * @author Nick Ciske
		 * @since 1.0
		 */
		public function get_template( $template_path, $template_file ) {
			ob_start();
			include $this->getTemplatePath( $template_path );
			return ob_get_clean();
		}

		/**
		 * Get a file's path.
		 *
		 * @param string $path The path.
		 * @param string $file The file.
		 * @return string The file's path.
		 * @author Nick Ciske
		 * @since 1.0
		 */
		public static function getTemplatePath( $path, $file ) {
			_deprecated_function( __FUNCTION__, '2.1', 'Tribe__Events__Community__Templates::getTemplateHierarchy()' );

			// protect duplicate call to views
			$template_path = $path == 'views' ? '' : $path;
			return Tribe__Events__Templates::getTemplateHierarchy( $file, array(
				'subfolder' => $path,
				'namespace' => 'community',
				'plugin_path' => self::instance()->pluginPath,
			) );
		}

		/**
		 * Filter the limit query.
		 *
		 * @return string The modified query.
		 * @author Nick Ciske
		 * @since 1.0
		 */
		public function limitQuery() {
			global $paged;
			if ( $paged - 1 <= 0 ) {
				$page = 0;
			} else {
				$page = $paged - 1;
			}

			$lq = 'LIMIT ' . ( ( $this->eventsPerPage * $page ) ) . ',' . $this->eventsPerPage;
			return $lq;
		}

		/**
		 * Add messages to the error/notice queue
		 *
		 * @param string $message
		 * @param null|string $type
		 * @todo support type per message
		 * @author Peter Chester
		 * @since 3.1
		 */
		public function enqueueOutputMessage( $message, $type = null ) {
			$this->messages[] = $message;
			if ( $type ) {
				$this->messageType = $type;
			}
		}

		/**
		 * Output a message to the user.
		 *
		 * @param string $type The message type.
		 * @param bool $echo Whether to display or return the message.
		 * @return string The message.
		 * @author Nick Ciske
		 * @since 1.0
		 */
		public function outputMessage( $type = null, $echo = true ) {
			if ( ! $type && ! $this->messageType ) {
				$type = 'updated';
			} elseif ( ! $type && $this->messageType ) {
				$type = $this->messageType;
			}

			$errors = null;

			if ( isset( $this->messages ) && ! empty( $this->messages ) )
				$errors = array(
					 array(
					 	'type' => $type,
						'message' => '<p>' . join( '</p><p>', $this->messages ) . '</p>',
					),
				);

			$errors = apply_filters( 'tribe_community_events_form_errors', $errors );

			$output = '';

			if ( is_array( $errors ) ) {
				foreach ( $errors as $error ) {
					$output .= '<div id="message" class="' . $error[ 'type' ] . '"><p>' . $error[ 'message' ] . '</p></div>';
				}

				unset( $this->messages );
			}

			if ( $echo ) {
				echo $output;
			} else {
				return $output;
			}

		}

		/**
		 * Filter pagination links.
		 *
		 * @param string $result The link.
		 * @return string The filtered link.
		 * @author Nick Ciske
		 * @since 1.0
		 */
		public function fix_pagenum_link( $result ) {

			// pretty permalinks - fix page one to have args so we don't redirect to todays's page
			if ( '' != get_option( 'permalink_structure' ) && ! strpos( $result, '/page/' ) ) {
				$result = $this->getUrl( 'list', null, 1 );
			}

			// ugly links - fix page one to have args so we don't redirect to todays's page
			if ( '' == get_option( 'permalink_structure' ) && ! strpos( $result, 'paged=' ) ) {
				$result = $this->getUrl( 'list', null, 1 );
			}

			return $result;

		}

		/**
		 * @param array $user_caps The capabilities the user has
		 * @param array $requested_caps The capabilities the user needs
		 * @param array $args [0] = The specific cap requested, [1] = The user ID
		 * @return array mixed
		 */
		public function filter_user_caps( $user_caps, $requested_caps, $args ) {
			if ( ! empty( $args[1] ) ) {
				if ( $this->allowUsersToEditSubmissions ) {
					$user_caps['edit_tribe_events'] = true;
					$user_caps['edit_tribe_venues'] = true;
					$user_caps['edit_tribe_organizers'] = true;

					$user_caps['edit_published_tribe_events'] = true;
					$user_caps['edit_published_tribe_venues'] = true;
					$user_caps['edit_published_tribe_organizers'] = true;
				}

				if ( $this->allowUsersToDeleteSubmissions ) {
					$user_caps['delete_tribe_events'] = true;
					$user_caps['delete_tribe_venues'] = true;
					$user_caps['delete_tribe_organizers'] = true;

					$user_caps['delete_published_tribe_events'] = true;
					$user_caps['delete_published_tribe_venues'] = true;
					$user_caps['delete_published_tribe_organizers'] = true;
				}
			}
			return $user_caps;
		}

		/**
		 * Determine if the specified user can edit the specified post.
		 *
		 * @param int|null $id The current post ID.
		 * @param string $post_type The post type.
		 * @return bool Whether the use has the permissions to edit a given post.
		 * @author Nick Ciske
		 * @since 1.0
		 * @deprecated since version 3.1
		 */
		public function userCanEdit( $id = null, $post_type = null ) {
			// if we're talking about a specific post, use standard WP permissions
			if ( $id ) {
				return current_user_can( 'edit_post', $id );
			}

			if ( empty( $post_type ) || ! is_user_logged_in() ) {
				return false;
			}

			// only supports Tribe Post Types
			if ( ! in_array( $post_type, Tribe__Events__Main::getPostTypes() ) ) {
				return false;
			}

			// admin override
			if ( is_super_admin() || current_user_can( 'manage_options' ) ) {
				return true;
			}

			return $this->allowUsersToEditSubmissions;
		}

		/**
		 * Add a settings tab.
		 *
		 * Additionally sets up a filter to append information to the existing events template setting tooltip.
		 *
		 * @return void
		 * @author Nick Ciske
		 * @since 1.0
		 */
		public function doSettings() {
			require_once $this->pluginPath . 'src/admin-views/community-options-template.php';
			new Tribe__Settings_Tab( 'community', __( 'Community', 'tribe-events-community' ), $communityTab );
			add_filter( 'tribe_field_tooltip', array( $this, 'amend_template_tooltip' ), 10, 3 );
		}

		/**
		 * This method filters the tooltip for the tribeEventsTemplate setting to make it clear that it also
		 * impacts on Community Events output.
		 *
		 * @param $text
		 * @param $tooltip
		 * @param $field = null (this may not provided when tribe_field_tooltip callbacks take place)
		 * @return string
		 */
		public function amend_template_tooltip( $text, $tooltip, $field = null ) {
			if ( null === $field || 'tribeEventsTemplate' !== $field->id ) {
				return $text;
			}
			$description = __( 'This template is also used for Community Events.', 'tribe-events-community' );
			return str_replace( $tooltip, "$tooltip $description ", $text );
		}

		/**
		 * If the anonymous submit setting is changed, flush the rewrite rules.
		 *
		 * @param string $field The name of the field being saved.
		 * @param string $value The new value of the field.
		 * @return void
		 * @author Paul Hughes
		 * @since 1.0.1
		 */
		public function flushRewriteOnAnonymous( $field, $value ) {
			if ( $field == 'allowAnonymousSubmissions' && $value != $this->allowAnonymousSubmissions ) {
				Tribe__Events__Main::flushRewriteRules();
			}
		}

		/**
		 * Add a community events origin to the audit system.
		 *
		 * @return string The community events slug.
		 * @author Nick Ciske
		 * @since 1.0
		 */
		public function filterPostOrigin() {
			return 'community-events';
		}


		/**
		 * Get all options for the plugin.
		 *
		 * @param bool $force
		 * @return array The current settings for the plugin.
		 * @since 1.0
		 * @author Nick Ciske
		 */
		public static function getOptions( $force = false ) {
			if ( ! isset( self::$options ) || $force ) {
				$options       = get_option( self::OPTIONNAME, array() );
				self::$options = apply_filters( 'tribe_community_events_get_options', $options );
			}
			return self::$options;
		}

		/**
		 * Get value for a specific option.
		 *
		 * @param string $optionName Name of option.
		 * @param mixed $default Default value.
		 * @param bool $force
		 * @return mixed Results of option query.
		 * @author Nick Ciske
		 * @since 1.0
		 */
		public function getOption( $optionName, $default = '', $force = false ) {
			if ( ! $optionName ) {
				return;
			}

			if ( ! isset( self::$options ) || $force ) {
				self::getOptions( $force );
			}

			$option = $default;
			if ( isset( self::$options[ $optionName ] ) ) {
				$option = self::$options[ $optionName ];
			} elseif ( is_multisite() && isset( self::$tribeCommunityEventsMuDefaults ) && is_array( self::$tribeCommunityEventsMuDefaults ) && in_array( $optionName, array_keys( self::$tribeCommunityEventsMuDefaults ) ) ) {
				$option = self::$tribeCommunityEventsMuDefaults[ $optionName ];
			}

			return apply_filters( 'tribe_get_single_option', $option, $default, $optionName );
		}

		public function setOption( $optionName, $value ) {
			if ( ! $optionName ) {
				return;
			}

			if ( ! isset( self::$options ) ) {
				self::getOptions();
			}
			self::$options[ $optionName ] = $value;
			update_option( self::OPTIONNAME, self::$options );
		}

		/**
		 * Get the plugin's path.
		 *
		 * @return string The path.
		 * @author Nick Ciske
		 * @since 1.0
		 */
		public static function getPluginPath() {
			return self::instance()->pluginPath;
		}

		/**
		 * Get the current user's role.
		 *
		 * @return string The role.
		 * @author Nick Ciske
		 * @since 1.0
		 */
		public function getCurrentUserRole() {
			$user_roles = $this->getUserRoles();
			if ( empty( $user_roles ) ) {
				return false;
			}
			return array_shift( $user_roles );
		}

		public function getUserRoles( $user_id = 0 ) {
			$user_id = $user_id ? $user_id : get_current_user_id();
			if ( empty( $user_id ) ) {
				return array();
			}

			$user = new WP_User( $user_id );
			if ( isset( $user->roles ) ) {
				return $user->roles;
			}
			return array();
		}

		/**
		 * Facilitate blocking specific roles from the admin environment.
		 */
		public function blockRolesFromAdmin() {
			//Get Current User ID
			$user_id = get_current_user_id();

			// Let WordPress worry about admin access for unauthenticated users
			if ( ! is_user_logged_in() ) {
				return;
			}
			//If User Cannot Access Admin Hide the Admin Bar
			if ( ! $this->user_can_access_admin( $user_id ) ) {
				add_filter( 'show_admin_bar', '__return_false' );
			}

			// If it is not an admin request - or if it is an ajax request - then we don't need to interfere
			if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
				return;
			}

			// If the user has access privileges then we don't need to interfere
			if ( $this->user_can_access_admin( $user_id ) ) {
				return;
			}

			// Redirect user to appropriate location
			wp_safe_redirect( wp_validate_redirect( trailingslashit( $this->blockRolesRedirect ), home_url() ) );
			exit;
		}

		/**
		 * Determine if the user has a role that allows him to access the admin
		 *
		 * @param int $user_id
		 * @return bool Whether the user is allowed to access the admin (by this plugin)
		 * @since 3.1
		 */
		protected function user_can_access_admin( $user_id = 0 ) {
			if ( ! is_array( $this->blockRolesList ) || empty( $this->blockRolesList ) ) {
				return true;
			}

			if ( is_super_admin( $user_id ) ) {
				return true;
			}
			$user_roles = $this->getUserRoles( $user_id );

			// if a user has multiple roles, still let him in if he has a non-blocked role
			$diff = array_diff( $user_roles, $this->blockRolesList );
			if ( empty( $diff ) ) {
				return false;
			}
			return true;
		}

		/**
		 * Get the appropriate logout URL for the current user
		 *
		 * @return string The logout URL
		 * @since 3.1
		 */
		public function logout_url() {
			if ( $this->user_can_access_admin() ) {
				$redirect = '';
			} else {
				$redirect = wp_validate_redirect( trailingslashit( $this->blockRolesRedirect ), home_url() );
			}
			return wp_logout_url( $redirect );
		}

		/**
		 * Add the communiy events toolbar items.
		 *
		 * @return void
		 * @author Paul Hughes
		 * @since 1.0.1
		 */
		public function addCommunityToolbarItems() {
			global $wp_admin_bar;

			$wp_admin_bar->add_group( array(
				'id' => 'tribe-community-events-group',
				'parent' => 'tribe-events-add-ons-group',
			) );

			$wp_admin_bar->add_menu( array(
				'id' => 'tribe-community-events-submit',
				'title' => sprintf( __( 'Community: Submit %s', 'tribe-events-community' ), tribe_get_event_label_singular() ),
				'href' => esc_url( $this->getUrl( 'add' ) ),
				'parent' => 'tribe-community-events-group',
			) );

			if ( is_user_logged_in() ) {
				$wp_admin_bar->add_menu( array(
					'id' => 'tribe-community-events-my-events',
					'title' => sprintf( __( 'Community: My %s', 'tribe-events-community' ), tribe_get_event_label_plural() ),
					'href' => esc_url( $this->getUrl( 'list' ) ),
					'parent' => 'tribe-community-events-group',
				) );
			}

			if ( current_user_can( 'manage_options' ) ) {
				$wp_admin_bar->add_menu( array(
					'id' => 'tribe-community-events-settings-sub',
					'title' => __( 'Community Events', 'tribe-events-community' ),
					'href' => Tribe__Settings::instance()->get_url( array( 'tab' => 'community' ) ),
					'parent' => 'tribe-events-settings',
				) );
			}
		}

		/**
		 * Return additional action for the plugin on the plugins page.
		 *
		 * @param array $actions
		 * @return array
		 * @since 1.0.2
		 */
		public function addLinksToPluginActions( $actions ) {
			if ( class_exists( 'Tribe__Events__Main' ) ) {
				$actions['settings'] = '<a href="' . Tribe__Settings::instance()->get_url( array( 'tab' => 'community' ) ) . '">' . __( 'Settings', 'tribe-events-community' ) . '</a>';
			}
			return $actions;
		}


		/**
		 * Load the plugin's textdomain.
		 *
		 * @return void
		 * @since 1.0
		 */
		public function loadTextDomain() {
			$mopath = $this->pluginDir . 'lang/';
			$domain = 'tribe-events-community';

			// If we don't have Common classes load the old fashioned way
			if ( ! class_exists( 'Tribe__Main' ) ) {
				load_plugin_textdomain( $domain, false, $mopath );
			} else {
				// This will load `wp-content/languages/plugins` files first
				Tribe__Main::instance()->load_text_domain( $domain, $mopath );
			}
		}

		/**
		 * Init the plugin.
		 *
		 * @return void
		 * @since 1.0
		 */
		public function init() {
			$this->communityRewriteSlug = $this->getOption( 'communityRewriteSlug', 'community' );

			$this->rewriteSlugs['edit']   = sanitize_title( __( 'edit', 'tribe-events-community' ) );
			$this->rewriteSlugs['add']    = sanitize_title( __( 'add', 'tribe-events-community' ) );
			$this->rewriteSlugs['delete'] = sanitize_title( __( 'delete', 'tribe-events-community' ) );
			$this->rewriteSlugs['list']   = sanitize_title( __( 'list', 'tribe-events-community' ) );

			$this->rewriteSlugs['venue']     = sanitize_title( __( 'venue', 'tribe-events-community' ) );
			$this->rewriteSlugs['organizer'] = sanitize_title( __( 'organizer', 'tribe-events-community' ) );
			$this->rewriteSlugs['event']     = sanitize_title( __( 'event', 'tribe-events-community' ) );
		}

		public function load_captcha_plugin() {
			$this->captcha = apply_filters( 'tribe_community_events_captcha_plugin', new Tribe__Events__Community__Captcha__Recaptcha_V2() );
			if ( empty( $this->captcha ) ) {
				$this->captcha = new Tribe__Events__Community__Captcha__Null_Captcha();
			}
			$this->captcha->init();
		}

		public function captcha() {
			return $this->captcha;
		}

		/**
		 * Singleton instance method.
		 *
		 * @return Tribe__Events__Community__Main The instance
		 * @author Nick Ciske
		 * @since 1.0
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Sets the setting variable that says the rewrite rules should be flushed upon plugin load.
		 *
		 * @return void
		 * @author Paul Hughes
		 * @since 1.0.1
		 */
		public static function activateFlushRewrite() {
			$options = self::getOptions();
			$options['maybeFlushRewrite'] = true;
			update_option( self::OPTIONNAME, $options );
		}

		/**
		 * Add Community Events to the list of add-ons to check required version.
		 *
		 * @param array $plugins
		 * @return array The existing plugins including CE.
		 * @author Paul Hughes
		 * @since 1.0.1
		 */
		public static function init_addon( $plugins ) {
			$plugins['TribeCE'] = array(
				'plugin_name'      => 'The Events Calendar: Community Events',
				'required_version' => self::REQUIRED_TEC_VERSION,
				'current_version'  => self::VERSION,
				'plugin_dir_file' => basename( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/tribe-community-events.php',
			);

			return $plugins;
		}

		/**
		 * Checks if it should flush rewrite rules (after plugin is loaded).
		 *
		 * @return void
		 * @author Paul Hughes
		 * @since 1.0.1
		 */
		 public function maybeFlushRewriteRules() {
		 	if ( $this->maybeFlushRewrite == true ) {
		 		Tribe__Events__Main::flushRewriteRules();
		 		$options = self::getOptions();
				$options['maybeFlushRewrite'] = false;
				update_option( self::OPTIONNAME, $options );
			}
		}

		/**
		 * Removes the Edit link from My Events and Edit Event community pages.
		 *
		 * @param string $content
		 * @return string An empty string.
		 * @author Paul Hughes
		 * @since 1.0.3
		 */
		public function removeEditPostLink( $content ) {
			$content = '';
			return $content;
		}

		/**
		 * Return the forums link as it should appear in the help tab.
		 *
		 * @param string $content
		 * @return string
		 * @author Paul Hughes
		 * @since 1.0.3
		 */
		public function helpTabForumsLink( $content ) {
			$promo_suffix = '?utm_source=helptab&utm_medium=plugin-community&utm_campaign=in-app';
			return ( isset( Tribe__Events__Main::$tecUrl ) ? Tribe__Events__Main::$tecUrl : Tribe__Events__Main::$tribeUrl ) . 'support/forums/' . $promo_suffix;
		}

		/**
		 * Allows multisite installs to override defaults for settings.
		 *
		 * @param mixed $value The current default.
		 * @param string $key The option key.
		 * @param array $field The field.
		 * @return mixed The MU default value of the option.
		 * @author Paul Hughes
		 * @since 1.0.6
		 */
		public function multisiteDefaultOverride( $value, $key, $field ) {
			if ( isset( $field['parent_option'] ) && $field['parent_option'] == self::OPTIONNAME ) {
				$current_options = $this->getOptions();
				if ( isset( $current_options[ $key ] ) ) {
					return $value;
				} elseif ( isset( self::$tribeCommunityEventsMuDefaults[ $key ] ) ) {
					$value = self::$tribeCommunityEventsMuDefaults[ $key ];
				}
			}
			return $value;
		}

		/**
		 * Find the ID of the page with the community shortcode on it
		 * @return int
		 */
		private function get_community_page_id() {
			if ( isset( $this->tcePageId ) ) {
				return $this->tcePageId;
			}
			$this->tcePageId = $this->findPageByShortcode( '[tribe_community_events]' );
			return $this->tcePageId;
		}


		/**
		 * Find the page id that has the specified shortcode in it.
		 *
		 * @param string $shortcode The shortcode to search for.
		 * @return int The page id.
		 * @author Nick Ciske
		 * @since 1.0
		 */
		public function findPageByShortcode( $shortcode ) {

			global $wpdb;
			$id = get_transient( 'tribe-community-events-page-id' );

			if ( $id === false ) {
				$id = $wpdb->get_var( $wpdb->prepare( "SELECT id from $wpdb->posts WHERE post_content LIKE '%%%s%%' AND post_type in ('page')", $shortcode ) );
				set_transient( 'tribe-community-events-page-id', $id, ( 60 * 60 * 24 * 10 ) );
			}
			return $id;
		}

		/**
		 * Add support for shortcodes in WP >= 4.4 wp_get_document_title calls
		 *
		 * @param array $title Array of title parts
		 *
		 * @return array
		 */
		public function support_shortcodes_in_post_title( $parts ) {
			foreach ( $parts as &$part ) {
				$part = do_shortcode( $part );
			}

			return $parts;
		}

		/**
		 * Add in Community Event Slugs to the System Info after Settings
		 *
		 * @param $systeminfo
		 *
		 * @return mixed
		 */
		public function support_info( $systeminfo ) {

			if ( '' != get_option( 'permalink_structure' ) ) {
				$community_urls = array( 'Community Add' => esc_url( $this->getUrl( 'add' ) ), 'Community List' => esc_url( $this->getUrl( 'list' ) ) );
				$systeminfo     = Tribe__Main::array_insert_after_key( 'Settings', $systeminfo, $community_urls );
			}

			return $systeminfo;
		}
	}
}
