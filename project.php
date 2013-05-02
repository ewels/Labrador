<?php
session_start();
include('includes/db_login.php');

$paper_id = false;

if(isset($_GET['id']) && is_numeric($_GET['id'])){
	$new_paper = false;
	$paper_id = $_GET['id'];
} else {
	$new_paper = true;
}

if(isset($_GET['edit']) && is_numeric($_GET['edit'])){
	$edit = true;
	$new_paper = false;
	$paper_id = $_GET['edit'];
} else {
	$edit = false;
}

if($paper_id){
	$papers = mysql_query("SELECT * FROM `papers` WHERE `id` = '".$paper_id."'");
	$paper = mysql_fetch_array($papers);
}

include('includes/header.php');

?>

<div class="sidebar-nav">
	<h3 id="sidebar_project_title">
	<?php if(!$new_paper){
		echo $paper['first_author'].'_'.$paper['year'];
	} else {
		echo '<span class="muted">New Project</span>';
	}?></h3>
	<ul class="project-tabs">
		<li class="active">
			<a href="#">Project Details</a>
			<?php if(!$new_paper) { ?><span class="subline"><a href="project.php?edit=<?php echo $paper['id']; ?>">Edit</a></span><?php } ?>
		</li>
	<?php if($new_paper){ ?>
		<li class="inactive">
			Datasets
			<span class="subline">No datasets</span>
		</li>
		<li class="inactive">
			Processing
			<span class="subline">No processing</span>
		</li>
		<li class="inactive">
			Reports
			<span class="subline">No reports</span>
		</li>
		<li class="inactive">
			Download
			<span class="subline">No downloads</span>
		</li>
	<?php } else { ?>
		<li>
			<a href="#">Datasets</a>
			<span class="subline"><a href="#">Add New</a></span>
		</li>
		<li>
			<a href="#">Processing</a>
			<span class="subline"><a href="#">Add New</a></span>
		</li>
		<li>
			<a href="#">Reports</a>
			<span class="subline"><a href="#">Add New</a></span>
		</li>
		<li>
			<a href="#">Download</a>
		</li>
	<?php } ?>
	</ul>
</div>

<?php 
///////
// VIEW EXISTING PROJECT DETAILS
///////

if(!$new_paper and !$edit){ ?>

<div class="sidebar-mainpage project-mainpage">
	<h1><?php echo $paper['id']; ?> <small><?php echo $paper['internal'] ? '(Internal)' : '(External)'; ?></small></h1>
	<?php if ($paper['internal']) { ?>
	
	<?php } else { ?>
	<fieldset>
		<legend>Paper Details</legend>
		<p class="lead"><?php echo $paper['paper_title']; ?></p>
		<p><?php echo $paper['paper_journal'].' ('.$paper['year'].')'; ?></p>
		<p><?php echo $paper['authors']; ?></p>
	</fieldset>
	<?php if( !empty($paper['geo_accession']) || !empty($paper['sra_accession']) || !empty($paper['pmid']) || !empty($paper['doi']) || !empty($paper['accession_ena']) || !empty($paper['accession_ddjb'])){ ?>
	<fieldset>
		<legend>Accession Numbers</legend>
		<dl class="dl-horizontal">
		<?php if(!empty($paper['geo_accession'])) { ?>
			<dt><abbr title="Gene Expression Omnibus">GEO</abbr></dt>
			<dd><a href="http://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=<?php echo $paper['geo_accession']; ?>" target="_blank" title="Open in new window"><?php echo $paper['geo_accession']; ?></a></dd>
		<?php }
		if(!empty($paper['sra_accession'])) { ?>
			<dt><abbr title="Sequence Read Archive">SRA</abbr></dt>
			<dd><?php echo $paper['sra_accession']; ?></dd>
		<?php }
		if(!empty($paper['PMID'])) { ?>
			<dt><abbr title="PubMed ID">PMID</abbr></dt>
			<dd><a href="http://www.ncbi.nlm.nih.gov/pubmed/<?php echo $paper['PMID']; ?>" target="_blank" title="Open in new window"><?php echo $paper['PMID']; ?></a></dd>
		<?php }
		if(!empty($paper['DOI'])) { ?>
			<dt><abbr title="Digital Object Identifier">DOI</abbr></dt>
			<dd><a href="http://dx.doi.org/<?php echo $paper['DOI']; ?>" target="_blank" title="Open in new window"><?php echo $paper['DOI']; ?></a></dd>
		<?php }
		if(!empty($paper['accession_ena'])) { ?>
			<dt><abbr title="European Nucleotide Archive">ENA</abbr></dt>
			<dd><?php echo $paper['accession_ena']; ?></dd>
		<?php }
		if(!empty($paper['accession_ddjb'])) { ?>
			<dt><abbr title="DNA Data Bank of Japan">DDJB</abbr></dt>
			<dd><?php echo $paper['accession_ddjb']; ?></dd>
		<?php } ?>
		</dl>
	</fieldset>
	<?php } // has accessions
	if(!empty($paper['notes'])){ ?>
	<fieldset>
		<legend>Comments</legend>
		<p><?php echo stripslashes($paper['notes']); ?></p>
	</fieldset>
	<?php } // has notes
	} // is external ?>
</div>

<?php 
///////
// ADD OR EDIT A PROJECT
///////

} else { ?>



<div class="sidebar-mainpage project-mainpage">
	<form class="form-horizontal">
		<fieldset>
			<legend>Project Identifier</legend>
			<p>Every project needs a unique identifier. For an external project, this is typically the first author's surname and year, <em>eg.</em> <code>Ewels_2013</code></p>
			<p><input type="text" id="project_identifier" placeholder="Norris_<?php echo date("Y"); ?>"></p>
		</fieldset>
		<fieldset>
			<legend>Project Type</legend>
			<p>Projects can be internal or external. If external, they can be associated with a published paper and data accessions.</p>
			<p><div class="btn-group" data-toggle="buttons-radio">
				<button type="button" id="project_type_external" class="project_type_button btn active">External</button>
				<button type="button" id="project_type_internal" class="project_type_button btn">Internal</button>
			</div></p>
		</fieldset>
		<fieldset id="project_accessions_fieldset">
			<legend>Accessions</legend>
			<p>External projects can have multiple accession numbers associated with them. Labrador may be able to automatically fetch data using these.</p>
			<p>Multiple accessions can be entered, separated by spaces.</p>
			<div class="control-group">
				<label class="control-label" for="accession_geo"><abbr title="Gene Expression Omnibus">GEO</abbr></label>
				<div class="controls">
					<input type="text" id="accession_geo" placeholder="GSE000000">
					<span class="help-inline"><a href="#" title="Auto-complete empty fields"><i class="icon-search"></i></a></span>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="accession_sra"><abbr title="Sequence Read Archive">SRA</abbr></label>
				<div class="controls">
					<input type="text" id="accession_sra" placeholder="SRX000000">
					<span class="help-inline"><a href="#" title="Auto-complete empty fields"><i class="icon-search"></i></a></span>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="accession_pmid"><abbr title="PubMed ID">PMID</abbr></label>
				<div class="controls">
					<input type="text" id="accession_pmid" placeholder="01234567">
					<span class="help-inline"><a href="#" title="Auto-complete empty fields"><i class="icon-search"></i></a></span>
				</div>
			</div>
			<div class="control-group ">
				<label class="control-label" for="accession_doi"><abbr title="Digital Object Identifier">DOI</abbr></label>
				<div class="controls">
					<input type="text" id="accession_doi" placeholder="10.1016/j.molcel.2012.11.001">
					<span class="help-block">Use <a href="http://www.pmid2doi.org/" target="_blank">this tool</a> if in doubt</span>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="accession_ena"><abbr title="European Nucleotide Archive">ENA</abbr></label>
				<div class="controls">
					<input type="text" id="accession_ena">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="accession_ddjb"><abbr title="DNA Data Bank of Japan">DDJB</abbr></label>
				<div class="controls">
					<input type="text" id="accession_ddjb">
				</div>
			</div>
		</fieldset>
		<fieldset id="project_paper_fieldset">
			<legend>Paper Details</legend>
			<p>If the data is published, please enter the publication details below.</p>
			<div class="control-group ">
				<label class="control-label" for="paper_title">Paper Title</label>
				<div class="controls">
					<input type="text" name="paper_title" value="" id="paper_title">
				</div>
			</div>
			<div class="control-group ">
				<label class="control-label" for="paper_journal">Journal</label>
				<div class="controls">
					<input type="text" name="paper_journal" id="paper_journal" placeholder="Nature">
				</div>
			</div>
			<div class="control-group ">
				<label class="control-label" for="year">Year of Publication</label>
				<div class="controls">
					<input type="text" name="year" maxlength="4" id="year" placeholder="<?php echo date("Y"); ?>">
				</div>
			</div>
			<div class="control-group ">
				<label class="control-label" for="authors">Authors</label>
				<div class="controls">
					<textarea name="authors" id="authors" rows="2" cols="40"></textarea>
				</div>
			</div>
		</fieldset>
		<fieldset id="project_internal_fieldset" style="display:none;">
			<legend>Internal Project Details</legend>
			<div class="control-group ">
				<label class="control-label" for="internal_description">Description</label>
				<div class="controls">
					<textarea name="internal_description" id="internal_description"></textarea>
				</div>
			</div>
			<div class="control-group ">
				<label class="control-label" for="internal_contact">Primary Contact</label>
				<div class="controls">
					<input type="text" name="internal_contact" id="internal_contact" placeholder="Chuck Norris">
				</div>
			</div>
			<div class="control-group ">
				<label class="control-label" for="internal_email">Contact E-mail</label>
				<div class="controls">
					<input type="text" name="internal_email" id="internal_email" placeholder="chuck.norris@babraham.ac.uk">
				</div>
			</div>
			<div class="control-group ">
				<label class="control-label" for="internal_email">Group</label>
				<div class="controls">
					<input type="text" name="internal_email" id="internal_group" placeholder="Wolf Reik">
				</div>
			</div>
		</fieldset>
		
		<fieldset id="project_notes_fieldset">
			<legend>Comments</legend>
			<p>You can add any project-specific notes below:</p>
			<textarea rows="5" class="input-xxlarge"></textarea>
		</fieldset>
		
		<div class="form-actions">
			<input type="submit" class="btn btn-primary btn-large" name="save_project" id="save_project" value="Save Project">
		</div>
</div>

<?php } // if($new or $edit) ?>

</div>


<!-- Le javascript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="js/jquery-1.8.3.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="includes/chosen/chosen.jquery.js"></script>
<script src="js/jquery.cookie.js"></script>
<script src="js/project.js"></script>


</body>
</html>