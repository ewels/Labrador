<?php

##########################################################################
# Copyright 2013, Philip Ewels (phil.ewels@babraham.ac.uk)               #
#                                                                        #
# This file is part of Labrador.                                         #
#                                                                        #
# Labrador is free software: you can redistribute it and/or modify       #
# it under the terms of the GNU General Public License as published by   #
# the Free Software Foundation, either version 3 of the License, or      #
# (at your option) any later version.                                    #
#                                                                        #
# Labrador is distributed in the hope that it will be useful,            #
# but WITHOUT ANY WARRANTY; without even the implied warranty of         #
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the          #
# GNU General Public License for more details.                           #
#                                                                        #
# You should have received a copy of the GNU General Public License      #
# along with Labrador.  If not, see <http://www.gnu.org/licenses/>.      #
##########################################################################

include('includes/start.php');

if(isset($_GET['id']) && is_numeric($_GET['id'])){
	$project_id = $_GET['id'];
	$projects = mysql_query("SELECT * FROM `projects` WHERE `id` = '".$project_id."'");
	if(mysql_num_rows($projects) == 1){
		$project = mysql_fetch_array($projects);
	} else {
		header("Location: index.php");
	}
} else {
	header("Location: index.php");
}

if($admin && isset($_POST['delete_processing'])){
	$processed_q = mysql_query("SELECT * FROM `processing` WHERE `project_id` = '$project_id'");
	if(mysql_num_rows($processed_q) > 0){
		while($processed = mysql_fetch_array($processed_q)){
			if(isset($_POST['check_'.$processed['id']]) && $_POST['check_'.$processed['id']] == 'on'){
				if(!mysql_query("DELETE FROM `processing` WHERE `id` = '".$processed['id']."'")){
					$error = true;
					$msg[] = 'Could not delete processing record: '.mysql_error();
				}
			}
		}
		if(!$error){
			$msg[] = 'Processing records deleted';
		}
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
		<li>
			<a href="datasets.php?id=<?php echo $project_id; ?>">Datasets</a>
		</li>
		<li class="active">
			<a href="processing.php?id=<?php echo $project_id; ?>">Processing</a>
		</li>
		<li>
			<a href="reports.php?id=<?php echo $project_id; ?>">Reports</a>
		</li>
		<li>
			<a href="files.php?id=<?php echo $project_id; ?>">Files</a>
		</li>
	</ul>
</div>

<?php if(!isset($_GET['create']) || !$admin) { ?>

<div class="sidebar-mainpage project-mainpage">
	<?php if(!empty($msg)): ?>
		<div class="container alert alert-<?php echo $error ? 'error' : 'success'; ?>">
			<button type="button" class="close" data-dismiss="alert">×</button>
			<?php echo $error ? '<strong>Error!</strong><br>' : ''; ?> 
			<?php foreach($msg as $var)	echo $var.'<br>'; ?>
		</div>
	<?php endif; ?>
	
	<?php if($admin){ ?>
		<a class="btn btn-primary pull-right" href="processing.php?id=<?php echo $project['id']; ?>&amp;create">Create New Processing Script</a>
	<?php } ?>
	<a class="labrador_help_toggle pull-right" href="#labrador_help" title="Help"><i class="icon-question-sign"></i></a>
	<?php project_header($project); ?>
	<div class="labrador_help" style="display:none;">
		<div class="well">
			<h3>Processing Page</h3>
			<p>This page shows records of any dataset processing held by Labrador. Administrators can use this page to quickly create scripts to process data,
			these are associated to their respective datasets and presented below for later reference.</p>
			<p>You can read the full <a href="<?php echo $labrador_url; ?>/documentation/">Labrador documenation here</a>.</p>
		</div>
	</div>
	
	
	<?php
	$processing_sql = "SELECT * FROM `processing` WHERE `project_id` = '$project_id' ORDER BY `created` DESC";
	$processed_q = mysql_query($processing_sql);
	if(mysql_num_rows($processed_q) > 0){
	?>
	<?php if($admin) { ?>
	<form action="processing.php?id=<?php echo $project_id; ?>" method="post" class="form-horizontal">
	<?php } ?>
	<table id="show_processing" class="table table-bordered table-condensed table-hover">
		<thead>
			<tr>
			<?php if($admin) { ?>
				<th class="select"><input type="checkbox" class="select-all"></th>
			<?php } ?>
				<th style="width:20%;">Dataset Name</th>
				<th style="width:10%;">Date Saved</th>
				<th>Processing Code</th>
			</tr>
		</thead>
		<tbody>
		<?php while($processed = mysql_fetch_array($processed_q)) {
			$dataset_q = mysql_query("SELECT * FROM `datasets` WHERE `id` = '".$processed['dataset_id']."'");
			$dataset = mysql_fetch_array($dataset_q);
			?>
			<tr>
			<?php if($admin) { ?>
				<td class="select"><input type="checkbox" class="select-row" id="check_<?php echo $processed['id']; ?>" name="check_<?php echo $processed['id']; ?>"></td>
			<?php } ?>
				<td><?php echo $dataset['name']; ?></td>
				<td><?php echo date('H:i, jS M Y', $processed['created']); ?></td>
				<td>
					<div class="processing_teaser" id="processing_teaser_<?php echo $processed['id']; ?>">
						<a href="#" style="float:right;" class="show_more_link" id="show_more_<?php echo $processed['id']; ?>">[ show full ]</a>
						<pre><?php echo substr($processed['commands'], 0, 150); ?></pre>
					</div>
					<div class="more_commands" style="display:none;" id="more_<?php echo $processed['id']; ?>">
						<pre><?php echo nl2br(htmlspecialchars($processed['commands'])); ?></pre>
						<a href="#" class="hide_more_link" id="hide_more_<?php echo $processed['id']; ?>">[ hide full ]</a>
					</div>
				</td>
			</tr>
		<?php } // while $processed ?>
		</tbody>
	</table>
	<?php if($admin) { ?>
		<div class="form-actions">
			<a class="btn btn-large btn-primary" href="processing.php?id=<?php echo $project['id']; ?>&amp;create">Create New Processing Script</a> &nbsp; 
			<input type="submit" class="btn btn-large btn-danger" name="delete_processing" value="Delete Checked Processing Records">
		</div>
	</form>	
	<?php } ?>
	<?php } else { ?>
	<p><em>No processing records found.</em></p>
	<?php } ?>	
</div>

<?php } else { // isset($_GET['create'] ?>

<div class="sidebar-mainpage project-mainpage">
		
	<?php project_header($project); ?>
	
	<?php
	$dataset_query = "SELECT * FROM `datasets` WHERE `project_id` = '$project_id'";
	$datasets = mysql_query($dataset_query);
	$existing_datasets = array();
	if(mysql_num_rows($datasets) > 0){
	?>
	
	<form class="form-horizontal" action="processing.php?id=<?php echo $project_id; ?>" method="post">
		<fieldset>
			<legend>Step 1: Choose Datasets</legend>
			<table id="processing_table" class="table table-bordered table-condensed table-hover">
				<thead>
					<tr>
						<th class="select"><input type="checkbox" class="select-all"></th>
						<th>Name</th>
						<th>Species</th>
						<th>Cell Type</th>
						<th>Data Type</th>
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
			
		</fieldset>
		<fieldset>
			<legend>Step 2: Choose Processing</legend>
			<div class="well">
				<input type="hidden" name="project_id" id="project_id" value="<?php echo $project_id; ?>">
				<table class="processing_table">
					<tr>
						<th><label for="server">Server:</label></th>
						<td><select class="server" name="server" id="server">
							<?php foreach($processing_servers as $server => $vars){ echo '<option value="'.$server.'" data-queueing="'.$vars['queueing'].'">'.$vars['name'].'</option>'; } ?>
						</select></td>
						<td rowspan="2">
							<span class="help-block">Available variables:
								<code><abbr title="File Name Base">{{fn}}</abbr></code>
								<code><abbr title="Wait for previous job in script to complete (queueing servers only)">{{hold_prev}}</abbr></code>
								<code><abbr title="SRA code">{{sra}}</abbr></code>
								<code><abbr title="SRA download URL">{{sra_url}}</abbr></code>
								<code><abbr title="Path to Genome Directory (server specific)">{{genome_path}}</abbr></code>
								<code><abbr title="Assigned To E-mail">{{assigned_email}}</abbr></code>
								<code><abbr title="Dataset Name">{{dataset}}</abbr></code>
								<code><abbr title="Project Name">{{project}}</abbr></code>
								<code><abbr title="Date and Time when executed">{{time}}</abbr></code>
							</span>
						</td>
					</tr>
					<tr>
						<th><label for="genome">Genome:</label></th>
						<td><select class="genome" name="genome" id="genome" disabled="disabled">
							<option value="">[ select genome ]</option>
							<?php foreach($genomes as $genome => $paths){ echo '<option>'.$genome.'</option>'; } ?>
						</select></td>
					</tr>
					<tr>
						<th>Steps:</th>
						<td colspan="2">
							<button class="btn" id="add_processing_step">Add Processing Step</button> &nbsp;
							<button class="btn" id="delete_processing_step" disabled="disabled">Delete Last Processing Step</button>
						</td>
	
					</tr>
					<tr>
						<th>Shortcuts:</th>
						<td colspan="2">
						<?php
						$first = true;
						foreach ($processing_pipelines as $id => $pipeline){
							if(!$first){
								echo ' &nbsp; / &nbsp; ';
							}
							$first = false;
							echo '<a href="#" id="'.$id.'" class="processing_shortcut">'.$pipeline['name'].'</a>';
						}
						?>
						</td>
					</tr>
				</table>

				<hr>
				<table class="processing_steps_table processing_table">
					<tr> 
						<th>Step 1:</th>
						<td class="dropdown">
							<select class="processing_type" name="processing_type_1" id="processing_type_1">
								<option value="manual" data-unit="accession_sra" data-genome="true">Manual Text Entry</option>
								<?php foreach($processing_steps as $type => $step){
									echo '<optgroup label="'.$type.'">';
									foreach ($step as $id => $vars){
										echo '<option value="'.$id.'" data-unit="'.$vars['unit'].'" data-genome="'.$vars['requires_genome'].'">'.$vars['name'].'</option>';
									}
									echo '</optgroup>';
								} ?>
							</select>
						</td>
						<td>
							<textarea class="processing_step" name="processing_step_1" id="processing_step_1"></textarea>
						</td>
					</tr>
				</table>
			</div>
		</fieldset>
		<fieldset>
			<legend>Step 3: Preview Bash Script</legend>
			<div class="well" id="bash_preview">
				<p><em>Select some datasets and pipelines to create your bash script. A preview will show here.</em></p>
			</div>
		</fieldset>
		<fieldset>
			<legend>Step 4: Save Script</legend>
			<?php $bash_fn = $project['name'].'_labrador_bash_'.date('d_m_Y').'.bash'; ?>
			<p>Script will be saved to <code><?php echo $data_root.$project['name']; ?>/<input type="text" id="bash_script_fn" value="<?php echo $bash_fn; ?>" class="input-xxlarge" style="font-family:monospace;"></code>
				<span class="help-block">If this file already exists, it will be overwritten.</span></p>
			<div class="form-actions">
				<button type="submit" id="save_bash_script" class="btn btn-large btn-primary">Save Bash Script</button>
			</div>
		</fieldset>
	</form>
	
	<div style="clear:both;"></div>
	
	<?php } else { ?>
	<p><em>No datasets found.</em></p>
	<?php } ?>
	
	<?php $processing_pipelines; ?>

</div>

<?php } // isset($_GET['create']) ?>

<?php include('includes/javascript.php');

if(isset($_GET['create'])) { ?>
<script type="text/javascript">
	var processing_modules = new Array();
<?php
	foreach ($processing_modules as $server => $modules){
		echo "\tprocessing_modules['$server'] = new Array();\n";
		foreach($modules as $type => $module){
			echo "\tprocessing_modules['$server']['$type'] = '$module';\n";
		}
	}
?>

	var processing_pipelines = new Array();
<?php
	foreach ($processing_pipelines as $id => $pipeline){
		echo "\tprocessing_pipelines['$id'] = new Array();\n";
		foreach ($pipeline['steps'] as $step){
			echo "\tprocessing_pipelines['$id'].push('$step');\n";
		}
	}
?>
</script>
<?php } // isset($_GET['create'])  ?>
<script src="js/processing.js" type="text/javascript"></script>
<?php include('includes/footer.php'); ?>