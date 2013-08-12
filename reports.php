<?php

include('includes/start.php');

if(isset($_GET['id']) && is_numeric($_GET['id'])){
	$project_id = $_GET['id'];
	$projects = mysql_query("SELECT * FROM `projects` WHERE `id` = '".$project_id."'");
	$project = mysql_fetch_array($projects);
} else {
	header("Location: index.php");
}

// Set report type and path
$active_type = false;
$report_path = false;
foreach($report_types as $type => $report_name){
	if(isset($_POST[$type]) && isset($_POST['report_type']) && $_POST['report_type'] == $type){
		$active_type = $type;
		$report_path = $_POST[$type];
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
		<li>
			<a href="#">Processing</a>
		</li>
		<li class="active">
			<a href="reports.php?id=<?php echo $project_id; ?>">Reports</a>
		</li>
	</ul>
</div>

<div class="sidebar-mainpage project-mainpage">
	<form action="reports.php?id=<?php echo $project_id; ?>" method="post" class="pull-right reports_form">
		<input type="hidden" value="" name="report_type" id="report_type">
		
	<?php
	// Find reports. Functions are in config.php
	if(isset($report_types) && count($report_types) > 0){
		// Setup - work out directory and get search terms
		$dir = $data_root.$project['name'];
		$datasets = mysql_query("SELECT * FROM `datasets` WHERE `project_id` = '$project_id'");
		$ds_needles = array();
		if(mysql_num_rows($datasets) > 0){
			while ($dataset = mysql_fetch_array($datasets)){
				$ds_needles[$dataset['name']] = array($dataset['name']);
				foreach(split(" ",$dataset['accession_geo']) as $geo){
					array_push($ds_needles[$dataset['name']], $geo);
				}
				foreach(split(" ",$dataset['accession_sra']) as $sra){
					array_push($ds_needles[$dataset['name']], $sra);
				}
			}
		}
		// Go through each report type
		foreach($report_types as $type => $report_name){
			// get matching filenames
			$paths = array();
			$it = new RecursiveDirectoryIterator($dir);
			foreach(new RecursiveIteratorIterator($it) as $file) {
				if(report_match ($file, $type)){
					$paths[] = $file->getPathname();
				}
			}
			sort($paths);
			if(count($paths) > 0){
				// Match up report filenames to datasets
				$count = 0;
				$output = '<option>[ Select Report ]</option>';
				foreach($ds_needles as $dsname => $needles){
					$output .= '<optgroup label="'.$dsname.'">';
					foreach($paths as $path){
						$path = substr($path, strlen($data_root));
						foreach($needles as $needle){
							if(stripos($path, $needle)){
								if(!$report_path){
									$report_path = $path;
									$active_type = $type;
								}
								$output .= '<option value="'.$path.'"';
								if($report_path == $path && $active_type == $type){
									$output .= ' selected="selected"';
									$dataset_name = $dsname;
								}
								$output .= '>'.report_naming($path, $type).'</option>';
								$count++;
								break;
							}
						}
					}
					$output .= '</optgroup>';
				}
				if($count > 0 ){
					echo '<label>'.$report_name.': <select name="'.$type.'" class="select_report_dataset" class="input-xlarge" data-type="'.$type.'">'.$output.'</select></label>';
				}
			}
		}
	}
	
	?>
	
	<div style="clear:both;"></div>
	</form>	
		
	<?php project_header($project); ?>
	
	<?php if(!$report_path){ ?>
	<div class="alert alert-info">No reports found.</div>
	<?php } else {
		echo '<h3>'.$dataset_name.'</h3>';
		$fileinfo = pathinfo(basename($report_path));
		$images = array('jpeg', 'jpg', 'png', 'gif');
		if(in_array($fileinfo['extension'], $images)){
			echo '<p style="text-align:center;"><img src="ajax/send_file.php?path='.$report_path.'"></p>';
		} else {
			echo '<iframe class="report" id="iframe_report" src="ajax/send_file.php?path='.$report_path.'"></iframe>';
		}
	} // if(!$dataset_id){ } else { ?>

</div>

<?php include('includes/javascript.php'); ?>
<script src="js/jquery.iframe-auto-height.plugin.1.9.3.min.js" type="text/javascript"></script>
<script src="js/reports.js" type="text/javascript"></script>
<?php include('includes/footer.php'); ?>