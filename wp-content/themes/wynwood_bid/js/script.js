/* Custom JavaScript */

jQuery(document).ready(function($){

	// Utility Nav 

	// Set search input placeholcer text 
	$('#searchform input').attr('placeholder', 'Enter search term');

	// Toggle search visibility on click
	$('.utility-nav .search-icon').click(function(){
		$('#searchform').slideToggle();
		return false;
		});

	// *** EVENTS CALENDAR *** 

	// Hack to event category sidebar on month view
	$('.tribe-events-month').closest('.main-content').addClass('month-view-styles');
	$('.tribe-events-list').closest('.main-content').addClass('list-view-styles');

	$('.tribe-bar-filters .tribe-events-button').after('<a class="clear-filters" href="/events"><i class="fa fa-times" aria-hidden="true"></i></a>');

	// Override toggle visibility of filter bar functionality from plugin
	$('#tribe-bar-collapse-toggle').click(function(){
		return false;
		});
	// Clear the filter bar -- Not working yet
	// https://theeventscalendar.com/support/forums/topic/is-it-possible-to-add-a-reset-or-clear-button-to-the-event-bar/
	// $('.clear-filters').on('click', function(){
	// 	$('#tribe-bar-form').tribe_clear_form();
	// 	});

	// If no category is selected, add 'current-menu-item' class to 'View All Events' option
	// if ($('.tribe-events-event-categories .current-menu-item').length) {
	// 	$('.view-all-events').removeClass('current-menu-item');
	// 	}
	// else {
	// 	$('.view-all-events').addClass('current-menu-item');
	// 	}

	$('.event-cta-buttons-wrapper .tribe-events-gmap').addClass('biz-cta-button').html('Get Directions <i class="fa fa-compass" aria-hidden="true"></i>');
	
	// GOOGLE MAPS API + ADVANCED CUSTOM FIELDS PLUGIN
	// -----------------------------------------------

	/*
	*  new_map
	*
	*  This function will render a Google Map onto the selected jQuery element
	*
	*  @type  function
	*  @date  8/11/2013
	*  @since 4.3.0
	*
	*  @param $el (jQuery element)
	*  @return  n/a
	*/

	function new_map( $el ) {
	  
	  // var
	  var $markers = $el.find('.marker');
	  
	  // vars
	  var args = {
	    zoom    : 16,
	    center    : new google.maps.LatLng(0, 0),
	    mapTypeId : google.maps.MapTypeId.ROADMAP
	  };
	  
	  // create map           
	  var map = new google.maps.Map( $el[0], args);
	  
	  // add a markers reference
	  map.markers = [];	  
	  
	  // add markers
	  $markers.each(function(){    
	      add_marker( $(this), map );
	  });	  
	  
	  // center map
	  center_map( map );
	  
	  // return
	  return map;
	  
	}

	/*
	*  add_marker
	*
	*  This function will add a marker to the selected Google Map
	*
	*  @type  function
	*  @date  8/11/2013
	*  @since 4.3.0
	*
	*  @param $marker (jQuery element)
	*  @param map (Google Map object)
	*  @return  n/a
	*/

	function add_marker( $marker, map ) {

	  // var
	  var latlng = new google.maps.LatLng( $marker.attr('data-lat'), $marker.attr('data-lng') );

	  // create marker
	  var marker = new google.maps.Marker({
	    position  : latlng,
	    map     : map
	  });

	  // add to array
	  map.markers.push( marker );

	  // if marker contains HTML, add it to an infoWindow
	  if( $marker.html() )
	  {
	    // create info window
	    var infowindow = new google.maps.InfoWindow({
	      content   : $marker.html()
	    });

	    // show info window when marker is clicked
	    	google.maps.event.addListener(marker, 'click', function() {
	      infowindow.open( map, marker );
	    });
	  }

	}

	/*
	*  center_map
	*
	*  This function will center the map, showing all markers attached to this map
	*
	*  @type  function
	*  @date  8/11/2013
	*  @since 4.3.0
	*
	*  @param map (Google Map object)
	*  @return  n/a
	*/

	function center_map( map ) {

	  // vars
	  var bounds = new google.maps.LatLngBounds();

	  // loop through all markers and create bounds
	  $.each( map.markers, function( i, marker ){
	    var latlng = new google.maps.LatLng( marker.position.lat(), marker.position.lng() );
	    bounds.extend( latlng );
	  });

	  // only 1 marker?
	  if( map.markers.length == 1 )
	  {
	    // set center of map
	      map.setCenter( bounds.getCenter() );
	      map.setZoom( 16 );
	  }
	  else
	  {
	    // fit to bounds
	    map.fitBounds( bounds );
	  }

	}

	/*
	*  document ready
	*
	*  This function will render each map when the document is ready (page has loaded)
	*
	*  @type  function
	*  @date  8/11/2013
	*  @since 5.0.0
	*
	*  @param n/a
	*  @return  n/a
	*/
	// global var
	var map = null;

	$(document).ready(function(){

	  $('.acf-map').each(function(){
	    // create map
	    map = new_map( $(this) );
	  });

	});


	// ** BUSINESS DIRECTORY (LIST VIEW) **

	
	// Set width of summary card details equalt to the parent '.results.list-view' div
	var listWidth = $('.results.list-view').width();

	$('.summary-card-details').width(listWidth);

	// Fix positioning of business listing details section so it's left-aligned with the grid
	var singleListingWidth = $('.business-index .single-listing').outerWidth();
	var betweenListItems = listWidth * .03; // get the actual width of the margin
	
	var secondLeftMargin = betweenListItems + singleListingWidth; // amount the 2nd box needs to be offset
	secondLeftMargin = -secondLeftMargin;

	var thirdLeftMargin = (betweenListItems * 2) + (singleListingWidth * 2); // amount the 3rd box needs to be offset
	thirdLeftMargin = -thirdLeftMargin;

	$('.business-index .single-listing:nth-child(3n+4)').css('clear','both');
	$('.business-index .single-listing:nth-child(3n+2) .summary-card-details').css('left',secondLeftMargin);
	$('.business-index .single-listing:nth-child(3n+3) .summary-card-details').css('left',thirdLeftMargin);


	// ... and do all the same stuff on resize
	$(window).resize(function(listWidth, singleListingWidth, betweenListItems, secondLeftMargin, thirdLeftMargin){
		listWidth = $('.results.list-view').width();
		$('.summary-card-details').width(listWidth);

		singleListingWidth = $('.business-index .single-listing').outerWidth();
		betweenListItems = listWidth * .03;
		
		secondLeftMargin = betweenListItems + singleListingWidth;
		secondLeftMargin = -secondLeftMargin;

		thirdLeftMargin = (betweenListItems * 2) + (singleListingWidth * 2);
		thirdLeftMargin = -thirdLeftMargin;

		$('.business-index .single-listing:nth-child(3n+4)').css('clear','both');
		$('.business-index .single-listing:nth-child(3n+2) .summary-card-details').css('left',secondLeftMargin);
		$('.business-index .single-listing:nth-child(3n+3) .summary-card-details').css('left',thirdLeftMargin);
		});


	// Show/hide biz listing details on click
	$('.toggle-biz-details').click(function(){
		// $('.active-card').toggleClass('active-card');
		// $('.active-details').toggleClass('active-details');
		// $('.active-card .summary-card-details').fadeToggle();
		$(this).closest('.single-listing').children('.summary-card-details').fadeToggle();
		$(this).toggleClass('active-details');
		$(this).closest('.summary-card').toggleClass('active-card');
		return false;
		});

	// Toggle results view
	// Don't need this now that map & grid use separate templates
	// $('.grid-map-view-toggle button').click(function(){
	// 	$('.active-view').toggleClass('active-view');
	// 	$(this).toggleClass('active-view');
	// 	});


	// Toggle 'Edit/Preview' Profile
	$('.edit-profile').click(function(){
		if ($(this).hasClass('now-editing')) {
			$(this).text('Edit Business Details');
			return false;
			}
		else {
			$(this).text('Preview Business Listing');
			}
			$('.biz-details-wrapper').fadeToggle();
			$('.edit-biz-details-form').fadeToggle();
			$(this).toggleClass('now-editing');
			return false;
		});


	// WP USER PROFILE
	$('#your-profile h3:contains("About Yourself")').hide();


	// FAVORITES MODAL
	// Get the modal
	var modal = document.getElementById('myModal');

	// Get the button that opens the modal
	var btn = document.getElementById("favorite-create-account");

	// Get the <span> element that closes the modal
	var span = document.getElementsByClassName("close")[0];

	// When the user clicks the button, open the modal and prevent the full page from scrolling
	// btn.onclick = function() {
	//     //modal.style.display = "block";
	// 	$('#myModal').delay(200).fadeIn(400);
	// 	$('html').addClass('noscroll');
	// }

	// // When the user clicks on <span> (x), close the modal
	// span.onclick = function() {
	//     //modal.style.display = "none";
	//     $('#myModal').fadeOut();
	// 	$('html').removeClass('noscroll');			
	// }

	// // When the user clicks anywhere outside of the modal, close it
	// window.onclick = function(event) {
	//     if (event.target == modal) {
	//         //modal.style.display = "none";
 //    		$('#myModal').fadeOut();
	// 		$('html').removeClass('noscroll');			
	//     }
	// }


} ); // End EVERTYTHING!!!