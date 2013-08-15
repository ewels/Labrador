<?php
/*
Script to write out the processing bash script to a file
*/

require_once('../includes/start.php');

if(isset($_POST['output']) && isset($_POST['project_id']) && is_numeric($_POST['project_id'])){
	
	$project_q = mysql_query("SELECT * FROM `projects` WHERE `id` = '".$_POST['project_id']."'");
	$project = mysql_fetch_array($project_q);
	
	$bash_fn = $project['name'].'_labrador_bash_'.date('d_m_Y').'.bash';
	$fn = $data_root.$project['name'].'/'.$bash_fn;
	
	// Save history message
	$query = sprintf("INSERT INTO `history` (`project_id`, `note`, `time`) VALUES ('%d', '%s', '%d')",
		$project['id'], mysql_real_escape_string("Saved bash script $bash_fn"), time());
	mysql_query($query);
	
	// Write to file
	$output = "# Bash script produced by Labrador at ".date('H:i, l \t\h\e jS F Y')."\n# Script written for the ".$_POST['server']." server\n\n";
	$output .= $_POST['output'];
	$output = str_replace("\r", "", $output);
	
	$fh = fopen($fn, 'w') or die("can't open file $fn");
	fwrite($fh, $output);
	fclose($fh);
	
	echo 'Bash script saved.';
	
} else {
	echo 'Missing vars to save bash script';
}

?>