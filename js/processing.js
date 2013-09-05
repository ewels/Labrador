/*
   processing.js
   Javascript for the Labrador Processing page
*/

// Show / hide more saved processing
$('.show_more_link').click(function(e){
	e.preventDefault();
	var id = $(this).attr('id').substr(10);
	$(this).hide();
	$('#more_'+id).show();
	$('#hide_more_'+id).show();
});
$('.hide_more_link').click(function(e){
	e.preventDefault();
	var id = $(this).attr('id').substr(10);
	$(this).hide();
	$('#more_'+id).hide();
	$('#show_more_'+id).show();
});

// Automatically select all datasets on page load
$(document).ready(function(){
	$('#processing_table input[type=checkbox]').attr('checked', 'checked');
	$('#processing_table tbody tr').addClass('success');
});

// Triggers to update the text areas and script preview
$('.server, .genome, #processing_table tbody tr td.select input, .select-all').change(function(e){
	updateProcessing();
});
$('.processing_steps_table').on('change', '.processing_type', function(e){
	updateProcessing();
});
// Manual edit of a row. Change type to 'Manual' so it doesn't get overwritten.
$('.processing_steps_table').on('change', '.processing_step', function(e){
	var id = $(this).attr('id').substr(16);
	$('#processing_type_'+id).val('manual');
	updateProcessing();
});

// Add processing step
$('#add_processing_step').click(function(e){
	e.preventDefault();
	addProcessingStep();
});
function addProcessingStep(){
	var i = $('.processing_steps_table tbody tr').length + 1;
	
	var newRow = $('.processing_steps_table tbody tr:first-child').clone();
	newRow.children('th').text('Step '+i+':');
	newRow.find('.processing_type').attr('name', 'processing_type_'+i);
	newRow.find('.processing_type').attr('id', 'processing_type_'+i);
	newRow.find('.processing_step').attr('name', 'processing_step_'+i);
	newRow.find('.processing_step').attr('id', 'processing_step_'+i);
	newRow.appendTo('.processing_steps_table tbody');
	
	if( i > 1){
		$('#delete_processing_step').removeAttr('disabled');
	}
}

// Remove processing step
$('#delete_processing_step').click(function(e){
	e.preventDefault();
	if($('.processing_type').length > 1){
		$('.processing_steps_table tbody tr:last-child').remove();
	}
	if($('.processing_type').length <= 1){
		$(this).attr('disabled','disabled');
	}
});

// Update step text areas (fires preview update on completion)
function updateProcessing () {
	var server = $('#server').val();
	var completedAjax = 0;
	var requiredAjax = $('.processing_type').length;
	var requireGenome = false;	
	
	$('.processing_type').each(function(){
		var type = $(this).val();
		if($(this).find('option:selected').data('genome') == true){
			requireGenome = true;
		}
		
		if(type != 'manual'){
			var id = $(this).attr('id').substr(16);
			var textBox = $('#processing_step_'+id);
			$.get('ajax/get_bash.php?server='+server+'&type='+type, function(data){
				textBox.val(data);
				completedAjax++;
				// If final ajax call, trigger a refresh of the preview
				if(completedAjax >= requiredAjax){
					updateBashPreview ();
				};
			});
		} else {
			updateBashPreview ();
		}
	});
	
	if(requireGenome){
		$('#genome').removeAttr('disabled');
	} else {
		$('#genome').attr('disabled', 'disabled');
	}
}

// Update bash preview area
function updateBashPreview () {
	
	$('#bash_preview').html('<pre></pre>');

	var server = $('#server').val();
	var genome = $('#genome').val();
	
	// Do we need the genome, is it set?
	var needGenome = false;
	
	// Add module load statements to start of script
	$('.processing_type').each(function(){
		var type = $(this).val();
		if(processing_modules[server][type] != undefined){
			$('#bash_preview pre').append(processing_modules[server][type]+"\n");
		}
		if($(this).find('option:selected').data('genome')){
			needGenome = true;
		}
	});
	
	if(needGenome && genome.length == 0){
		$('#bash_preview').html('<p class="text-error">No genome selected</p>');
	} else {
	
		// Generate templates
		var sra_template = '';
		var project_template = '';
		$('.processing_step').each(function(){
			
			var processing_type = $(this).parents('tr').find('.processing_type');
			
			var selected = processing_type.find('option:selected');
			var unit  = selected.data('unit');
			
			if(unit == 'accession_sra'){
				sra_template += $(this).val() + "\n";
			}
			if(unit == 'project'){
				project_template += $(this).val() + "\n";
			}
		});
		
		var completedAjax = 0;
		var requiredAjax = $('#processing_table tbody tr td.select input:checked').length;
		
		// Get bash for accession_sra entries
		if(requiredAjax == 0){
			$('#bash_preview').html('<p class="text-error">No datasets selected</p>');
		} else {
		
			$('#processing_table tbody tr td.select input:checked').each(function(){
				var dataset = $(this).attr('id').substr(6);
				$.post('ajax/get_bash.php', {'dataset': dataset, 'template': sra_template, 'unit': 'accession_sra', 'genome': genome, 'server': server},  function(data){
					$('#bash_preview pre').append(data);
					completedAjax++;
					if(completedAjax == requiredAjax){
					
						// Get bash for project entries
						dataset = $('#project_id').val();
						$.post('ajax/get_bash.php', {'dataset': dataset, 'template': project_template, 'unit': 'project', 'genome': genome, 'server': server},  function(data){
							$('#bash_preview pre').append(data);
							variable_replace_bash();
						});
					
					}
				});
				
			});
		
		}
	}
	
}

// Javascript variable replacement of preview area
function variable_replace_bash(){
	var output = $('#bash_preview pre').text();
	var n = 0;
	var success = false;

	var regex = /{{hold_prev}}/gi, result;
	while ( (result = regex.exec(output)) ) {
		n = result.index;
		if(n > 0){
			var nlPos = output.substr(0, n).lastIndexOf("\n");
			if(nlPos > 0){
				var Npos = output.substr(0, nlPos).lastIndexOf("-N ") + 3;
				if(Npos > 0){
					var Nend = output.substr(Npos, n).indexOf(" ");
					var name = output.substr(Npos, Nend);
					output = output.substr(0,n) + ' -hold_jid ' + name + output.substr(n+13);
					success = true;
				}
			}
		}
		if(!success){
			output = output.substr(0,n) + output.substr(n+13);
		}
	}
	
	
	$('#bash_preview pre').text(output);
	
}




// Pipeline shortcuts
$('.processing_shortcut').click(function(e){
	e.preventDefault();
	var pipeline = $(this).attr('id');
	
	// Add new rows
	while($('.processing_steps_table tbody tr').length < processing_pipelines[pipeline].length){
		addProcessingStep();
	}
	
	// Complete rows
	$.each(processing_pipelines[pipeline], function(index, value){
		var i = index + 1;
		$('#processing_type_'+i).val(value);
	});
	
	updateProcessing ();
});



// Save the bash script
$('#save_run_bash_script, #save_bash_script').click(function(e){
	e.preventDefault();
	if($('#bash_preview pre').text().length > 0){
		saveBashDB();
	} else {
		alert('Error - nothing to save!');
	}
});
function saveBashScript (){
	var server = $('#server').val();
	var output = $('#bash_preview pre').text();
	var project_id = $('#project_id').val();
	$.post('ajax/write_bash.php', {'output': output, 'project_id': project_id, 'server': server},  function(data){
		alert(data);
	});
}

function saveBashDB(){
	var server = $('#server').val();
	var genome = $('#genome').val();
	
	// Generate template
	var sra_template = '';
	$('.processing_step').each(function(){
		var processing_type = $(this).parents('tr').find('.processing_type');
		var selected = processing_type.find('option:selected');
		var unit  = selected.data('unit');
		
		if(unit == 'accession_sra'){
			sra_template += $(this).val();
		}
	});
	
	// Save bash for accession_sra entries to the database
	var saved = 0;
	var required = $('#processing_table tbody tr td.select input:checked').length;
	$('#processing_table tbody tr td.select input:checked').each(function(){
		var dataset = $(this).attr('id').substr(6);
		$.post('ajax/get_bash.php', {'dataset': dataset, 'template': sra_template, 'unit': 'accession_sra', 'genome': genome, 'server': server, 'save_to_db': 'true'}, function(){
			saved++;
			if(saved == required){
				saveBashScript();
			}
		});
		
	});
}
