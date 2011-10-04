function create_slideshow() {
  var slideshow = jQuery('div#re_slideshow div.list');

  slideshow.after('<button class="button" id="jqc-prev">&laquo;</button>')
	         .after('<button class="button" id="jqc-next">&raquo;</button>')
	         .after('<div id="slideshow_pager"></div>');
	

var slideTime = slideshow_settings.slide_time > 0 ? (slideshow_settings.slide_time * 1000) : 5000;
var speed = slideshow_settings.slide_time > 0 ? slideshow_settings.transition_speed : 500;
var scrollType = slideshow_settings.slide_time.length > 1 ? slideshow_settings.transition_type : "scrollHorz";

	slideshow.cycle({
		fx: scrollType,
		cleartypeNoBg: true,
		speed: speed,
		pager: 'div#re_slideshow #slideshow_pager',
		timeout: slideTime,
		prev: '#jqc-prev',
		next: '#jqc-next',
 	});
}



jQuery(document).ready(function() {
	jQuery.noConflict();
	create_slideshow();

      //Stop the slideshow if the user clicks on anything
      var slideshow = jQuery('div#re_slideshow div.list');
      var buttons = jQuery('div#re_slideshow .button, div#re_slideshow #slideshow_pager a');
      
      jQuery('div#re_slideshow div.list .item object').bind('click', function(){alert('something Click');});
      
      slideshow.bind('click', function(){
          //pause the slideshow.
          slideshow.cycle('pause');
          //alert('paused');

          buttons.bind('click', function(event){
                    slideshow.cycle('resume');
              buttons.unbind(event);
          });
       }); 

});



function onYouTubePlayerReady(){
  var players = jQuery('#re_slideshow embed').each(function(){
    if (this.addEventListener) {
      this.addEventListener('onStateChange', 'handlePlayerStateChange');
    }
    else {
      this.attachEvent('onStateChange', 'handlePlayerStateChange');
    }
  
  });

}


function handlePlayerStateChange (state) {
  var slideshow = jQuery('#re_slideshow div.list');
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