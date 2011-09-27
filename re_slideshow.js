function create_slideshow() {
	jQuery('div#re_slideshow').append('<button class="button-l" id="jqc-prev">&laquo;</button>');
	jQuery('div#re_slideshow').append('<button class="button-r" id="jqc-next">&raquo;</button>');
	
	len = jQuery('div#re_slideshow div.list div.item').length;
	jQuery('div#re_slideshow .button-l').css('z-index',len+100);
	jQuery('div#re_slideshow .button-r').css('z-index',len+101);
	
	jQuery('div#re_slideshow div.list').cycle({
		fx: 'scrollHorz',
		cleartypeNoBg: true,
		speed: 1500,
		timeout: 5500,
		prev: '#jqc-prev',
		next: '#jqc-next',
 	});
}


jQuery(document).ready(function() {
	jQuery.noConflict();
	create_slideshow();
});