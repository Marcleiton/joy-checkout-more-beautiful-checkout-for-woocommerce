(function($) {
	$(document).ready(function(){	
	   $('.joyc_rating_wrap').delay(2000).slideDown('slow');
    });
	
	$(".joyc_rating_links .button2").on("click",function(){
                        var data = {
                    		action: 'joyc_update_rating',
                    		joyc_ratings: 'no',
						};
						
						
                    	$.post(joyc_ajax.ajax_url, data, function( response )
                		{
							$('.joyc_rating_wrap').slideUp('slow');
						}); 
                    });
	$(".joyc_rating_links .button3").on("click",function(){
	$('.joyc_rating_wrap').slideUp('slow');
	
	});		
	
	})(jQuery);