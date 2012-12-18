<?php
/*******************************
paper_modal.php
Loaded into a modal box with ajax in index.php
Displays details of paper and datasets
********************************/

// Connect to database
include('db_login.php');

if(isset($_GET['id']) && is_numeric($_GET['id'])) : 
	$papers = mysql_query("SELECT * FROM `papers` WHERE `id` = '".$_GET['id']."'");
	$paper = mysql_fetch_array($papers);
?>

<div class="modal-header">
	<a class="close" data-dismiss="modal">&times;</a>
	<h3>Paper Details &nbsp;
		<small><?= $paper['first_author'] ?>_<?= $paper['year'] ?></small></h3>
</div>
<div class="modal-body">
		
		<dl>
			<?php if(!empty($paper['paper_title'])) { ?>
			<dt>Title</dt>
			<dd><small><?= stripslashes($paper['paper_title']) ?></small></dd>
			<?php }
			if(!empty($paper['authors'])) { ?>
			<dt>Authors</dt>
			<dd><small><?= $paper['authors'] ?></small></dd>
			<?php } ?>
			<dt>Accession Numbers</dt>
			<dd><small>
				<?php if(!empty($paper['PMID'])) { ?>
				PMID: <a href="http://www.ncbi.nlm.nih.gov/pubmed/<?= $paper['PMID'] ?>" target="_blank"><?= $paper['PMID'] ?></a> &nbsp; &nbsp; 
				<?php } if(!empty($paper['DOI'])) { ?>
				DOI: <a href="http://dx.doi.org/<?= $paper['DOI'] ?>" target="_blank"><?= $paper['DOI'] ?></a> &nbsp; &nbsp; 
				<?php } if(!empty($paper['geo_accession'])) { ?>
				GEO: <a href="http://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=<?= $paper['geo_accession'] ?>" target="_blank"><?= $paper['geo_accession'] ?></a> &nbsp; &nbsp;  
				<?php } if(!empty($paper['sra_accession'])) { ?>
				SRA: <a href="http://www.ncbi.nlm.nih.gov/sra/<?= $paper['sra_accession'] ?>"><?= $paper['sra_accession'] ?></a>
				<?php } ?>
				</small></dd>
			<?php if(!empty($paper['notes'])) { ?>
			<dt>Notes</dt>
			<dd><small><?= $paper['notes'] ?></small></dd>
			<?php } 
			if(!empty($paper['requested_by'])) { ?>
			<dt>Requested by</dt>
			<dd><small><a href="mailto:<?= $paper['requested_by'] ?>"><?= $paper['requested_by'] ?></a></small></dd>
			<?php } 
			if(!empty($paper['processed_by'])) { ?>
			<dt>Processed by</dt>
			<dd><small><a href="mailto:<?= $paper['processed_by'] ?>"><?= $paper['processed_by'] ?></a></small></dd>
			<?php } ?>
		</dl>
		
		
		<hr>
		
		<h4>Datasets</h4>
		<?php 
		$datasets = mysql_query("SELECT * FROM `datasets` WHERE `paper_id` = '".$_GET['id']."'");
		if (mysql_num_rows($datasets) == 0) {
			echo '<div class="alert clearfix">No datasets found. <a href="create_dataset.php?paper_id='.$_GET['id'].'" class="btn btn-small pull-right paperModal_add_datasets_button">Add datasets</a></div>';
		} else { ?>
			
		<table class="table table-condensed table-bordered table-hover table-striped small" id="modal_dataset_table">
			<tr>
				<th>Name</th>
				<th>Species</th>
				<th>Cell Type</th>
				<th>Data Type</th>
				<th>Accessions</th>
			</tr>
		
		<?php	while ($dataset = mysql_fetch_array($datasets)) { ?>
			
			<tr id="geo_<?= $dataset['geo_accession'] ?>">
				<td><?= stripslashes($dataset['name']) ?></td>
				<td><?= $dataset['species'] ?></td>
				<td><?= $dataset['cell_type'] ?></td>
				<td><?= $dataset['data_type'] ?></td>
				<td>
					<?php if(!empty($dataset['geo_accession'])) { ?>
					<a href="http://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=<?= $dataset['geo_accession'] ?>" target="_blank" title="<?= $dataset['geo_accession'] ?>" rel="tooltip">GEO</a> &nbsp; &nbsp;  
					<?php } if(!empty($dataset['sra_accession'])) { ?>
					<a href="http://www.ncbi.nlm.nih.gov/sra/<?= $dataset['sra_accession'] ?>" target="_blank" title="<?= $dataset['sra_accession'] ?>" rel="tooltip">SRA</a>
					<?php } ?>
					</td>
			</tr>
			
			
		<?php } // while - end of datasets while loop  ?>
		
		</table>
		
		<?php } // if - end of check for paper datasetss
		
		if (!empty($paper['geo_accession'])) : ?>
		<hr>
		
		<h4>GEO Datasets</h4>
		<div class="geo_dataset_search_controls">
			<p><small>Click below to check the NCBI GEO database for datasets associated with <?= $paper['geo_accession'] ?></small></p>
			<p id="<?= $paper['geo_accession'] ?>" class="geo_datasets_search_button"><button class="btn">Check for missing datasets</button></p>
			<!-- code for this button is found in the main page - index.php -->
		</div>
		<div class="geo_dataset_search_results">
		</div>
		
		
	<?php endif; // check for empty geo accession for missing datasets
	else: // check for correct paper id ?>
		<p>Error: Paper ID looks wrong: <code><?=$_GET['id']?></code></p>
	<?php endif; ?>	
</div>
<div class="modal-footer">
	<a class="btn btn-primary" data-dismiss="modal">Close</a>
	<a class="btn pull-left paperModal_add_datasets_button" href="create_dataset.php?paper_id=<?= $_GET['id'] ?>">Add Datasets</a>
	<a class="btn pull-left" href="edit_paper.php?paper_id=<?= $_GET['id'] ?>">Edit Paper</a>
</div>