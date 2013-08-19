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
	var searchWidth = total_width - width - 50;
	$('.navbar .navbar-search').css('width', searchWidth + 'px');
	$('.navbar .navbar-search').css('width', searchWidth + 'px');
	$('.navbar .navbar-search .search-query').css('width', searchWidth - 100 + 'px');
}

$(document).ready(resizeSearch());
$(window).resize(function() { resizeSearch(); });


// Form validation
$('.form_validate').validate({
	errorClass:'help-inline text-error',
	validClass:'help-inline text-success',
	errorElement:'span',
	highlight: function (element, errorClass, validClass) { 
		$(element).parents("div[class='clearfix']").addClass(errorClass).removeClass(validClass);
		$(element).addClass('inputError');
	}, 
	unhighlight: function (element, errorClass, validClass) { 
		$(element).parents(".error").removeClass(errorClass).addClass(validClass); 
		$(element).removeClass('inputError');
	}
});

// Bootstrap popover for delete buttons
$('.popover_button').popover().click(function(e){
	e.preventDefault();
});

// Select Buttons
$('table').on('click', '.select-row', function(e){
	if($(this).is(':checked')){
		$(this).closest('tr').addClass('success');
	} else {
		$(this).closest('tr').removeClass('success');
	}
});

$('.select-all').click(function(e){
	if($(this).is(':checked')){
		$(this).closest('table').find('tbody tr:visible').addClass('success');
		$(this).closest('table').find('tbody tr:visible td .select-row').attr('checked','checked');
	} else {
		$(this).closest('table').find('tbody tr:visible').removeClass('success');
		$(this).closest('table').find('tbody tr:visible td .select-row').removeAttr('checked');
	}
});

// Fake links
$('.fake_link').click(function(e){
	e.preventDefault();
});

// Sortable tables
$("table.sortable").stupidtable();

// Is a number?
function isNumber(n) {
	return !isNaN(parseFloat(n)) && isFinite(n);
}