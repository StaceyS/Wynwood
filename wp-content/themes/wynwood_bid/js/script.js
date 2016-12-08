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



	// GOOGLE MAPS API + ADVANCED CUSTOM FIELDS PLUGIN

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

	// ... and on resize
	$(window).resize(function(listWidth){
		listWidth = $('.results.list-view').width();
		$('.summary-card-details').width(listWidth);
		});

	// Show/hide biz listing details on click
	$('.toggle-biz-details').click(function(){
		$(this).closest('.single-listing').children('.summary-card-details').fadeToggle();
		$(this).toggleClass('active-details');
		$(this).closest('.summary-card').toggleClass('active-card');
		return false;
		});

	// Toggle results view
	$('.grid-map-view-toggle button').click(function(){
		$('.active-view').toggleClass('active-view');
		$(this).toggleClass('active-view');
		});


	// Toggle 'Edit/Preview' Profile
	$('.edit-profile').click(function(){
		if ($(this).hasClass('now-editing')) {
			$(this).text('Edit Business Details');
			}
		else {
			$(this).text('Preview Business Listing');
			}
			$('.biz-details-wrapper').fadeToggle();
			$('.edit-biz-details-form').fadeToggle();
			$(this).toggleClass('now-editing');
			return false;
		});


} ); // End EVERTYTHING!!!