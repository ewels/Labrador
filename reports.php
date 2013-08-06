<?php

include('includes/start.php');

if(isset($_GET['id']) && is_numeric($_GET['id'])){
	$project_id = $_GET['id'];
	$projects = mysql_query("SELECT * FROM `projects` WHERE `id` = '".$project_id."'");
	$project = mysql_fetch_array($projects);
} else {
	header("Location: index.php");
}

$report_path = false;
$show_fastqc = false;
$show_fastq_screen = false;
$show_alignment_overview = false;
$show_m_bias = false;
$dataset_name = false;
if(isset($_POST['fastqc']) && isset($_POST['report_type']) && $_POST['report_type'] == 'fastqc'){
	$report_path = $_POST['fastqc'];
	$show_fastqc = true;
} else if(isset($_POST['fastq_screen']) && isset($_POST['report_type']) && $_POST['report_type'] == 'fastq_screen'){
	$report_path = $_POST['fastq_screen'];
	$show_fastq_screen = true;
} else if(isset($_POST['alignment_overview']) && isset($_POST['report_type']) && $_POST['report_type'] == 'alignment_overview'){
	$report_path = $_POST['alignment_overview'];
	$show_alignment_overview = true;
} else if(isset($_POST['m_bias']) && isset($_POST['report_type']) && $_POST['report_type'] == 'm_bias'){
	$report_path = $_POST['m_bias'];
	$show_m_bias = true;
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
		<input type="hidden" value="fastqc" name="report_type" id="report_type">
		
	<?php
	// Find FastQC reports
	$fastqc_paths = array();
	$dir = $data_root.$project['name'];
	$it = new RecursiveDirectoryIterator($dir);
	foreach(new RecursiveIteratorIterator($it) as $file) {
		if(basename($file) == 'fastqc_report.html'){
			$fastqc_paths[] = $file->getPathname();
		}
	}
	sort($fastqc_paths);
	$fastqc_count = 0;
	$fastqc = '<option>[ Select Report ]</option>';
	
	// Load datasets and match against fastqc reports
	$dataset_query = "SELECT * FROM `datasets` WHERE `project_id` = '$project_id'";
	$datasets = mysql_query($dataset_query);
	$reports = array();
	if(mysql_num_rows($datasets) > 0){
		while ($dataset = mysql_fetch_array($datasets)){
			$fastqc .= '<optgroup label="'.$dataset['name'].'">';
			$needles = array();
			$needles[] = $dataset['name'];
			foreach(split(" ",$dataset['accession_geo']) as $geo){
				array_push($needles, $geo);
			}
			foreach(split(" ",$dataset['accession_sra']) as $sra){
				array_push($needles, $sra);
			}
			foreach($fastqc_paths as $path){
				$path = substr($path, strlen($data_root));
				foreach($needles as $needle){
					if(stripos($path, $needle)){
						if(!$report_path){
							$report_path = $path;
							$show_fastqc = true;
						}
						$fastqc .= '<option value="'.$path.'"';
						if($report_path == $path && $show_fastqc){
							$fastqc .= ' selected="selected"';
							$dataset_name = $dataset['name'];
						}
						$fastqc .= '>'.substr(basename(dirname($path)), 0, -7).'</option>';
						$fastqc_count++;
						break;
					}
				}
			}
			$fastqc .= '</optgroup>';
		}
	}
	if($fastqc_count > 0 ){
		echo '<label>FastQC Reports: <select name="fastqc" class="select_report_dataset" class="input-xlarge" data-type="fastqc">'.$fastqc.'</select></label>';
	}
	
	////////////////////////////
	// Find Fastq Screen reports
	////////////////////////////
	$fastq_screen_paths = array();
	$dir = $data_root.$project['name'];
	$it = new RecursiveDirectoryIterator($dir);
	foreach(new RecursiveIteratorIterator($it) as $file) {
		if(stripos(basename($file), '_screen.png')){
			$fastq_screen_paths[] = $file->getPathname();
		}
	}
	sort($fastq_screen_paths);
	$fastq_screen_count = 0;
	$fastq_screen = '<option>[ Select Report ]</option>';
	
	// Load datasets and match against fastq screen reports
	$dataset_query = "SELECT * FROM `datasets` WHERE `project_id` = '$project_id'";
	$datasets = mysql_query($dataset_query);
	$reports = array();
	if(mysql_num_rows($datasets) > 0){
		while ($dataset = mysql_fetch_array($datasets)){
			$fastq_screen .= '<optgroup label="'.$dataset['name'].'">';
			$needles = array();
			$needles[] = $dataset['name'];
			foreach(split(" ",$dataset['accession_geo']) as $geo){
				array_push($needles, $geo);
			}
			foreach(split(" ",$dataset['accession_sra']) as $sra){
				array_push($needles, $sra);
			}
			foreach($fastq_screen_paths as $path){
				$path = substr($path, strlen($data_root));
				foreach($needles as $needle){
					if(stripos($path, $needle)){
						if(!$report_path){
							$report_path = $path;
							$show_fastq_screen = true;
						}
						$fastq_screen .= '<option value="'.$path.'"';
						if($report_path == $path && $show_fastq_screen){
							$fastq_screen .= ' selected="selected"';
							$dataset_name = $dataset['name'];
						}
						$fastq_screen .= '>'.substr(basename($path),0, -11).'</option>';
						$fastq_screen_count++;
						break;
					}
				}
			}
			$fastq_screen .= '</optgroup>';
		}
	}
	if($fastq_screen_count > 0 ){
		echo '<label>FastQ Screen Reports: <select name="fastq_screen" class="select_report_dataset" class="input-xlarge" data-type="fastq_screen">'.$fastq_screen.'</select></label>';
	}
	
	
	//////////////////////
	// Find Bismark Alignmnent Overview Plots
	//////////////////////
	$alignment_overview_paths = array();
	$dir = $data_root.$project['name'];
	$it = new RecursiveDirectoryIterator($dir);
	foreach(new RecursiveIteratorIterator($it) as $file) {
		if(substr($file, -23) == '.alignment_overview.png'){
			$alignment_overview_paths[] = $file->getPathname();
		}
	}
	sort($alignment_overview_paths);
	$alignment_overview_count = 0;
	$alignment_overview = '<option>[ Select Report ]</option>';
	
	// Load datasets and match against M bias reports
	$dataset_query = "SELECT * FROM `datasets` WHERE `project_id` = '$project_id'";
	$datasets = mysql_query($dataset_query);
	$reports = array();
	if(mysql_num_rows($datasets) > 0){
		while ($dataset = mysql_fetch_array($datasets)){
			$alignment_overview .= '<optgroup label="'.$dataset['name'].'">';
			$needles = array();
			$needles[] = $dataset['name'];
			foreach(split(" ",$dataset['accession_geo']) as $geo){
				array_push($needles, $geo);
			}
			foreach(split(" ",$dataset['accession_sra']) as $sra){
				array_push($needles, $sra);
			}
			foreach($alignment_overview_paths as $path){
				$path = substr($path, strlen($data_root));
				foreach($needles as $needle){
					if(stripos($path, $needle)){
						if(!$report_path){
							$report_path = $path;
							$show_alignment_overview = true;
						}
						$alignment_overview .= '<option value="'.$path.'"';
						if($report_path == $path && $show_alignment_overview){
							$alignment_overview .= ' selected="selected"';
							$dataset_name = $dataset['name'];
						}
						$alignment_overview .= '>'.substr(basename($path),0, -23).'</option>';
						$alignment_overview_count++;
						break;
					}
				}
			}
			$alignment_overview .= '</optgroup>';
		}
	}
	if($alignment_overview_count > 0 ){
		echo '<label>Alignment Overview Plots: <select name="alignment_overview" class="select_report_dataset" class="input-xlarge" data-type="alignment_overview">'.$alignment_overview.'</select></label>';
	}
	
	
	
	//////////////////////
	// Find M-Bias reports
	//////////////////////
	$m_bias_paths = array();
	$dir = $data_root.$project['name'];
	$it = new RecursiveDirectoryIterator($dir);
	foreach(new RecursiveIteratorIterator($it) as $file) {
		if(stripos(basename($file), 'M-bias') && substr($file, -4) == '.png'){
			$m_bias_paths[] = $file->getPathname();
		}
	}
	sort($m_bias_paths);
	$m_bias_count = 0;
	$m_bias = '<option>[ Select Report ]</option>';
	
	// Load datasets and match against M bias reports
	$dataset_query = "SELECT * FROM `datasets` WHERE `project_id` = '$project_id'";
	$datasets = mysql_query($dataset_query);
	$reports = array();
	if(mysql_num_rows($datasets) > 0){
		while ($dataset = mysql_fetch_array($datasets)){
			$m_bias .= '<optgroup label="'.$dataset['name'].'">';
			$needles = array();
			$needles[] = $dataset['name'];
			foreach(split(" ",$dataset['accession_geo']) as $geo){
				array_push($needles, $geo);
			}
			foreach(split(" ",$dataset['accession_sra']) as $sra){
				array_push($needles, $sra);
			}
			foreach($m_bias_paths as $path){
				$path = substr($path, strlen($data_root));
				foreach($needles as $needle){
					if(stripos($path, $needle)){
						if(!$report_path){
							$report_path = $path;
							$show_m_bias = true;
						}
						$m_bias .= '<option value="'.$path.'"';
						if($report_path == $path && $show_m_bias){
							$m_bias .= ' selected="selected"';
							$dataset_name = $dataset['name'];
						}
						$m_bias .= '>'.substr(basename($path),0, -4).'</option>';
						$m_bias_count++;
						break;
					}
				}
			}
			$m_bias .= '</optgroup>';
		}
	}
	if($m_bias_count > 0 ){
		echo '<label>M-Bias Reports: <select name="m_bias" class="select_report_dataset" class="input-xlarge" data-type="m_bias">'.$m_bias.'</select></label>';
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