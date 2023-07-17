// Code By Webdevtrick ( https://webdevtrick.com )
(function($) { "use strict";

	$(function() {
		var header = $(".start-style");
		$(window).scroll(function() {    
			var scroll = $(window).scrollTop();
		
			if (scroll >= 10) {
				header.removeClass('start-style').addClass("scroll-on");
			} else {
				header.removeClass("scroll-on").addClass('start-style');
			}
		});
	});		
	
	//Animation
	
	$("div.switch-toggle").children("label").on('click', function (event) {
	
	    if ($("body").hasClass("dark") && event.target.id=="0") {
			$("body").removeClass("dark");
			$("a.btn").removeClass("btn-white");
			$("a.btn").addClass("btn-dark");
		}
		if (!$("body").hasClass("dark") && event.target.id=="1") {
			$("body").addClass("dark");
			$("a.btn").removeClass("btn-dark");
			$("a.btn").addClass("btn-white");
		}
	
	});
	
	$(document).ready(function() {
		$('body.hero-anime').removeClass('hero-anime');
		
		if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches)
		    $("label#1").trigger('click');
	});

	//Menu On Hover
		
	$('body').on('mouseenter mouseleave','.nav-item',function(e){
			if ($(window).width() > 750) {
				var _d=$(e.target).closest('.nav-item');_d.addClass('show');
				setTimeout(function(){
				_d[_d.is(':hover')?'addClass':'removeClass']('show');
				},1);
			}
	});	
	
	//Switch light/dark
	
	window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', event => { 
	
	    if (event.matches)
	         $("label#1").trigger('click');
	});
	
	
	
  })(jQuery);
  
	
	
	