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
   download.js
   Javascript for the Labrador Downloads page
*/

// Submit dataset filter on change
$('#filter_dataset').change(function(e){
	$(this).closest('form').submit();
});

// Filter downloads by type
// Takes search needles from PHP printed variables in download.php
$('#filter_projects').click(function(e){
	e.preventDefault();
	$(this).toggleClass('active');
	filter_downloads();
});
$('#filter_raw').click(function(e){
	e.preventDefault();
	$(this).toggleClass('active');
	filter_downloads();
});
$('#filter_aligned').click(function(e){
	e.preventDefault();
	$(this).toggleClass('active');
	filter_downloads();
});
$('#filter_reports').click(function(e){
	e.preventDefault();
	$(this).toggleClass('active');
	filter_downloads();
});
$('#filter_other').click(function(e){
	e.preventDefault();
	$(this).toggleClass('active');
	filter_downloads();
});
$('.filter_text').keyup(function(e){
	filter_downloads();
});

function filter_downloads () {
	if(!$('#filter_raw').hasClass('active') && 
			!$('#filter_projects').hasClass('active') && 
			!$('#filter_aligned').hasClass('active') && 
			!$('#filter_reports').hasClass('active') && 
			!$('#filter_other').hasClass('active') &&
			$('.filter_text').val().length == 0) {
		$('.download_table tbody tr').show();
	} else {
		$('.download_table tbody tr').hide();
		if($('#filter_projects').hasClass('active')){
			$.each(project_filename_filters, function(index, filterText){
				$('.download_table tbody tr td.path').filter(function(i){
					if($(this).text().substr(filterText.length * -1) == filterText){
						return true;
					} else {
						return false;
					}
				}).parent().show();
			});
		}
		if($('#filter_raw').hasClass('active')){
			$.each(raw_filename_filters, function(index, filterText){
				$('.download_table tbody tr td.path').filter(function(i){
					if($(this).text().substr(filterText.length * -1) == filterText){
						return true;
					} else {
						return false;
					}
				}).parent().show();
			});
		}
		if($('#filter_aligned').hasClass('active')){
			$.each(aligned_filename_filters, function(index, filterText){
				$('.download_table tbody tr td.path').filter(function(i){
					if($(this).text().substr(filterText.length * -1) == filterText){
						return true;
					} else {
						return false;
					}
				}).parent().show();
			});
		}
		if($('#filter_reports').hasClass('active')){
			$.each(reports_filename_filters, function(index, filterText){
				$('.download_table tbody tr td.path').filter(function(i){
					if($(this).text().substr(filterText.length * -1) == filterText){
						return true;
					} else {
						return false;
					}
				}).parent().show();
			});
		}
		if($('#filter_other').hasClass('active')){
			var all_filters = raw_filename_filters.concat(aligned_filename_filters, reports_filename_filters);
			$('.download_table tbody tr td.path').filter(function(i){
				var path = $(this).text();
				var matched = false;
				$.each(all_filters, function(index, filterText){
					if(path.substr(filterText.length * -1) == filterText){
						matched = true;
					}
				});
				if(matched) {
					return false;
				} else {
					return true;
				}
			}).parent().show();
		}
		// Hide those not matching filter text
		if($('.filter_text').val().length != 0){
			var filterText = $('.filter_text').val().toLowerCase();
			// Buttons not active - show any rows matching text
			if(!$('#filter_raw').hasClass('active') && 
					!$('#filter_aligned').hasClass('active') && 
					!$('#filter_reports').hasClass('active') && 
					!$('#filter_other').hasClass('active')) {
				var matched = $('.download_table tbody tr td.path').filter(function(i){
					if($(this).text().toLowerCase().indexOf(filterText) >= 0){
						return true;
					} else {
						return false;
					}
				}).parent().show();
			} else {
				// Buttons active - restrict hide any rows not matching text
				var matched = $('.download_table tbody tr td.path').filter(function(i){
					if($(this).text().toLowerCase().indexOf(filterText) >= 0){
						return false;
					} else {
						return true;
					}
				}).parent().hide();
			}
		}
	}
}

