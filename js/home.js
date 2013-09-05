/*
   home.js
   Javascript for the Labrador homepage
*/

// Redirect to project after clicking anywhere in a project row
$('#paper-browser-table tr td').click(function(e){
	var id = $(this).parent().attr('id').substr(8);
	window.location = 'project.php?id='+id;
});


//////////
// FILTER LIST FUNCTIONS
//////////

// Filter on page load
$(document).ready(function(){
	updateFilters();
});

// Status checkboxes
$('#filter_status_bar label').click(function(e){
	var id = $(this).attr('for');
	if($('#'+id).is(':checked')){
		$(this).removeClass('checked');
	} else {
		$(this).addClass('checked');
	}
});
$('#filter_status_bar input').change(function(){
	updateFilters();
});

$('.nav-list.filters li a').click(function(e){
	e.preventDefault();
	if($(this).parent().hasClass('active')){
		$(this).parent().removeClass('active');
		updateFilters();
	} else {
		$(this).parent().addClass('active');
		updateFilters();
	}
});
$('#homepage_text_filter').keyup(function(e){
	updateFilters();
});

function updateFilters() {
	
	$('#paper-browser-table tbody tr').show();
	
	var textFilterText = $('#homepage_text_filter').val().toLowerCase();
	$.each($('#paper-browser-table tbody tr:visible'), function() {
		var hideRow = true;
		$(this).children('td').each(function(){
			if($(this).text().toLowerCase().indexOf(textFilterText) >= 0){
				hideRow = false;
			}
		});
		if(hideRow){
			$(this).hide();
		}
	});
	
	$.each($('#paper-browser-table tbody tr:visible'), function() {
		var hideRow = true;
		var filterText = $(this).children('.project_name').text().toLowerCase().substr(0,1);
		var filterCounter = 0;
		$.each($('.nav-list.filters .alphabetical-filter.active'), function(){
			filterCounter++;
			var searchString = $(this).text().toLowerCase();
			if(searchString == '0-9'){
				if(isNumber(searchString)){
					hideRow = false;
				}
			} else {
				if(searchString == filterText){
					hideRow = false;
				}
			}
		});
		if(hideRow && filterCounter > 0){
			$(this).hide();
		}
	});
	
	
	$.each($('#paper-browser-table tbody tr:visible'), function() {
		var hideRow = true;
		var filterText = $(this).children('.species').text().toLowerCase();
		var filterCounter = 0;
		$.each($('.nav-list.filters .species-filter.active'), function(){
			filterCounter++;
			var searchString = $(this).text().toLowerCase();
			if(searchString == filterText){
				hideRow = false;
			}
		});
		if(hideRow && filterCounter > 0){
			$(this).hide();
		}
	});
	
	$.each($('#paper-browser-table tbody tr:visible'), function() {
		var hideRow = true;
		var filterText = $(this).children('.data_type').text().toLowerCase();
		var filterCounter = 0;
		$.each($('.nav-list.filters .datatype-filter.active'), function(){
			filterCounter++;
			var searchString = $(this).text().toLowerCase();
			if(searchString == filterText){
				hideRow = false;
			}
		});
		if(hideRow && filterCounter > 0){
			$(this).hide();
		}
	});
	
	$.each($('#paper-browser-table tbody tr:visible'), function() {
		var hideRow = false;
		var status = $(this).data('status');
		$('input[name=filter_status]:not(:checked)').each(function(){
			if($(this).val() == status){
				hideRow = true;
			}
		});
		if(hideRow){
			$(this).hide();
		}
	});
	
	// Show "no datasets message" if we've hidden everything
	if($('#paper-browser-table tbody tr:visible').length == 0){
		$('#no_datasets').show();
	} else {
		$('#no_datasets').hide();
	}
	
}

