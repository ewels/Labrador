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
		<p>If you would like to access a new dataset not listed here, please use the <a href="#request-dataset">form below</a>.</p>
		
		
		<form id="search" class="form-search">
			<fieldset>
				<legend>Search Datasets</legend>
				
				<p>
					<strong>Search Fields:</strong> &nbsp;
					<span id="active_fields_text">All Fields</span>
					 &nbsp; &nbsp; &nbsp; &nbsp;
					<strong>Active filters:</strong> &nbsp;
					<span id="active_filters_text">No Filters</span>
				</p>
				
				<div style="text-align:center;">
					<div class="input-prepend input-append">
						<a href="#search_fields" role="button" data-toggle="modal" class="btn btn-info btn-large">Search Fields</a>
						<input type="text" class="span6 input-large" placeholder="Specific search string (optional)" autocomplete="off" id="labrador_search_string">
						<a href="#search_filters" role="button" data-toggle="modal" class="btn btn-info btn-large">Set Filters</a>
					</div>
				</div>
				
			</fieldset>
		</form>
		<!-- search Fields Modal -->
		<div id="search_fields" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="fields_title" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="filters_title">Search Fields</h3>
			</div>
			<div class="modal-body">
				<form>
					<p><small>You can specify which fields you would like to search in here. Leaving a box empty will search all fields. To ignore all fields, select "No Dataset Fields".</small></p>
					<h5>Paper Fields</h5>
					<select multiple style="width:500px;" id="search_fields_paper">
						<option value="search_fields_p_title">Title</option>
						<option value="search_fields_p_authors">Authors</option>
						<option value="search_fields_p_PMID">PMID</option>
						<option value="search_fields_p_DOI">DOI</option>
						<option value="search_fields_p_geo">GEO Accession</option>
						<option value="search_fields_p_sra">SRA Accession</option>
						<option value="search_fields_p_notes">Notes</option>
						<option value="search_fields_p_requested">Requested By</option>
						<option value="search_fields_p_processed">Processed By</option>
						<option value="search_fields_p_noFields">No Paper Fields</option>
					</select>
					<h5>Dataset Fields</h5>
					<select multiple style="width:500px;" id="search_fields_dataset">
						<option value="search_fields_d_name">Name</option>
						<option value="search_fields_d_species">Species</option>
						<option value="search_fields_d_cellType">Cell Type</option>
						<option value="search_fields_d_dataType">Data Type</option>
						<option value="search_fields_d_geo">GEO Accession</option>
						<option value="search_fields_d_sra">SRA Accession</option>
						<option value="search_fields_d_notes">Notes</option>
						<option value="search_fields_d_noFields">No Dataset Fields</option>
					</select>
				</form>
			</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
				<button class="btn btn-primary" id="search_fields_save">Set Fields</button>
				<button class="btn btn-danger pull-left">Reset Fields</button>
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
					<p><small>You can filter your search results using these boxes. Leaving a box empty equates to unfiltered.</small></p>
					<h5>Species</h5>
					<select multiple style="width:500px;" id="search_filters_species">
						<option>All species</option>
						<?php $results = mysql_query("SELECT `species` FROM `datasets` GROUP BY `species` ORDER BY count(*) DESC");
						while($result = mysql_fetch_array($results)): ?>
						<option value="search_filters_sp_<?= preg_replace('/\s+/','_', $result['species']); ?>"><?= $result['species'] ?></option>
						<?php endwhile; ?>
					</select>
					<h5>Cell Type</h5>
					<select multiple style="width:500px;" id="search_filters_cellType">
						<option>All cell types</option>
						<?php $results = mysql_query("SELECT DISTINCT `cell_type` FROM `datasets` ORDER BY `cell_type` ASC");
						while($result = mysql_fetch_array($results)): ?>
						<option value="search_filters_ct_<?= preg_replace('/\s+/','_', $result['cell_type']); ?>"><?= $result['cell_type'] ?></option>
						<?php endwhile; ?>
					</select>
					<h5>Data Type</h5>
					<select multiple style="width:500px;" id="search_filters_dataType">
						<option>All data types</option>
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
				<button class="btn btn-danger pull-left">Reset Filters</button>
			</div>
		</div>
		  
		<div id="ajax_search"></div>

		<img class="pull-right visible-desktop" src="img/puppies/sleepy_puppy_1_300px.jpg" style="margin: 0 -20px -30px 0;">
		<div class="clearfix"></div>
                <small>Labrador Dataset Browser made by <a href="mailto:phil.ewels@babraham.ac.uk">Phil Ewels</a>, 2012</small>
	</div> <!-- /container -->
		
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
			// Load ajax without search term on page load
			loadSearchAjax();
			// Update active filters
			updateFieldsFilters ();
			// Filter chosen dropdowns (run after updated selected fields)
			$("select").chosen();
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
			$('#search_fields').modal('hide')
		});
		$('#search_filters_save').click(function(){
			setFiltersCookie();
			updateFieldsFilters();
			$('#search_filters').modal('hide')
		});
		
		function setFieldsCookie () {
			$.removeCookie('search_fields');
			var fields = new Array();
			if($('#search_fields_paper').val() !== null){ fields.push($('#search_fields_paper').val()); }
			if($('#search_fields_dataset').val() !== null){ fields.push($('#search_fields_dataset').val()); }
			$.cookie('search_fields', fields);
		}
		function setFiltersCookie () {
			$.removeCookie('search_filters');
			var fields = new Array();
			if($('#search_filters_species').val() !== null){ fields.push($('#search_filters_species').val()); }
			if($('#search_filters_cellType').val() !== null){ fields.push($('#search_filters_cellType').val()); }
			if($('#search_filters_dataType').val() !== null){ fields.push($('#search_filters_dataType').val()); }
			$.cookie('search_filters', fields);
		}
		function updateFieldsFilters () {
			if($.cookie('search_fields') !== null){ 
				var fields = $.cookie('search_fields').split(',');
			} else { var fields = new Array(); }
			if($.cookie('search_filters') !== null){ 
				var filters = $.cookie('search_filters').split(',');
			} else { var filters = new Array(); }
			var fieldsContent = '';
			var filtersContent = '';
			$.each(fields, function(index, value) {
				$('#search_fields_paper option[value='+value+']').attr('selected', 'selected');
				$('#search_fields_dataset option[value='+value+']').attr('selected', 'selected');
				var thisText = value.substr(16).replace(/_/, ' ');
				if(thisText.length > 0){
					fieldsContent += '<span class="label label-info" title="Search Field">'+thisText+'</span> ';
				}
			});
			$.each(filters, function(index, value) {
				$('#search_filters_species option[value='+value+']').attr('selected', 'selected');
				$('#search_filters_cellType option[value='+value+']').attr('selected', 'selected');
				$('#search_filters_dataType option[value='+value+']').attr('selected', 'selected');
				var thisText = value.substr(18).replace(/_/, ' ');
				if(thisText.length > 0){
					filtersContent += '<span class="label label-warning" title="Search Filter">'+thisText+'</span> ';
				}
			});
			$('#active_fields_text').html(fieldsContent);
			$('#active_filters_text').html(filtersContent);
			
		}
		
		
		// Launch paper modal on select
		$('#paper-browser-table tr td').on('click', function() {
			var id = $(this).parent().attr('id');
			var url = 'includes/paper_modal.php?id=' + id;
			$.get(url, function(data) {
				$('<div class="modal hide fade ajaxModal">' + data + '</div>').modal();
			});
		});
		
		
		
		<?php /* * / // No idea what this is doing
		// Get
		$('.geo_datasets_search_button').live('click', function(){
			var accession = $(this).attr('id');
			var dataset_url = 'fetch_geo.php?datasets=true&GEO=' + accession;
			$(this).children('button').html('Searching..');
			$(this).children('button').attr('disabled', 'disabled');
			$.getJSON(dataset_url, function(data) {
				$('#geo_datasets_search_button').slideUp();
				var items = [];
				$.each(data, function(key, val) {
					var is_known = 'No';
					var row_status = ' class="error"';
					if( $('#geo_' + key).length != 0 ) {
						is_known = 'Yes';
						row_status = ' class="success"';
					}
					items.push('<tr' + row_status + '><td>' + key + '</td><td>' + val + '</td><td>' + is_known + '</td></tr>');
				});
				$("[rel=tooltip]").tooltip(); // refresh this for ajax conte
				$('.geo_dataset_search_controls').slideUp();
				$('.geo_dataset_search_results').html('<table class="table table-condensed table-bordered table-hover small"><tr><th>GEO Accession</th><th>Name</th><th>Added?</th></tr>' + items.join('') + '</table>');
			});
		});
		/* */ ?>
		
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
		
		
		
		// SMOOTH SCROLL
		$(document).ready(function() {
			function filterPath(string) {
				return string.replace(/^\//,'').replace(/(index|default).[a-zA-Z]{3,4}$/,'').replace(/\/$/,'');
			}
			var locationPath = filterPath(location.pathname);
			var scrollElem = scrollableElement('html', 'body');

			$('.nav li a[href*=#]').each(function() {
				var thisPath = filterPath(this.pathname) || locationPath;
				if (  locationPath == thisPath && (location.hostname == this.hostname || !this.hostname) && this.hash.replace(/#/,'') ) {
					var $target = $(this.hash), target = this.hash;
					if (target) {
						var targetOffset = $target.offset().top - 40; // -40 added to compensate for top bar
						$(this).click(function(event) {
							$(this).parent().parent().children('li').removeClass('active');
							$(this).parent().addClass('active');
							event.preventDefault();
							$(scrollElem).animate({scrollTop: targetOffset}, 400, function() {
								// location.hash = target; // stop jump when the url is replaced
							});
						});
					}
				}
			});

			// use the first element that is "scrollable"
			function scrollableElement(els) {
				for (var i = 0, argLength = arguments.length; i <argLength; i++) {
					var el = arguments[i],
					$scrollElement = $(el);
					if ($scrollElement.scrollTop()> 0) {
						return el;
					} else {
						$scrollElement.scrollTop(1);
						var isScrollable = $scrollElement.scrollTop()> 0;
						$scrollElement.scrollTop(0);
						if (isScrollable) {
							return el;
						}
					}
				}
				return [];
			}
		});

		
		<?php if(!isset($_SESSION['email'])){ ?>
		// prompt for e-mail address if we don't have it in the session data
		$('#request-dataset-nav-link').click(function(e){
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
