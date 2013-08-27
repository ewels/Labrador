<?php
/*
Script to make a chunk of bash script for Processing steps
*/

require_once('../includes/start.php');

// SRA UNIT REQUESTED
if(isset($_POST['unit']) && $_POST['unit'] == 'accession_sra' && 
	isset($_POST['dataset']) && is_numeric($_POST['dataset']) && 
	isset($_POST['genome']) && isset($_POST['server']) && 
	isset($_POST['template']) && strlen($_POST['template']) > 0){
	
	echo "\n";
	
	$printout = '';
	
	$dataset_q = mysql_query("SELECT * FROM `datasets` WHERE `id` = '".$_POST['dataset']."'");
	if(mysql_num_rows($dataset_q) > 0){
		$dataset = mysql_fetch_array($dataset_q);
		
		$project_q = mysql_query("SELECT * FROM `projects` WHERE `id` = '".$dataset['project_id']."'");
		$project = mysql_fetch_array($project_q);
		
		$dataset_fn = substr(preg_replace("/[^A-Za-z0-9_]/", '_', $dataset['name']), 0, 100);
		$sras = split(" ",$dataset['accession_sra']);
		$contact_email = $project['contact_email'];
		$assigned_email = $project['assigned_to'];
		
		foreach($sras as $sra){
			$sra = trim($sra);
			if(strlen($sra) > 0){
				$fn = $sra."_".$dataset_fn;
				$sra_url = "ftp://ftp-trace.ncbi.nlm.nih.gov/sra/sra-instant/reads/ByRun/sra/SRR/".substr($sra,0,6)."/".$sra."/".$sra.".sra -O ".$fn.".sra";
				
				$output = $_POST['template'];
				
				$patterns = array(
					'/{{fn}}/',
					'/{{sra}}/',
					'/{{sra_url}}/',
					'/{{contact_email}}/',
					'/{{assigned_email}}/',
					'/{{dataset}}/',
					'/{{project}}/',
					'/{{time}}/'
				);
				$replacements = array(
					$fn,
					$sra,
					$sra_url,
					$contact_email,
					$assigned_email,
					$dataset['name'],
					$project['name'],
					date('H:i, l \t\h\e jS F Y')
				);
				if(in_array($_POST['genome'], array_keys($genomes))){
					if(in_array($_POST['server'], array_keys($genomes[$_POST['genome']]))){
						$patterns[] = '/{{genome_path}}/';
						$replacements[] = $genomes[$_POST['genome']][$_POST['server']];
					}
				}
				$printout .= preg_replace($patterns, $replacements, $output);
			}
		}
	}
	
	// SAVE TO DATABASE
	if(isset($_POST['save_to_db']) && $_POST['save_to_db'] == 'true'){
		$bash_fn = $project['name'].'_labrador_bash_'.date('d_m_Y').'.bash';
		$sql = sprintf("INSERT INTO `processing` (`project_id`, `dataset_id`, `filename`, `commands`, `created`)
			VALUES ('%d','%d', '%s', '%s', '%d')",
			$project['id'], $dataset['id'], mysql_real_escape_string($bash_fn), mysql_real_escape_string($printout), time());
		mysql_query($sql);
	
	// PRINT OUTPUT
	} else {
		echo $printout;
	}

	
// PROJECT UNIT REQUESTED
} else if(isset($_POST['unit']) && $_POST['unit'] == 'project' && 
	isset($_POST['dataset']) && is_numeric($_POST['dataset']) && 
	isset($_POST['genome']) && isset($_POST['server']) && 
	isset($_POST['template']) && strlen($_POST['template']) > 0){
	
	echo "\n";
	
	$project_q = mysql_query("SELECT * FROM `projects` WHERE `id` = '".$_POST['dataset']."'");
	$project = mysql_fetch_array($project_q);
	
	$contact_email = $project['contact_email'];
	$assigned_email = $project['assigned_to'];
	
	$output = $_POST['template'];
			
	$patterns = array(
		'/{{genome_path}}/',
		'/{{contact_email}}/',
		'/{{assigned_email}}/',
		'/{{project}}/',
		'/{{time}}/'
	);
	$replacements = array(
		$genomes[$_POST['genome']][$_POST['server']],
		$contact_email,
		$assigned_email,
		$project['name'],
		date('H:i, l \t\h\e jS F Y')
	);
	echo preg_replace($patterns, $replacements, $output);

// TEXT AREA REQUESTED
} else if (isset($_GET['type']) && isset($_GET['server']) && in_array($_GET['server'], array_keys($processing_servers))){
		
	if(isset($processing_codes[$_GET['type']][$_GET['server']])){
		echo $processing_codes[$_GET['type']][$_GET['server']];
	}
	
}

?>