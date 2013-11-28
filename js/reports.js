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
   reports.js
   Javascript for the Labrador Reports page
*/


// Auto-submit form on dropdown change
$('.select_report_dataset').change(function(){
	$('#report_type').val($(this).attr('data-type'));
	$(this).closest('form').trigger('submit');
});


// Automatically even up width of report dropdowns
$(document).ready(function() {
	var maxwidth = 0;
	$('.reports_form select').each(function() {
		if($(this).width() > maxwidth){
			maxwidth = $(this).width();
		}
	});
	$('.reports_form select').width(maxwidth);
});


// Automatically resize iFrame according to content
// NB - doesn't work for FastQC reports as the main content collapses itself
// This means that the page height is tiny.
// Uses https://github.com/house9/jquery-iframe-auto-height
$('iframe').load(function() {
  this.style.height = this.contentWindow.document.body.offsetHeight + 'px';
});