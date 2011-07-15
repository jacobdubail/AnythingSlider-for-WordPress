(jQuery(function($) {

	$(".anythingslider-holder > h3")
		.click(function() {    
			$('.inside').slideUp('slow');    
			$(this).next().stop().slideToggle('slow');    
			return false;  
		})
		.next()
		.hide();

})(jQuery));


