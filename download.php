<?php

include('includes/start.php');

if(isset($_GET['id']) && is_numeric($_GET['id'])){
	$project_id = $_GET['id'];
	$projects = mysql_query("SELECT * FROM `projects` WHERE `id` = '".$project_id."'");
	$project = mysql_fetch_array($projects);
} else {
	header("Location: index.php");
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
			<a href="#">Processing</a>
		</li>
		<li>
			<a href="reports.php?id=<?php echo $project_id; ?>">Reports</a>
		</li>
	</ul>
</div>

<div class="sidebar-mainpage project-mainpage">

	<?php /* * / if(!empty($_POST['edit_datasets']) && $_POST['edit_datasets'] == 'Edit Datasets'){
		echo '<pre>'.print_r($_POST, true).'</pre>';
	} /* */ ?>
	<?php if(!empty($msg)): ?>
		<div class="container alert alert-<?php echo $error ? 'error' : 'success'; ?>">
			<button type="button" class="close" data-dismiss="alert">×</button>
			<?php echo $error ? '<strong>Error!</strong><br>' : ''; ?> 
			<?php foreach($msg as $var)	echo $var.'<br>'; ?>
		</div>
	<?php endif; ?>
	
	<h1>
		<a class="btn pull-right" href="datasets.php?edit=<?php echo $project['id']; ?>">Edit Datasets</a>
		<a style="margin-right:15px;" class="btn pull-right" href="datasets.php?edit=<?php echo $project['id']; ?>">Add Datasets</a>
		<?php echo $project['name']; ?>
		<?php echo accession_badges ($project['accession_geo'], 'geo'); ?>
		<?php echo accession_badges ($project['accession_sra'], 'sra'); ?>
		<?php echo accession_badges ($project['accession_ena'], 'ena'); ?>
		<?php echo accession_badges ($project['accession_ddjb'], 'ddjb'); ?>
	</h1>
	<p>
		<?php if($project['status'] == 'Processing Complete'){ $status_label = 'label-success'; }
		else if($project['status'] == 'Currently Processing'){ $status_label = 'label-warning'; }
		else if($project['status'] == 'Not Started'){ $status_label = 'label-important'; }
		else { $status_label = ''; } ?>
		<span style="margin-right:20px;" class="label <?php echo $status_label; ?>"><?php echo $project['status']; ?></span>	
		
		<?php if(!empty($project['contact_name']) && !empty($project['contact_email'])) { echo '<small style="white-space:nowrap; margin-right:20px;">Requested by: <a href="mailto:'.$project['contact_email'].'">'.$project['contact_name'].'</a></small>'; } ?>
		<?php if(empty($project['contact_name']) && !empty($project['contact_email'])) { echo '<small style="white-space:nowrap; margin-right:20px;">Requested by: <a href="mailto:'.$project['contact_email'].'">'.$project['contact_email'].'</a></small>'; } ?>
				
		<?php if($project['assigned_to'] != ''){ ?>
			<small style="white-space:nowrap; margin-right:20px;">Assigned to: <a href="mailto:<?php echo $project['assigned_to']; ?>"><?php echo $project['assigned_to']; ?></a></small>
		<?php } ?>
	</p>
	
	<applet code="biz.jupload.jdownload.Manager" archive="includes/jdownload/jdownload.jar" width="100%" height="500px" name="JDownload" mayscript="mayscript" alt="JDownload by www.jupload.biz">
	 <!-- Java Plug-In Options -->
	 <param name="progressbar" value="true">
	 <param name="boxmessage" value="Loading JDownload Applet ...">
	 <!-- URL pointing to the data structure containing the list of files and folders to download -->
	 <param name="dataURL" value="includes/download_xml.php">
	 <!-- Show or Hide the controls. If hidden (set all to 'false'), remote control the applet using JavaScript buttons -->
	 <param name="showExplorer" value="true">
	 <param name="showControls" value="true">
	 <param name="showBrowser" value="true">
	 <param name="showStatus" value="true">
	 <!-- Error message for browsers not supporting Java applets -->
	 Your browser does not support applets, or you have disabled applets in your options.
	 To use this applet, please update your Java. You can get it from <a href="http://www.java.com/">java.com</a>
	</applet>

</div>

<?php include('includes/javascript.php'); ?>
<script src="js/download.js" type="text/javascript"></script>
<?php include('includes/footer.php'); ?>