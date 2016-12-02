/**
 * This script is borrowed from Filament Group
 * http://filamentgroup.com/lab/responsive_design_approach_for_complex_multicolumn_data_tables
 * Edited by Paul Hughes
*/

(function( window, $ ) {
	'use strict';

	var tribe_community_events = {};

	/**
	 * Initialize the events ui
	 */
	tribe_community_events.init = function() {
		this.$event_categories = $( document.getElementById( 'event-categories' ) );
		this.$show_hidden_categories = $( document.getElementById( 'show_hidden_categories' ) );

		this.init_categories();
	};

	/**
	 * Initialize the event category checkbox area
	 */
	tribe_community_events.init_categories = function() {
		var more_text = this.$event_categories.data( 'more-text' );
		var shown_item_count = parseInt( this.$event_categories.data( 'shown-items' ), 10 );
		var $parents = this.$event_categories.find( '> ul > li' );
		var $children = $parents.find( '> ul' );

		// if there aren't any children, let the categories remain side-by-side
		if ( ! $children.length ) {
			this.$event_categories.find( '.tribe-categories-with-children' ).removeClass( 'tribe-categories-with-children' );
		} else {
			$parents.each( function() {
				var $el = $( this );
				var $ul = $el.find( 'ul' );

				if ( $ul.length ) {
					var count = $ul.find( 'li' ).length;

					$el.prepend( '<span class="tribe-toggle"><span>' + count + ' ' + more_text + '</span></span>' );
				}
			});
		}

		// hide categories
		var $hideable_categories = $parents.slice( shown_item_count );
		$hideable_categories = $hideable_categories.filter( this.filter_out_selected_categories );

		if ( $hideable_categories.length ) {
			$hideable_categories.addClass( 'hidden_category' );
			this.$show_hidden_categories.closest( 'div' ).removeClass( 'tribe-hide' );
		}

		$children.filter( this.filter_out_selected_categories ).addClass( 'tribe-hide' );
		$children.not( '.tribe-hide' ).closest( 'li' ).addClass( 'tribe-expanded' );

		this.$event_categories.removeClass( 'tribe-hide' );

		$( document ).on( 'click', '#event-categories .tribe-toggle', function() {
			var $el = $( this );
			var $container = $el.closest( 'li' );

			$container.toggleClass( 'tribe-expanded' );
		} );
	};

	/**
	 * Utility method for filtering out <li>s. NOTE: this method is executed in the context of
	 * a jQuery filter() call.
	 *
	 * @return boolean
	 */
	tribe_community_events.filter_out_selected_categories = function() {
		return ! $( this ).find( 'input:checked' ).length;
	};

	$( function() {

		$( '.my-events' ).addClass( 'enhanced' );

		tribe_community_events.init();

		var container = $( '.table-menu' ),
			is_2014 = $( '#twentyfourteen-style-css' ).length;

		if( is_2014 ){
			$( 'body' ).addClass( 'tribe-2014' );
		}

		$( '#my-events-display-headers th' ).each( function( i ) {

			var th = $( this ),
				id = th.attr( 'id' ),
				classes = th.attr( 'class' );  // essential, optional (or other content identifiers)
			// assign an ID to each header, if none is in the markup
			if ( !id ) {
				id = ( 'col-' ) + i;
				th.attr( 'id', id );
			}
			// loop through each row to assign a "headers" attribute and any
			// classes (essential, optional) to the matching cell
			// the "headers" attribute value = the header's ID
			$( '#tribe-community-events tbody tr' ).each( function() {
				var cell = $( this ).find( 'th, td' ).eq( i );
				cell.attr( 'headers', id );
				if ( classes ) { cell.addClass( classes ); }
			} );
			// create the menu hide/show toggles
			if ( !th.is( '.persist' ) ) {
				// note that each input's value matches the header's ID;
				// later we'll use this value to control the visibility
				// of that header and it's associated cells
				var toggle = $( '<li><input type="checkbox" name="toggle-cols" id="toggle-col-' + i + '" value="' + id + '" /> <label for="toggle-col-' + i + '">' + th.text() + '</label></li>' );

				// append each toggle to the container
				container.find( 'ul' ).append( toggle );

				toggle.find( 'input' ).change( function() {
					var input = $( this ),
						val = input.val(),  // this equals the header's ID, i.e. "company"
						cols = $( '#' + val + ', [headers=' + val + ']' ); // so we can easily find the matching header (id="company") and cells (headers="company")

					if ( input.is( ':checked' ) ) { cols.show(); }
					else { cols.hide(); }
				} )
					// custom event that sets the checked state for each toggle based
					// on column visibility, which is controlled by @media rules in the CSS
					// called whenever the window is resized or reoriented (mobile)
					.bind( 'updateCheck', function() {
						if ( th.css( 'display' ) === 'table-cell' ) {
							$( this ).attr( 'checked', true );
						}
						else {
							$( this ).attr( 'checked', false );
						}
					} )

					// call the custom event on load
					.trigger( 'updateCheck' );
			}
		} );
		// update the inputs' checked status
		$( window ).bind( 'orientationchange resize', function() {
			container.find( 'input' ).trigger( 'updateCheck' );
		} );

		var menuBtn = $( '.table-menu-btn' );

		menuBtn.click( function() {
			container.toggleClass( 'table-menu-hidden' );
			return false;
		} );
		// assign click-away-to-close event
		$( document ).click( function( e ) {
			if ( !$( e.target ).is( container ) ) {
				if ( !$( e.target ).is( container.find( '*' ) ) ) {
					container.addClass( 'table-menu-hidden' );
				}
			}
		} );

		// our events list js

		$( '#show_hidden_categories' ).click( function() {
			$( '.hidden_category' ).removeClass( 'hidden_category' );
			$( '#show_hidden_categories' ).hide();
			return false;
		} );
	} );
})( window, jQuery );
