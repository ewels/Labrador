<?php

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
			<a href="files.php?id=<?php echo $project_id; ?>">Files</a>
		</li>
		<li>
			<a href="processing.php?id=<?php echo $project_id; ?>">Processing</a>
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
	
	<a class="labrador_help_toggle pull-right" href="#labrador_help" title="Help"><i class="icon-question-sign"></i></a>
	<?php project_header($project); ?>
	<div class="labrador_help" style="display:none;">
		<div class="well">
			<h3>Reports</h3>
			<p>This page shows you all of the processing reports found within the project directory, associated to the project datasets.</p>
			<p>Reports can be selected using the drop down boxes in the top right of the screen - clicking a name will refresh the page with that report.</p>
			<p>Currently, Labrador supports the following report types:</p>
			
			<dl  class="dl-horizontal">
				<dt>FastQC</dt>
				<dd>Processing reports from <a href="http://www.bioinformatics.babraham.ac.uk/projects/fastqc/" target="_blank">FastQC</a>, gives details about the quality of the raw sequencing data.</dd>
				
				<dt>FastQ Screen</dt>
				<dd>Processing reports from <a href="http://www.bioinformatics.babraham.ac.uk/projects/fastq_screen/" target="_blank">FastQ Screen</a>, a tool to detect which genome(s) raw sequences align to.</dd>
				
				<dt>Bismark Alignment Overview</dt>
				<dd>Alignment reports from <a href="http://www.bioinformatics.babraham.ac.uk/projects/bismark/" target="_blank">Bismark</a>, a tool to map bisulfite converted sequence reads and determine cytosine methylation states.</dd>
				
				<dt>Bismark M-Bias Reports</dt>
				<dd>Metylation-Bias plots from <a href="http://www.bioinformatics.babraham.ac.uk/projects/bismark/" target="_blank">Bismark</a>, shows the methylation proportion across each possible position in the reads.</dd>
				
				<dt>HiCUP Di-Tag Analysis</dt>
				<dd>Classification of paired end read types by <a href="http://www.bioinformatics.babraham.ac.uk/projects/bismark/" target="_blank">HiCUP</a>, a tool for mapping and performing quality control on Hi-C data.</dd>
				
				<dt>HiCUP <em>cis</em>/<em>trans</em> Analysis</dt>
				<dd>Proportion of read pairs falling in <em>cis</em> and <em>trans</em>, as processed by <a href="http://www.bioinformatics.babraham.ac.uk/projects/bismark/" target="_blank">HiCUP</a>.</dd>
				
			</dl>
		</div>
	</div>
	
	
	<?php if(!$report_path){ ?>
	<div class="alert alert-info">No reports found.</div>
	<?php } else {
		echo '<h3>'.$dataset_name.'</h3>';
		$fileinfo = pathinfo(basename($report_path));
		$images = array('jpeg', 'jpg', 'png', 'gif');
		$text = array('out', 'log');
		if(in_array($fileinfo['extension'], $images)){
			echo '<p style="text-align:center;"><img src="ajax/send_file.php?path='.$report_path.'"></p>';
		} else if(in_array($fileinfo['extension'], $text)){
			echo '<pre>'.file_get_contents($data_root.$report_path).'</pre>';
		} else {
			echo '<iframe class="report" id="iframe_report" src="ajax/send_file.php?path='.$report_path.'"></iframe>';
		}
	} // if(!$dataset_id){ } else { ?>

</div>

<?php include('includes/javascript.php'); ?>
<script src="js/jquery.iframe-auto-height.plugin.1.9.3.min.js" type="text/javascript"></script>
<script src="js/reports.js" type="text/javascript"></script>
<?php include('includes/footer.php'); ?>