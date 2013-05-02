/*
   global.js
   Javascript used on all Labrador pages
*/

/* Resize Search bar to fit screen */
function resizeSearch() {
	var width = 0;
	width += $('.navbar .brand').width();
	$('.navbar .nav').each(function(){
		width += $(this).width();
	});
	width += $('.navbar-text').width();
	var total_width = $('.navbar .container-fluid').width();
	var searchWidth = total_width - width - 100;
	$('.navbar .navbar-search').css('width', searchWidth + 'px');
	$('.navbar .navbar-search').css('width', searchWidth + 'px');
	$('.navbar .navbar-search .search-query').css('width', searchWidth - 100 + 'px');
}

$(document).ready(resizeSearch());
$(window).resize(function() { resizeSearch(); });