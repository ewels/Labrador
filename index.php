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

include('includes/header.php');
?>

		<p class="lead">This browser is designed for you to find all of the internal and publicly available datasets known to the Reik lab and the Babraham Bioinformatics group.</p>
		<p>You can search for specific datasets or browse papers and their datasets below.</p>
		<p>If you would like to access a new dataset not listed here, please use the <a href="#request-dataset">form below</a>.</p>
		
		
		<form id="search" class="form-search">
			<fieldset>
				<legend>Search Datasets</legend>
				
				<p>
					<strong>Active filters:</strong> &nbsp;
					None
				</p>
				
				<div style="text-align:center;">
					<div class="input-prepend input-append">
						<a href="#search_filters" role="button" data-toggle="modal" class="btn btn-info btn-large">Set Filters</a>
						<input type="text" class="span6 input-large" placeholder="Specific search string (optional)">
						<button type="submit" class="btn btn-primary btn-large">Search</button>
					</div>
				</div>
				
			</fieldset>
		</form>
		<!-- Modal -->
		<div id="search_filters" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="filters_title" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="filters_title">Search Filters</h3>
			</div>
			<div class="modal-body">
				<p>Please choose the type of data that you would like to search for..</p>
				<form>
					<div class="row">
						<div class="span2">
							<h5>Cell Type</h5>
							<label><input type="checkbox" id="cell_type_all" checked="checked"> <small>All cell types</small></label>
							<?php $results = mysql_query("SELECT DISTINCT `cell_type` FROM `datasets` ORDER BY `cell_type` ASC");
							while($result = mysql_fetch_array($results)): ?>
							<label><input type="checkbox" name="cell_type" value="<?= $result['cell_type'] ?>" checked="checked"> <small><?= $result['cell_type'] ?></small></label>
							<?php endwhile; ?>
						</div>
						<div class="span2">
							<h5>Data Type</h5>
							<label><input type="checkbox" id="data_type_all" checked="checked"> <small>All data types</small></label>
							<?php $results = mysql_query("SELECT DISTINCT `data_type` FROM `datasets` ORDER BY `data_type` ASC");
							while($result = mysql_fetch_array($results)): ?>
							<label><input type="checkbox" name="cell_type" value="<?= $result['data_type'] ?>" checked="checked"> <small><?= $result['data_type'] ?></small></label>
							<?php endwhile; ?>
						</div>
						<div class="span2">
							<h5>Another filter here</h5>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
				<button class="btn btn-primary">Set Filters</button>
			</div>
		</div>
		  
		
		<!-- Unsymantic (redundant) form to keep consistent page styling - sorry! -->
		<form id="browse">
			<fieldset>
				<legend>Browse Papers</legend>
			</fieldset>
		</form>
		<div style="width:100%; overflow:auto;">
			<table id="paper-browser-table" class="table table-striped table-condensed table-bordered table-hover small" style="cursor:pointer;">
				<tr>
					<th width="10%">First Author</th>
					<th width="10%">Year of Publication</th>
					<th width="40%">Paper Title</th>
					<th width="40%">Authors</th>
				</tr>
				<?php
				$query = "SELECT * FROM `papers` ORDER BY `first_author`, `year`";
				$results = mysql_query($query);
				while($result = mysql_fetch_array($results)):
					?>
					<tr id="<?= $result['id'] ?>">
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
				<?php endwhile;	?>
			</table>
		</div>
		
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
		
		// tooltip binding that works with dynamic content
		$('body').tooltip({
		    selector: '[rel=tooltip]'
		});
		
		$('#paper-browser-table tr td').click(function() {
			var id = $(this).parent().attr('id');
			var url = 'includes/paper_modal.php?id=' + id;
			$.get(url, function(data) {
				$('<div class="modal hide fade ajaxModal">' + data + '</div>').modal();
			});
		});
		
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
		
		// loading modal when add datasets clicked from modal
		$('.paperModal_add_datasets_button').live('click', function(){
			$('.ajaxModal').modal('hide');
			$('#loadingModal').modal({
				backdrop: 'static',
				keyboard: false
			});
		});
		
		
		
		// SMOOTH SCROLL
		$(document).ready(function() {
		  function filterPath(string) {
		  return string
		    .replace(/^\//,'')
		    .replace(/(index|default).[a-zA-Z]{3,4}$/,'')
		    .replace(/\/$/,'');
		  }
		  var locationPath = filterPath(location.pathname);
		  var scrollElem = scrollableElement('html', 'body');

		  $('.nav li a[href*=#]').each(function() {
		    var thisPath = filterPath(this.pathname) || locationPath;
		    if (  locationPath == thisPath
		    && (location.hostname == this.hostname || !this.hostname)
		    && this.hash.replace(/#/,'') ) {
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
	</script>
	
  </body>
</html>
