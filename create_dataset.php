<?php

// Include entrez functions
include('includes/entrez_api_functions.php');

// Connect to database
include('includes/db_login.php');

if(isset($_GET['paper_id']) && is_numeric($_GET['paper_id'])) {
	$paper_id = $_GET['paper_id'];
	$geo_acc = false;
	$sra_acc = false;
} else {
	$paper_id = false;
	$geo_acc = false;
}

// Handle form submissions
if($_POST['create_paper_submitted'] == 'submitted') {
	include('includes/create_paper.php');
}
if($_POST['create_dataset_submitted'] == 'submitted') {
	include('includes/create_dataset.php');
}

// JSON DATA URL
// http://bilin1/labrador/fetch_geo.php?GEO=GSE11523&datasets=true

include('includes/header.php');

?>
		<p class="lead">Great! We have your paper details, now we need to know about each dataset that you'd like..</p>
		
		
		<form action="index.php" method="post" id="create-dataset" class="form-horizontal">
			<input type="hidden" name="create_dataset_submitted" value="submitted" id="create_dataset_submitted">
			<fieldset>
				<legend>Paper</legend>
				<img class="pull-right visible-desktop" src="img/puppies/puppy_flowers.jpg" style="max-height:300px; margin:-20px 0 -60px;">
				<p>This is the paper that we're about to add datasets to. You can select another paper from the 
					drop down box and the page will refresh.</p>
				<div class="control-group <?php echo in_array('paper', $error_fields) ? 'error' : ''; ?>">
					<label class="control-label" for="paper">Paper</label>
					<div class="controls">
						<select name="paper" id="paper">
							<?php
							$selected = false;
							$papers = array();
							$this_paper = '';
							$query = "SELECT * FROM `papers` ORDER BY `first_author`";
							$results = mysql_query($query);
							while($result = mysql_fetch_array($results)) {
								$papers[$result['last_modified']] = array(
									'id' => $result['id'],
									'year' => $result['year'],
									'first_author' => $result['first_author'],
									'authors' => $result['authors'],
									'paper_title' => $result['paper_title'],
									'PMID' => $result['PMID'],
									'geo_accession' => $result['geo_accession'],
									'sra_accession' => $result['sra_accession']
								);
							}
							echo '<optgroup label="Recently added">';
							$mostrecent_papers = $papers; // Duplicate papers array so to keep in original order for later
							krsort($mostrecent_papers); // Sort array by keys (modified stamps) in reverse order
							$i = 0;
							foreach ($mostrecent_papers as $mostrecent){
								if(!$paper_id && $i == 0) {
									$paper_id = $mostrecent['id'];
									$geo_acc = $mostrecent['geo_accession'];
								}
								echo '<option ';
								if ($paper_id == $mostrecent['id']) {
									echo 'selected="selected" ';
									$this_paper = $mostrecent;
									$selected = true;
								}
								echo 'value="'.$mostrecent['id'].'">'.$mostrecent['first_author'].' '.$mostrecent['year'].'</option>';
								$i++;
								if($i >= 2){
									break;
								}
							}
							echo '</optgroup>
							<optgroup label="All papers">';
							foreach ($papers as $paper){
								echo '<option ';
								if ($paper_id == $paper['id'] && !$selected) {
									echo 'selected="selected" ';
									$this_paper = $paper;
									$selected = true;
								}
								echo 'value="'.$paper['id'].'">'.$paper['first_author'].' '.$paper['year'].'</option>';
								if(!$geo_acc && $paper_id == $paper['id']) {
									$geo_acc = $paper['geo_accession'];
								}
								if(!$sra_acc && $paper_id == $paper['id']) {
									$sra_acc = $paper['sra_accession'];
								}
							}
							echo '</optgroup>';
							
							?>
						</select>
					</div>
					<br><br><br>
					<p><strong><?= stripslashes($this_paper['paper_title']) ?></strong>
					<p>
						<em><?= stripslashes($this_paper['authors']) ?></em> &nbsp; 
						(<?= $this_paper['year'] ?>) &nbsp;
						<a href="http://www.ncbi.nlm.nih.gov/pubmed/<?= $this_paper['PMID'] ?>" target="_blank">PMID: <?= $this_paper['PMID'] ?></a>
					</p>
				</div>
				
			</fieldset>
			
			
			<?php
			$dataset_query = "SELECT * FROM `datasets` WHERE `paper_id` = '$paper_id'";
			$datasets = mysql_query($dataset_query);
			$existing_datasets = array();
			if(mysql_num_rows($datasets) > 0):
			?>
			<fieldset>
				<legend>Existing Datasets</legend>
				<div class="alert alert-info">
					<strong>Brilliant!</strong> We already have these datasets in the system for this paper..
				</div>
				<table class="table table-bordered table-condensed table-hover">
					<tr>
						<th>Name</th>
						<th>Cell Type</th>
						<th>Data Type</th>
						<th>GEO Accession</th>
						<th>SRA Accession</th>
						<th>Notes</th>
					</tr>
				<?php while ($dataset = mysql_fetch_array($datasets)) :
					$existing_datasets[] = $dataset['geo_accession'];
					foreach(explode(' ', $dataset['sra_accession']) as $sra){
						$existing_datasets[] = $sra; 
					} ?>
					<tr>
						<td><?=$dataset['name']?></td>
						<td><?=$dataset['cell_type']?></td>
						<td><?=$dataset['data_type']?></td>
						<td><?=$dataset['geo_accession']?></td>
						<td><?=$dataset['sra_accession']?></td>
						<td style="text-align:center;">
							<?php if(!empty($dataset['notes'])) { ?>
							<a href="javascript:void(0);" rel="popover" data-content="<?= $dataset['notes'] ?>" data-placement="left" title="Notes"><i class="icon-search"></i></a>
							<?php } ?>
						</td>
					</tr>
				<?php endwhile; ?>
				</table>
			</fieldset>
			<?php endif; //check for existing datasets ?>
			
			
			<fieldset>
				<legend>Add Datasets</legend>
				<?php if($geo_acc) { ?>
				<p>The paper specified above has the GEO accession
					<a href="http://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=<?=$geo_acc?>" title="Click to open the GEO page in a new window" target="_blank"><?=$geo_acc?></a>,
					below are the associated datasets.</p>
				<p>Click the icon on the left of each row to select or deselect the dataset.</p>
				<?php } else if($sra_acc){ ?>
				<p>The paper specified above has the GEO accession
					<a href="http://www.ncbi.nlm.nih.gov/sra/<?=$sra_acc?>" title="Click to open the SRA page in a new window" target="_blank"><?=$sra_acc?></a>,
					below are the associated datasets.</p>
				<p>Click the icon on the left of each row to select or deselect the dataset.</p>
				<?php } else { ?>
				<p>Couldn't find a GEO or SRA accession number with the above paper, so datasets will have to be added manually.</p>
				<?php } ?>
				
				<hr>
				
				<p>Set values for all <abbr rel="tooltip" title="You can select and deselect rows to batch change certain values">selected</abbr> datasets: <small><em><strong>(this will overwrite any values you've entered below)</strong></em></small></p>
				<div class="row">
					<div class="span4">
				 		<label>Species &nbsp; <input type="text" name="species" id="species" class="span2 input-change_all"></label>
						<small class="help-block"><em>eg.</em> Human, Mouse, Saccharomyces cerevisiae</small>
				 	</div>
				 	<div class="span4">
				 		<label>Cell type &nbsp; <input type="text" name="cell_type" id="cell_type" class="span2 input-change_all"></label>
				 		<small class="help-block"><em>eg.</em> ES cells, Rag1−/− pro-B cells</small>
					</div>
				 	<div class="span4">
				 		<label>Data type &nbsp; <input type="text" name="data_type" id="data_type" class="span2 input-change_all"></label>
				 		<small class="help-block"><em>eg.</em> BS-Seq, ChIP-Seq, MeDIP</small>
					</div>
				 </div>
				
				<hr>
				
				<?php
				if(count($existing_datasets) > 0) {
					echo '<div class="alert alert-info">Datasets found with the same GSM numbers as those already listed above have been automagically deselected..</div>';
				}
				?>
				
				<table class="table table-bordered table-condensed table-hover" id="dataset_select_table">
					<thead>
						<tr>
							<th width="3%" id="select_all_datasets" style="text-align:center;"><i class="icon-remove"></i></th>
							<th width="20%">Name</th>
							<th width="15%">Species</th>
							<th width="15%">Cell Type</th>
							<th width="10%">Data Type</th>
							<th width="12.5%"><abbr rel="tooltip" title="Gene Expression Omnibus Accession">GEO</abbr></th>
							<th width="12.5%"><abbr rel="tooltip" title="Sequence Read Archive Accession">SRA</abbr></th>
							<th width="12%"><abbr rel="tooltip" title="Sequence Read Archive Experiment Accession">SRX</abbr></th>
						</tr>
					</thead>
					<tbody>
					<?php
					
					// Should already have GEO accession from above papers select dropdown
					$active_datasets = array();
					if($geo_acc){
						$acc_meta = get_GEO_GSE($geo_acc, true);
					} else if($sra_acc){
						$acc_meta = get_SRA($sra_acc, true);
					} else {
						$acc_meta = false;
					}
					$i = 0;
					if ($acc_meta && !empty($acc_meta['samples'])) {
						foreach($acc_meta['samples'] as $acc => $dataset) {
							$i++;
							$active_datasets[] = $i;
							$disabled = in_array($acc, $existing_datasets) ? 'disabled="disabled"' : '';
							?>
							<tr class="<?php echo in_array($acc, $existing_datasets) ? 'error' : 'success'; ?> dataset_row" id="<?=$i?>_dataset_row">
								<td class="select_dataset_row" style="text-align:center;"><i class="icon-<?php echo in_array($acc, $existing_datasets) ? 'remove' : 'ok'; ?>" title="select / deselect this row"></i></td>
								<td><input <?= $disabled; ?>type="text" name="<?=$i?>_name" class="input-block-level input-name" value="<?=$dataset['name']?>"></td>
								<td><input <?= $disabled; ?>type="text" name="<?=$i?>_species" class="input-block-level input-species" value="<?=$dataset['organism']?>"></td>
								<td><input <?= $disabled; ?>type="text" name="<?=$i?>_cell_type" class="input-block-level input-cell_type"></td>
								<td><input <?= $disabled; ?>type="text" name="<?=$i?>_data_type" class="input-block-level input-data_type" value="<?=$dataset['methodology']?>"></td>
								<td><input <?= $disabled; ?>type="text" name="<?=$i?>_geo_accession" class="input-block-level input-geo_accession" value="<?=$dataset['geo']?>"></td>
								<td><input <?= $disabled; ?>type="text" name="<?=$i?>_sra_accession" class="input-block-level input-sra_accession" value="<?=$dataset['sra']?>"></td>
								<td><input <?= $disabled; ?>type="text" name="<?=$i?>_srx_accession" class="input-block-level input-srx_accession" value="<?=$dataset['srx']?>"></td>
							</tr>
							<?php
						}
					}

					?>
					</tbody>
					<tfoot>
					<?php /* * / ?>
						<tr><td colspan="7"><pre><?= print_r($acc_meta['msg']) ?></pre></td></tr>
					<?php /* * / ?>
						<tr><td colspan="7"><pre><?= print_r($acc_meta['samples']) ?></pre></td></tr>
					<?php /* */ ?>
						<tr>
							<td colspan="8"><a class="btn" href="javascript:void(0);" id="add_row_button">Add</a> <input type="text" id="add_row_rows" class="span1" value="1"> rows</td>
						</tr>
					</tfoot>
				</table>
				<input type="hidden" name="active_datasets" value="<?php echo implode(' ', $active_datasets); ?>" id="active_datasets">
				
				<div class="form-actions">
					<input type="submit" class="btn btn-primary" name="save_datasets" id="save_datasets" value="Save Datasets">
				</div>
				
			</fieldset>
		</form>

    </div> <!-- /container -->

	<!-- Loading Modal -->
	<div id="loadingModal" class="modal fade" tabindex="-1" role="dialog">
		<div class="modal-header">
			<h3>Loading Page</h3>
		</div>
		<div class="modal-body">
			<p>The page is loading, please wait.. <br>
				<small><em>(it's fetching all of the GEO and SRA numbers, which can take a while)</em></small></p>
		</div>
	</div>
	
	<!-- Duplicate Dataset Modal -->
	<div id="duplicateDatasetsModal" class="modal hide fade" tabindex="-1" role="dialog">
		<div class="modal-header">
			<h3>Duplicate Datasets</h3>
		</div>
		<div class="modal-body">
			<p>It looks like you just tried to add a dataset with a GEO or SRA accession number identical to
				an existing dataset.</p>
			<p> The troublesome datasets were: <code id="dupDatasetModalAccessions"></code></p>
			<p>To avoid duplicates, these dataset rows have been automagically deselected for you.</p>
		</div>
		<div class="modal-footer">
			<button class="btn btn-primary" data-dismiss="modal">Close</button>
		</div>
	</div>
	
	<!-- No Datasets Modal -->
	<div id="noDatasetsModal" class="modal hide fade" tabindex="-1" role="dialog">
		<div class="modal-header">
			<h3>No Datasets</h3>
		</div>
		<div class="modal-body">
			<p>Oops! It looks like you haven't added any new datasets... Make sure that they're selected!</p>
		</div>
		<div class="modal-footer">
			<button class="btn btn-primary" data-dismiss="modal">Close</button>
		</div>
	</div>
	
	<!-- Missing Name Modal -->
	<div id="missingNameModal" class="modal hide fade" tabindex="-1" role="dialog">
		<div class="modal-header">
			<h3>Missing Name</h3>
		</div>
		<div class="modal-body">
			<p>Sorry, each dataset must have a name. Please make sure that each selected dataset has a name.</p>
		</div>
		<div class="modal-footer">
			<button class="btn btn-primary" data-dismiss="modal">Close</button>
		</div>
	</div>
	

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="js/jquery-1.8.3.min.js"></script>
    <script src="js/bootstrap-transition.js"></script>
    <script src="js/bootstrap-alert.js"></script>
    <script src="js/bootstrap-modal.js"></script>
    <script src="js/bootstrap-dropdown.js"></script>
    <script src="js/bootstrap-scrollspy.js"></script>
    <script src="js/bootstrap-tab.js"></script>
    <script src="js/bootstrap-tooltip.js"></script>
    <script src="js/bootstrap-popover.js"></script>
    <script src="js/bootstrap-button.js"></script>
    <script src="js/bootstrap-collapse.js"></script>
    <script src="js/bootstrap-carousel.js"></script>
    <script src="js/bootstrap-typeahead.js"></script>

	<script type="text/javascript">
	
		$("[rel=tooltip]").tooltip();
		$("[rel=popover]").popover()
	
		// Auto refresh page when paper select is changed
		$('#paper').change(function(){
			$('#loadingModal').modal({
				backdrop: 'static',
				keyboard: false
			});
			var selected_paper = $('#paper').val();
			window.location.replace ( 'create_dataset.php?paper_id=' + selected_paper );
		});

// hide loading modal when the page changes (to avoid buggy back button behaviour)
$(window).unload(function(){
		   $('#loadingModal').modal('hide');
		 });
		
		// Select and deselect datasets
		$('#dataset_select_table').on("click", '.select_dataset_row', function(){ // delegated function, works on dynamic content
			if($(this).parent().hasClass('success')){
				$(this).parent().removeClass('success');
				$(this).parent().addClass('error');
				$(this).parent().children().children('i').removeClass('icon-ok');
				$(this).parent().children().children('i').addClass('icon-remove');
				$(this).parent().children().children('input').attr('disabled', 'disabled');
			} else {
				$(this).parent().removeClass('error');
				$(this).parent().addClass('success');
				$(this).parent().children().children('i').removeClass('icon-remove');
				$(this).parent().children().children('i').addClass('icon-ok');
				$(this).parent().children().children('input').removeAttr('disabled', 'disabled');
			}
			reset_active_datasets();
		});
		
		// Select or deselect all rows at once
		$('#select_all_datasets').click(function(){ // always rendered onload, so no delegation required
			if($(this).children('i').hasClass('icon-remove')) {
				$('.dataset_row').removeClass('success');
				$('.dataset_row').addClass('error');
				$('.dataset_row').children().children('i').removeClass('icon-ok');
				$('.dataset_row').children().children('i').addClass('icon-remove');
				$('.dataset_row').children().children('input').attr('disabled', 'disabled');
				$(this).children('i').removeClass('icon-remove');
				$(this).children('i').addClass('icon-ok');
			} else {
				$('.dataset_row').removeClass('error');
				$('.dataset_row').addClass('success');
				$('.dataset_row').children().children('i').removeClass('icon-remove');
				$('.dataset_row').children().children('i').addClass('icon-ok');
				$('.dataset_row').children().children('input').removeAttr('disabled', 'disabled');
				$(this).children('i').removeClass('icon-ok');
				$(this).children('i').addClass('icon-remove');
			}
			reset_active_datasets();
		});
		
		// Change all values at once
		$('.input-change_all').keyup(function(){
			var id = $(this).attr('id');
			var value = $(this).val();
			$('.input-' + id + ":not([disabled])").val(value);
		});
		
		// Add extra dataset rows
		$('#add_row_button').click(function(){
			var current_rows = $('.dataset_row').length;
			var next_row = current_rows + 1;
			var new_rows = Number($('#add_row_rows').val());
			var max_rows = next_row + new_rows; // less than, so no -1 needed
			for (var i = next_row; i < max_rows; i++) {
				$('#dataset_select_table tbody').append('<tr class="success dataset_row" id="' + i + '_dataset_row"><td class="select_dataset_row" style="text-align:center;"><i class="icon-ok" title="select / deselect this row"></i></td><td><input type="text" name="' + i + '_name" class="input-block-level input-name"></td><td><input type="text" name="' + i + '_species" class="input-block-level input-species"></td><td><input type="text" name="' + i + '_cell_type" class="input-block-level input-cell_type"></td><td><input type="text" name="' + i + '_data_type" class="input-block-level input-data_type"></td><td><input type="text" name="' + i + '_geo_accession" class="input-block-level input-geo_accession"></td><td><input type="text" name="' + i + '_sra_accession" class="input-block-level input-sra_accession"></td><td><input type="text" name="' + i + '_srx_accession" class="input-block-level input-srx_accession"></td></tr>');
			}
			reset_active_datasets();
		});
		
		// Rewrite hidden form variable saying which datasets are active
		function reset_active_datasets () {
			var active_datasets = new Array();
			$('#dataset_select_table tr.success').each(function(){
				var id_raw = $(this).attr('id');
				var id = id_raw.substr(0, id_raw.length - 12);
				active_datasets.push(id);
			});
			var active_datasets_string = active_datasets.join(' ');
			$('#active_datasets').val( active_datasets_string );
		}
		
		// Flag error when trying to add datasets with the same GEO or SRA as existing datasets
		// (on form submission)
		
		var existing_geo = new Array(<?php echo '"' . implode ('","', array_keys($existing_datasets['samples'])) . '"'; ?>);
		var existing_sra = new Array(<?php echo '"' . implode ('","', $existing_datasets) . '"'; ?>);
		$('#create-dataset').submit(function(){
			
			// check for duplicates
			var duplicate_accessions = new Array();
			var error = false;
			$('.input-geo_accession').each(function(){
				if($(this).val() !== '' && $.inArray($.trim($(this).val()), existing_geo) > -1 && $(this).attr('disabled') != 'disabled') {
					error = true;
					duplicate_accessions.push($.trim($(this).val()));
					$(this).parent().parent().removeClass('success');
					$(this).parent().parent().addClass('error');
					$(this).parent().parent().children('td').children('input').attr('disabled', 'disabled');
					$(this).parent().parent().children('td').children('i').removeClass('icon-ok');
					$(this).parent().parent().children('td').children('i').addClass('icon-remove');
				}
			});
			// This won't trigger if GSM is duplicate too, as it'll be disabled already so skipped
			$('.input-sra_accession').each(function(){
				if($.trim($(this).val()) && $.inArray($.trim($(this).val()), existing_sra) > -1 && $(this).attr('disabled') != 'disabled') {
					error = true;
					duplicate_accessions.push($.trim($(this).val()));
					$(this).parent().parent().removeClass('success');
					$(this).parent().parent().addClass('error');
					$(this).parent().parent().children('td').children('input').attr('disabled', 'disabled');
					$(this).parent().parent().children('td').children('i').removeClass('icon-ok');
					$(this).parent().parent().children('td').children('i').addClass('icon-remove');
				}
			});
			if(error){
				$('#dupDatasetModalAccessions').html(duplicate_accessions.join(', '));
				$('#duplicateDatasetsModal').modal();
				return false;
			}
			
			// Check that we have some new datasets and that they all have names
			var namecount = 0;
			$('.input-name').each(function(){
				if($(this).attr('disabled') != 'disabled'){
					if($.trim($(this).val()) == ''){
						error = true;
					} else {
						namecount++;
					}
				}
			});
			
			if(error) {
				$('#missingNameModal').modal();
				return false;
			}
			if(namecount == 0){
				$('#noDatasetsModal').modal();
				return false;
			}
			
			
		});
		
	</script>

  </body>
</html>
