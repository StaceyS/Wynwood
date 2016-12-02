<?php


class Tribe__Events__Community__Modules__Taxonomy_Block {

	/**
	 * @var int
	 */
	public static $shown_item_default = 9;

	/**
	 * @var static
	 */
	protected static $instance;

	/**
	 * Whether anonymous submissions are allowed or not.
	 *
	 * @var bool
	 */
	protected $allow_anonymous_submissions;
	/**
	 * @var Tribe__Events__Community__Walker_Category_Checklist
	 */
	private $walker;

	/**
	 * Singleton class constructor.
	 *
	 * @return Tribe__Events__Community__Modules__Taxonomy_Block
	 */
	public static function instance() {
		if ( empty( self::$instance ) ) {
			// require an admin functions file so we can leverage wp_terms_checklist
			include_once ABSPATH . '/wp-admin/includes/template.php';
			$walker                      = new Tribe__Events__Community__Walker_Category_Checklist;
			$allow_anonymous_submissions = Tribe__Events__Community__Main::instance()->allowAnonymousSubmissions;
			self::$instance              = new self( $walker, $allow_anonymous_submissions );
		}

		return self::$instance;
	}

	/**
	 * Tribe__Events__Community__Modules__Taxonomy_Block constructor.
	 *
	 * @param Tribe__Events__Community__Walker_Category_Checklist $walker
	 * @param bool                                                $allow_anonymous_submissions
	 */
	public function __construct( Tribe__Events__Community__Walker_Category_Checklist $walker, $allow_anonymous_submissions ) {
		$this->walker                      = $walker;
		$this->allow_anonymous_submissions = $allow_anonymous_submissions;
	}

	/**
	 * Prints the event category checklist.
	 *
	 * @param object $event              The event to display the tile for.
	 *
	 * @return array An array containing the used value for debug purposes {
	 *
	 * @param int    $num_items          The total number of terms printed to the page.
	 * @param int    $shown_item_count   The filtered numbet of terms shown before collapsing.
	 * @param bool   $show_all_link      Whether the "Show all categories" link has been printed to the page or not.
	 *                                   }
	 *
	 * @author Nick Ciske, Paul Hughes
	 * @since  1.0
	 */
	public function the_category_checklist( $event = null ) {
		/**
		 * Filters the maximum number of categories shown.
		 *
		 * Setting the value to `0` will hide all categories to show them only when the "Show all categories" link
		 * is clicked.
		 * Setting the value to anything greater than `0` will show that number of categories before hiding the
		 * remaining ones in the link.
		 *
		 * @since 4.1.0
		 *
		 * @param int $shown_item_count The number of event categories to show before collapsing them by default.
		 */
		$shown_item_count = apply_filters( 'tribe_events_community_category_dropdown_shown_item_count',
			self::$shown_item_default );
		?>
		<div id="event-categories" class="tribe-hide"
		     data-more-text="<?php echo esc_attr__( 'more', 'tribe-events-community' ); ?>"
		     data-shown-items="<?php echo absint( $shown_item_count ); ?>">
			<ul class="tribe-categories-with-children">
				<?php
				$args = array(
					'checked_ontop' => false,
					'popular_cats'  => true,
					'selected_cats' => ! empty( $_POST['tax_input']['tribe_events_cat'] ) ? $_POST['tax_input']['tribe_events_cat'] : $this->get_event_cat_ids( $event ),
					'taxonomy'      => Tribe__Events__Main::TAXONOMY,
					'walker'        => $this->walker,
				);
				add_filter( 'user_has_cap', array( $this, 'assign_terms_cap_filter' ) );
				wp_terms_checklist( empty( $event->ID ) ? 0 : $event->ID, $args );
				remove_filter( 'user_has_cap', array( $this, 'assign_terms_cap_filter' ) );
				?>
			</ul>
		</div>
		<?php
		$num_items     = $this->walker->num_items;
		$show_all_link = $shown_item_count < $num_items;

		if ( $show_all_link ) {
			echo '<div class="tribe-hide"><a id="show_hidden_categories" href="">' . sprintf( __( 'Show all categories ( %d )',
					'tribe-events-community' ),
					$num_items ) . '</a></div>';
		}

		return array(
			'num_items'        => $num_items,
			'shown_item_count' => $shown_item_count,
			'show_all_link'    => $show_all_link
		);
	}

	/**
	 * Filter on current_user_can to allow anonymous users the ability to assign terms.
	 *
	 * Note: this filter is only executed when rendering wp_terms_checklist and the anonymous
	 * submission feature is enabled.
	 *
	 * @param array $allcaps All the capabilities of the user
	 */
	public function assign_terms_cap_filter( $allcaps ) {
		//Filter Capabilities for both Anonymous and Logged in Users
		if ( is_user_logged_in() || $this->allow_anonymous_submissions ) {
			$allcaps['edit_tribe_events'] = true;
		}

		return $allcaps;
	}

	/**
	 * @param mixed $event
	 */
	private function get_event_cat_ids( $event ) {
		if ( empty( $event->ID ) ) {
			return array();
		}

		return wp_get_object_terms( $event->ID, Tribe__Events__Main::TAXONOMY, array( 'fields' => 'ids' ) );
	}
}