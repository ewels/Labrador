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
		<li>
			<a href="datasets.php?id=<?php echo $project_id; ?>">Datasets</a>
		</li>
		<li class="active">
			<a href="files.php?id=<?php echo $project_id; ?>">Files</a>
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
	
	<a class="labrador_help_toggle pull-right" href="#labrador_help" title="Help"><i class="icon-question-sign"></i></a>
	<?php project_header($project); ?>
	
	<?php if(isset($_POST['java_download_paths']) && $_POST['java_download_paths'] == 'Download Checked Files With Java Applet'){ ?>
	
	<div class="labrador_help" style="display:none;">
		<div class="well">
			<h3>Downloads with the Java Applet</h3>
			<p>The Java applet below allows many large files to be downloaded from the server to your computer in succession. This is useful, as trying to download a lot of data through your browser
			can fail and choke the network. If you are downloading many files, the applet is better as it maintains the directory structure found on the server, so keeping your files organised and not over-writing anything.</p>
			<p>To use the applet, choose the folder that you would like the files to be downloaded to by clicking 'Browse'. Select the files to be downloaded (these should be those selected in the previos page) and click Download.</p>
		</div>
	</div>
	
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
	
	<div class="labrador_help" style="display:none;">
		<div class="well">
			<h3>Files Page</h3>
			<p>You can use this page to download data from the Bioinformatics server to your computer.</p>
			<p>File size and Genome is dynamically generated from the files themselves - BAM/SAM and SeqMonk files record the genome that they use within their file headers, which is extracted below.</p>
			<p>Files within the project directory will be matched up to datasets, those associated with the datasets selected on the previous page will be shown below, along with any not matched to a dataset.
			You can filter the table for files with extensions commonly used for Aligned data, Raw data, Processing Reports and everything else. You can also use the text box to filter the file names. The table can be sorted by column heading.</p>
			<p>There are two ways to download files: either <strong>click the file name</strong> to download the file through your browser, or select the files you wish to download and press <strong>Download Checked Files With Java Applet</strong>.
			The Java applet is best for downloading large files and maintains directory struture, useful if downloading many files.</p>
		</div>
	</div>
	
	<form action="files.php" method="get" class="form-horizontal">
		<input type="hidden" name="id" value="<?php echo $project_id; ?>">
		<div class="well">
			<!-- <input type="submit" name="java_download_paths" class="btn btn-primary pull-right" value="Download Checked Files With Java Applet"> -->
			Filter files: &nbsp; 
			<div class="btn-group">
				<button class="btn" id="filter_projects">Projects</button>
				<button class="btn" id="filter_aligned">Aligned</button>
				<button class="btn" id="filter_raw">Raw</button>
				<button class="btn" id="filter_reports">Reports</button>
				<button class="btn" id="filter_other">Other</button>
			</div> &nbsp; 
			<input type="text" class="input-small filter_text" id="name" placeholder="Filename">
			<select name="ds" id="filter_dataset" class="span4">
				<option value="all">All Datasets</option>
				<option value="none" <?php if(isset($_GET['ds']) && $_GET['ds'] == 'none') { echo 'selected="selected"'; } ?>>Unmatched</option>
				<?php
				$dataset_query = mysql_query("SELECT * FROM `datasets` WHERE `project_id` = '$project_id'");
				if(mysql_num_rows($dataset_query) > 0){
					while($dataset = mysql_fetch_array($dataset_query)){
						echo '<option value="'.$dataset['id'].'"';
						if(isset($_GET['ds']) && $_GET['ds'] == $dataset['id']){
							echo ' selected="selected"';
						}
						echo '>'.$dataset['name'].'</option>';
					}
				}
				?>
			</select>
			<span class="help-block" style="margin:10px 0 0;"><?php echo $download_instructions; ?></span>
		</div>
	</form>
	<form action="files.php?id=<?php echo $project_id; ?>" method="post">
	<?php // Get dataset details for filename search needles
	$datasets = array();
	$orphans = array();
	$sql = "SELECT * FROM `datasets` WHERE `project_id` = '$project_id'";
	if(isset($_GET['ds']) && is_numeric($_GET['ds'])){
		$sql .= " AND `id` = '".$_GET['ds']."'";
	}
	$dataset_query = mysql_query($sql);
	if(mysql_num_rows($dataset_query) > 0){
		while ($dataset = mysql_fetch_array($dataset_query)){
			$id = $dataset['id'];
			$datasets[$id] = $dataset;
			$datasets[$id]['files'] = array();
		}
	}
	// Loop through files and match to datasets
	$num_paths = 0;
	$dir = $data_root.$project['name'];
	$it = new RecursiveDirectoryIterator($dir);
	foreach(new RecursiveIteratorIterator($it) as $file) {
		$size = $file->getSize();
		$path = $file->getPathname();
		$matched = false;
		if(substr($path, -1) !== '~' && substr(basename($path), 0, 1) !== '.' && !stripos($path, 'fastqc/')){
			$num_paths++;
			// Look for SRA accessions first
			foreach ($datasets as $id => $dataset){
				$accessions = explode(" ", $dataset['accession_sra']);
				foreach($accessions as $accession){
					if(stripos($path, $accession)){
						$datasets[$id]['paths'][$path] = $size;
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
							$datasets[$id]['paths'][$path] = $size;
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
						$datasets[$id]['paths'][$path] = $size;
						$matched = true;
						break;
					}
				}
			}
			// Can't find this one - an orphan
			if(!$matched && (!isset($_GET['ds']) || $_GET['ds'] == 'all' || $_GET['ds'] == 'none')){
				$orphans[$path] = $size;
			}
			
		}
	}
	// Kill matched dataset paths if filtering for unmatched
	if(isset($_GET['ds']) && $_GET['ds'] == 'none'){
		$datasets = array();
	}
	?>
	
		<table class="table table-condensed table-bordered table-striped sortable download_table">
			<thead>
				<tr>
					<th class="select" style="width:20px;"><input type="checkbox" class="select-all"></th>
					<th data-sort="string-ins" style="width:30%;">Dataset Name</th>
					<th data-sort="int" style="width:10%;">File Size</th>
					<th data-sort="string-ins" style="width:15%;">Genome</th>
					<th data-sort="string-ins">Filename</th>
				</tr>
			</thead>
			<tbody>
			<?php
			function find_genome($path){
				$genome = '';
				// Find genome from BAM or SAM files
				if(substr($path, -4) == '.bam' || substr($path, -4) == '.bam'){
					$bam_header = shell_exec (escapeshellcmd ('samtools view -H '.$path));
					$bam_headers = explode("\n", $bam_header);
					foreach($bam_headers as $header){
						if(stripos($header, 'Genomes/')){
							$genomes = explode(" ", substr($header, stripos($header, 'Genomes/') + 8));
							$genomes2 = split("/", $genomes[0]);
							$genome = $genomes2[0].' - '.$genomes2[1];
						}
					}
				}
				// Find genome from SeqMonk projects
				if(substr($path, -4) == '.smk' || substr($path, -7) == '.smk.gq'){
					$type = shell_exec (escapeshellcmd ('file '.$path));
					$types = explode(": ", $type);
					$type = $types[1];
					if(substr($type,0,4) == 'gzip'){
						$header = shell_exec ('zcat '.$path.' | head');
					} else {
						$header = shell_exec (escapeshellcmd ('head '.$path));
					}
					$headers = explode("\n", $header);
					$genomes = explode("\t", $headers[1]);
					$genome = $genomes[1].' - '.$genomes[2];
				}
				return $genome;
			}
			function find_parameters($path){
				// BAM and SAM files
				if(substr($path, -4) == '.bam' || substr($path, -4) == '.bam'){
					$bam_header = shell_exec (escapeshellcmd ('samtools view -H '.$path));
					$bam_headers = explode("\n", $bam_header);
					foreach($bam_headers as $header){
						if(stripos($header, 'Genomes/')){
							return '<i class="icon-info-sign" title="'.htmlspecialchars ($header).'"></i>';
						}
					}
				}
			}
			
			$j = 0;
			foreach($datasets as $dataset){
				$paths = $dataset['paths'];
				ksort($paths);
				foreach($paths as $raw_path => $size){
					$j++;
					$path = substr($raw_path, strlen($dir)); ?>
				<tr>
					<td class="select"><input type="checkbox" class="select-row" id="check_<?php echo $j; ?>" name="check_<?php echo $j; ?>"><input type="hidden" name="path_<?php echo $j; ?>" value="<?php echo $path; ?>"></td>
					<td><?php echo $dataset['name']; ?></td>
					<td data-sort-value="<?php echo $size; ?>"><?php echo human_filesize($size); ?></td>
					<td><?php echo find_genome($raw_path); ?> <?php echo find_parameters($raw_path); ?></td>
					<td class="path"><a href="download_file.php?fn=<?php echo substr($raw_path, strlen($data_root)); ?>"><?php echo $path; ?></a></td>
				</tr>
			<?php 
				} //foreach path
			} // foreach dataset
			ksort($orphans);
			foreach ($orphans as $raw_path => $size){
				$j++;
				$path = substr($raw_path, strlen($dir)); ?>
				<tr>
					<td class="select"><input type="checkbox" class="select-row" id="check_<?php echo $j; ?>" name="check_<?php echo $j; ?>"><input type="hidden" name="path_<?php echo $j; ?>" value="<?php echo $path; ?>"></td>
					<td><em>Not matched to any datasets</em></td>
					<td data-sort-value="<?php echo $size; ?>"><?php echo human_filesize($size); ?></td>
					<td><?php echo find_genome($raw_path); ?></td>
					<td class="path"><a href="download_file.php?fn=<?php echo substr($raw_path, strlen($data_root)); ?>"><?php echo $path; ?></a></td>
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
	var project_filename_filters = new Array("<?php echo implode('", "', $project_filename_filters); ?>");
	var raw_filename_filters = new Array("<?php echo implode('", "', $raw_filename_filters); ?>");
	var aligned_filename_filters = new Array("<?php echo implode('", "', $aligned_filename_filters); ?>");
	var reports_filename_filters = new Array("<?php echo implode('", "', $reports_filename_filters); ?>");
</script>
<script src="js/files.js" type="text/javascript"></script>
<?php include('includes/footer.php'); ?>