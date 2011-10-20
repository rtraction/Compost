$("#deleteme9064").remove();
$(document).ready(function(){
	$("#deleteme9064").remove();
});
var jQT = $.jQTouch({
                icon: base_url+'images/iphone/icon.png',
                startupScreen: base_url+'images/iphone/startup.png',
								addGlossToIcon: true,
								preloadImages: [
								
								]
            });
            $(function(){
							$("#deleteme9064").remove();
							
							$('a[target="_blank"]').click(function() {
                    if (confirm('This link opens in a new window.')) {
                        return true;
                    } else {
                        $(this).removeClass('active');
                        return false;
                    }
                });

            });