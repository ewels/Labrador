/*
   project.js
   Javascript for the Labrador Project page
*/

$('#project_identifier').keyup(function(){
	$('#sidebar_project_title').html( $(this).val() );
});

