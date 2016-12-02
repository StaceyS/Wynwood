<?php
/**
 * Templating functionality for Tribe Events Calendar
 */

// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( ! class_exists( 'Tribe__Events__Community__Templates' ) ) {

	/**
	 * Handle views and template files.
	 */
	class Tribe__Events__Community__Templates {

		public function __construct() {
			add_filter( 'tribe_events_template_paths', array( $this, 'add_community_template_paths' ) );
			add_filter( 'tribe_support_registered_template_systems', array( $this, 'add_template_updates_check' ) );
		}

		/**
		 * Filter template paths to add the community plugin to the queue
		 *
		 * @param array $paths
		 * @return array $paths
		 * @author Peter Chester
		 * @since 3.1
		 */
		public function add_community_template_paths( $paths ) {
			$paths['community'] = Tribe__Events__Community__Main::instance()->pluginPath;
			return $paths;
		}

		/**
		 * Register Community Events with the template updates checker.
		 *
		 * @param array $plugins
		 *
		 * @return array
		 */
		public function add_template_updates_check( $plugins ) {
			// ET+ views can be in one of a range of different subdirectories (eddtickets, shopptickets
			// etc) so we will tell the template checker to simply look in views/tribe-events and work
			// things out from there
			$plugins[ __( 'Community Events', 'tribe-events-community' ) ] = array(
				Tribe__Events__Community__Main::VERSION,
				Tribe__Events__Community__Main::instance()->pluginPath . 'src/views/community',
				trailingslashit( get_stylesheet_directory() ) . 'tribe-events/community',
			);

			return $plugins;
		}

		/********** Singleton **********/

		/**
		 * @var Tribe__Events__Community__Templates $instance
		 */
		protected static $instance;

		/**
		 * Static Singleton Factory Method
		 *
		 * @return Tribe__Events__Community__Templates
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) ) {
				$className = __CLASS__;
				self::$instance = new $className;
			}
			return self::$instance;
		}
	}
}
