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
				
 		<div class="pull-right visible-desktop home-lab">
			<a data-toggle="modal" href="#tutorial_video_modal"><img src="img/screencast_thumb.png"></a>
		</div>
		<h1><?php echo $homepage_title; ?> <small><?php echo $homepage_subtitle; ?></small></h1>
	
		<p class="lead">You can use labrador to find and download processed data or request new datasets.
		Projects are annotated with how they were processed. <a class="labrador_help_toggle" href="#labrador_help" title="Help"><i class="icon-question-sign"></i></a></p>
		
		<div class="labrador_help" style="display:none;">
			<div class="well">
				<h2>Help!</h2>
				<p>Each page within Labrador has this question mark icon in the top right. Click the icon for contextual help about the page that you are on.
				<strong>You can also read the full <a href="<?php echo $labrador_url; ?>/documentation/">Labrador documenation</a></strong>.</p>
				
				<h3>What is Labrador?</h3>
				<p>Labrador is a web based tool to manage projects and automate the processing of publicly available datasets.</p>
				<p>Researchers can use it to search through previously processed data, find how it was analysed, read processing reports and download the relevant files to their computers.
				If a required dataset isn't yet available, they can <a href="project.php">Create a New Project</a> - this information about the required data sets is then passed on to your resident bioinformaticians, who can process it for you.
				The status of projects is tracked, and everything is kept together in a logical place.</p>
				<p>Administrators (bioinformaticians) can delegate the process of choosing required data to researchers. Labrador automatically retrieves public data accession numbers and 
				can write bash scripts to download and process data. This helps to standardise in-house processing and streamline pipelines.</p>
				
				<h3>What does this page do?</h3>
				<p>You're currently viewing the home page of Labrador. Here, you can browse all of the projects in the system. Rows in the table are colour-coded to indicate their current status
				<em>(complete / currently processing / not started)</em>.</p>
				<p>You can quickly filter the projects by their species and data type using the tools on the left. Options within a filter group combine as OR, options between groups combine as AND.
				If you're looking for something specific you can filter by the first letter of the project's name, or just use a free text filter at the top.
				These tools don't interrogate all of the information held within Labrador - if you would like a more complete search please use the Search Bar at the top.</p>
				<p>Clicking on a project row will take you to that project's page: here you can find out more information about the project and access it's datasets and reports.</p>
				
				<h3>Data structure</h3>
				<p>Labrador maintains records of each project in a hierarchy, summarised below:</p>
				<ul>
					<li>Projects
						<ul>
							<li>Description, status, contacts</li>
							<li>Publications</li>
							<li>Datasets
								<ul>
									<li>Downloads</li>
									<li>Processing Records</li>
									<li>Reports</li>
								</ul>
							</li>
						</ul>
					</li>
				</ul>
				<p>The name of each project corresponds to a folder with the same name on the server. <em>eg.</em> 'Norris_2013' would correspond to <code><?php echo $data_root; ?>Norris_2013/</code></p>
			</div>
		</div>
		
		
		
		
		<p>You can use the table below to browse the projects and datasets. 
		You can filter the visible data using the tools on the left.
		If you're looking for something really specific, try the search bar at the top of the page.</p>
		
		<p id="filter_status_bar"><strong>Key:</strong>
		
			<input type="checkbox" name="filter_status" id="filter_status_pc" value="Processing Complete" <?php if(!isset($_GET['assigned_projects']) && !isset($_GET['unassigned'])) { echo 'checked="checked"'; } ?>>
			<input type="checkbox" name="filter_status" id="filter_status_cp" value="Currently Processing" checked="checked">
			<input type="checkbox" name="filter_status" id="filter_status_ns" value="Not Started" checked="checked">
			<input type="checkbox" name="filter_status" id="filter_status_nf" value="Directory Not Found" checked="checked">
			
			<label for="filter_status_pc" <?php if(!isset($_GET['assigned_projects']) && !isset($_GET['unassigned'])) { echo 'class="checked"'; } ?>><span></span> Processing Complete</label>
			<label for="filter_status_cp" class="checked"><span class=" info"></span> Currently Processing</label>
			<label for="filter_status_ns" class="checked"><span class=" error"></span> Not Started</label>
			<label for="filter_status_nf" class="checked"><span class=" warning"></span> Directory not found</label>
		</p>
		
		<table id="paper-browser-table" class="table table-hover table-condensed table-bordered sortable">
			<thead>
				<tr>
					<th data-sort="string-ins" style="width:10%;">Name</th>
					<th data-sort="int" style="width:5%;">Datasets</th>
					<th data-sort="string-ins" style="width:15%;">Species</th>
					<th data-sort="string-ins" style="width:40%;">Cell Types</th>
					<th data-sort="string-ins" style="width:30%;">Data Types</th>
				</tr>
			</thead>
			<tbody>
			<?php 
			$sql = "SELECT *, `projects`.`id` AS `pid` FROM `projects`";
			if(isset($_GET['my_projects'])){
				$sql .= sprintf(" LEFT JOIN `project_contacts` on `projects`.`id` = `project_contacts`.`project_id` WHERE `project_contacts`.`user_id` = '%d'", $user['id']);
			} else if($admin && isset($_GET['assigned_projects']) && filter_var($_GET['assigned_projects'], FILTER_VALIDATE_EMAIL)){
				$assigned = filter_var($_GET['assigned_projects'], FILTER_SANITIZE_EMAIL);
				$sql .= " WHERE `assigned_to` = '".mysql_real_escape_string($assigned)."'";
			} else if($admin && isset($_GET['assigned_projects'])){
				$sql .= " WHERE `assigned_to` = '".$user['email']."'";
			} else if($admin && isset($_GET['unassigned'])){
				$sql .= " WHERE `assigned_to` IS NULL OR `assigned_to` = ''";
			}
			$sql .=  " ORDER BY `name`";
			$projects = mysql_query($sql);
			if(mysql_num_rows($projects) > 0){
				while($project = mysql_fetch_array($projects)){
					
					// Check directory exists
					if(file_exists($data_root.$project['name']) || $project['status'] == 'Not Started' || $project['status'] == 'Currently Processing' || $project['status'] == ''){
						$file_exists = true;
					} else {
						$file_exists = false;
						$project['status'] = 'Directory Not Found';
					}
					
					// Find papers
					$papers_q = mysql_query("SELECT * FROM `papers` WHERE `project_id` = '".$project['pid']."'");
					$papers = array();
					while($paper = mysql_fetch_array($papers_q)){
						$authors = explode(' ', $paper['authors']);
						$papers[] = $authors[0].' '.$paper['journal'].' ('.$paper['year'].')';
					}
					
					// Find datasets
					$datasets = mysql_query("SELECT * FROM `datasets` WHERE `project_id` = '".$project['pid']."'");
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
					<tr id="project_<?php echo $project['pid']; ?>" data-status="<?php echo $project['status']; ?>" class="project <?php
						if($project['status'] == 'Not Started' || ($project['status'] == '' && !file_exists($data_root.$project['name']))){
							echo "error";
						} else if($project['status'] == 'Currently Processing'){
							echo "info";
						} else if(!$file_exists){
							echo "warning";
						} ?>">
						<td class="project_name">
							<a href="project.php?id=<?php echo $project['pid']; ?>">
								<?php echo $project['name']; 
								if($project['status'] == 'Not Started'){
									echo ' &nbsp; <i class="icon-time" title="Project has not yet started processing"></i>';
								} else if($project['status'] == 'Currently Processing'){
									echo ' &nbsp; <i class="icon-pencil" title="Project is currently being processed by '.$project['assigned_to'].'"></i>';
								} else if(!$file_exists){
									echo ' &nbsp; <i class="icon-folder-open" title="Directory not found"></i><i class="icon-warning-sign" title="Directory not found"></i>';
								} ?>
							</a>
						</td>
						<td class="num num_datasets">
							<a href="project.php?id=<?php echo $project['pid']; ?>">
								<?php echo $num_datasets; ?>
							</a>
						</td>
						<td class="species">
							<a href="project.php?id=<?php echo $project['pid']; ?>">
								<?php echo implode(', ', $species); ?>
							</a>
						</td>
						<td class="cell_type">
							<a href="project.php?id=<?php echo $project['pid']; ?>">
								<?php echo implode(', ', $cell_types); ?>
							</a>
						</td>
						<td class="data_type">
							<a href="project.php?id=<?php echo $project['pid']; ?>">
								<?php echo implode(', ', $data_types); ?>
							</a>
						</td>
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
			<p><small><a href="<?php echo $labrador_url; ?>/documentation/">Read the Labrador Documenation here</a>.</small></p>
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
	
	<!-- Tutorial Video Modal -->
	<div id="tutorial_video_modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="tutorial_video_modal_label" aria-hidden="true">
		<div class="modal-header">
			<a class="close" data-dismiss="modal">&times;</a>
			<h3 id="tutorial_video_modal_label">Labrador Tutorial Video</h3>
		</div>
		<div class="modal-body">
			<iframe width="530px" height="298px" src="//www.youtube.com/embed/m03HTQtSGFg" frameborder="0" allowfullscreen></iframe>
		</div>
	</div>
	
	<?php if(function_exists('labrador_login_modal')){ labrador_login_modal(); } ?>
	
	<?php include('includes/javascript.php'); ?>
	<script src="js/home.js" type="text/javascript"></script>
	</body>
</html>
