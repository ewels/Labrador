/*
##########################################################################
# Copyright 2013, Philip Ewels (phil.ewels@babraham.ac.uk)               #
#                                                                        #
# This file is part of Labrador.                                         #
#                                                                        #
# Labrador is free software: you can redistribute it and/or modify       #
# it under the terms of the GNU General Public License as published by   #
# the Free Software Foundation, either version 3 of the License, or      #
# (at your option) any later version.                                    #
#                                                                        #
# Labrador is distributed in the hope that it will be useful,            #
# but WITHOUT ANY WARRANTY; without even the implied warranty of         #
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the          #
# GNU General Public License for more details.                           #
#                                                                        #
# You should have received a copy of the GNU General Public License      #
# along with Labrador.  If not, see <http://www.gnu.org/licenses/>.      #
##########################################################################
*/

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
	var searchWidth = total_width - width - 90;
	$('.navbar .navbar-search').css('width', searchWidth + 'px');
	$('.navbar .navbar-search').css('width', searchWidth + 'px');
	$('.navbar .navbar-search .search-query').css('width', searchWidth - 100 + 'px');
}

$(document).ready(resizeSearch());
$(window).resize(function() { resizeSearch(); });

// Search bar typeahead
$('.search-query').typeahead({
    source: function (query, process) {
        return $.get('ajax/project_names.php', { query: query }, function (data) {
            return process(data.projects);
        });
    },
	updater:function (item) {
		window.location.href = "project.php?p_name="+item;
    }
});

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

// Help text toggle
$('.labrador_help_toggle').click(function(e){
	e.preventDefault();
	$('.labrador_help').slideToggle();
});