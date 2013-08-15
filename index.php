<?php

include('includes/start.php');

include('includes/header.php');
?>
	

				

		<div class="homepage sidebar-mainpage">		
				
		<?php if(!empty($msg)): ?>
		<div class="container alert alert-<?php echo $error ? 'error' : 'success'; ?>">
			<button type="button" class="close" data-dismiss="alert">×</button>
			<?php echo $error ? '<strong>Error!</strong><br>' : ''; ?> 
			<?php foreach($msg as $var)	echo $var.'<br>'; ?>
		</div>
		<?php endif; ?>
				
       	<h1>Labrador Dataset Browser <small>A database of datasets processed by the BI Bioinformatics group.</small></h1>
	
	 <img class="pull-right visible-desktop" style="margin-top:-50px; height:200px;" src="img/puppies/puppy_2.jpg" title="woof!">
		<p class="lead">You can use labrador to find and download data. Datasets are annotated with how they were processed. You can use the system to request new datasets.</p>
		<p>You can use the table below to browse the projects and datasets. 
		You can filter the visible data using the tools on the left.
		If you're looking for something really specific, try the search bar at the top of the page.</p>
		
		<p><strong>Key:</strong>
			<span class="homepage_key"></span> Processing Complete
			<span class="homepage_key info"></span> Currently Processing 
			<span class="homepage_key error"></span> Not Started 
			<span class="homepage_key warning"></span> Directory not found
		</p>
		
		<table id="paper-browser-table" class="table table-hover table-condensed table-bordered">
			<thead>
				<tr>
					<th style="width:10%;">Name</th>
					<th style="width:5%;">Datasets</th>
					<th style="width:15%;">Species</th>
					<th style="width:40%;">Cell Types</th>
					<th style="width:30%;">Data Types</th>
				</tr>
			</thead>
			<tbody>
			<?php $projects = mysql_query("SELECT * FROM `projects` ORDER BY `name`");
			if(mysql_num_rows($projects) > 0){
				while($project = mysql_fetch_array($projects)){
					
					// Check directory exists
					if(file_exists($data_root.$project['name'])){
						$file_exists = true;
					} else {
						$file_exists = false;
					}
					
					// Find papers
					$papers_q = mysql_query("SELECT * FROM `papers` WHERE `project_id` = '".$project['id']."'");
					$papers = array();
					while($paper = mysql_fetch_array($papers_q)){
						$authors = explode(' ', $paper['authors']);
						$papers[] = $authors[0].' '.$paper['journal'].' ('.$paper['year'].')';
					}
					
					// Find datasets
					$datasets = mysql_query("SELECT * FROM `datasets` WHERE `project_id` = '".$project['id']."'");
					$num_datasets = mysql_num_rows($datasets);
					$species = array();
					$cell_types = array();
					$data_types = array();
					while($dataset = mysql_fetch_array($datasets)){
						if(!in_array($dataset['species'], $species)){
							$species[] = $dataset['species'];
						}
						if(!in_array($dataset['cell_type'], $cell_types)){
							$cell_types[] = $dataset['cell_type'];
						}
						if(!in_array($dataset['data_type'], $data_types)){
							$data_types[] = $dataset['data_type'];
						}
					}
					?>
					<tr id="project_<?php echo $project['id']; ?>" class="project <?php
						if($project['status'] == 'Not Started'){
							echo "error";
						} else if($project['status'] == 'Currently Processing'){
							echo "info";
						} else if(!$file_exists){
							echo "warning";
						} ?>">
						<td class="project_name"><?php echo $project['name']; 
						if($project['status'] == 'Not Started'){
							echo ' &nbsp; <i class="icon-time" title="Project has not yet started processing"></i>';
						} else if($project['status'] == 'Currently Processing'){
							echo ' &nbsp; <i class="icon-pencil" title="Project is currently being processed"></i>';
						} else if(!$file_exists){
							echo ' &nbsp; <i class="icon-folder-open" title="Directory not found"></i><i class="icon-warning-sign" title="Directory not found"></i>';
						} ?></td>
						<td class="num num_datasets"><?php echo $num_datasets; ?></td>
						<td class="species"><?php echo implode(', ', $species); ?></td>
						<td class="cell_type"><?php echo implode(', ', $cell_types); ?></td>
						<td class="data_type"><?php echo implode(', ', $data_types); ?></td>
					</tr>
				<?php } ?>
				<tr id="no_datasets" style="display:none;">
					<td colspan="5"><em>No datasets found</em></td>
				</tr>
				<?php } else { ?>
				<tr id="no_datasets">
					<td colspan="5"><em>No datasets found</em></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
		
		
		<img class="pull-right visible-desktop" src="img/puppies/sleepy_puppy_1_300px.jpg" style="margin: 0 -20px -40px 0;">
		<div class="clearfix"></div>
		<footer>
			<hr>
			<p><small>Labrador Data Management System. Written by <a href="http://phil.ewels.co.uk" target="_blank">Phil Ewels</a> at the <a href="http://www.babraham.ac.uk" target="_blank">Babraham Institute</a>, Cambridge, UK.</small></p>
		</footer>
		
		
	</div>
	<div class="homepage sidebar-nav">
		<h2>Filters</h2>
		<ul class="nav nav-list filters">
			
			<li class="nav-header">Text Filter</li>
			<li class="text-filter"><input type="text" id="homepage_text_filter"></li>
			
			<li class="nav-header">Project Name</li>
			<?php foreach (range('A', 'Z') as $i){
				echo '<li class="alphabetical-filter"><a href="#">'.$i.'</a></li> ';
			} ?>
			<li class="alphabetical-filter"><a href="#">0-9</a></li>
			
			<li class="nav-header">Species</li>
			<?php
			$query = "SELECT `species` FROM `datasets` GROUP BY `species` ORDER BY count(`species`) DESC";
			$species_q = mysql_query($query);
			if(mysql_num_rows($species_q) > 0){
				while($species = mysql_fetch_array($species_q)){
					echo '<li class="species-filter"><a href="#">'.$species['species'].'</a></li>';
				}
			}
			?>
			
			<li class="nav-header">Data Types</li>
			<?php
			$query = "SELECT `data_type` FROM `datasets` GROUP BY `data_type` ORDER BY count(`data_type`) DESC";
			$data_type_q = mysql_query($query);
			if(mysql_num_rows($data_type_q) > 0){
				while($data_type = mysql_fetch_array($data_type_q)){
					echo '<li class="datatype-filter"><a href="#">'.$data_type['data_type'].'</a></li>';
				}
			}
			?>
			
		</ul>
		
	</div><!--/.sidebar-nav -->
		
		
	</div> <!-- /container -->
	
	
	<?php include('includes/javascript.php'); ?>
	<script src="js/jquery.validate.min.js" type="text/javascript"></script>
	<script src="js/home.js" type="text/javascript"></script>
	</body>
</html>
