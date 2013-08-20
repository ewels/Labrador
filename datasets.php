<?php include('includes/start.php');

$project_id = false;

if(isset($_GET['id']) && is_numeric($_GET['id'])){
	$project_id = $_GET['id'];
}

if(isset($_GET['edit']) && is_numeric($_GET['edit'])){
	$edit = true;
	$project_id = $_GET['edit'];
} else {
	$edit = false;
}

if(isset($_GET['add']) && is_numeric($_GET['add'])){
	$add = true;
	$edit = false;
	$project_id = $_GET['add'];
} else {
	$add = false;
}

if($project_id){
	$projects = mysql_query("SELECT * FROM `projects` WHERE `id` = '".$project_id."'");
	if(mysql_num_rows($projects) == 1){
		$project = mysql_fetch_array($projects);
	} else {
		header("Location: index.php");
	}
} else {
	header("Location: index.php");
}

$error = false;

// DELETE DATASETS
if(!empty($_POST['delete_datasets_submit']) && $_POST['delete_datasets_submit'] == 'I’m sure - delete the datasets'){
	$dataset_query = "SELECT * FROM `datasets` WHERE `project_id` = '$project_id'";
	$datasets = mysql_query($dataset_query);
	$counter = 0;
	if(mysql_num_rows($datasets) > 0){
		while ($dataset = mysql_fetch_array($datasets)){
			$id = $dataset['id'];
			if(isset($_POST["check_$id"]) && $_POST["check_$id"] == 'on'){
				$query = "DELETE FROM `datasets` WHERE `id` = '$id'";
				if(!mysql_query($query)){
					$error = true;
					$msg[] = "Could not delete dataset. mySQL error: <code>".mysql_error()."</code><br>mySQL query: <code>$query</code>";
				} else {
					$counter++;
				}
			}
		}
	}
	// Save history message
	$query = sprintf("INSERT INTO `history` (`project_id`, `note`, `time`) VALUES ('%d', '%s', '%d')", $project_id, mysql_real_escape_string("Deleted $counter datasets."), time());
	if(!mysql_query($query)){
		$error = true;
		$msg[] = "Could not save history log to database. mySQL error: <code>".mysql_error()."</code><br>mySQL query: <code>$query</code>";
	}
	
	// Set up page	
	if(!$error){
		$msg[] = "Deleted $counter datasets.";
	}
}

// SAVE EDITED DATASETS
if(!empty($_POST['edit_datasets']) && $_POST['edit_datasets'] == 'Edit Datasets'){
	$dataset_query = "SELECT * FROM `datasets` WHERE `project_id` = '$project_id'";
	$datasets = mysql_query($dataset_query);
	$counter = 0;
	if(mysql_num_rows($datasets) > 0){
		while ($dataset = mysql_fetch_array($datasets)){
			$id = $dataset['id'];
			$sql = array();
			$sql['name'] = $_POST["name_$id"];
			$sql['species'] = $_POST["species_$id"];
			$sql['cell_type'] = $_POST["cell_type_$id"];
			$sql['data_type'] = $_POST["data_type_$id"];
			$sql['accession_geo'] = $_POST["accession_geo_$id"];
			$sql['accession_sra'] = $_POST["accession_sra_$id"];
			$sql['notes'] = $_POST["notes_$id"];
			$sql['modified'] = time();
			$query = "UPDATE `datasets` SET ";
			foreach($sql as $field => $var){
				$query .= "`$field` = '".mysql_real_escape_string($var)."', ";
			}
			$query = substr($query, 0, -2)." WHERE `id` = '$id'";
			// Save to database
			if(!mysql_query($query)){
				$error = true;
				$msg[] = "Could not save dataset to database. mySQL error: <code>".mysql_error()."</code><br>mySQL query: <code>$query</code>";
			} else {
				$counter++;
			}
		}
	}
	// Save history message
	$query = sprintf("INSERT INTO `history` (`project_id`, `note`, `time`) VALUES ('%d', '%s', '%d')", $project_id, mysql_real_escape_string("Edited datasets."), time());
	if(!mysql_query($query)){
		$error = true;
		$msg[] = "Could not save history log to database. mySQL error: <code>".mysql_error()."</code><br>mySQL query: <code>$query</code>";
	}
	
	// Set up page	
	if($error){
		$edit = true;
	} else {
		$msg[] = "Successfully edited $counter datasets.";
	}
}

// SAVE ADDED DATASETS
if(!empty($_POST['add_datasets']) && $_POST['add_datasets'] == 'Save All Datasets'){
	$i = 1;
	while(isset($_POST["name_$i"])){
		$sql = array();
		$sql['project_id'] = $project_id;
		$sql['name'] = $_POST["name_$i"];
		$sql['species'] = $_POST["species_$i"];
		$sql['cell_type'] = $_POST["cell_type_$i"];
		$sql['data_type'] = $_POST["data_type_$i"];
		$sql['accession_geo'] = $_POST["accession_geo_$i"];
		$sql['accession_sra'] = $_POST["accession_sra_$i"];
		$sql['modified'] = time();
		
		$query = "INSERT INTO `datasets` (";
		foreach($sql as $field => $var){
			$query .= "`$field`, ";
		}
		$query = substr($query, 0, -2).") VALUES (";
		foreach($sql as $field => $var){
			$query .= "'".mysql_real_escape_string($var)."', ";
		}
		$query = substr($query, 0, -2).")";
		// Save to database
		if(!mysql_query($query)){
			$error = true;
			$msg[] = "Could not save dataset to database. mySQL error: <code>".mysql_error()."</code><br>mySQL query: <code>$query</code>";
		}
		$i++;
	}
	
	// Save history message
	$query = sprintf("INSERT INTO `history` (`project_id`, `note`, `time`) VALUES ('%d', '%s', '%d')", $project_id, mysql_real_escape_string("Added $i datasets."), time());
	if(!mysql_query($query)){
		$error = true;
		$msg[] = "Could not save history log to database. mySQL error: <code>".mysql_error()."</code><br>mySQL query: <code>$query</code>";
	}
	
	// Set up page	
	if($error){
		$add = true;
	} else {
		$msg[] = "Successfully added $i datasets.";
	}
}




include('includes/header.php'); ?>

<div class="sidebar-nav">
	<h3 id="sidebar_project_title">
	<?php echo '<a href="project.php?id='.$project_id.'">'.$project['name'].'</a>'; ?></h3>
	<ul class="project-tabs">
		<li>
			<a href="project.php?id=<?php echo $project_id; ?>">Project Details</a>
		</li>
		<li class="active">
			<a href="datasets.php?id=<?php echo $project_id; ?>">Datasets</a>
		</li>
		<li>
			<a href="processing.php?id=<?php echo $project_id; ?>">Processing</a>
		</li>
		<li>
			<a href="reports.php?id=<?php echo $project_id; ?>">Reports</a>
		</li>
	</ul>
</div>

<?php if(!$edit && !$add){ ?>

<div class="sidebar-mainpage project-mainpage">

	<?php if(!empty($msg)): ?>
		<div class="container alert alert-<?php echo $error ? 'error' : 'success'; ?>">
			<button type="button" class="close" data-dismiss="alert">×</button>
			<?php echo $error ? '<strong>Error!</strong><br>' : ''; ?> 
			<?php foreach($msg as $var)	echo $var.'<br>'; ?>
		</div>
	<?php endif; ?>
	
	<a class="btn pull-right" href="datasets.php?edit=<?php echo $project['id']; ?>">Edit Datasets</a>
	<a style="margin-right:15px;" class="btn pull-right" href="datasets.php?add=<?php echo $project['id']; ?>">Add Datasets</a>
	<?php project_header($project); ?>
	
	<?php
	$dataset_query = "SELECT * FROM `datasets` WHERE `project_id` = '$project_id'";
	$datasets = mysql_query($dataset_query);
	$existing_datasets = array();
	if(mysql_num_rows($datasets) > 0){
	?>

		<form class="form-horizontal" action="download.php?id=<?php echo $project_id; ?>" method="post">
			<input type="submit" name="download_datasets" class="btn pull-right" value="Download Checked Datasets">
			<p style="margin-bottom:20px;"><label>Filter datasets: &nbsp; <input type="text" id="filter-datasets" /></label></p>
			<table id="existing_datasets_table" class="table table-bordered table-condensed table-hover sortable">
				<thead>
					<tr>
						<th class="select"><input type="checkbox" class="select-all"></th>
						<th data-sort="string-ins">Name</th>
						<th data-sort="string-ins">Species</th>
						<th data-sort="string-ins">Cell Type</th>
						<th data-sort="string-ins">Data Type</th>
						<th style="width:20%;">Accession Codes</th>
					</tr>
				</thead>
				<tbody>
			<?php while ($dataset = mysql_fetch_array($datasets)){ ?>
					<tr>
						<td class="select"><input type="checkbox" class="select-row" id="check_<?php echo $dataset['id']; ?>" name="check_<?php echo $dataset['id']; ?>"></td>
						<td><label for="check_<?php echo $dataset['id']; ?>"><?php echo $dataset['name']; ?></label>
						<?php if(!empty($dataset['notes'])) { ?>
							<i class="icon-tag pull-right" title="<?php echo $dataset['notes']; ?>"></i>
						<?php } ?></td>
						<td><?php echo $dataset['species']; ?></td>
						<td><?php echo $dataset['cell_type']; ?></td>
						<td><?php echo $dataset['data_type']; ?></td>
						<td><?php 
						echo accession_badges ($dataset['accession_geo'], 'geo');
						echo accession_badges ($dataset['accession_sra'], 'sra');
						?></td>
					</tr>
			<?php } // dataset while loop ?>
				</tbody>
			</table>
			<div class="form-actions">
				<input class="btn btn-large btn-primary" type="submit" name="download_datasets" value="Download Checked Datasets">
			</div>
		</form>

	<?php } //check for existing datasets ?>
</div>

<?php } // if(!$edit && !$add)

if($edit) { ?>

<div class="sidebar-mainpage project-mainpage">
	
	<?php if(!empty($msg)): ?>
		<div class="container alert alert-<?php echo $error ? 'error' : 'success'; ?>">
			<button type="button" class="close" data-dismiss="alert">×</button>
			<?php echo $error ? '<strong>Error!</strong><br>' : ''; ?> 
			<?php foreach($msg as $var)	echo $var.'<br>'; ?>
		</div>
	<?php endif; ?>
	
	<?php project_header($project); ?>
	
	<form class="form-inline well">
		Batch update checked datasets: &nbsp; 
		<input type="text" class="input-small bulk_update" id="name" placeholder="Name"> &nbsp; 
		<input type="text" class="input-small bulk_update" id="species" placeholder="Species"> &nbsp; 
		<input type="text" class="input-small bulk_update" id="cell_type" placeholder="Cell Type"> &nbsp; 
		<input type="text" class="input-small bulk_update" id="data_type" placeholder="Data Type"> &nbsp; 
		<input type="text" class="input-small bulk_update" id="notes" placeholder="Notes"> &nbsp; 
		<small><em>(overwrites current values)</em></small>
	</form>
	
	<form action="datasets.php?id=<?php echo $project_id; ?>" method="post" class="form-horizontal">
		
		<table id="edit_existing_datasets_table" class="table table-bordered table-condensed table-hover table_form">
			<thead>
				<tr>
					<th class="select"><input type="checkbox" class="select-all"></th>
					<th style="width:30%;">Name</th>
					<th>Species</th>
					<th>Cell Type</th>
					<th>Data Type</th>
					<th>GEO Accession</th>
					<th>SRA Accessions</th>
					<th>Notes</th>
				</tr>
			</thead>
			<tbody>
		<?php $dataset_query = "SELECT * FROM `datasets` WHERE `project_id` = '$project_id'";
		$datasets = mysql_query($dataset_query);
		if(mysql_num_rows($datasets) > 0){
			while ($dataset = mysql_fetch_array($datasets)){ ?>
				<tr>
					<td class="select"><input type="checkbox" class="select-row" id="check_<?php echo $dataset['id']; ?>" name="check_<?php echo $dataset['id']; ?>"></td>
					<td><input class="name" type="text" name="name_<?php echo $dataset['id']; ?>" value="<?php echo $dataset['name']; ?>"></td>
					<td><input class="species" type="text" name="species_<?php echo $dataset['id']; ?>" value="<?php echo $dataset['species']; ?>"></td>
					<td><input class="cell_type" type="text" name="cell_type_<?php echo $dataset['id']; ?>" value="<?php echo $dataset['cell_type']; ?>"></td>
					<td><input class="data_type" type="text" name="data_type_<?php echo $dataset['id']; ?>" value="<?php echo $dataset['data_type']; ?>"></td>
					<td><input class="accession_geo" type="text" name="accession_geo_<?php echo $dataset['id']; ?>" value="<?php echo $dataset['accession_geo']; ?>"></td>
					<td><input class="accession_sra" type="text" name="accession_sra_<?php echo $dataset['id']; ?>" value="<?php echo $dataset['accession_sra']; ?>"></td>
					<td><input class="notes" type="text" name="notes_<?php echo $dataset['id']; ?>" value="<?php echo $dataset['notes']; ?>"></td>
				</tr>
		<?php } // dataset while loop
		} else { // existing datasets check ?>
				<tr>
					<td colspan="8"><em>No datasets to edit!</em></td>
				</tr>
		<?php } ?>
			</tbody>
		</table>
		
		<div class="form-actions">
			<input type="submit" class="btn btn-primary btn-large" name="edit_datasets" id="edit_datasets" value="Edit Datasets">
			&nbsp; <a href="#" id="delete_datasets_button" class="btn btn-large btn-danger popover_button" data-toggle="popover" data-html="true" title="Delete Checked Datasets" data-content="Are you sure? <strong>This cannot be undone</strong>. Data on the server will not be affected. <br><br> <input type='submit' class='btn btn-danger btn-block' name='delete_datasets_submit' value='I&#8217;m sure - delete the datasets'>" data-original-title="Delete Checked Datasets">Delete Checked Datasets</a>
		</div>
		
	</form>
	
</div>

<?php } // if($edit) 

if($add) { ?>

<div class="sidebar-mainpage project-mainpage">

	<?php if(!empty($msg)): ?>
		<div class="container alert alert-<?php echo $error ? 'error' : 'success'; ?>">
			<button type="button" class="close" data-dismiss="alert">×</button>
			<?php echo $error ? '<strong>Error!</strong><br>' : ''; ?> 
			<?php foreach($msg as $var)	echo $var.'<br>'; ?>
		</div>
	<?php endif; ?>
	
	<?php project_header($project); ?>
	
	<div class="alert alert-error" style="display:none;" id="lookup_error">
		<button type="button" class="close" data-dismiss="alert">×</button>
		<div class="msg_content">
		
		</div>
	</div>
	
	<form class="form-inline well action_buttons">
		<div class="input-prepend">
			<button class="btn" type="button" id="btn_add_datasets">Add datasets</button>
			<input style="width:20px;" id="num_datasets" type="text" value="1">			
		</div>
		&nbsp; &nbsp; 
		<?php if(strlen($project['accession_geo']) > 0) {
			$geo_accessions = explode(" ",$project['accession_geo']);
			foreach($geo_accessions as $acc) { ?>
			<button class="btn geo_accession_lookup" type="button" data-accession="<?php echo $acc; ?>">Look up accession <?php echo $acc; ?> &nbsp; <i class="icon-search"></i></button>
		<?php }
		} ?>
		<hr>
		Batch update checked datasets: &nbsp; 
		<input type="text" class="input-small bulk_update" id="name" placeholder="Name"> &nbsp; 
		<input type="text" class="input-small bulk_update" id="species" placeholder="Species"> &nbsp; 
		<input type="text" class="input-small bulk_update" id="cell_type" placeholder="Cell Type"> &nbsp; 
		<input type="text" class="input-small bulk_update" id="data_type" placeholder="Data Type"> &nbsp; 
		<small><em>(overwrites current values)</em></small>
	</form>
	
	<form action="datasets.php?id=<?php echo $project_id; ?>" method="post" class="form-horizontal">
		<table id="add_existing_datasets_table" class="table table-bordered table-condensed table-hover table_form">
			<thead>
				<tr>
					<th class="select"><input type="checkbox" class="select-all"></th>
					<th style="width:30%;">Name</th>
					<th>Species</th>
					<th>Cell Type</th>
					<th>Data Type</th>
					<th>GEO Accession</th>
					<th>SRA Accessions</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="select"><input type="checkbox" class="select-row" id="check_1"></td>
					<td><input class="name" type="text" id="name_1" name="name_1"></td>
					<td><input class="species" type="text" id="species_1" name="species_1"></td>
					<td><input class="cell_type" type="text" id="cell_type_1" name="cell_type_1"></td>
					<td><input class="data_type" type="text" id="data_type_1" name="data_type_1"></td>
					<td><input class="accession_geo" type="text" id="accession_geo_1" name="accession_geo_1"></td>
					<td><input class="accession_sra" type="text" id="accession_sra_1" name="accession_sra_1"></td>
				</tr>
			</tbody>
		</table>
		
		<div class="alert alert-info" style="display:none;" id="lookup_warning">
			<button type="button" class="close" data-dismiss="alert">×</button>
			<strong>Please remember to remove any datasets that you don't need.</strong>
			Adding additional datasets will delay the processing of your project.
		</div>
		
		<div class="form-actions">
			<input type="submit" class="btn btn-primary btn-large" name="add_datasets" id="add_datasets" value="Save All Datasets">
			&nbsp; <a href="#" id="remove_datasets_button" class="btn btn-large btn-danger popover_button">Remove Checked Datasets</a>
		</div>
	</form>
</div>

<?php } // if($add)

include('includes/javascript.php'); ?>
<script src="js/datasets.js" type="text/javascript"></script>
<?php include('includes/footer.php'); ?>