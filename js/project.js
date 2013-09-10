/*
   project.js
   Javascript for the Labrador Project page
*/

///////
// UX HELPER FUNCTIONS
///////

// Update the project name in the top left as the field is filled in
$('#name').keyup(updateProjectName);
function updateProjectName (){
	var name = $.trim($('#name').val()).replace(/[^A-Za-z0-9_]/g, "_");
	if(name.length > 0){
		$('#sidebar_project_title').html( name );
	} else {
		$('#sidebar_project_title').html('<span class="muted">New Project</span>');
	}
}
$('#name').blur(function(){
	var name = $.trim($('#name').val()).replace(/[^A-Za-z0-9_]/g, "_");
	$('#name').val( name );
});

// Shortcut fields to fill in assignment e-mail addresses
$('.assign_quickFill').click(function(e){
	e.preventDefault();
	$('#assigned_to').val($(this).attr('title'));
	if($('#status').val() == 'Not Started'){
		$('#status').val('Currently Processing');
	}
});
$('.groupAssign_quickFill').click(function(e){
	e.preventDefault();
	$('#contact_group').val($(this).attr('title'));
});

// Add paper button
$('#paper_add_paper').click(function(e){
	e.preventDefault();
	$('.no_papers_tr').remove();
	var num_papers = $('.edit_publications tbody tr').length;
	pid = num_papers + 1;
	$('.edit_publications tbody').append('<tr id="paper_row_'+pid+'"><td><input type="text" maxlength="4" class="paper_year" id="paper_year_'+pid+'" name="paper_year_'+pid+'"  /></td><td><input type="text" class="paper_journal" id="paper_journal_'+pid+'" name="paper_journal_'+pid+'"  /></td><td><input type="text" class="paper_title" id="paper_title_'+pid+'" name="paper_title_'+pid+'" ></td><td><input type="text" class="paper_authors" id="paper_authors_'+pid+'" name="paper_authors_'+pid+'" ></td><td><input type="text" class="paper_pmid" id="paper_pmid_'+pid+'" name="paper_pmid_'+pid+'"  /></td><td><input type="text" class="paper_doi" id="paper_doi_'+pid+'" name="paper_doi_'+pid+'"  /></td><td><button class="paper_delete paper_delete_nodb btn btn-small btn-danger" id="paper_delete_'+pid+'">Delete</button></td></tr>');
});							

// Paper delete button
$('.edit_publications').on('click', '.paper_delete', function(e){
	e.preventDefault();
	var row_id = $(this).attr('id').substr(13);
	if($(this).hasClass('paper_delete_nodb')){
		// This paper was dynamically generated on this page load and hasn't been saved yet
		removePaper(row_id);
	} else {
		var paper_id = $('#paper_id_'+row_id).val();
		$.getJSON('ajax/delete_paper.php?pid='+paper_id, function(data) {
			if(data[0] == 1){
				// Deleted from database, remove from page..
				alert(data[1]);
				removePaper(row_id);
			} else {
				alert(data[1]);
			}
		});
	}
});
function removePaper(id){
	$('#paper_row_'+id).slideUp(500, function(){
		$('#paper_row_'+id).remove();
		// Re-do all of the paper IDs
		$.each($('.edit_publications tbody tr'), function(key, row){
			var id = key + 1;
			$(this).attr('id', 'paper_row_'+id);
			$(this).find('.paper_id').attr('id', 'paper_id_'+id).attr('name', 'paper_id_'+id);
			$(this).find('.paper_year').attr('id', 'paper_year_'+id).attr('name', 'paper_year_'+id);
			$(this).find('.paper_journal').attr('id', 'paper_journal_'+id).attr('name', 'paper_journal_'+id);
			$(this).find('.paper_title').attr('id', 'paper_title_'+id).attr('name', 'paper_title_'+id);
			$(this).find('.paper_authors').attr('id', 'paper_authors_'+id).attr('name', 'paper_authors_'+id);
			$(this).find('.paper_pmid').attr('id', 'paper_pmid_'+id).attr('name', 'paper_pmid_'+id);
			$(this).find('.paper_doi').attr('id', 'paper_doi_'+id).attr('name', 'paper_doi_'+id);
			$(this).find('.paper_delete').attr('id', 'paper_delete_'+id);
		});
		// Put in 'no paper if now empty'
		if($('.edit_publications tbody tr').length == 0){
			$('.edit_publications tbody').html('<tr class="no_papers_tr"><td colspan="7"><em>No papers found..</em></td></tr>');
		}
	});
}

///////
// ACCESSION NUMBER LOOKUP FUNCTIONS
///////

// NCBI GEO accession lookup
$('#geo_lookup').click(function(e){
	e.preventDefault();
	var icon = $(this).children();
	
	var acc = $.trim($('#accession_geo').val());
	
	accessions = acc.split(" ");
	
	$.each(accessions, function(index, acc){
	
		if(acc.length > 1){

			icon.removeClass('icon-search icon-remove').addClass('icon-refresh icon-rotate-animate');
			$('#geo_error_message').remove();
			
			$.getJSON('ajax/get_geo_project.php?acc='+acc, function(data) {
				
				// Check that the call succeeded
				if(data['status'] == 1){
					$.each(data, function(key, value){
						// title
						if(key == 'title' && $('#title').val().length == 0){
							$('#title').val(value);
						}
						// summary
						if(key == 'description' && $('#description').val().length == 0){
							$('#description').val(value);
						}
						// PubMedIds
						if(key == 'PMIDs'){
							var PMIDs = value;
							var first_author = false;
							var first_year = false;
							$.each(PMIDs, function(key, PMID){
								// Check if we have this paper already
								var pmid_exists = false;
								var pid = false;
								$('.paper_pmid').each(function(){
									if($(this).val() == PMID){
										pmid_exists = true;
										pid = $(this).attr('id').substr(11);
									}
								});
								
								$.getJSON('ajax/lookup_pmid.php?PMID='+PMID, function(data) {
									// Check that the call succeeded
									if(data['status'] == 1){
										// Create a new paper
										if(!pmid_exists){
											$('.no_papers_tr').remove();
											var num_papers = $('.edit_publications tbody tr').length;
											pid = num_papers + 1;
											$('.edit_publications tbody').append('<tr id="paper_row_'+pid+'"><td><input type="text" maxlength="4" class="paper_year" id="paper_year_'+pid+'" name="paper_year_'+pid+'"  /></td><td><input type="text" class="paper_journal" id="paper_journal_'+pid+'" name="paper_journal_'+pid+'"  /></td><td><input type="text" class="paper_title" id="paper_title_'+pid+'" name="paper_title_'+pid+'" ></td><td><input type="text" class="paper_authors" id="paper_authors_'+pid+'" name="paper_authors_'+pid+'" ></td><td><input type="text" class="paper_pmid" id="paper_pmid_'+pid+'" name="paper_pmid_'+pid+'"  /></td><td><input type="text" class="paper_doi" id="paper_doi_'+pid+'" name="paper_doi_'+pid+'"  /></td><td><button class="paper_delete paper_delete_nodb btn btn-small btn-danger" id="paper_delete_'+pid+'">Delete</button></td></tr>');
											$('#paper_pmid_'+pid).val(PMID);
										}
										
										$.each(data, function(pmid_key, pmid_value){								
											// year
											if(pmid_key == 'year' && (typeof $('#paper_year_'+pid).val() == 'undefined' || $('#paper_year_'+pid).val().length == 0)){
												$('#paper_year_'+pid).val(pmid_value);
												if(!first_year){
													first_year = pmid_value;
												}
											}
											// journal
											if(pmid_key == 'journal' && (typeof $('#paper_journal_'+pid).val() == 'undefined' || $('#paper_journal_'+pid).val().length == 0)){
												$('#paper_journal_'+pid).val(pmid_value);
											}
											// title
											if(pmid_key == 'title' && (typeof $('#paper_title_'+pid).val() == 'undefined' || $('#paper_title_'+pid).val().length == 0)){
												$('#paper_title_'+pid).val(pmid_value);
											}
											// authors
											if(pmid_key == 'authors' && (typeof $('#paper_authors_'+pid).val() == 'undefined' || $('#paper_authors_'+pid).val().length == 0)){
												$('#paper_authors_'+pid).val(pmid_value);
												if(!first_author){
													var authors = pmid_value.split(" ");
													first_author = authors[0];
												}
											}
											// DOI
											if(pmid_key == 'DOI' && (typeof $('#paper_doi_'+pid).val() == 'undefined' || $('#paper_doi_'+pid).val().length == 0)){
												$('#paper_doi_'+pid).val(pmid_value);
											}
										});
										// If we don't have both first author and year, wipe the vars
										// Otherwise, try to fill in the data.
										if(!first_year || !first_author){
											first_author = false;
											first_year = false;
										} else if(first_year.length > 0 && first_author.length > 0 && (typeof $('#name').val() == 'undefined' || $('#name').val().length == 0)){
											var name = first_author+'_'+first_year
											name = name.replace(/[^A-Za-z0-9_]/g, "_");
											$('#name').val(name);
											updateProjectName();
										}
									}
								});
							});
						}
					});
				
					icon.removeClass('icon-refresh icon-rotate-animate').addClass('icon-ok');
					icon.parent().after('<span id="geo_message"> &nbsp; '+data['message']+'</span>');
					
				} else {
					// Something went wrong
					icon.removeClass('icon-refresh icon-rotate-animate').addClass('icon-remove');
					icon.parent().after('<span id="geo_error_message"> &nbsp; Error: '+data['message']+'</span>');
				}
				
			});
			
		}
	
	});
});



// PubMed ID lookup
$('#pmid_lookup').click(function(e){

	e.preventDefault();
	var icon = $(this).children();
	
	var PMID = $.trim($('#accession_pmid').val());
	if(PMID.length > 1){
	
		icon.removeClass('icon-search icon-remove').addClass('icon-refresh icon-rotate-animate');
		$('#pmid_error_message').remove();
		
		// Check if we have this paper already
		var pmid_exists = false;
		var pid = false;
		$('.paper_pmid').each(function(){
			if($(this).val() == PMID){
				pmid_exists = true;
				pid = $(this).attr('id').substr(11);
			}
		});
		
		$.getJSON('ajax/lookup_pmid.php?PMID='+PMID, function(data) {
			
			// Check that the call succeeded
			if(data['status'] == 1){
				
				var first_author = false;
				var first_year = false;
				
				// Create a new paper
				if(!pmid_exists){
					$('.no_papers_tr').remove();
					var num_papers = $('.edit_publications tbody tr').length;
					pid = num_papers + 1;
					$('.edit_publications tbody').append('<tr id="paper_row_'+pid+'"><td><input type="text" maxlength="4" class="paper_year" id="paper_year_'+pid+'" name="paper_year_'+pid+'"  /></td><td><input type="text" class="paper_journal" id="paper_journal_'+pid+'" name="paper_journal_'+pid+'"  /></td><td><input type="text" class="paper_title" id="paper_title_'+pid+'" name="paper_title_'+pid+'" ></td><td><input type="text" class="paper_authors" id="paper_authors_'+pid+'" name="paper_authors_'+pid+'" ></td><td><input type="text" class="paper_pmid" id="paper_pmid_'+pid+'" name="paper_pmid_'+pid+'"  /></td><td><input type="text" class="paper_doi" id="paper_doi_'+pid+'" name="paper_doi_'+pid+'"  /></td><td><button class="paper_delete paper_delete_nodb btn btn-small btn-danger" id="paper_delete_'+pid+'">Delete</button></td></tr>');
					$('#paper_pmid_'+pid).val(PMID);
				}
				$.each(data, function(pmid_key, pmid_value){
					// year
					if(pmid_key == 'year' && (typeof $('#paper_year_'+pid).val() == 'undefined' || $('#paper_year_'+pid).val().length == 0)){
						$('#paper_year_'+pid).val(pmid_value);
						if(!first_year){
							first_year = pmid_value;
						}
					}
					// journal
					if(pmid_key == 'journal' && (typeof $('#paper_journal_'+pid).val() == 'undefined' || $('#paper_journal_'+pid).val().length == 0)){
						$('#paper_journal_'+pid).val(pmid_value);
					}
					// title
					if(pmid_key == 'title' && (typeof $('#paper_title_'+pid).val() == 'undefined' || $('#paper_title_'+pid).val().length == 0)){
						$('#paper_title_'+pid).val(pmid_value);
					}
					// authors
					if(pmid_key == 'authors' && (typeof $('#paper_authors_'+pid).val() == 'undefined' || $('#paper_authors_'+pid).val().length == 0)){
						$('#paper_authors_'+pid).val(pmid_value);
						if(!first_author){
							var authors = pmid_value.split(" ");
							first_author = authors[0];
						}
					}
					// DOI
					if(pmid_key == 'DOI' && (typeof $('#paper_doi_'+pid).val() == 'undefined' || $('#paper_doi_'+pid).val().length == 0)){
						$('#paper_doi_'+pid).val(pmid_value);
					}
				});
				
				// Fill in project name if it's empty
				if(first_year.length > 0 && first_author.length > 0 && (typeof $('#name').val() == 'undefined' || $('#name').val().length == 0)){
					var name = first_author+'_'+first_year
					name = name.replace(/[^A-Za-z0-9_]/g, "_");
					$('#name').val(name);
					updateProjectName();
				}
				
				// Success!
				icon.removeClass('icon-refresh icon-rotate-animate').addClass('icon-ok');
			} else {
				// Lookup failed
				icon.removeClass('icon-refresh icon-rotate-animate').addClass('icon-remove');
				icon.parent().after('<span id="pmid_error_message"> &nbsp; Error: '+data['message']+'</span>');
			}
		});
	}
});