jQuery( document ).ready( function( $ ) {

	// Equalize
	// ------------------------------------------------------------------------------------------------------------------------------
	function equalizer(){
	    $('.equalizecontainer').equalize({children: '.x-column .equalize', reset: true});
	}

	var doequalizer;
	window.onresize = function(){
	  clearTimeout(doequalizer);
	  doequalizer = setTimeout(equalizer, 100);
	};
	window.onload = equalizer;

	// Video Modal 16:9 ratio maintainer
	function videomodal(){
		$width = $('#video-modal').width();
		$height = ( $width / 16 ) * 9;
	    $('#video-modal').height( $height );
	}

	var dovideoheight;
	window.onresize = function(){
	  clearTimeout(dovideoheight);
	  dovideoheight = setTimeout(videomodal, 100);
	};
	window.onload = videomodal;

});