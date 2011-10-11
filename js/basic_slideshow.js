function create_slideshow() {
  var slideshow = jQuery('div#basic_slideshow div.list');

  slideshow.after('<button class="button" id="jqc-prev">&laquo;</button>')
	         .after('<button class="button" id="jqc-next">&raquo;</button>')
	         .after('<div id="slideshow_pager"></div>');
	
	
    //Grab the variables that the wordpress admin page will give us
    var slideTime = slideshow_settings.slide_time > 0 ? (slideshow_settings.slide_time * 1000) : 5000;
    var speed = slideshow_settings.slide_time > 0 ? slideshow_settings.transition_speed : 500;
    var scrollType = slideshow_settings.slide_time.length > 1 ? slideshow_settings.transition_type : "scrollHorz";
    
    var slide_unit = slideshow_settings.slide_unit in {'px':'', 'em':'', '%':''} ? slideshow_settings.slide_unit : 'px';
    var slide_width = slideshow_settings.slide_width > 0 ? slideshow_settings.slide_width + slide_unit : '550px';
    var slide_height = slideshow_settings.slide_height > 0 ? slideshow_settings.slide_height + slide_unit: '330px';

    
    //Start the slideshow
    	slideshow.cycle({
    		fx: scrollType,
    		cleartypeNoBg: true,
    		speed: speed,
    		pager: 'div#basic_slideshow #slideshow_pager',
    		timeout: slideTime,
    		prev: '#jqc-prev',
    		next: '#jqc-next',
    		fit: 1,
    		width: slide_width,
    		height: slide_height,
     	});
     	
     	jQuery('#basic_slideshow').css({width: slide_width, height: slide_height});
}


/*****************************************************

  Set up the slideshow and set up listenners to pause
  things when 

*****************************************************/
jQuery(document).ready(function() {
	jQuery.noConflict();
	create_slideshow();

      //Stop the slideshow if the user clicks on anything
      var slideshow = jQuery('div#basic_slideshow div.list');
      var buttons = jQuery('div#basic_slideshow .button, div#basic_slideshow #slideshow_pager a');
      
      // Pause the slideshow when any part of the slide is clicked. 
      // Resume the slideshow when any nav button is clicked.
      slideshow.bind('click', function(){
          slideshow.cycle('pause'); 
          slideshow_settings.hardPaused = true; 
          buttons.bind('click', function(event){
              slideshow.cycle('resume');
              slideshow_settings.hardPaused = false; 
              buttons.unbind(event);
          });
       }); 
       
      // Pause the slideshow when any part of the slide is clicked. 
      // Resume the slideshow when any nav button is clicked.
      var pauseOnHover = slideshow_settings.pauseOnHover == 1 ? true : false;
      if (pauseOnHover){
        slideshow.hover(
            function(event){ 
              slideshow.cycle('pause'); 
            },
            function(event){
              if (!slideshow_settings.hardPaused){
                slideshow.cycle('resume');
              }
            }
        );
      }

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
      slideshow.cycle('pause');
      break;
    case 2:
    case 0:
      // Video has been paused/ended
      slideshow.cycle('resume');
      break;
  }
}