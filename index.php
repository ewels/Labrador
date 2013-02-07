<?php

session_start();

// Connect to database
include('includes/db_login.php');

// Handle form submissions
if($_POST['create_paper_submitted'] == 'submitted') {
	include('includes/create_paper.php');
}
if($_POST['create_dataset_submitted'] == 'submitted') {
	include('includes/create_dataset.php');
}

// Stats
$num_papers = mysql_fetch_row(mysql_query("SELECT COUNT(*) FROM `papers`"));
$dataset_papers = mysql_fetch_row(mysql_query("SELECT COUNT(DISTINCT `paper_id`) FROM `datasets`"));
$num_dataset = mysql_fetch_row(mysql_query("SELECT COUNT(*) FROM `datasets`"));
$num_files_raw = mysql_fetch_row(mysql_query("SELECT COUNT(*) FROM `files_raw`"));
$num_files_aligned = mysql_fetch_row(mysql_query("SELECT COUNT(*) FROM `files_aligned`"));
$num_files_derived = mysql_fetch_row(mysql_query("SELECT COUNT(*) FROM `files_derived`"));
$num_files = $num_files_raw[0] + $num_files_aligned[0] + $num_files_derived[0];

include('includes/header.php');
?>
        <img class="pull-right visible-desktop" style="margin-top:-50px;" src="img/puppies/puppy_2.jpg" title="woof!">
		<p class="lead">This browser is designed for you to find all of the internal and publicly available datasets known to the Reik lab and the Babraham Bioinformatics group.</p>
		<p><strong><?= $num_papers[0] ?></strong> papers have been added, of which <strong><?= $dataset_papers[0] ?></strong> have datasets associated.
		<strong><?= $num_dataset[0] ?></strong> datasets known. <strong><?= $num_files ?></strong> files / directories recorded.
		<?php // this is fun, but slows the page load
		/*
		$path="/data/pipeline/public/TIDIED/"; 
		$ar=getDirectorySize($path); 

		function getDirectorySize($path) { 
		  $totalsize = 0; $totalcount = 0; $dircount = 0; 
		  if ($handle = opendir ($path)) { 
			while (false !== ($file = readdir($handle))) { 
			  $nextpath = $path . '/' . $file; 
			  if ($file != '.' && $file != '..' && !is_link ($nextpath)) { 
				if (is_dir ($nextpath)) { 
				  $dircount++; 
				  $result = getDirectorySize($nextpath); 
				  $totalsize += $result['size']; 
				  $totalcount += $result['count']; 
				  $dircount += $result['dircount']; 
				} elseif (is_file ($nextpath)) { 
				  $totalsize += filesize ($nextpath); 
				  $totalcount++; 
				} 
			  } 
			} 
		  } 
		  closedir ($handle); 
		  return array('size' => $totalsize, 'count' => $totalcount, 'dircount' => $dircount); 
		}
		echo '<strong>'.$ar['count'].'</strong> files found in <strong>'.$ar['dircount'].'</strong> directories on the file system, totalling <strong>'.round($ar['size']/(1024*1024*1024*1024),1).' TB</strong> data stored.';
		//*/
		?>
		</p>
		<p>You can search for specific datasets or browse papers and their datasets below.</p>
		<p>If you would like to access a new dataset not listed here, please use the <a href="create_paper.php" class="request-dataset-nav-link">request dataset form</a>.</p>
		
		
		<form id="search" class="form-search">
			<fieldset>
				<legend>Search Datasets</legend>
				
				<p><strong>Search Fields:</strong> &nbsp; <span id="active_fields_text">All Fields</span></p>
				<p><strong>Active filters:</strong> &nbsp; <span id="active_filters_text">No Filters</span></p>
				
				<div style="text-align:center;">
					<div class="input-prepend input-append">
						<a href="#search_fields" role="button" data-toggle="modal" class="btn btn-info btn-large">Search Fields</a>
						<input type="text" class="span6 input-large" placeholder="Specific search string (optional)" autocomplete="off" id="labrador_search_string">
						<a href="#search_filters" role="button" data-toggle="modal" class="btn btn-warning btn-large">Set Filters</a>
					</div>
				</div>
				
			</fieldset>
		</form>
		
		<!-- Search results go here -->
		<div id="ajax_search"></div>
		
		<!-- Unsymantic (redundant) html to keep consistent page styling - sorry! -->
		<p id="browse">&nbsp;</p>
		<p>&nbsp;</p>
		<p>&nbsp;</p>
		<form>
			<fieldset>
				<legend>Browse Papers</legend>
			</fieldset>
		</form>
		<?php $query = "SELECT * FROM `papers` ORDER BY `first_author`, `year`";
		$datasets = mysql_query($query);		?>
		<div style="width:100%; overflow:auto;">
			<table id="paper-browser-table" class="table table-striped table-hover table-condensed table-bordered small" style="cursor:pointer;">
				<tr>
					<th width="10%">First Author</th>
					<th width="10%">Year of Publication</th>
					<th width="40%">Paper Title</th>
					<th width="40%">Authors</th>
				</tr>
				<?php
				while($result = mysql_fetch_array($datasets)): ?>
					<tr id="paper_<?= $result['id'] ?>" class="paper">
						<td><?= $result['first_author'] ?></td>
						<td><?= $result['year'] ?></td>
						<td><?= stripslashes($result['paper_title']) ?></td>
						<td><?php // limit the number of authors displayed and underline first and last.
						$authors_array = explode(',', $result['authors']);
						//echo $link;
						echo '<u>'.$authors_array[0].'</u>, ';
						if(count($authors_array) > 12){
							echo implode(', ', array_slice($authors_array, 1, 11)) . ' <span style="background-color:#CDCDCD;">...</span> ';
						} else {
							echo implode(', ', array_slice($authors_array, 1, -1));
						}
						echo ', <u>'.trim($authors_array[count($authors_array) - 1]).'</u>';
						;?>
						</td>
					</tr>
				<?php endwhile; ?>
			</table>
		</div>
		
		
		<img class="pull-right visible-desktop" src="img/puppies/sleepy_puppy_1_300px.jpg" style="margin: 0 -20px -30px 0;">
		<div class="clearfix"></div>
		<small>Labrador Dataset Browser made by <a href="mailto:phil.ewels@babraham.ac.uk">Phil Ewels</a>, 2012</small>
	</div> <!-- /container -->
	
	
	
	<!-- search Fields Modal -->
	<div id="search_fields" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="fields_title" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<h3 id="filters_title">Search Fields</h3>
		</div>
		<div class="modal-body">
			<form>
				<p><small>You can specify which fields you would like to search in here.</small></p>
				<h5>Paper Fields &nbsp; <a href="javascript:selectAll('#search_fields_paper');" class="btn btn-mini">All Fields</a> &nbsp; <a href="javascript:selectNone('#search_fields_paper');" class="btn btn-mini">No Fields</a></h5>
				<select multiple style="width:500px;" id="search_fields_paper" data-placeholder="Not searching any paper fields...">
					<option value="search_fields_p_title">Title</option>
					<option value="search_fields_p_authors">Authors</option>
					<option value="search_fields_p_PMID">PMID</option>
					<option value="search_fields_p_DOI">DOI</option>
					<option value="search_fields_p_geo">GEO Accession</option>
					<option value="search_fields_p_sra">SRA Accession</option>
					<option value="search_fields_p_notes">Notes</option>
					<option value="search_fields_p_requested">Requested By</option>
					<option value="search_fields_p_processed">Processed By</option>
				</select>
				<h5>Dataset Fields &nbsp; <a href="javascript:selectAll('#search_fields_dataset');" class="btn btn-mini">All Fields</a> &nbsp; <a href="javascript:selectNone('#search_fields_dataset');" class="btn btn-mini">No Fields</a></h5>
				<select multiple style="width:500px;" id="search_fields_dataset" data-placeholder="Not searching any dataset fields...">
					<option value="search_fields_d_name">Name</option>
					<option value="search_fields_d_species">Species</option>
					<option value="search_fields_d_cellType">Cell Type</option>
					<option value="search_fields_d_dataType">Data Type</option>
					<option value="search_fields_d_geo">GEO Accession</option>
					<option value="search_fields_d_sra">SRA Accession</option>
					<option value="search_fields_d_notes">Notes</option>
				</select>
			</form>
		</div>
		<div class="modal-footer">
			<button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
			<button class="btn btn-primary" id="search_fields_save">Set Fields</button>
			<a class="btn btn-danger pull-left" href="javascript:selectAll('#search_fields_paper, #search_fields_dataset');">Reset Fields</a>
		</div>
	</div>
	<!-- Search Filters Modal -->
	<div id="search_filters" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="filters_title" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<h3 id="filters_title">Search Filters</h3>
		</div>
		<div class="modal-body">
			<form>
				<p><small>You can filter your search results using these boxes. This will restrict the search results to datasets from the requested species, cell type or data type.</small></p>
				<h5>Species &nbsp; <a href="javascript:selectNone('#search_filters_species');" class="btn btn-mini">Clear</a></h5>
				<select multiple style="width:500px;" id="search_filters_species" data-placeholder="Any species">
					<?php $results = mysql_query("SELECT `species` FROM `datasets` GROUP BY `species` ORDER BY count(*) DESC");
					while($result = mysql_fetch_array($results)): ?>
					<option value="search_filters_sp_<?= preg_replace('/\s+/','_', $result['species']); ?>"><?= $result['species'] ?></option>
					<?php endwhile; ?>
				</select>
				<h5>Cell Type &nbsp; <a href="javascript:selectNone('#search_filters_cellType');" class="btn btn-mini">Clear</a></h5>
				<select multiple style="width:500px;" id="search_filters_cellType" data-placeholder="Any cell type">
					<?php $results = mysql_query("SELECT DISTINCT `cell_type` FROM `datasets` ORDER BY `cell_type` ASC");
					while($result = mysql_fetch_array($results)): ?>
					<option value="search_filters_ct_<?= preg_replace('/\s+/','_', $result['cell_type']); ?>"><?= $result['cell_type'] ?></option>
					<?php endwhile; ?>
				</select>
				<h5>Data Type &nbsp; <a href="javascript:selectNone('#search_filters_dataType');" class="btn btn-mini">Clear</a></h5>
				<select multiple style="width:500px;" id="search_filters_dataType" data-placeholder="Any data type">
					<?php $results = mysql_query("SELECT DISTINCT `data_type` FROM `datasets` ORDER BY `data_type` ASC");
					while($result = mysql_fetch_array($results)): ?>
					<option value="search_filters_dt_<?= preg_replace('/\s+/','_', $result['data_type']); ?>"><?= $result['data_type'] ?></option>
					<?php endwhile; ?>
				</select>
			</form>
		</div>
		<div class="modal-footer">
			<button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
			<button class="btn btn-primary" id="search_filters_save">Set Filters</button>
			<a class="btn btn-danger pull-left" href="javascript:selectNone('#search_filters_species, #search_filters_cellType, #search_filters_dataType');">Reset Filters</a>
		</div>
	</div>
		  

		
	<!-- Loading Modal -->
	<div id="loadingModal" class="modal hide fade" tabindex="-1" role="dialog">
		<div class="modal-header">
			<h3>Fetching New Page</h3>
		</div>
		<div class="modal-body">
			<p>I'm sorry, the page is loading - I'll be as quick as I can! </p>
			<img src="img/puppies/sad_puppy_2.jpg" class="pull-left visible-desktop" style="max-width:300px;">
			<p><small><em>(I'm fetching all of the GEO and SRA numbers, which can take a while)</em></small></p>
		</div>
	</div>

	<!-- E-mail Log In Modal' -->
	<div id="emailLoginModal" class="modal hide fade" tabindex="-1" role="dialog">
		<div class="modal-header">
			<h3>Please Log In</h3>
		</div>
		<div class="modal-body">
			<p>So that we know who is requesting this dataset, please enter your e-mail adderss:</p>
			<form onsubmit="return false;" class="form-inline" style="text-align:center;">
				<input type="text" class="input-xlarge" placeholder="Email" id="emailLoginInput">
				<a class="btn btn-primary" href="create_paper.php?email=" id="emailLoginLink">Sign In</a>
			</form>
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
	<script src="includes/chosen/chosen.jquery.js"></script>
	<script src="js/jquery.cookie.js"></script>
	<script type="text/javascript">

		// tooltip binding that works with dynamic content
		$('body').tooltip({
			selector: '[rel=tooltip]'
		});
		
		// Load ajax search results
		function loadSearchAjax (query){
			if(typeof query == 'undefined'){
				var q = '';
			} else {
				var q = '?q='+query;
			}
			$.get('ajax_search.php'+q, function(data) {
				$('#ajax_search').html(data);
			});
		}
		
		$(document).ready(function() {
			// Update active filters without loading ajax results
			updateFieldsFilters ( false );
			// Filter chosen dropdowns (run after updated selected fields)
			$("select").chosen();
		});
		
		
		// Fire search results if user presses enter on search field (keypress rather than keyup)
		$('#labrador_search_string').keypress(function(e){
			if (e.which == 13) {
				e.preventDefault();
				loadSearchAjax($('#labrador_search_string').val());
			}
		});
		
		// Fire search results each time a key is pressed in the search field
		$('#labrador_search_string').keyup(function(e){
			e.preventDefault();
			loadSearchAjax($('#labrador_search_string').val());
		});
		
		// Search field and filter cookie sets
		$('#search_fields_save').click(function(){
			setFieldsCookie();
			updateFieldsFilters();
			$('#search_fields').modal('hide');
		});
		$('#search_filters_save').click(function(){
			setFiltersCookie();
			updateFieldsFilters();
			$('#search_filters').modal('hide');
		});
		
		function selectAll ( selectID ) {
			$(selectID).children('option').each(function(index) {
				$(this).attr('selected', 'selected');
			});
			$("select").trigger("liszt:updated");
			return false;
		}
		function selectNone ( selectID ) {
			$(selectID).children('option').each(function(index) {
				$(this).removeAttr('selected');
			});
			$("select").trigger("liszt:updated");
			return false;
		}
		

		
		function setFieldsCookie () {
			$.removeCookie('search_fields');
			var fields = new Array();
			if($('#search_fields_paper').val() !== null){ fields.push($('#search_fields_paper').val()); }
			if($('#search_fields_dataset').val() !== null){ fields.push($('#search_fields_dataset').val()); }
			$.cookie('search_fields', fields, { expires: 365 });
		}
		function setFiltersCookie () {
			$.removeCookie('search_filters');
			var fields = new Array();
			if($('#search_filters_species').val() !== null){ fields.push($('#search_filters_species').val()); }
			if($('#search_filters_cellType').val() !== null){ fields.push($('#search_filters_cellType').val()); }
			if($('#search_filters_dataType').val() !== null){ fields.push($('#search_filters_dataType').val()); }
			$.cookie('search_filters', fields, { expires: 365 });
		}
		function updateFieldsFilters (loadAjax) {
			loadAjax = typeof loadAjax !== 'undefined' ? loadAjax : true;
			if($.cookie('search_fields') !== null){ 
				var fields = $.cookie('search_fields').split(',');
			} else {
				var fields = new Array();
				$('#search_fields_paper, #search_fields_dataset').children('option').each(function(){
					fields.push($(this).val());
				});
			}
			if($.cookie('search_filters') !== null){ 
				var filters = $.cookie('search_filters').split(',');
			} else { var filters = new Array(); }
			var fieldsContent = '';
			var filtersContent = '';
			$.each(fields, function(index, value) {
				$('#search_fields_paper option[value='+value+']').attr('selected', 'selected');
				$('#search_fields_dataset option[value='+value+']').attr('selected', 'selected');
				if(value.substr(14,1) == 'p'){
					var thisText = 'Paper ';
				} else {
					var thisText = 'Dataset ';
				}
				thisText += value.substr(16).replace(/_/, ' ');
				if(thisText.length > 0){
					fieldsContent += '<span class="label label-info" title="Search Field">'+thisText+'</span> ';
				}
			});
			if(fields.length == 16){
				fieldsContent = 'All Fields';
			} else if(fieldsContent.length == 0){
				fieldsContent = '<span class="label labe-danger">No Fields</span>';
			}
			$.each(filters, function(index, value) {
				$('#search_filters_species option[value='+value+']').attr('selected', 'selected');
				$('#search_filters_cellType option[value='+value+']').attr('selected', 'selected');
				$('#search_filters_dataType option[value='+value+']').attr('selected', 'selected');
				var thisText = value.substr(18).replace(/_/, ' ');
				if(thisText.length > 0){
					filtersContent += '<span class="label label-warning" title="Search Filter">'+thisText+'</span> ';
				}
			});
			if(filtersContent.length == 0){
				filtersContent = 'No Filters';
			}
			$('#active_fields_text').html(fieldsContent);
			$('#active_filters_text').html(filtersContent);
			if(loadAjax){
				loadSearchAjax($('#labrador_search_string').val());
			}
		}
		
		
		// Launch paper modal on select
		$('#paper-browser-table tr td').on('click', function() {
			var id = $(this).parent().attr('id').substr(6);
			var url = 'includes/paper_modal.php?id=' + id;
			$.get(url, function(data) {
				$('<div class="modal hide fade ajaxModal">' + data + '</div>').modal();
			});
		});
		
		// loading modal when add datasets clicked from modal
		$('.paperModal_add_datasets_button').live('click', function(){
			$('.ajaxModal').modal('hide');
			$('#loadingModal').modal({
				backdrop: 'static',
				keyboard: false
			});
		});
		// hide loading modal when the page changes (to avoid buggy back button behaviour)
		$(window).unload(function(){
			$('#loadingModal').modal('hide');
		});

		
		<?php if(!isset($_SESSION['email'])){ ?>
		// prompt for e-mail address if we don't have it in the session data
		$('.request-dataset-nav-link').click(function(e){
			e.preventDefault();
			$('#emailLoginModal').modal();
		});
		$('#emailLoginInput').keyup(function(){
			$('#emailLoginLink').attr('href','create_paper.php?email=' + $(this).val());
		});
		<?php } // session data if check ?>
	</script>
	
  </body>
</html>
