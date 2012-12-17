<?php

// Connect to database
include('includes/db_login.php');

// Handle form submissions
if($_POST['create_paper_submitted'] == 'submitted') {
	include('includes/create_paper.php');
}
if($_POST['create_dataset_submitted'] == 'submitted') {
	include('includes/create_dataset.php');
}

// JSON DATA URL
// http://localhost/fetch_geo.php?GEO=GSE11523

include('includes/header.php');

?>
		<p class="lead">To request some publicly available data, please fill in the details below...</p>
		<p>There are two stages to requesting publicly available data: filling out the details of the paper
			and filling out the details of the individual datasets. If the data you'd like isn't published 
			or is complicated, please <a href="mailto:phil.ewels@babraham.ac.uk">e-mail us</a> instead.</p>
		
		<form class="form-search" onsubmit="return false;" id="geo_search_form">
			<fieldset>
				<legend>Find Paper from GEO Accession</legend>
				<p>This system works best if the data you'd like is on the
					<a href="http://www.ncbi.nlm.nih.gov/geo/" target="_blank">NCBI GEO database</a> - if you know the
					GEO accession number pop it in below and most of the information will found for you automagically.</p>
				<div style="text-align:center;">
					<div class="input-append">
						<input type="text" id="geo_search" class="span2 search-query input-large" placeholder="GSE00000">
						<input type="submit" class="btn btn-primary btn-large" id="geo_search_submit" style="font-size:17.5px;" value="Find Dataset">
					</div>
				</div>
			</fieldset>
		</form>
		<div id="geo_fetch_results">
		</div>
			
		<form action="create_dataset.php" method="post" id="create-paper" class="form-horizontal" onsubmit="$('#loadingModal').modal({ keyboard: false });">
			<input type="hidden" name="create_paper_submitted" value="submitted" id="create_paper_submitted">
			<fieldset>
				<legend>Create New Paper</legend>
				
				
				
				<div class="control-group <?php echo in_array('first_author', $error_fields) ? 'error' : ''; ?>">
					<label class="control-label" for="first_author">First Author</label>
					<div class="controls">
						<input type="text" name="first_author" value="<?= $form['first_author']; ?>" id="first_author">
					</div>
				</div>
				<div class="control-group <?php echo in_array('year', $error_fields) ? 'error' : ''; ?>">
					<label class="control-label" for="year">Year of Publication</label>
					<div class="controls">
						<input type="text" name="year" maxlength="4" value="<?= $form['year']; ?>" id="year">
					</div>
				</div>
				<div class="control-group <?php echo in_array('paper_title', $error_fields) ? 'error' : ''; ?>">
					<label class="control-label" for="paper_title">Paper Title</label>
					<div class="controls">
						<input type="text" name="paper_title" value="<?= $form['paper_title']; ?>" id="paper_title">
					</div>
				</div>
				<div class="control-group <?php echo in_array('authors', $error_fields) ? 'error' : ''; ?>">
					<label class="control-label" for="authors">Authors</label>
					<div class="controls">
						<textarea name="authors" id="authors" rows="2" cols="40"><?= $form['authors']; ?></textarea>
					</div>
				</div>
				<div class="control-group <?php echo in_array('PMID', $error_fields) ? 'error' : ''; ?>">
					<label class="control-label" for="PMID">PMID</label>
					<div class="controls">
						<input type="text" name="PMID" value="<?= $form['PMID']; ?>" id="PMID">
						<span class="help-inline">PubMed ID</span>
					</div>
				</div>
				<div class="control-group <?php echo in_array('DOI', $error_fields) ? 'error' : ''; ?>">
					<label class="control-label" for="DOI">DOI</label>
					<div class="controls">
						<input type="text" name="DOI" value="<?= $form['DOI']; ?>" id="DOI">
						<span class="help-inline">Use <a href="http://www.pmid2doi.org/" target="_blank">this tool</a> if in doubt</span>
					</div>
				</div>
				<div class="control-group <?php echo in_array('geo_accession', $error_fields) ? 'error' : ''; ?>">
					<label class="control-label" for="geo_accession">GEO Accession</label>
					<div class="controls">
						<input type="text" name="geo_accession" value="<?= $form['geo_accession']; ?>" id="geo_accession">
						<span class="help-inline"><em>eg.</em> GSE01234</span>
					</div>
				</div>
				<div class="control-group <?php echo in_array('sra_accession', $error_fields) ? 'error' : ''; ?>">
					<label class="control-label" for="sra_accession">SRA Accession</label>
					<div class="controls">
						<input type="text" name="sra_accession" value="<?= $form['sra_accession']; ?>" id="sra_accession">
						<span class="help-inline"><em>eg.</em> SRX012345</span>
					</div>
				</div>
				<div class="control-group <?php echo in_array('notes', $error_fields) ? 'error' : ''; ?>">
					<label class="control-label" for="notes">Notes</label>
					<div class="controls">
						<textarea name="notes" rows="4" cols="40"><?= $form['notes']; ?></textarea>
					</div>
				</div>
				<div class="control-group <?php echo in_array('requested_by', $error_fields) ? 'error' : ''; ?>">
					<label class="control-label" for="requested_by">Requested by</label>
					<div class="controls">
						<input type="text" name="requested_by" value="<?= $form['requested_by']; ?>" id="requested_by">
						<span class="help-inline">Contact e-mail address</span>
					</div>
				</div>
				<div class="control-group <?php echo in_array('processed_by', $error_fields) ? 'error' : ''; ?>">
					<label class="control-label" for="processed_by">Requested by</label>
					<div class="controls">
						<input type="text" name="processed_by" value="<?= $form['processed_by']; ?>" id="processed_by">
						<span class="help-inline">Contact e-mail address</span>
					</div>
				</div>
				<div class="form-actions">
					<input type="submit" class="btn btn-primary" name="create_dataset" id="create_dataset" value="Create Paper">
				</div>
			</fieldset>
		</form>
		

    </div> <!-- /container -->

	<!-- Loading Modal -->
	<div id="loadingModal" class="modal hide fade" tabindex="-1" role="dialog">
		<div class="modal-header">
			<h3>Loading Page</h3>
		</div>
		<div class="modal-body">
			<p>The page is loading, please wait.. <br>
				<small><em>(it's fetching all of the GEO and SRA numbers, which can take a while)</em></small></p>
		</div>
	</div>
	
	<!-- Paper Exists Modal -->
	<div id="paperExistsModal" class="modal hide fade" tabindex="-1" role="dialog">
		<div class="modal-header">
			<h3>Paper Already Exists</h3>
		</div>
		<div class="modal-body">
			<p>It looks like this paper is already in the system! To avoid duplication, please either
				edit the paper and its datasets or add new datasets using the buttons below..</p>
		</div>
		<div class="modal-footer">
			<a class="btn" href="create_paper.php">Cancel</a>
			<a class="btn btn-primary" href="edit_paper.php" id="existing_paper_edit_button">Edit Paper</a>
			<a class="btn btn-primary" href="create_dataset.php" id="existing_paper_addDatasets_button">Add Datasets</a>
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
	
		$('#geo_search_form').submit(function(){
			$('#geo_search_submit, #geo_search').attr('disabled','disabled');
			$('#geo_search_submit').val('Searching..');
			find_GEO();
		});
		
		$('#existing_paper_addDatasets_button').click(function(){
			$('#paperExistsModal').modal('hide');
			$('#loadingModal').modal({
				backdrop: 'static',
				keyboard: false
			});
		});
		
		function find_GEO() {
			var geo_search = $('#geo_search').val();
			$('#geo_accession').val(geo_search);
			var ajax_url = "fetch_geo.php?GEO=" + geo_search;
			$.getJSON(ajax_url, function(data,status) {
				$.each(data, function(key, value) {
					if(key == 'existing_paper'){
						if(value > 0) {
							$('#existing_paper_edit_button').attr('href', 'edit_paper.php?paper_id=' + value);
							$('#existing_paper_addDatasets_button').attr('href', 'create_dataset.php?paper_id=' + value);
							$('#paperExistsModal').modal({
								backdrop: 'static',
								keyboard: false
							});
						}
					} else {
						$('#' + key).val(value);
					}
				});
			});
		}
		
	</script>

  </body>
</html>
