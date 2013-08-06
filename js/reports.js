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