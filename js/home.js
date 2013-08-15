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
// SORT COLUMN FUNCTIONS
//////////
// Modified function from here:
// http://stackoverflow.com/questions/2694460/sorting-a-table-based-on-which-header-cell-was-clicked
function OrderBy(a,b,n) {
    if (n) return a-b;
    if (a < b) return -1;
    if (a > b) return 1;
    return 0;
}
$('#paper-browser-table tr th').click(function() {
    $(this).toggleClass('selected');
    var isSelected = $(this).hasClass('selected');
    var column = $(this).index();
    var $table = $(this).closest('table');
    var isNum= $table.find('tbody > tr').children('td').eq(column).hasClass('num');
    var rows = $table.find('tbody > tr').get();
    rows.sort(function(rowA,rowB) {
        var keyA = $(rowA).children('td').eq(column).text().toUpperCase();
        var keyB = $(rowB).children('td').eq(column).text().toUpperCase();
        if (isSelected) return OrderBy(keyA,keyB,isNum);
        return OrderBy(keyB,keyA,isNum);
    });
    $.each(rows, function(index,row) {
        $table.children('tbody').append(row);
    });
    return false;
});


//////////
// FILTER LIST FUNCTIONS
//////////
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
	
	// Show "no datasets message" if we've hidden everythign
	if($('#paper-browser-table tbody tr:visible').length == 0){
		$('#no_datasets').show();
	} else {
		$('#no_datasets').hide();
	}
	
}

function isNumber(n) {
	return !isNaN(parseFloat(n)) && isFinite(n);
}

/*
// Load ajax search results
function loadSearchAjax (query){
	if(typeof query == 'undefined'){
		var q = '';
	} else {
		var q = '?q='+query;
	}
	$.get('ajax_search.php'+q, function(data) {
		$('#ajax_search').html(data);
	});
}


// Fire search results if user presses enter on search field (keypress rather than keyup)
$('#labrador_search_string').keypress(function(e){
	if (e.which == 13) {
		e.preventDefault();
		loadSearchAjax($('#labrador_search_string').val());
	}
});

// Fire search results each time a key is pressed in the search field
$('#labrador_search_string').keyup(function(e){
	e.preventDefault();
	loadSearchAjax($('#labrador_search_string').val());
});

// Search field and filter cookie sets
$('#search_fields_save').click(function(){
	setFieldsCookie();
	updateFieldsFilters();
	$('#search_fields').modal('hide');
});
$('#search_filters_save').click(function(){
	setFiltersCookie();
	updateFieldsFilters();
	$('#search_filters').modal('hide');
});

function selectAll ( selectID ) {
	$(selectID).children('option').each(function(index) {
		$(this).attr('selected', 'selected');
	});
	$("select").trigger("liszt:updated");
	return false;
}
function selectNone ( selectID ) {
	$(selectID).children('option').each(function(index) {
		$(this).removeAttr('selected');
	});
	$("select").trigger("liszt:updated");
	return false;
}

*/