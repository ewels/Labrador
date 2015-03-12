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

	$dataset_q = mysqli_query($dblink, "SELECT * FROM `datasets` WHERE `id` = '".$_POST['dataset']."'");
	if(mysqli_num_rows($dataset_q) > 0){
		$dataset = mysqli_fetch_array($dataset_q);

		$project_q = mysqli_query($dblink, "SELECT * FROM `projects` WHERE `id` = '".$dataset['project_id']."'");
		$project = mysqli_fetch_array($project_q);

		$dataset['name'] = str_replace('–', '-', $dataset['name']);
		$dataset_fn = substr(preg_replace("/[^A-Za-z0-9_-]/", '_', $dataset['name']), 0, 100);
		$dataset_fn = preg_replace('/_+/', '_', $dataset_fn);
		$sras = split(" ",$dataset['accession_sra']);
		$assigned_email = $project['assigned_to'];

		foreach($sras as $sra){
			$sra = trim($sra);
			if(strlen($sra) > 0){
				$fn = $sra."_".$dataset_fn;
				$sra_url = "ftp://ftp-trace.ncbi.nlm.nih.gov/sra/sra-instant/reads/ByRun/sra/".substr($sra,0,3)."/".substr($sra,0,6)."/".$sra."/".$sra.".sra";
				$sra_url_wget = "$sra_url -O $fn.sra";

				$output = $_POST['template']."\n";

				$patterns = array(
					'/{{fn}}/',
					'/{{sra}}/',
					'/{{sra_url_wget}}/',
					'/{{sra_url}}/',
					'/{{assigned_email}}/',
					'/{{dataset}}/',
					'/{{project}}/',
					'/{{time}}/'
				);
				$replacements = array(
					$fn,
					$sra,
					$sra_url_wget,
					$sra_url,
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
			$project['id'], $dataset['id'], mysqli_real_escape_string($dblink, $bash_fn), mysqli_real_escape_string($dblink, $printout), time());
		mysqli_query($dblink, $sql);

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

	$project_q = mysqli_query($dblink, "SELECT * FROM `projects` WHERE `id` = '".$_POST['dataset']."'");
	$project = mysqli_fetch_array($project_q);

	$assigned_email = $project['assigned_to'];

	$output = $_POST['template']."\n";

	$patterns = array(
		'/{{genome_path}}/',
		'/{{assigned_email}}/',
		'/{{project}}/',
		'/{{time}}/'
	);
	$replacements = array(
		$genomes[$_POST['genome']][$_POST['server']],
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
