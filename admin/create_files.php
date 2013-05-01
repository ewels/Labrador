<?php

// Connect to database
include('../includes/db_login.php');


include('includes/admin_header.php');

$query = "SELECT * FROM `papers` WHERE `id` = '".$_GET['paper_id']."'";
$results = mysql_query($query);
$paper = mysql_fetch_array($results);
$directory = "/data/pipeline/public/TIDIED/".$paper['first_author']."_".$paper['year'].'/';
$dirs_exist = $root_dir_exists = $raw_dir_exists = $aligned_dir_exists = $derived_dir_exists = true;
if(!file_exists($directory) || !is_dir($directory)){
	$dirs_exist = $root_dir_exists = $raw_dir_exists = $aligned_dir_exists = $derived_dir_exists = false;
} else {
	if(!file_exists($directory.'/raw') || !is_dir($directory.'/raw')){
		$dirs_exist = $raw_dir_exists = false;
	}
	if(!file_exists($directory.'/aligned') || !is_dir($directory.'/aligned')){
		$dirs_exist = $aligned_dir_exists = false;
	}
	if(!file_exists($directory.'/derived') || !is_dir($directory.'/derived')){
		$dirs_exist = $derived_dir_exists = false;
	}
}

$admins = array(
	'ewelsp' => 'phil.ewels@babraham.ac.uk',
	'bigginsl' => 'laura.biggins@babraham.ac.uk',
	'kruegerf' => 'felix.krueger@babraham.ac.uk',
	'andrewss' => 'simon.andrews@babraham.ac.uk',
	'wingets' => 'steven.wingett@babraham.ac.uk',
	'segondsa' => 'anne.segonds-pichon@babraham.ac.uk'
);

if($_GET['mkdir'] == 'true'){
	$errors = array();
	if(!$root_dir_exists){
		if (!mkdir($directory)) {
			$errors[] = "Could not create $directory";
		} else {
			$root_dir_exists = true;
		}
	}
	if(!$raw_dir_exists){
		if (!mkdir($directory.'/raw')) {
			$errors[] = "Could not create $directory/raw";
		} else {
			$raw_dir_exists = true;
		}
	}
	if(!$aligned_dir_exists){
		if (!mkdir($directory.'/aligned')) {
			$errors[] = "Could not create $directory/aligned";
		} else {
			$aligned_dir_exists = true;
		}
	}
	if(!$derived_dir_exists){
		if (!mkdir($directory.'/derived')) {
			$errors[] = "Could not create $directory/derived";
		} else {
			$derived_dir_exists = true;
		}
	}
	if($root_dir_exists && $raw_dir_exists && $aligned_dir_exists && $derived_dir_exists){
		$dirs_exist = true;
	}
	
	# Write list of SRA filenames and fastq_dump file
	if(file_exists($directory.'/raw') && is_dir($directory.'/raw')){
		$lines = array();
		$datasets = mysql_query("SELECT * FROM `datasets` WHERE `paper_id` = '".$_GET['paper_id']."' ORDER BY `geo_accession`");
		while($dataset = mysql_fetch_array($datasets)){
			if($dataset['sra_accession'] !== NULL && $dataset['srx_accession'] !== NULL){
				$sras = split(" ",$dataset['sra_accession']);
				foreach($sras as $sra){
					$lines[] = "wget -nv ftp://ftp-trace.ncbi.nlm.nih.gov/sra/sra-instant/reads/ByExp/sra/SRX/".substr($dataset['srx_accession'], 0, 6)."/".$dataset['srx_accession']."/".$sra."/".$sra.".sra";
				}
			}
		}
		$lines[] = "perl /data/pipeline/public/Phil/fastq-dump-split.pl *.sra";
		$lines[] = "fastqc *.fastq";
		$lines[] = "fastq_screen --subset 100000 *.fastq";
		
		$fh = fopen($directory.'/raw/download_dump_fastqc_fastqscreen.bash', 'w') or die("can't open file $directory/raw/download_dump_fastqc_fastqscreen.bash");
		fwrite($fh, implode("\n", $lines));
		fclose($fh);
	}
}

if(count($errors) > 0){
	echo '<div class="alert alert-error">'.implode("<br>", $errors).'</div>';
}

?>

<img class="pull-right visible-desktop" src="../img/puppies/puppy_5.jpg" style="max-height:250px; margin-top:-50px;">
<p class="lead">This page enables you to register files in the file system and associate them with datasets.</p>
<p>The directory being searched for files is: <code><?= $directory ?></code> <em>(directory structure <?php echo $dirs_exist ? 'looks good' : 'is missing some directories'; ?>).</em></p>
<p><a href="create_files.php?paper_id=<?php echo $_GET['paper_id']; ?>&mkdir=true">Click here</a> to create any missing directories and a SRA ftp filename list file (in /raw - will overwrite existing filename list).</p>
<p>You can add file attributes such as raw file read length, alignment genome build etc once files are in an appropriate column.
	You can also <a href="#batchAnnotationModal" data-toggle="modal">batch edit these values</a> <em>(use with caution, will overwrite anything already in place).</em></p>

<hr style="clear:both;">

<h2>Controls</h2>
<div class="btn-group">
	<button class="btn" id="auto_sort_button">Auto sort by folder</button>
	<button class="btn" id="auto_sort_filename_button">Auto sort by extension</button>
</div> &nbsp; &nbsp; 
<a href="#batchAnnotationModal" data-toggle="modal" class="btn btn-info">Batch update annotation</a> &nbsp; &nbsp;
<button class="btn btn-success" id="lockSavedButton">Previously saved settings locked</button>

<hr>

<div class="row">
	<div class="span3">
		<h2>Files</h2>

		<h4>Directory 'raw'</h4>
		<ul class="unknown_files nav nav-pills nav-stacked">
		<?php
		$raw_files = array();
		if($fh = opendir($directory.'raw/')) {
			while (false !== ($entry = readdir($fh))) {
				if($entry != "." && $entry != ".."){
					$raw_files[] = $entry;
				}
			}
		}
		closedir($fh);
		natcasesort($raw_files);
		foreach($raw_files as $file){
			echo '<li class="file raw_dir" data-path="raw/"><a><i class="icon-search hide pull-right"></i> '.$file.'</a></li>';
		}
		if(count($raw_files) == 0 ) { 
			echo '<li><em class="muted">[ empty ]</em></li>';
		}
		?>
		</ul>


		<h4>Directory 'aligned'</h4>
		<ul class="unknown_files nav nav-pills nav-stacked">
		<?php
		$aligned_files = array();
		if($fh = opendir($directory.'aligned/')) {
			while (false !== ($entry = readdir($fh))) {
				if($entry != "." && $entry != ".."){
				$aligned_files[] = $entry;
				}
			}
		}
		closedir($fh);
		natcasesort($aligned_files);
		foreach($aligned_files as $file){
			echo '<li class="file aligned_dir" data-path="aligned/"><a><i class="icon-search hide pull-right"></i> '.$file.'</a></li>';
		}
		if(count($aligned_files) == 0 ) { 
			echo '<li><em class="muted">[ empty ]</em></li>';
		}
		?>
		</ul>


		<h4>Directory 'derived'</h4>
		<ul class="unknown_files nav nav-pills nav-stacked">
		<?php
		$derived_files = array();
		if($fh = opendir($directory.'derived/')) {
			while (false !== ($entry = readdir($fh))) {
				if($entry != "." && $entry != ".."){
					$derived_files[] = $entry;
				}
			}
		}
		closedir($fh);
		natcasesort($derived_files);
		foreach($derived_files as $file){
			echo '<li class="file derived_dir" data-path="derived/"><a><i class="icon-search hide pull-right"></i> '.$file.'</a></li>';
		}
		if(count($derived_files) == 0 ) { 
			echo '<li><em class="muted">[ empty ]</em></li>';
		}
		?>
		</ul>



		<h4>Root Directory</h4>
		<ul class="unknown_files nav nav-pills nav-stacked">
		<?php
		$root_files = array();
		if($fh = opendir($directory)) {
			while (false !== ($entry = readdir($fh))) {
				if($entry != "." && $entry != ".." && $entry != "raw" && $entry != "aligned" && $entry != "derived"){
					$root_files[] = $entry;
				}
			}
		}
		closedir($fh);
		natcasesort($root_files);
		foreach($root_files as $file){
			echo '<li class="file root_dir" data-path=""><a><i class="icon-search hide pull-right"></i> '.$file.'</a></li>';
		}
		if(count($root_files) == 0 ) { 
			echo '<li><em class="muted">[ empty ]</em></li>';
		}
		?>
		</ul>
	</div>
	
	<div class="span9">
		<h2>Datasets</h2>
		<?php $query = "SELECT * FROM `datasets` WHERE `paper_id` = '".$_GET['paper_id']."' ORDER BY `geo_accession`";
		$results = mysql_query($query);
		while($result = mysql_fetch_array($results)): ?>

		<div class="dataset_div" id="dataset_div_<?= $result['id'] ?>">
			<h4><button type="button" class="close dataset_delete" id="delete_<?= $result['id'] ?>">&times;</button><span class="titleText"><?= $result['name']; ?></span> &nbsp; &nbsp; 
				<small>
					<a href="http://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=<?= $result['geo_accession'] ?>" target="_blank" class="geo_accession label label-success"><?= $result['geo_accession'] ?></a> &nbsp; &nbsp; 
					<?php $sras = explode(' ', $result['sra_accession']);
					foreach($sras as $sra){
						if(!empty($sra)){
							echo "\n".'<a href="http://www.ncbi.nlm.nih.gov/sra/'.$sra.'" target="_blank" class="sra_accession label label-info">'.$sra.'</a> &nbsp; &nbsp; ';
						}
					} ?>
				</small>
			</h4>

			<div class="dataset_raw_div">
				<h5>Raw</h5>
				<ul class="nav nav-pills nav-stacked">
				</ul>
			</div>
	
			<div class="dataset_aligned_div">
				<h5>Aligned</h5>
				<ul class="nav nav-pills nav-stacked">
				</ul>
			</div>
	
			<div class="dataset_derived_div">
				<h5>Derived</h5>
				<ul class="nav nav-pills nav-stacked">
				</ul>
			</div>
			<div class="clearfix"></div>
		</div>

		<?php  endwhile; // end of dataset mysql while loop  ?>
		
	</div>
</div>






		
<div class="form-actions" style="text-align:center;">
<button class="btn btn-primary btn-large" id="save_button">Save Dataset File Associations</button>
</div>
		

		
		</div> <!-- /container -->


<div class="modal hide fade" id="batchAnnotationModal">
<div class="modal-header">
<h3>Batch add annotation</h3>
</div>
<div class="modal-body">
<p>These fields are optional. Values will overwrite all unlocked dataset details.</p>
<form class="form-horizontal">
<fieldset>
<legend>Raw Files</legend>
<div class="control-group">
  <label class="control-label" for="rawReadLengthBatch">Read Length</label>
  <div class="controls">
    <div class="input-append">
       <input type="text" class="input-small" id="rawReadLengthBatch">
       <span class="add-on">bp</span>
    </div>
  </div>
</div>
<div class="control-group">
  <label class="control-label" for="rawNumReadsBatch">Number of reads</label>
  <div class="controls">
    <div class="input-append">
      <input type="text" class="input-small" id="rawNumReadsBatch">
      <span class="add-on">million reads</span>
    </div>
  </div>
</div>
</fieldset>
<fieldset>
<legend>Aligned Files</legend>
<div class="control-group">
  <label class="control-label" for="alignedGenomeBatch">Genome Build</label>
  <div class="controls">
    <input type="text" class="input-small" placeholder="NCBIM37" id="alignedGenomeBatch">
  </div>
</div>
<div class="control-group">
  <label class="control-label" for="alignedParametersBatch">Parameters</label>
  <div class="controls">
    <input type="text" class="input-xlarge" placeholder="bowtie -p 4 -m 1 --chunk-mbs 514" id="alignedParametersBatch">
  </div>
</div>
<div class="control-group">
  <label class="control-label" for="alignedNumReadsBatch">Number of reads</label>
  <div class="controls">
    <div class="input-append">
      <input type="text" class="input-small" id="alignedNumReadsBatch">
      <span class="add-on">million reads</span>
    </div>
  </div>
</div>
</fieldset>
<fieldset>
<legend>Derived Files</legend>
<div class="control-group">
  <label class="control-label" for="derivedTypeBatch">Type</label>
  <div class="controls">
    <input type="text" class="input-small" placeholder="FastQC" id="derivedTypeBatch">
  </div>
</div>
<div class="control-group">
  <label class="control-label" for="derivedNotesBatch">Notes</label>
  <div class="controls">
    <textarea id="derivedNotesBatch"></textarea>
  </div>
</div>
</fieldset>
<fieldset>
<legend>Admin-only paper details</legend>
<div class="control-group">
  <label class="control-label" for="batch_processed_by">Processed by</label>
  <div class="controls">
    <input type="text" id="batch_processed_by" value="<?= $admins[$_SERVER['PHP_AUTH_USER']] ?>" class="input-xlarge">
  </div>
</div>
</fieldset>
</form>
</div>
<div class="modal-footer">
<button class="btn" data-dismiss="modal">Cancel</button>
<button class="btn btn-primary" id="batchAnnotationSubmit">Update Annotation</button>
</div>
</div>





<!-- RAW MODAL -->
<div class="modal hide fade" id="rawAnnotationModal">
<div class="modal-header">
<h3>Raw File <small></small></h3>
</div>
<div class="modal-body">
<form class="form-horizontal">
<div class="control-group">
  <label class="control-label" for="rawReadLength">Read Length</label>
  <div class="controls">
    <div class="input-append">
       <input type="text" class="input-small" id="rawReadLength">
       <span class="add-on">bp</span>
    </div>
  </div>
</div>
<div class="control-group">
  <label class="control-label" for="rawNumReads">Number of reads</label>
  <div class="controls">
    <div class="input-append">
      <input type="text" class="input-small" id="rawNumReads">
      <span class="add-on">million reads</span>
    </div>
  </div>
</div>
</form>
</div>
<div class="modal-footer">
<button class="btn" data-dismiss="modal">Cancel</button>
<button class="btn btn-primary" id="rawAnnotationSubmit">Update Annotation</button>
</div>
</div>


<!-- ALIGNED MODAL -->
<div class="modal hide fade" id="alignedAnnotationModal">
<div class="modal-header">
<h3>Aligned File <small></small></h3>
</div>
<div class="modal-body">
<form class="form-horizontal">
<div class="control-group">
  <label class="control-label" for="alignedGenome">Genome Build</label>
  <div class="controls">
    <input type="text" class="input-small" placeholder="NCBIM37" id="alignedGenome">
  </div>
</div>
<div class="control-group">
  <label class="control-label" for="alignedParameters">Parameters</label>
  <div class="controls">
    <input type="text" class="input-xlarge" placeholder="bowtie -p 4 -m 1 --chunk-mbs 514" id="alignedParameters">
  </div>
</div>
<div class="control-group">
  <label class="control-label" for="alignedNumReads">Number of reads</label>
  <div class="controls">
    <div class="input-append">
      <input type="text" class="input-small" id="alignedNumReads">
      <span class="add-on">million reads</span>
    </div>
  </div>
</div>
</form>
</div>
<div class="modal-footer">
<button class="btn" data-dismiss="modal">Cancel</button>
<button class="btn btn-primary" id="alignedAnnotationSubmit">Update Annotation</button>
</div>
</div>



<!-- DERIVED MODAL -->
<div class="modal hide fade" id="derivedAnnotationModal">
<div class="modal-header">
<h3>Derived File <small></small></h3>
</div>
<div class="modal-body">
<form class="form-horizontal">
<div class="control-group">
  <label class="control-label" for="derivedType">Type</label>
  <div class="controls">
    <input type="text" class="input-small" placeholder="FastQC" id="derivedType">
  </div>
</div>
<div class="control-group">
  <label class="control-label" for="derivedNotes">Notes</label>
  <div class="controls">
    <textarea id="derivedNotes"></textarea>
  </div>
</div>
</form>
</div>
<div class="modal-footer">
<button class="btn" data-dismiss="modal">Cancel</button>
<button class="btn btn-primary" id="derivedAnnotationSubmit">Update Annotation</button>
</div>
</div>

		
<!-- POST return vars Modal -->
<div class="modal hide fade" id="submitModal">

</div>

		
<!-- Delete dataset Modal -->
<div class="modal hide fade" id="deleteDatasetModal">
<div class="modal-header">
<h3>Delete Dataset <small class="title"></small></h3>
</div>
<div class="modal-body">
<p>Are you sure you want to delete the <strong class="title"></strong> dataset? This cannot be undone. Any file information associated with the dataset will also be deleted.</p>
</div>
<div class="modal-footer">
<button class="btn" data-dismiss="modal">Cancel</button>
<button class="btn btn-primary" id="deleteModalSubmit">Delete Dataset</button>
</div>
</div>


		<!-- Le javascript
		================================================== -->
		<!-- Placed at the end of the document so the pages load faster -->
		<script src="../js/jquery-1.8.3.min.js"></script>
		<script src="../js/bootstrap-transition.js"></script>
		<script src="../js/bootstrap-alert.js"></script>
		<script src="../js/bootstrap-modal.js"></script>
		<script src="../js/bootstrap-dropdown.js"></script>
		<script src="../js/bootstrap-scrollspy.js"></script>
		<script src="../js/bootstrap-tab.js"></script>
		<script src="../js/bootstrap-tooltip.js"></script>
		<script src="../js/bootstrap-popover.js"></script>
		<script src="../js/bootstrap-button.js"></script>
		<script src="../js/bootstrap-collapse.js"></script>
		<script src="../js/bootstrap-carousel.js"></script>
		<script src="../js/bootstrap-typeahead.js"></script>
		<script src="../js/bootstrap-tooltip.js"></script>
		<script src="../js/jquery-ui-1.9.2.custom.min.js"></script>

		<script type="text/javascript">
		$(function() {
			$('.dataset_raw_div ul, .dataset_aligned_div ul, .dataset_derived_div ul, .unknown_files').sortable({
				connectWith:  ".nav-pills",
				cancel: ".locked"
			}).disableSelection();
		});
		$('.dataset_raw_div ul, .dataset_aligned_div ul, .dataset_derived_div ul, .unknown_files').on('sortupdate', function(event, ui) {
			$('.dataset_raw_div i, .dataset_aligned_div i, .dataset_derived_div i').removeClass('hide');
			$('.unknown_files i').addClass('hide');
		});

		$('.icon-search').click(function(){
			var pDiv = $(this).parent().parent().parent().parent();
			var pEl = $(this).parent().parent();
			if(!pEl.hasClass('locked')){
				$('#modalTarget').removeAttr('id');
				pEl.attr('id', 'modalTarget');
				if(pDiv.hasClass('dataset_raw_div')){
					$('#rawReadLength').val(pEl.attr('data-rawReadLength'));
					$('#rawNumReads').val(pEl.attr('data-rawNumReads'));
					$('#rawAnnotationModal').modal();
				}
				if(pDiv.hasClass('dataset_aligned_div')){
					$('#alignedGenome').val(pEl.attr('data-alignedGenome'));
					$('#alignedParameters').val(pEl.attr('data-alignedParameters'));
					$('#alignedNumReads').val(pEl.attr('data-alignedNumReads'));
					$('#alignedAnnotationModal').modal();
				}
				if(pDiv.hasClass('dataset_derived_div')){
					$('#derivedType').val(pEl.attr('data-derivedType'));
					$('#derivedNotes').val(pEl.attr('data-derivedNotes'));
					$('#derivedAnnotationModal').modal();
				}
			} // end of locked check
		});

		$('.icon-search').hover(function(){
			var pEl = $(this).parent().parent();
			var pDiv = pEl.parent().parent();
			var title = '';
			if(pDiv.hasClass('dataset_raw_div')){
				if(typeof pEl.attr('data-rawReadLength') !== "undefined"){
					title += 'Read length: '+pEl.attr('data-rawReadLength')+'<br>';
				}
				if(typeof pEl.attr('data-rawNumReads') !== "undefined"){
					title += 'Num Reads: '+pEl.attr('data-rawNumReads');
				}
				}
				if(pDiv.hasClass('dataset_aligned_div')){
				if(typeof pEl.attr('data-alignedGenome') !== "undefined"){
					title += 'Genome: '+pEl.attr('data-alignedGenome')+'<br>';
				}
				if(typeof pEl.attr('data-alignedParameters') !== "undefined"){
					title += 'Parameters: '+pEl.attr('data-alignedParameters')+'<br>';
				}
				if(typeof pEl.attr('data-alignedNumReads') !== "undefined"){
					title += 'Num Reads:: '+pEl.attr('data-alignedNumReads');
				}
			}
			if(pDiv.hasClass('dataset_derived_div')){
				if(typeof pEl.attr('data-derivedType') !== "undefined"){
					title += 'Type: '+pEl.attr('data-derivedType')+'<br>';
				}
				if(typeof pEl.attr('data-derivedNotes') !== "undefined"){
					title += 'Notes: '+pEl.attr('data-derivedNotes')+'<br>';
				}
			}
			$(this).attr('title', title);
		});
		$('.icon-search').tooltip();


		$('#rawAnnotationSubmit').click(function(){
			$('#modalTarget').attr('data-rawReadLength', $('#rawReadLength').val());
			$('#modalTarget').attr('data-rawNumReads', $('#rawNumReads').val());
			$('#rawAnnotationModal').modal('hide');
			$('#modalTarget').removeAttr('id');
		});
		$('#alignedAnnotationSubmit').click(function(){
			$('#modalTarget').attr('data-alignedGenome', $('#alignedGenome').val());
			$('#modalTarget').attr('data-alignedParameters', $('#alignedParameters').val());
			$('#modalTarget').attr('data-alignedNumReads', $('#alignedNumReads').val());
			$('#alignedAnnotationModal').modal('hide');
			$('#modalTarget').removeAttr('id');
		});
		$('#derivedAnnotationSubmit').click(function(){
			$('#modalTarget').attr('data-derivedType', $('#derivedType').val());
			$('#modalTarget').attr('data-derivedNotes', $('#derivedNotes').val());
			$('#derivedAnnotationModal').modal('hide');
			$('#modalTarget').removeAttr('id');
		});
		$('#batchAnnotationSubmit').click(function(){
			$('.file:not(.locked)').attr('data-rawReadLength', $('#rawReadLengthBatch').val());
			$('.file:not(.locked)').attr('data-rawNumReads', $('#rawNumReadsBatch').val());
			$('.file:not(.locked)').attr('data-alignedGenome', $('#alignedGenomeBatch').val());
			$('.file:not(.locked)').attr('data-alignedParameters', $('#alignedParametersBatch').val());
			$('.file:not(.locked)').attr('data-alignedNumReads', $('#alignedNumReadsBatch').val());
			$('.file:not(.locked)').attr('data-derivedType', $('#derivedTypeBatch').val());
			$('.file:not(.locked)').attr('data-derivedNotes', $('#derivedNotesBatch').val());
			$('#batchAnnotationModal').modal('hide');
		});



		$('#auto_sort_button, #auto_sort_filename_button').click(function(){
			var once = 0;
			var raw_ext = new Array('fastq','fasta','fastq','fasta','fq','fa','fq','fa');
			var aligned_ext = new Array('bowtie','tophat','bam','sam');
			var derived_ext = new Array('fastqc');
			var sort_ext = false;
			if($(this).attr('id') == "auto_sort_filename_button"){
				sort_ext = true;
			}
			$('.file').each(function(){						
				var filename = $(this).text();
				var number = filename.match(/[0-9]+/);
				var extension = '';
				var fileFound = false;
				for (var j=0; j<raw_ext.length; j++){
					if (filename.match(raw_ext[j])){
						extension = 'raw';
					}
				}
				for (var j=0; j<aligned_ext.length; j++){
					if (filename.match(aligned_ext[j])){
						extension = 'aligned';
					}
				}
				// needs to be last so that fastqc can overwrite fastq
				for (var j=0; j<derived_ext.length; j++){
					if (filename.match(derived_ext[j])){
						console.log(derived_ext[j]);
						extension = 'derived';
					}
				}
				var file = $(this);
				if(!!number && number.toString().length > 5){
					$('.geo_accession').each(function(){
						var isFound = $(this).text().indexOf(number);
						if(isFound != -1){
							var datasetID = $(this).parent().parent().parent().attr('id');
							if((sort_ext == false && $(file).hasClass('raw_dir')) || (sort_ext == true && extension == 'raw')){
								$(file).slideUp('fast', function(){
									$(file).appendTo('#'+datasetID+' .dataset_raw_div ul');
									$(file).children().children().removeClass('hide');
									$(file).slideDown();
									fileFound = true;
								});
							}
							if((sort_ext == false && $(file).hasClass('aligned_dir')) || (sort_ext == true && extension == 'aligned')){
								$(file).slideUp('fast', function(){
									$(file).appendTo('#'+datasetID+' .dataset_aligned_div ul');
									$(file).children().children().removeClass('hide');
									$(file).slideDown();
									fileFound = true;
								});
							}
							if((sort_ext == false && $(file).hasClass('derived_dir')) || (sort_ext == true && extension == 'derived')){
								$(file).slideUp('fast', function(){
									$(file).appendTo('#'+datasetID+' .dataset_derived_div ul');
									$(file).children().children().removeClass('hide');
									$(file).slideDown();
									fileFound = true;
								});
							}
						}
					});
					if(!fileFound){
						$('.sra_accession').each(function(){
							var isFound = $(this).text().indexOf(number);
							if(isFound != -1){
								var datasetID = $(this).parent().parent().parent().attr('id');
								if((sort_ext == false && $(file).hasClass('raw_dir')) || (sort_ext == true && extension == 'raw')){
									$(file).slideUp('fast', function(){
										$(file).appendTo('#'+datasetID+' .dataset_raw_div ul');
										$(file).children().children().removeClass('hide');
										$(file).slideDown();
										fileFound = true;
									});
								}
								if((sort_ext == false && $(file).hasClass('aligned_dir')) || (sort_ext == true && extension == 'aligned')){
									$(file).slideUp('fast', function(){
										$(file).appendTo('#'+datasetID+' .dataset_aligned_div ul');
										$(file).children().children().removeClass('hide');
										$(file).slideDown();
										fileFound = true;
									});
								}
								if((sort_ext == false && $(file).hasClass('derived_dir')) || (sort_ext == true && extension == 'derived')){
									$(file).slideUp('fast', function(){
										$(file).appendTo('#'+datasetID+' .dataset_derived_div ul');
										$(file).children().children().removeClass('hide');
										$(file).slideDown();
										fileFound = true;
									});
								}
							}
						});
					}
				} // end of if check for numbers
				if(!fileFound){
					var matchedStringLength = 0;
					var datasetID = '';
					$('.titleText').each(function(){
						var isFound = filename.indexOf($(this).text());
						if(isFound != -1){
							var thisDatasetID = $(this).parent().parent().attr('id');
							var searchStringLength = $(this).text().length;
							if(searchStringLength > matchedStringLength){
								matchedStringLength = searchStringLength;
								datasetID = thisDatasetID;
							}
						}
					});
					if(matchedStringLength > 0){
						if((sort_ext == false && $(file).hasClass('raw_dir')) || (sort_ext == true && extension == 'raw')){
							$(file).slideUp('fast', function(){
								$(file).appendTo('#'+datasetID+' .dataset_raw_div ul');
								$(file).children().children().removeClass('hide');
								$(file).slideDown();
								fileFound = true;
							});
						}
						if((sort_ext == false && $(file).hasClass('aligned_dir')) || (sort_ext == true && extension == 'aligned')){
							$(file).slideUp('fast', function(){
								$(file).appendTo('#'+datasetID+' .dataset_aligned_div ul');
								$(file).children().children().removeClass('hide');
								$(file).slideDown();
								fileFound = true;
							});
						}
						if((sort_ext == false && $(file).hasClass('derived_dir')) || (sort_ext == true && extension == 'derived')){
							$(file).slideUp('fast', function(){
								$(file).appendTo('#'+datasetID+' .dataset_derived_div ul');
								$(file).children().children().removeClass('hide');
								$(file).slideDown();
								fileFound = true;
							});
						}
					}
				}
			}); // end of .file loop
		}); // end of sort button click

		$('#save_button').click(function(){
			var rawDatasetFiles = {};
			var alignedDatasetFiles = {};
			var derivedDatasetFiles = {};
			$('.dataset_div').each(function(){
				var datasetID = $(this).attr('id');
				var rawFiles = [];
				var alignedFiles = [];
				var derivedFiles = [];
				$(this).children('.dataset_raw_div').children('ul').children('li').each(function(){
					var pathVar = $.trim($(this).attr('data-path')) + $.trim($(this).text());
					var readLengthVar = $(this).attr('data-rawReadLength');
					var numReadsVar = $(this).attr('data-rawNumReads');
					rawFiles.push({path:pathVar, readLength:readLengthVar, numReads: numReadsVar });
				});
				$(this).children('.dataset_aligned_div').children('ul').children('li').each(function(){ 
					var pathVar = $.trim($(this).attr('data-path')) + $.trim($(this).text());
					var genomeVar = $(this).attr('data-alignedGenome');
					var parametersVar = $(this).attr('data-alignedParameters');
					var numReadsVar = $(this).attr('data-alignedNumReads');
					alignedFiles.push({path:pathVar, genome:genomeVar, parameters:parametersVar, numReads: numReadsVar });
				});
				$(this).children('.dataset_derived_div').children('ul').children('li').each(function(){
					var pathVar = $.trim($(this).attr('data-path')) + $.trim($(this).text());
					var typeVar = $(this).attr('data-derivedType');
					var notesVar = $(this).attr('data-derivedNotes');
					derivedFiles.push({path:pathVar, type:typeVar, notes: notesVar });
				});
				rawDatasetFiles[datasetID] = rawFiles;
				alignedDatasetFiles[datasetID] = alignedFiles;
				derivedDatasetFiles[datasetID] = derivedFiles;
			});
			
			$.post('includes/create_files_post.php', { raw: rawDatasetFiles, aligned: alignedDatasetFiles, derived: derivedDatasetFiles },
				function(data) {
					$('#submitModal').html(data);
					$('#submitModal').modal();
				}
			);
		}); // end of save button click
		
		$('.dataset_delete').click(function(){
			var title = $(this).parent().children('.titleText').text();
			var theID = $(this).attr('id').substr(7);
			$('#deleteDatasetModal .title').html(title);
			$('#deleteModalSubmit').attr('data-target', 'includes/delete_dataset.php?dataset_id=' + theID);
			$('#deleteModalSubmit').attr('data-divID', 'dataset_div_'+theID);
			$('#deleteDatasetModal').modal();
		});

		$('#deleteModalSubmit').click(function(){
			var datasetDiv = '#' + $(this).attr('data-divID');
			$.get($(this).attr('data-target'), function(data){
				if(data == 'deleted'){
					$('#deleteDatasetModal').modal('hide');
					$(datasetDiv).slideUp();
				} else {
					alert(data);
				}
			});
		});

		$('#lockSavedButton').click(function(){
			if($(this).hasClass('btn-success')){
				$(this).removeClass('btn-success').addClass('btn-danger').text('Saved Settings Unlocked');
				$('.file').removeClass('locked');
			} else {
				$(this).removeClass('btn-danger').addClass('btn-success').text('Saved Settings Locked');
				$('.file').addClass('locked');
			}
		});

	</script>
  </body>
</html>
