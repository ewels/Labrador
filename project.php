<?php include('includes/start.php');

$project_id = false;

if(isset($_GET['id']) && is_numeric($_GET['id'])){
	$new_project = false;
	$project_id = $_GET['id'];
} else {
	$new_project = true;
}

if(isset($_GET['edit']) && is_numeric($_GET['edit'])){
	$edit = true;
	$new_project = false;
	$project_id = $_GET['edit'];
} else {
	$edit = false;
}

if($project_id){
	$projects = mysql_query("SELECT * FROM `projects` WHERE `id` = '".$project_id."'");
	$project = mysql_fetch_array($projects);
}

include('includes/header.php'); ?>

<div class="sidebar-nav">
	<h3 id="sidebar_project_title">
	<?php if(!$new_project){
		echo $project['name'];
	} else {
		echo '<span class="muted">New Project</span>';
	}?></h3>
	<ul class="project-tabs">
		<li class="active">
			<a href="#">Project Details</a>
			<?php if(!$new_project) { ?><span class="subline"><a href="project.php?edit=<?php echo $project['id']; ?>">Edit</a></span><?php } ?>
		</li>
	<?php if($new_project){ ?>
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

if(!$new_project and !$edit){ ?>

<div class="sidebar-mainpage project-mainpage">
	<h1>
		<?php echo $project['name']; ?>
		<?php echo accession_badges ($project['accession_geo'], 'geo'); ?>
		<?php echo accession_badges ($project['accession_sra'], 'sra'); ?>
		<?php echo accession_badges ($project['accession_ena'], 'ena'); ?>
		<?php echo accession_badges ($project['accession_ddjb'], 'ddjb'); ?>
	</h1>

	<?php $papers = mysql_query("SELECT * from `papers` WHERE `project_id` = '".$project['id']."'");
	if(mysql_num_rows($papers) > 0) { ?>
	<fieldset id="project_paper_fieldset">
		<legend>Publications</legend>
		<table class="table">
			<thead>
				<th>Year</th>
				<th>Journal</th>
				<th>Title</th>
				<th>Authors</th>
				<th>PMID</th>
				<th>DOI</th>
			<thead>
			<tbody>
			<?php while($paper = mysql_fetch_array($papers)){
				echo '<tr>';
				echo '<td>'.$paper['year'].'</td>';
				echo '<td>'.$paper['journal'].'</td>';
				echo '<td>'.$paper['title'].'</td>';
				echo '<td>'.$paper['authors'].'</td>';
				echo '<td><a href="http://www.ncbi.nlm.nih.gov/pubmed/'.$paper['pmid'].'" target="_blank">'.$paper['pmid'].'</a></td>';
				echo '<td><a href="http://dx.doi.org/'.$paper['doi'].'" target="_blank">'.$paper['doi'].'</a></td>';
				echo '</tr>';
			} // while ?>
			</tbody>
		</table>
	</fieldset>
	<?php } // > 0 papers
	
	
	if(!empty($project['notes'])){ ?>
	<fieldset>
		<legend>Comments</legend>
		<p><?php echo nl2br(stripslashes($project['notes'])); ?></p>
	</fieldset>
	<?php } // has notes
	
	if(!empty($project['contact_name']) || !empty($project['contact_email']) || !empty($project['contact_group'])){ ?>
	<fieldset>
		<legend>Contacts</legend>
		<dl>
			<?php if(!empty($project['contact_name']) || !empty($project['contact_email']) ){?>
				<dt>Primary Contact</dt>
				<?php if(!empty($project['contact_name']) && empty($project['contact_email'])) { echo '<dd>'.$project['contact_name'].'</dd>'; } ?>
				<?php if(!empty($project['contact_name']) && !empty($project['contact_email'])) { echo '<dd>'.$project['contact_name'].' <em>(<a href="mailto:'.$project['contact_email'].'">'.$project['contact_email'].'</a>)</em></dd>'; } ?>
				<?php if(empty($project['contact_name']) && !empty($project['contact_email'])) { echo '<dd><a href="mailto:'.$project['contact_email'].'">'.$project['contact_email'].'</a></dd>'; } ?>
			<?php } ?>
			<?php if(!empty($project['contact_group'])){?>
				<dt>Group</dt>
				<dd><?php echo $project['contact_group']; ?></dd>
			<?php } ?>
		</dl>
	</fieldset>
	<?php } // has notes ?>
</div>

<?php 
///////
// ADD OR EDIT A PROJECT
///////

} else {
	
	$names = array ("Chuck Norris", "Albert Einstein", "Charles Darwin", "George Martin", "Galileo Galilei", "Barack Obama", "Margaret Thatcher",
					"Jean-Claude Van Damme", "Isaac Newton", "Darth Vader", "William Shatner", "Dolly Parton", "David Hasselhoff", "Mr T", "B. A. Baracus", "MC Hammer",
					"Daenerys Targaryen", "Tin Tin", "James Bond", "Indiana Jones", "Alex Ferguson", "Lord Nelson", "Leonardo da Vinci", "Clark Kent", "Yoda",
					"Miss Moneypenny", "Harry Houdini", "Edmund Blackadder", "Hannibal Lector", "Evel Knievel", "Dr Evil", "Neil Armstrong", "Alan Partridge", 
					"John Lennon", "Marilyn Monroe", "Elvis Presley", "Michael Corleone", "Napoleon Bonaparte", "Marie Antoinette", "Oliver Cromwell", "Flash Gordon", 
					"Kermit the Frog", "Thom Yorke", "George Clooney", "Homer Simpson", "Harry Potter", "Sherlock Holmes", "Bilbo Baggins", "Julius Caesar", "Bruce Lee",
					"Michael Jackson", "Freddy Mercury", "Winne the Pooh");
	$name = $names[array_rand($names)];
	
	$values = array (
		"name" => "",
		"accession_geo" => "",
		"accession_sra" => "",
		"accession_ena" => "",
		"accession_ddjb" => "",
		"contact_name" => "",
		"contact_email" => "",
		"contact_group" => "",
		"notes" => ""
	);
	$papers = array();
	
	if($edit){
		$values = array (
			"name" => $project['name'],
			"accession_geo" => $project['accession_geo'],
			"accession_sra" => $project['accession_sra'],
			"accession_ena" => $project['accession_ena'],
			"accession_ddjb" => $project['accession_ddjb'],
			"contact_name" => $project['contact_name'],
			"contact_email" => $project['contact_email'],
			"contact_group" => $project['contact_group'],
			"notes" => $project['notes']
		);
		
	}



?>



<div class="sidebar-mainpage project-mainpage">
	<form class="form-horizontal">
		<fieldset>
			<legend>Project Identifier</legend>
			<p>Every project needs a unique identifier. For an external project, this is typically the first author's surname and year, <em>eg.</em> <code>Ewels_2013</code></p>
			<p><input type="text" id="name" placeholder="Surname_<?php echo date("Y"); ?>" value="<?php echo $values['name']; ?>"></p>
			<p>All of the remaining fields are optional.</p>
		</fieldset>
		
		<fieldset id="project_accessions_fieldset">
			<legend>Accessions</legend>
			<p>External projects can have multiple accession numbers associated with them. If you click a magnifying glass, Labrador will try to fill in empty fields elsewhere using these.</p>
			<p>Multiple accessions can be entered, separated by spaces. When auto-completing, fields will be filled in order of accessions.</p>
			<div class="control-group">
				<label class="control-label" for="accession_geo"><abbr title="Gene Expression Omnibus">GEO</abbr></label>
				<div class="controls">
					<input type="text" id="accession_geo" placeholder="GSE000000" value="<?php echo $values['accession_geo']; ?>">
					<span class="help-inline"><a href="#" title="Auto-complete empty fields"><i class="icon-search"></i></a></span>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="accession_sra"><abbr title="Sequence Read Archive">SRA</abbr></label>
				<div class="controls">
					<input type="text" id="accession_sra" placeholder="SRX000000" value="<?php echo $values['accession_sra']; ?>">
					<span class="help-inline"><a href="#" title="Auto-complete empty fields"><i class="icon-search"></i></a></span>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="accession_ena"><abbr title="European Nucleotide Archive">ENA</abbr></label>
				<div class="controls">
					<input type="text" id="accession_ena" placeholder="BN000000" value="<?php echo $values['accession_ena']; ?>">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="accession_ddjb"><abbr title="DNA Data Bank of Japan">DDJB</abbr></label>
				<div class="controls">
					<input type="text" id="accession_ddjb" placeholder="DRP000000" value="<?php echo $values['accession_ddjb']; ?>">
				</div>
			</div>
		</fieldset>
		<fieldset id="project_paper_fieldset">
			<legend>Publications</legend>
			<p>If the data is published, please enter the publication details below.</p>
			<table class="table">
				<thead>
					<th>Year</th>
					<th>Journal</th>
					<th>Title</th>
					<th>Authors</th>
					<th>PMID</th>
					<th>DOI</th>
					<th style="width:150px;">Actions</th>
				<thead>
				<tbody>
					<?php if($edit){
						$papers = mysql_query("SELECT * FROM `papers` WHERE `project_id` = '".$project['id']."'");
					}
					if(!$edit || mysql_num_rows($papers) == 0){ ?>
					<tr>
						<td colspan="6"><em>No papers found..</em></td>
					</tr>
					<?php } else {
						while ($paper = mysql_fetch_array($papers)){ ?>
					<tr>
						<td><?php echo $paper['year']; ?></td>
						<td><?php echo $paper['journal']; ?></td>
						<td><?php echo $paper['title']; ?></td>
						<td><?php echo $paper['authors']; ?></td>
						<td><a href="http://www.ncbi.nlm.nih.gov/pubmed/<?php echo $paper['pmid']; ?>" target="_blank"><?php echo $paper['pmid']; ?></a></td>
						<td><a href="http://dx.doi.org/<?php echo $paper['doi']; ?>" target="_blank"><?php echo $paper['doi']; ?></a></td>
						<td stlye="text-align:center;"><a href="#" class="btn btn-small"><i class="icon-pencil"></i> &nbsp; Edit</a> &nbsp; <a href="#" class="btn btn-small btn-danger"><i class="icon-trash icon-white"></i> &nbsp; Delete</a></td>
					<?php }
					} ?>
				</tbody>
			</table>
			<p><a href="#" class="btn">Add Paper</a></p>
		</fieldset>
		<fieldset id="project_internal_fieldset">
			<legend>Project Details</legend>
			<p>These fields help us track who generated the data (if internal) or who originally requested the data (if external).</p>
			<div class="control-group ">
				<label class="control-label" for="contact_name">Primary Contact</label>
				<div class="controls">
					<input type="text" name="contact_name" id="contact_name" placeholder="<?php echo $name; ?>" value="<?php echo $values['contact_name']; ?>">
				</div>
			</div>
			<div class="control-group ">
				<label class="control-label" for="contact_email">Contact E-mail</label>
				<div class="controls">
					<input type="text" name="contact_email" id="contact_email" placeholder="<?php echo preg_replace('/\s+/', '.', strtolower($name)); ?>@babraham.ac.uk" value="<?php echo $values['contact_email']; ?>">
				</div>
			</div>
			<div class="control-group ">
				<label class="control-label" for="contact_group">Group</label>
				<div class="controls">
					<input type="text" name="contact_group" id="contact_group" placeholder="<?php echo $names[array_rand($names)]; ?>" value="<?php echo $values['contact_group']; ?>">
				</div>
			</div>
		</fieldset>
		
		<fieldset id="project_notes_fieldset">
			<legend>Comments</legend>
			<p><label for="notes">You can add any project-specific notes below:</label></p>
			<textarea rows="5" class="input-xxlarge" name="notes" id="notes"><?php echo $values['notes']; ?></textarea>
		</fieldset>
		
		<div class="form-actions">
			<input type="submit" class="btn btn-primary btn-large" name="save_project" id="save_project" value="Save Project">
		</div>
</div>

<?php } // if($new or $edit)

include('includes/javascript.php'); ?>
<script src="js/project.js" type="text/javascript"></script>
<?php include('includes/footer.php'); ?>