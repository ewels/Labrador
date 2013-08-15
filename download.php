<?php

include('includes/start.php');

if(isset($_GET['id']) && is_numeric($_GET['id'])){
	$project_id = $_GET['id'];
	$projects = mysql_query("SELECT * FROM `projects` WHERE `id` = '".$project_id."'");
	$project = mysql_fetch_array($projects);
} else {
	header("Location: index.php");
}

if(isset($_POST['java_download_paths']) && $_POST['java_download_paths'] == 'Download Checked Files With Java Applet'){
	$i = 1;
	$paths = array();
	while(isset($_POST["path_$i"])){
		if(isset($_POST["check_$i"]) && $_POST["check_$i"] == 'on'){
			$paths[] = $project['name'].$_POST["path_$i"];
		}
		$i++;
	}
	// no checked boxes - download all
	if(count($paths) == 0){
		$i = 1;
		while(isset($_POST["path_$i"])){
			$paths[] = $project['name'].$_POST["path_$i"];
			$i++;
		}
	}
	$_SESSION['files'] = $paths;
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

<div class="sidebar-mainpage project-mainpage">

	<?php project_header($project); ?>
	
	<?php if(isset($_POST['java_download_paths']) && $_POST['java_download_paths'] == 'Download Checked Files With Java Applet'){ ?>
	
	<applet code="biz.jupload.jdownload.Manager" archive="includes/jdownload/jdownload.jar" width="100%" height="500px" name="JDownload" mayscript="mayscript" alt="JDownload by www.jupload.biz">
		<!-- Java Plug-In Options -->
		<param name="progressbar" value="true">
		<param name="boxmessage" value="Loading JDownload Applet ...">
		<!-- URL pointing to the data structure containing the list of files and folders to download -->
		<param name="dataURL" value="ajax/java_download_xml.php">
		<!-- Show or Hide the controls. If hidden (set all to 'false'), remote control the applet using JavaScript buttons -->
		<param name="showExplorer" value="true">
		<param name="showControls" value="true">
		<param name="showBrowser" value="true">
		<param name="showStatus" value="true">
		<!-- Error message for browsers not supporting Java applets -->
		Your browser does not support applets, or you have disabled applets in your options.
		To use this applet, please update your Java. You can get it from <a href="http://www.java.com/">java.com</a>
	</applet>
	
	<?php } else { ?>
	
	<form action="download.php?id=<?php echo $project_id; ?>" method="post" class="form-horizontal">
		<div class="well">
			<input type="submit" name="java_download_paths" class="btn btn-primary pull-right" value="Download Checked Files With Java Applet">
			Filter downloads: &nbsp; 
			<div class="btn-group">
				<button class="btn" id="filter_aligned">Aligned</button>
				<button class="btn" id="filter_raw">Raw</button>
				<button class="btn" id="filter_reports">Reports</button>
				<button class="btn" id="filter_other">Other</button>
			</div> &nbsp; 
			<input type="text" class="input-small filter_text" id="name" placeholder="Filename">
			<span class="help-block" style="margin:10px 0 0;"><?php echo $download_instructions; ?></span>
		</div>
	
	<?php // Get dataset details for filename search needles
	$datasets = array();
	$orphans = array();
	$checked = 0;
	$dataset_query = mysql_query("SELECT * FROM `datasets` WHERE `project_id` = '$project_id'");
	if(mysql_num_rows($dataset_query) > 0){
		while ($dataset = mysql_fetch_array($dataset_query)){
			$id = $dataset['id'];
			$datasets[$id] = $dataset;
			if(isset($_POST["check_$id"]) && $_POST["check_$id"] == 'on'){
				$datasets[$id]['checked'] = true;
				$checked++;
			} else {
				$datasets[$id]['checked'] = false;
			}
			$datasets[$id]['paths'] = array();
		}
	}
	// Loop through files and match to datasets
	$num_paths = 0;
	$dir = $data_root.$project['name'];
	$it = new RecursiveDirectoryIterator($dir);
	foreach(new RecursiveIteratorIterator($it) as $file) {
		$path = $file->getPathname();
		$matched = false;
		if(substr($path, -1) !== '~' && substr(basename($path), 0, 1) !== '.' && !stripos($path, 'fastqc/')){
			$num_paths++;
			// Look for SRA accessions first
			foreach ($datasets as $id => $dataset){
				$accessions = explode(" ", $dataset['accession_sra']);
				foreach($accessions as $accession){
					if(stripos($path, $accession)){
						$datasets[$id]['paths'][] = $path;
						$matched = true;
						break 2;
					}
				}
			}
			// Look for GEO accessions
			if(!$matched){
				foreach ($datasets as $id => $dataset){
					$accessions = explode(" ", $dataset['accession_geo']);
					foreach($accessions as $accession){
						if(stripos($path, $accession)){
							$datasets[$id]['paths'][] = $path;
							$matched = true;
							break;
						}
					}
				}
			}
			// Still nothing - look for names
			if(!$matched){
				foreach ($datasets as $id => $dataset){
					if(stripos($path, $dataset['name'])){
						$datasets[$id]['paths'][] = $path;
						$matched = true;
						break;
					}
				}
			}
			// Can't find this one - an orphan
			if(!$matched){
				$orphans[] = $path;
			}
		}
	}	
	?>
	
		<table class="table table-condensed table-bordered table-striped download_table">
			<thead>
				<tr>
					<th class="select" style="width:20px;"><input type="checkbox" class="select-all"></th>
					<th style="width:30%;">Dataset Name</th>
					<th>Filename</th>
				</tr>
			</thead>
			<tbody>
			<?php $j = 0;
			foreach($datasets as $dataset){
				if($checked == 0 || $dataset['checked']){
					$paths = $dataset['paths'];
					sort($paths);
					foreach($paths as $raw_path){
						$j++;
						$path = substr($raw_path, strlen($dir)); ?>
					<tr>
						<td class="select"><input type="checkbox" class="select-row" id="check_<?php echo $j; ?>" name="check_<?php echo $j; ?>"><input type="hidden" name="path_<?php echo $j; ?>" value="<?php echo $path; ?>"></td>
						<td><?php echo $dataset['name']; ?></td>
						<td class="path"><a href="download_file.php?fn=<?php echo substr($raw_path, strlen($data_root)); ?>"><?php echo $path; ?></a></td>
					</tr>
			<?php } // if checked
				} //foreach path
			} // foreach dataset
			sort($orphans);
			foreach ($orphans as $id => $orphan){
				$j++;
				$path = substr($orphan, strlen($dir)); ?>
				<tr>
					<td class="select"><input type="checkbox" class="select-row" id="check_<?php echo $j; ?>" name="check_<?php echo $j; ?>"><input type="hidden" name="path_<?php echo $j; ?>" value="<?php echo $path; ?>"></td>
					<td><em>Not matched to any datasets</em></td>
					<td class="path"><a href="download_file.php?fn=<?php echo substr($orphan, strlen($data_root)); ?>"><?php echo $path; ?></a></td>
				</tr>
			<?php } // foreach orhpans ?></tbody>
		</table>
		
		<div class="form-actions">
			<input type="submit" class="btn btn-primary btn-large" name="java_download_paths" id="java_download_paths" value="Download Checked Files With Java Applet">
		</div>
		
	</form>
	
	<?php } // java applet check ?>
	
</div>

<?php include('includes/javascript.php'); ?>
<script type="text/javascript">
	var raw_filename_filters = new Array("<?php echo implode('", "', $raw_filename_filters); ?>");
	var aligned_filename_filters = new Array("<?php echo implode('", "', $aligned_filename_filters); ?>");
	var reports_filename_filters = new Array("<?php echo implode('", "', $reports_filename_filters); ?>");
</script>
<script src="js/download.js" type="text/javascript"></script>
<?php include('includes/footer.php'); ?>