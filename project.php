<?php include('includes/start.php');

$project_id = false;
$error = false;
$stop_page_after_message = false;

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

if(isset($_GET['delete']) && is_numeric($_GET['delete'])){
	$delete = true;
	$new_project = false;
	$edit = false;
	$project_id = $_GET['delete'];
} else {
	$delete = false;
}

if($project_id){
	$projects = mysql_query("SELECT * FROM `projects` WHERE `id` = '".$project_id."'");
	if(mysql_num_rows($projects) == 1){
		$project = mysql_fetch_array($projects);
	} else {
		$new_project = true;
		$edit = false;
		$project_id = false;
		$error = true;
		$msg[] = "Could not find project ID. This may be an error in the URL or it could have been deleted.";
		$stop_page_after_message = true;
	}
}

///////
// SAVE SUBMITTED FORM
///////
if(isset($_POST['save_project']) && $_POST['save_project'] == 'Save Project'){

	// Collect and validate the submitted input
	$error = false;
	$msg = array();
	$values = array (
		"name" => preg_replace("/[^A-Za-z0-9_]/", '_', $_POST['name']),
		"status" => $_POST['status'],
		"assigned_to" => filter_var($_POST['assigned_to'], FILTER_SANITIZE_EMAIL),
		"contact_name" => $_POST['contact_name'],
		"contact_email" => filter_var($_POST['contact_email'], FILTER_SANITIZE_EMAIL),
		"contact_group" => $_POST['contact_group'],
		"accession_geo" => $_POST['accession_geo'],
		"accession_sra" => $_POST['accession_sra'],
		"accession_ena" => $_POST['accession_ena'],
		"accession_ddjb" => $_POST['accession_ddjb'],
		"title" => $_POST['title'],
		"description" => $_POST['description'],
		"notes" => $_POST['notes']
	);
	if(strlen($values['name']) == 0){
		$error = true;
		$msg[] = "Project Identifier cannot be blank";
	}
	
	// Passed validation - save project
	if(!$error){
		if(!isset($project_id) || !is_numeric($project_id)){
			$query = "INSERT INTO `projects` (";
			foreach($values as $id => $var) {
				$query .= "`$id`, ";
			}
			$query = substr($query, 0, -2) . ") VALUES (";
			foreach($values as $id => $var) {
				$query .= "'".mysql_real_escape_string($var)."', ";
			}
			$query = substr($query, 0, -2) . ")";
			$history = "Created project.";
		} else {
			$query = "UPDATE `projects` SET ";
			foreach($values as $id => $var) {
				$query .= "`$id` = '".mysql_real_escape_string($var)."', ";
			}
			$query = substr($query, 0, -2) . " WHERE `id` = '$project_id'";
			$history = "Edited project.";
		}
		if(mysql_query($query)){
			// Saved project - now save papers
			if(!isset($project_id) || !is_numeric($project_id)){
				$project_id = mysql_insert_id();
			}
			if($project_id > 0){
				$i = 1;
				while(isset($_POST['paper_year_'.$i])){
					// Collect variables
					$values = array (
						"project_id" => $project_id,
						"year" => $_POST['paper_year_'.$i],
						"journal" => $_POST['paper_journal_'.$i],
						"title" => $_POST['paper_title_'.$i],
						"authors" => $_POST['paper_authors_'.$i],
						"pmid" => $_POST['paper_pmid_'.$i],
						"doi" => $_POST['paper_doi_'.$i],
					);
					// Build mysql queries
					if(isset($_POST['paper_id_'.$i]) && is_numeric($_POST['paper_id_'.$i])){
						$query = "UPDATE `papers` SET ";
						foreach($values as $id => $var) {
							$query .= "`$id` = '".mysql_real_escape_string($var)."', ";
						}
						$query = substr($query, 0, -2) . " WHERE `id` = '".$_POST['paper_id_'.$i]."'";
					} else {
						$query = "INSERT INTO `papers` (";
						foreach($values as $id => $var) {
							$query .= "`$id`, ";
						}
						$query = substr($query, 0, -2) . ") VALUES (";
						foreach($values as $id => $var) {
							$query .= "'".mysql_real_escape_string($var)."', ";
						}
						$query = substr($query, 0, -2) . ")";
					}
					// Save to database
					if(!mysql_query($query)){
						$error = true;
						$msg[] = "Could not save paper to database. mySQL error: <code>".mysql_error()."</code><br>mySQL query: <code>$query</code>";
					}
					// increment counter
					$i++;
				}
				
				// Save history message
				$query = sprintf("INSERT INTO `history` (`project_id`, `note`, `time`) VALUES ('%d', '%s', '%d')", $project_id, mysql_real_escape_string($history), time());
				if(!mysql_query($query)){
					$error = true;
					$msg[] = "Could not save history log to database. mySQL error: <code>".mysql_error()."</code><br>mySQL query: <code>$query</code>";
				}
				
				// Success!
				if(isset($_GET['id']) && is_numeric($_GET['id'])){
					$msg[] = '<strong>Successfully saved project.</strong> &nbsp; <a href="project.php?id='.$project_id.'">View project</a>.';
				} else {
					$msg[] = '<strong>Successfully saved project.</strong> &nbsp; <a href="datasets.php?add='.$project_id.'">Add datasets</a>.';
				}
				$new_project = false;
				$stop_page_after_message = true;
				
			} else {
				$error = true;
				$msg[] = "Saved project to database but couldn't find inserted ID so didn't save papers.";
			}
		} else {
			$error = true;
			$msg[] = "Could not save project to database. mySQL error: <code>".mysql_error()."</code><br>mySQL query: <code>$query</code>";
		}
	}
	
}

///////
// DELETE PROJECT
///////
if($delete && $project_id){
	// Start off by deleting file records and reports etc
	///// NOTE - not written yet, are we going to keep file db records?
	
	// Now go through and delete everything else which refers to project_id
	$query = sprintf("DELETE FROM `datasets` WHERE `project_id` = '%d'", $project_id);
	if(mysql_query($query)){
		$s = mysql_affected_rows() > 1 ? 's' : '';
		$msg[] = mysql_affected_rows(). " datasets$s deleted";
	} else {
		$error = true;
		$msg[] = "Could not delete datasets: ".mysql_error();
	}
	
	$query = sprintf("DELETE FROM `papers` WHERE `project_id` = '%d'", $project_id);
	if(mysql_query($query)){
		$s = mysql_affected_rows() > 1 ? 's' : '';
		$msg[] = mysql_affected_rows(). " paper$s deleted";
	} else {
		$error = true;
		$msg[] = "Could not delete papers: ".mysql_error();
	}
	
	$query = sprintf("DELETE FROM `history` WHERE `project_id` = '%d'", $project_id);
	if(mysql_query($query)){
		$s = mysql_affected_rows() > 1 ? 's' : '';
		$msg[] = mysql_affected_rows(). " history log$s deleted";
	} else {
		$error = true;
		$msg[] = "Could not delete history logs: ".mysql_error();
	}
	
	$query = sprintf("DELETE FROM `projects` WHERE `id` = '%d'", $project_id);
	if(mysql_query($query)){
		$msg[] = "Project deleted";
	} else {
		$error = true;
		$msg[] = "Could not delete project: ".mysql_error();
	}
	
	$stop_page_after_message = true;
}



include('includes/header.php'); ?>

<div class="sidebar-nav">
	<h3 id="sidebar_project_title">
	<?php if($project_id){
		echo '<a href="project.php?id='.$project_id.'">'.$project['name'].'</a>';
	} else {
		echo '<span class="muted">New Project</span>';
	}?></h3>
	<ul class="project-tabs">
	<?php if($new_project){ ?>
		<li class="active">
			<a href="project.php">Project Details</a>
		</li>
		<li class="inactive">
			<a href="#" class="fake_link">Datasets</a>
		</li>
		<li class="inactive">
			<a href="#" class="fake_link">Processing</a>
		</li>
		<li class="inactive">
			<a href="#" class="fake_link">Reports</a>
		</li>
		<li class="inactive">
			<a href="#" class="fake_link">Download</a>
		</li>
	<?php } else { ?>
		<li class="active">
			<a href="project.php?id=<?php echo $project_id; ?>">Project Details</a>
		</li>
		<li>
			<a href="datasets.php?id=<?php echo $project_id; ?>">Datasets</a>
		</li>
		<li>
			<a href="#">Processing</a>
		</li>
		<li>
			<a href="reports.php?id=<?php echo $project_id; ?>">Reports</a>
		</li>
	<?php } ?>
	</ul>
</div>

<?php 
///////
// VIEW EXISTING PROJECT DETAILS
///////

if(!$new_project and !$edit and !$error){ ?>

<div class="sidebar-mainpage project-mainpage">
	
	<?php // if(!empty($_POST)) { echo '<pre>'.print_r($_POST, true).'</pre>'; } ?>
	<?php if(!empty($msg)): ?>
		<div class="container alert alert-<?php echo $error ? 'error' : 'success'; ?>">
			<button type="button" class="close" data-dismiss="alert">×</button>
			<?php echo $error ? '<strong>Error!</strong><br>' : ''; ?> 
			<?php foreach($msg as $var)	echo $var.'<br>'; ?>
		</div>
	<?php endif; ?>
	
	<?php // End page if it's a terminal message
	if($stop_page_after_message){ ?>
		</div>
		<?php include('includes/javascript.php'); ?>
		<script src="js/jquery.validate.min.js" type="text/javascript"></script>
		<script src="js/project.js" type="text/javascript"></script>
		<?php include('includes/footer.php');
		exit;
	 } ?>
	
	
	
	<?php if(!$new_project) { ?><a style="float:right;" class="btn" href="project.php?edit=<?php echo $project['id']; ?>">Edit Project</a><?php } ?>
	<?php project_header($project); ?>

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
	<?php } // has notes

	// HISTORY LOG
	$histories = mysql_query("SELECT * FROM `history` WHERE `project_id` = '$project_id' ORDER BY `time` DESC");
	if(mysql_num_rows($histories) > 0){	?>
	
	<fieldset>
		<legend>History</legend>
		<dl class="dl-horizontal muted">
			<?php while($history = mysql_fetch_array($histories)){ ?>
			<dt><small><?php echo date("j/m/Y, G:i", $history['time']); ?></small></dt>
			<dd><small><?php echo $history['note']; ?></small></dd>
			<?php } ?>
		</dl>
	</fieldset>
	
	<?php } ?>
	
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
		"status" => "",
		"assigned_to" => "",
		"contact_name" => "",
		"contact_email" => "",
		"contact_group" => "",
		"accession_geo" => "",
		"accession_sra" => "",
		"accession_ena" => "",
		"accession_ddjb" => "",
		"title" => "",
		"description" => "",
		"notes" => ""
	);
	$papers = array();
	
	if($edit){
		$values = array (
			"name" => $project['name'],
			"status" => $project['status'],
			"assigned_to" => $project['assigned_to'],
			"contact_name" => $project['contact_name'],
			"contact_email" => $project['contact_email'],
			"contact_group" => $project['contact_group'],
			"accession_geo" => $project['accession_geo'],
			"accession_sra" => $project['accession_sra'],
			"accession_ena" => $project['accession_ena'],
			"accession_ddjb" => $project['accession_ddjb'],
			"title" => $project['title'],
			"description" => $project['description'],
			"notes" => $project['notes']
		);
		
	}
	
	if($error) {
		$values = array (
			"name" => $_POST['name'],
			"status" => $_POST['status'],
			"assigned_to" => $_POST['assigned_to'],
			"contact_name" => $_POST['contact_name'],
			"contact_email" => $_POST['contact_email'],
			"contact_group" => $_POST['contact_group'],
			"accession_geo" => $_POST['accession_geo'],
			"accession_sra" => $_POST['accession_sra'],
			"accession_ena" => $_POST['accession_ena'],
			"accession_ddjb" => $_POST['accession_ddjb'],
			"title" => $_POST['title'],
			"description" => $_POST['description'],
			"notes" => $_POST['notes']
		);
	}

?>



<div class="sidebar-mainpage project-mainpage">
	<form action="project.php<?php if($edit){ echo '?id='.$project_id; } ?>" method="post" class="form-horizontal add_edit_project form_validate">
		
		<?php if(!empty($msg)): ?>
			<div class="container alert alert-<?php echo $error ? 'error' : 'success'; ?>">
				<button type="button" class="close" data-dismiss="alert">×</button>
				<?php echo $error ? '<strong>Error!</strong><br>' : ''; ?> 
				<?php foreach($msg as $var)	echo $var.'<br>'; ?>
			</div>
		<?php endif; ?>

		<?php // End page if it's a terminal message
		if($stop_page_after_message){ ?>
			</div>
			<?php include('includes/javascript.php'); ?>
			<script src="js/project.js" type="text/javascript"></script>
			<?php include('includes/footer.php');
			exit;
		 } ?>
		 
		<input style="float:right;" type="submit" class="btn btn-primary" name="save_project" id="save_project" value="Save Project">
		
		<fieldset>
			<legend>Project Identifier</legend>
			<p>Every project needs a unique identifier. For an external project, this is typically the first author's surname and year, <em>eg.</em> <code>Ewels_2013</code>. Numbers, letters and underscores only.</p>
			<p><input type="text" id="name" name="name" maxlength="255" required placeholder="Surname_<?php echo date("Y"); ?>" value="<?php echo $values['name']; ?>"></p>
			<p>All of the remaining fields are optional.</p>
		</fieldset>
		
		<fieldset id="project_status_fieldset">
			<legend>Project Contacts</legend>
			
			<div class="control-group">
				<label class="control-label" for="assigned_to">Assigned To</label>
				<div class="controls">
					<input type="email" id="assigned_to" name="assigned_to" maxlength="250" placeholder="<?php echo preg_replace('/\s+/', '.', strtolower($names[array_rand($names)])); ?>@babraham.ac.uk" value="<?php echo $values['assigned_to']; ?>" />
					<span class="help-inline">Who is processing the data? <small> &nbsp; 
						<?php foreach($project_assignQuickFill as $qf_email => $qf_name){
							echo ' / <a href="#" class="assign_quickFill" title="'.$qf_email.'">'.$qf_name.'</a>'; 
						} ?>
					</small></span>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="status">Status</label>
				<div class="controls">
					<select id="status" name="status" autofocus>
						<option <?php if($values['status'] == 'Not Started'){ echo 'selected="selected"'; } ?>>Not Started</option>
						<option <?php if($values['status'] == 'Currently Processing'){ echo 'selected="selected"'; } ?>>Currently Processing</option>
						<option <?php if($values['status'] == 'Processing Complete'){ echo 'selected="selected"'; } ?>>Processing Complete</option>
					</select>
				</div>
			</div>
			
			<div class="control-group ">
				<label class="control-label" for="contact_name">Primary Contact</label>
				<div class="controls">
					<input type="text" name="contact_name" id="contact_name" maxlength="250" placeholder="<?php echo $name; ?>" value="<?php echo $values['contact_name']; ?>">
					<span class="help-inline">Who requested / generated the data?</span>
				</div>
			</div>
			<div class="control-group ">
				<label class="control-label" for="contact_email">Contact E-mail</label>
				<div class="controls">
					<input type="email" name="contact_email" id="contact_email" maxlength="250" placeholder="<?php echo preg_replace('/\s+/', '.', strtolower($name)); ?>@babraham.ac.uk" value="<?php echo $values['contact_email']; ?>">
				</div>
			</div>
			<div class="control-group ">
				<label class="control-label" for="contact_group">Group</label>
				<div class="controls">
					<input type="text" name="contact_group" id="contact_group" maxlength="250" placeholder="<?php echo $names[array_rand($names)]; ?>" value="<?php echo $values['contact_group']; ?>">
					<span class="help-inline"><small>
						<?php foreach($project_groupAssignQuickFill as $qf_name => $qf_display) {
							echo ' / <a href="#" class="groupAssign_quickFill" title="'.$qf_name.'">'.$qf_display.'</a>'; 
						} ?>
					</small></span>
				</div>
			</div>
		</fieldset>
		
		<fieldset id="project_accessions_fieldset">
			<legend>Accessions</legend>
			<p>External projects can have multiple accession numbers associated with them. If you click a magnifying glass, Labrador will try to fill in empty fields elsewhere using these.</p>
			<p>Multiple accessions can be entered, separated by spaces. When auto-completing, fields will be filled in order of accessions.</p>
			<div class="control-group">
				<label class="control-label" for="accession_geo"><abbr title="Gene Expression Omnibus">GEO</abbr></label>
				<div class="controls">
					<input type="text" name="accession_geo" id="accession_geo" maxlength="50" placeholder="GSE000000" value="<?php echo $values['accession_geo']; ?>">
					<span class="help-inline"><a href="#" id="geo_lookup" title="Auto-complete empty fields"><i class="icon-search"></i></a></span>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="accession_sra"><abbr title="Sequence Read Archive">SRA</abbr></label>
				<div class="controls">
					<input type="text" name="accession_sra" id="accession_sra" maxlength="50" placeholder="SRX000000" value="<?php echo $values['accession_sra']; ?>">
					<span class="help-inline"><a href="#" title="Auto-complete empty fields"><i class="icon-search"></i></a></span>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="accession_ena"><abbr title="European Nucleotide Archive">ENA</abbr></label>
				<div class="controls">
					<input type="text" name="accession_ena" id="accession_ena" maxlength="50" placeholder="BN000000" value="<?php echo $values['accession_ena']; ?>">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="accession_ddjb"><abbr title="DNA Data Bank of Japan">DDJB</abbr></label>
				<div class="controls">
					<input type="text" name="accession_ddjb" id="accession_ddjb" maxlength="50" placeholder="DRP000000" value="<?php echo $values['accession_ddjb']; ?>">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="accession_geo"><abbr title="PubMed ID">PMID</abbr></label>
				<div class="controls">
					<input type="text" id="accession_pmid" maxlength="50" placeholder="12345678">
					<span class="help-inline"><a href="#" id="pmid_lookup" title="Auto-complete paper details"><i class="icon-search"></i></a></span>
				</div>
			</div>
		</fieldset>
		
		<fieldset id="project_paper_fieldset">
			<legend>Publications</legend>
			<table class="table table_form edit_publications">
				<thead>
					<th style="width:40px">Year</th>
					<th style="width:100px">Journal</th>
					<th>Title</th>
					<th>Authors</th>
					<th style="width:60px">PMID</th>
					<th style="width:140px;">DOI</th>
					<th style="width:60px;">Actions</th>
				</thead>
				<tbody>
					<?php 
					$papers = array();
					if($error){
						while(isset($_POST['project_year_'.$i])){
							// Collect variables
							$paper = array (
								"year" => $_POST['paper_year_'.$i],
								"journal" => $_POST['paper_journal_'.$i],
								"title" => $_POST['paper_journal_'.$i],
								"authors" => $_POST['paper_journal_'.$i],
								"pmid" => $_POST['paper_journal_'.$i],
								"doi" => $_POST['paper_journal_'.$i],
							);
							if(isset($_POST['paper_id_'.$i]) && is_numeric($_POST['paper_id_'.$i])){
								$paper['id'] = $_POST['paper_id_'.$i];
							}
							$papers[] = $paper;
						}
					} else if($edit){
						$papers_q = mysql_query("SELECT * FROM `papers` WHERE `project_id` = '".$project_id."'");
						while ($paper = mysql_fetch_array($papers_q)){ 
							$papers[] = $paper;
						}
					}
					if(count($papers) == 0){ ?>
					<tr class="no_papers_tr">
						<td colspan="6"><em>No papers found..</em></td>
					</tr>
					<?php 
					} else {
						$i = 0;
						foreach($papers as $paper){
							$i++; ?>
							<tr id="paper_row_<?php echo $i; ?>">
								<td>
									<input type="hidden" class="paper_id" id="paper_id_<?php echo $i; ?>" name="paper_id_<?php echo $i; ?>" value="<?php echo $paper['id']; ?>" />
									<input type="text" maxlength="4" class="paper_year" id="paper_year_<?php echo $i; ?>" name="paper_year_<?php echo $i; ?>" value="<?php echo $paper['year']; ?>" />
								</td>
								<td><input type="text" class="paper_journal" id="paper_journal_<?php echo $i; ?>" name="paper_journal_<?php echo $i; ?>" value="<?php echo $paper['journal']; ?>" /></td>
								<td><input type="text" class="paper_title" id="paper_title_<?php echo $i; ?>" name="paper_title_<?php echo $i; ?>" value="<?php echo $paper['title']; ?>"></td>
								<td><input type="text" class="paper_authors" id="paper_authors_<?php echo $i; ?>" name="paper_authors_<?php echo $i; ?>" value="<?php echo $paper['authors']; ?>"></td>
								<td><input type="text" class="paper_pmid" id="paper_pmid_<?php echo $i; ?>" name="paper_pmid_<?php echo $i; ?>" value="<?php echo $paper['pmid']; ?>" /></td>
								<td><input type="text" class="paper_doi" id="paper_doi_<?php echo $i; ?>" name="paper_doi_<?php echo $i; ?>" value="<?php echo $paper['doi']; ?>" /></td>
								<td><button class="paper_delete btn btn-small btn-danger" id="paper_delete_<?php echo $i; ?>">Delete</button></td>
							</tr>
					<?php }
					} ?>
				</tbody>
			</table>
			<p><a href="#" class="btn" id="paper_add_paper">Add Paper Manually</a></p>
		</fieldset>
		
		<fieldset id="project_notes_fieldset">
			<legend>Description &amp; Comments</legend>
			
			<div class="control-group ">
				<label class="control-label" for="title">Project Title</label>
				<div class="controls">
					<input type="text" name="title" id="title" class="input-xlarge" maxlength="250" value="<?php echo $values['title']; ?>">
				</div>
			</div>
			
			<div class="control-group ">
				<label class="control-label" for="description">Project Description</label>
				<div class="controls">
					<textarea name="description" class="input-xlarge" id="description"><?php echo $values['description']; ?></textarea>
				</div>
			</div>
			
			<div class="control-group ">
				<label class="control-label" for="notes">Notes</label>
				<div class="controls">
					<textarea name="notes" class="input-xlarge" id="notes"><?php echo $values['notes']; ?></textarea>
				</div>
			</div>
		</fieldset>
		
		<div class="form-actions">
			<input type="submit" class="btn btn-primary btn-large" name="save_project" id="save_project" value="Save Project">
			<?php if($edit){ ?>
				&nbsp; &nbsp; <a href="#" id="delete_project_button" class="btn btn-large btn-danger popover_button" data-toggle="popover" data-html="true" title="Delete Project" data-content="Are you sure? This will delete the project and all papers &amp; datasets associated with it from the database. <strong>This cannot be undone</strong>. Data on the server will not be affected. <br><br> <a href='project.php?delete=<?php echo $project_id; ?>' class='btn btn-danger btn-block'>I'm sure - delete the project</a>" data-original-title="Delete Project">Delete Project</a>
			<?php } ?>
		</div>
</div>

<?php } // if($new or $edit or $delete)

include('includes/javascript.php'); ?>
<script src="js/project.js" type="text/javascript"></script>
<?php include('includes/footer.php'); ?>