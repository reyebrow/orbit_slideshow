function create_slideshow() {
  var slideshow = jQuery('#basic_slideshow');
		
    //Grab the variables that the wordpress admin page will give us
    var slideTime = slideshow_settings.slide_time > 0 ? (slideshow_settings.slide_time * 1000) : 5000;
    var speed = slideshow_settings.slide_time > 0 ? slideshow_settings.transition_speed : 500;
    var scrollType = slideshow_settings.slide_time.length > 1 ? slideshow_settings.transition_type : "fade";
    
    var slide_unit = slideshow_settings.slide_unit in {'px':'', 'em':'', '%':''} ? slideshow_settings.slide_unit : 'px';
    var slide_width = slideshow_settings.slide_width > 0 ? slideshow_settings.slide_width + slide_unit : '550px';
    var slide_height = slideshow_settings.slide_height > 0 ? slideshow_settings.slide_height + slide_unit: '330px';


    slideshow.orbit({
       animation: scrollType,        // fade, horizontal-slide, vertical-slide, horizontal-push
       animationSpeed: speed,        // how fast animtions are
       timer: true, 			           // true or false to have the timer
       advanceSpeed: slideTime, 		 // if timer is enabled, time between transitions 
       pauseOnHover: true, 		       // if you hover pauses the slider
       startClockOnMouseOut: true, 	 // if clock should start on MouseOut
       startClockOnMouseOutAfter: 1000, 	 // how long after MouseOut should the timer start again
       directionalNav: true,           // manual advancing directional navs
       captions: true,                // do you want captions?
       captionAnimation: 'fade',      // fade, slideOpen, none
       captionAnimationSpeed: 2800, 	  // if so how quickly should they animate in
       bullets: true,			            // true or false to activate the bullet navigation
       bulletThumbs: false,		        // thumbnails for the bullets
       bulletThumbLocation: '',		    // location from this file where thumbs will be
       afterSlideChange: function(){}, 	 // empty function 
       fluid: slideshow_settings.slide_width+'x'+slideshow_settings.slide_height              // or set a aspect ratio for content slides (ex: '4x3') 
    });

}


/*****************************************************

  Set up the slideshow and set up listenners to pause
  things when 

*****************************************************/
jQuery(document).ready(function() {
	jQuery.noConflict();
	create_slideshow();

});
/*****************************************************

  Capture Youtube clicks so we can pause our slideshow
  when video plays.

*****************************************************/

function onYouTubePlayerReady(){
  var players = jQuery('#basic_slideshow embed').each(function(){
    if (this.addEventListener) {
      this.addEventListener('onStateChange', 'handlePlayerStateChange');
    }
    else {
      this.attachEvent('onStateChange', 'handlePlayerStateChange');
    }
  });

}

function handlePlayerStateChange (state) {
  var slideshow = jQuery('#basic_slideshow div.list');
  switch (state) {
    case 1:
    case 3:
      // Video has begun playing/buffering
      //slideshow.cycle('pause');
      break;
    case 2:
    case 0:
      // Video has been paused/ended
      //slideshow.cycle('resume');
      break;
  }
}