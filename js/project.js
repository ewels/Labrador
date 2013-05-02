/*
   project.js
   Javascript for the Labrador Project page
*/

$('#project_identifier').keyup(function(){
	$('#sidebar_project_title').html( $(this).val() );
});

$('.project_type_button').click(function(){
	if($(this).attr('id') == 'project_type_external' && $('#project_internal_fieldset').is(':visible')){
		$('#project_accessions_fieldset, #project_paper_fieldset').slideDown();
		$('#project_internal_fieldset').slideUp();
	}
	if($(this).attr('id') == 'project_type_internal' && $('#project_accessions_fieldset').is(':visible')){
		$('#project_accessions_fieldset, #project_paper_fieldset').slideUp();
		$('#project_internal_fieldset').slideDown();
	}
});