<?php

session_start();

// Connect to database
include('includes/db_login.php');

include('includes/header.php');

// DOWNLOAD FILES
if($_GET['action'] == 'download'){ 
	$_SESSION['files'] = $_POST['files'];
	?>
	<p class="lead">Please use the Java applet interface below to download your files..</p>
	<p>If the applet doesn't work or fails to load you can <a href="#manual_download">manually download</a> the individual files using the links below.
	If you keep having problems please come and tell someone in Bioinformatics so that we can fix it and give you your files on a USB stick...</p>
	
	<applet 
	  code="biz.jupload.jdownload.Manager"
	  archive="includes/jdownload/jdownload.jar"
	  width="100%"
	  height="500px"
	  name="JDownload"
	  mayscript="mayscript"
	  alt="JDownload by www.jupload.biz">

	 <!-- Java Plug-In Options -->
	 <param name="progressbar" value="true">
	 <param name="boxmessage" value="Loading JDownload Applet ...">

	 <!-- URL pointing to the data structure containing the list of
		  files and folders to download -->
	 <param name="dataURL" value="includes/download_xml.php">

	 <!-- Show or Hide the controls 
		  If hidden (set all to 'false'), remote control the applet
		  using JavaScript buttons -->
	 <param name="showExplorer" value="true">
	 <param name="showControls" value="true">
	 <param name="showBrowser" value="true">
	 <param name="showStatus" value="true">

	 Your browser does not support applets.
	 Or you have disabled applets in your options.
	 To use this applet, please update your Java.
	 You can get it from <a href="http://www.java.com/">java.com</a>

	</applet>
	
	<hr id="manual_download">
	<h3>Manual Download</h3>
	<table class="table table-condensed"><tbody>
<?php
	foreach($_POST['files'] as $file){
		echo '<tr><td><a href="download_file.php?fn='.$file.'">'.$file.'</a></td></tr>';
	}
	?>
	</tbody></table>
	
	</div>
	</body></html>
	<?php
	exit;
}



// Get paper details
$paper = mysql_fetch_array(mysql_query("SELECT * FROM `papers` WHERE `id` = '".$_GET['paper_id']."'"));
$folder = '/data/pipeline/public/TIDIED/'.$paper['first_author'].'_'.$paper['year'].'/';
$shortfolder = $paper['first_author'].'_'.$paper['year'].'/';
$files = array();
// raw directory structure and files
foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folder)) as $filename) {
	$files[] = $filename;
}
natsort($files);

// Get datasets
$datasets_query = "SELECT * FROM `datasets` WHERE `paper_id` = '".$_GET['paper_id']."'";
$datasets_sql = mysql_query($datasets_query);
$datasets = array();
while($dataset = mysql_fetch_array($datasets_sql)){
	$datasets[$dataset['id']] = $dataset;
	// Get raw files
	$datasets[$dataset['id']]['files_raw'] = array();
	$files_raw = mysql_query("SELECT * FROM `files_raw` WHERE `dataset_id` = '".$dataset['id']."'");
	if(mysql_num_rows($files_raw) > 0){
		while($file_raw = mysql_fetch_array($files_raw)){
			$datasets[$dataset['id']]['files_raw'][] = $file_raw;
		}
	}
	// Get aligned files
	$datasets[$dataset['id']]['files_aligned'] = array();
	$files_aligned = mysql_query("SELECT * FROM `files_aligned` WHERE `dataset_id` = '".$dataset['id']."'");
	if(mysql_num_rows($files_aligned) > 0){
		while($file_aligned = mysql_fetch_array($files_aligned)){
			$datasets[$dataset['id']]['files_aligned'][] = $file_aligned;
		}
	}
	// Get derived files
	$datasets[$dataset['id']]['files_derived'] = array();
	$files_derived = mysql_query("SELECT * FROM `files_derived` WHERE `dataset_id` = '".$dataset['id']."'");
	if(mysql_num_rows($files_derived) > 0){
		while($file_derived = mysql_fetch_array($files_derived)){
			$datasets[$dataset['id']]['files_derived'][] = $file_derived;
		}
	}
}

?>
                <img src="img/puppies/puppy_fetch.jpg" class="pull-right visible-desktop" style="margin-top:-40px;" title="I got it!">
		<p class="lead">You can see the files processed by the Babraham bioinformatics team below.</p>
		<p>Click file names to select them; when you are happy with your selection click download at the bottom of the page.</p>
		<div class="well"><strong><?= $paper['paper_title'] ?></strong><br>
		<small>
			<?php if(!empty($paper['sra_accession'])) { ?>
				<a class="pull-right" style="margin-right:20px;" href="http://www.ncbi.nlm.nih.gov/sra/<?= $paper['sra_accession'] ?>" target="_blank">SRA</a>
			<?php }
			if(!empty($paper['geo_accession'])) { ?>
			<a class="pull-right" style="margin-right:20px;" href="http://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=<?= $paper['geo_accession'] ?>" target="_blank">GEO</a>
			<?php }
			if(!empty($paper['DOI'])) { ?>
			<a class="pull-right" style="margin-right:20px;" href="http://dx.doi.org/<?= $paper['DOI'] ?>" target="_blank">DOI</a>
			<?php }
			if(!empty($paper['PMID'])) { ?>
			<a class="pull-right" style="margin-right:20px;" href="http://www.ncbi.nlm.nih.gov/pubmed/<?= $paper['PMID'] ?>/" target="_blank">PubMed</a>
			<?php } ?>
			<?= $paper['authors'] ?> <em>(<?= $paper['year'] ?>)</em>
		</small></div>
		<p>Files for this paper are stored in <code><?= $folder ?></code></p>
		


		<ul class="nav nav-tabs">
			<li class="active"><a href="#download_datasets" class="tab-link">Download by Dataset</a></li>
			<li><a href="#download_files" class="tab-link">Download Individual Files</a></li>
		</ul>

		<div id="download_datasets" class="tab-content">
			<h2>Download by Dataset</h2>
			<div class="well pull-right" style="width:30%; margin-top: 20px; clear:right;">
				<p>To quickly download datasets, select their rows on the left and the data type you would like.</p>
				<p>This will download the relevant files in a single .zip file</p>
				<form action="download.php?action=download" method="post">
					<div id="downloadDatasetsHiddenFields"></div>
					<p><input type="submit" class="btn btn-primary" value="Download"></p>
				</form>
			</div>
			<table class="table table-bordered table-hover table-striped" style="width:60%;" id="datasetDownloadTable">
			  <thead>
				<tr>
					<th width="3%" class="select_all_rows check"><i class="icon-ok"></i></th>
					<th width="52%">Name</th>
					<th width="15%" class="check select_column" data-selectTarget="raw"><i class="icon-remove"></i> Raw</i></th>
					<th width="15%" class="check select_column" data-selectTarget="aligned"><i class="icon-ok"></i> Aligned</th>
					<th width="15%" class="check select_column" data-selectTarget="derived"><i class="icon-remove"></i> Derived</th>
				</tr>
			  </thead>
			  <tbody>
			  <?php
				foreach($datasets as $dataset){
					echo '<tr class="success" id="dataset_'.$dataset['id'].'"><td class="select_row check"><i class="icon-ok"></i></td><td>'.$dataset['name'].'</td>';
					echo (count($dataset['files_raw']) > 0) ? '<td class="icon error raw"><i class="icon-remove"></i></td>' : '<td class="empty"></td>';
					echo (count($dataset['files_aligned']) > 0) ? '<td class="icon aligned"><i class="icon-ok"></i></td>' : '<td class="empty"></td>';
					echo (count($dataset['files_derived']) > 0) ? '<td class="icon derived"><i class="icon-ok"></i></td>' : '<td class="empty"></td>';
					echo '</tr>';
				}
			  ?>
			  </tbody>
			</table>
			
			<h3>Files to be downloaded</h3>
			<table class="table table-bordered table-striped table-condensed" style="width:60%;" id="datasetDownloadFileList">
				<thead>
					<tr>
						<th>Filename</th>
					</tr>
				</thead>
				<tbody>
				</tbody>				
			</table>
			
		</div>


		<div id="download_files" style="display:none;" class="tab-content">
			<h2>Download Individual Files</h2>
			<div class="well pull-right" style="width:30%; margin-top: 20px; clear:right;">
				<p>Here you can see all files found in <code><?= $folder ?></code></p>
				<p>Selected files will be downloaded as a single .zip file</p>
				<form action="download.php?action=download" method="post">
					<div id="downloadFilesHiddenFields"></div>
					<p><input type="submit" class="btn btn-primary" value="Download"></p>
				</form>
			</div>
			<table class="table table-bordered table-hover table-striped table-condensed" style="width:60%;" id="downloadFilesTable">
				<tr>
					<th width="3%" class="select_all_rows check"><i class="icon-ok"></i></th>
					<th>Filename</th>
				</tr>
				<?php
					foreach ($files as $file) {
						$file = substr($file, strlen($folder));
						echo '<tr class="success"><td class="select_row check"><i class="icon-ok"></i></td><td>'.$file.'</td></tr>';
					}
				?>
			</table>
		</div>
		
		

    </div> <!-- /container -->



	


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
		// Make the tabs work
		$('.tab-link').click(function(e){
			e.preventDefault();
			$('.tab-content').hide();
			$($(this).attr('href')).show();
			$('li.active').removeClass('active');
			$(this).parent().addClass('active');
		});

		// Select and deselect datasets
		$('.select_row').click(function(){
			if($(this).parent().hasClass('success')){
				$(this).parent().removeClass('success');
				$(this).parent().addClass('error');
				$(this).parent().children().removeClass('success');
				$(this).parent().children().children('i').removeClass('icon-ok');
				$(this).parent().children().children('i').addClass('icon-remove');
			} else {
				$(this).parent().removeClass('error');
				$(this).parent().addClass('success');
				$(this).parent().children(':not(.error)').children('i').removeClass('icon-remove');
				$(this).parent().children(':not(.error)').children('i').addClass('icon-ok');
			}
			updateDownloadList();
		});
		
		// Select or deselect all rows at once
		$('.select_all_rows').click(function(){
			var rows = $(this).parent().parent().parent().children('tbody').children('tr');
			if($(this).children('i').hasClass('icon-ok')) {
				rows.removeClass('success');
				rows.addClass('error');
				rows.children().removeClass('success');
				rows.children().children('i').removeClass('icon-ok');
				rows.children().children('i').addClass('icon-remove');
				$(this).parent().children('th').children('i').removeClass('icon-ok');
				$(this).parent().children('th').children('i').addClass('icon-remove');
			} else {
				rows.removeClass('error');
				rows.addClass('success');
				rows.children().removeClass('error');
				rows.children().children('i').removeClass('icon-remove');
				rows.children().children('i').addClass('icon-ok');
				$(this).parent().children('th').children('i').removeClass('icon-remove');
				$(this).parent().children('th').children('i').addClass('icon-ok');
			}
			updateDownloadList();
		});
		
		// Select or deselect a column
		$('.select_column').click(function(){
			var target = $(this).attr('data-selectTarget');
			var cells = $(this).parent().parent().parent().children('tbody').children('tr:not(.error)').children('td.'+target);
			if($(this).children('i').hasClass('icon-ok')) {
				cells.removeClass('success');
				cells.addClass('error');
				cells.children('i').removeClass('icon-ok');
				cells.children('i').addClass('icon-remove');
				$(this).children('i').removeClass('icon-ok');
				$(this).children('i').addClass('icon-remove');
			} else {
				cells.removeClass('error');
				cells.addClass('success');
				cells.children('i').removeClass('icon-remove');
				cells.children('i').addClass('icon-ok');
				$(this).children('i').removeClass('icon-remove');
				$(this).children('i').addClass('icon-ok');
			}
			updateDownloadList();
		});
		
		// Function to update which files are to be downloaded
		function updateDownloadList() {
			// Static array of dataset filenames
			var datasets = [];
			<?php
			foreach($datasets as $dataset){
				echo "\n\t\tdatasets[".$dataset['id']."] = [];";
				echo "\n\t\tdatasets[".$dataset['id']."][0] = [];";
				echo "\n\t\tdatasets[".$dataset['id']."][1] = [];";
				echo "\n\t\tdatasets[".$dataset['id']."][2] = [];";
				foreach($dataset['files_raw'] as $file_raw){
					echo "\n\t\tdatasets[".$dataset['id']."][0].push('".$file_raw['filename']."');";
				}
				foreach($dataset['files_aligned'] as $file_aligned){
					echo "\n\t\tdatasets[".$dataset['id']."][1].push('".$file_aligned['filename']."');";
				}
				foreach($dataset['files_derived'] as $file_derived){
					echo "\n\t\tdatasets[".$dataset['id']."][2].push('".$file_derived['filename']."');";
				}
			}
			?>
			var files = new Array();
			$('#datasetDownloadTable tbody tr').each(function(){
				var i = $(this).attr('id').substr(8);
				if($(this).hasClass('success')){
					if(!$(this).children('td.raw').hasClass('error')){
						$.each(datasets[i][0], function(key, val){
							files.push(datasets[i][0][key]);
						});
					}
					if(!$(this).children('td.aligned').hasClass('error')){
						$.each(datasets[i][1], function(key, val){
							files.push(datasets[i][1][key]);
						});
					}
					if(!$(this).children('td.derived').hasClass('error')){
						$.each(datasets[i][2], function(key, val){
							files.push(datasets[i][2][key]);
						});
					}
				}
			});
			$('#datasetDownloadFileList tbody').empty();
			$('#downloadDatasetsHiddenFields').empty();
			$('#downloadFilesHiddenFields').empty();
			files.sort();
			var folder = '<?= $shortfolder ?>';
			$.each(files, function(j, val){
				if(val.length > 0){
					$('#datasetDownloadFileList tbody').append('<tr><td>'+val+'</td></tr>');
					$('#downloadDatasetsHiddenFields').append('<input type="hidden" name="files[]" value="'+folder+val+'">');
				}
			});
			$('#downloadFilesTable tbody tr.success:not(.error) td:not(.select_row)').each(function(){
				$('#downloadFilesHiddenFields').append('<input type="hidden" name="files[]" value="'+folder+$(this).text()+'">');
			});
		}
		$(document).ready(function() {
			updateDownloadList(); // run on page load
		});

	</script>

  </body>
</html>
