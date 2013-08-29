<?php

/*
Script to handle NCBI GEO lookups using GSE accessions
Provides a function if included, returns JSON if called directly
*/

require_once('../includes/start.php');

function get_geo_project ($acc) {
	// Get the first XML file with GEO ID accessions, using the supplied GEO accession
	// Only get the info we want for the Project
	// uses eSearch
	
	if(substr($acc, 0, 3) == 'GSM'){
		$results['status'] = 0;
		$results['message'] = "Accession is a GEO sample, not series. Needs to start GSE not GSM.";
		return $results;
	} else if(substr($acc, 0, 3) !== 'GSE'){
		$results['status'] = 0;
		$results['message'] = "Accession does not start with GSE. ";
		return $results;
	}
	
	$results = array();
	
	$url_1 = 'http://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?db=gds&term='.$acc.'&usehistory=y';
	$xml_1 = simplexml_load_file($url_1);
	if($xml_1 === FALSE){
		$results['status'] = 0;
		$results['message'] = "Could not load GEO information. This usually means that the NCBI GEO API is down, try again later. API call URL: $url_1";
		return $results;
	}
	// Check if we have any Ids - if not, accession probably wrong
	if(!isset($xml_1->IdList->Id)){
		$results['status'] = 0;
		$results['message'] = "No datasets found with accession $acc";
		return $results;
	}
	$WebEnv = $xml_1->WebEnv;
	
	// Get the second XML file with GEO meta data and dataset information
	$url_2 = 'http://eutils.ncbi.nlm.nih.gov/entrez/eutils/esummary.fcgi?db=gds&query_key=1&WebEnv='.$WebEnv;
	$xml_2 = simplexml_load_file($url_2);
	if($xml_2 === FALSE){
		$results['status'] = 0;
		$results['message'] = "Could not load second NCBI GEO API call: $url_2";
		return $results;
	}
	// Check for an error
	if(isset($xml_2->ERROR)) {
		$results['status'] = 0;
		$results['message'] = "Second NCBI GEO API call returned an error: ".$xml_2->ERROR;
		return $results;
	}
	
	$firstDocSum = true;
	foreach($xml_2->children() as $DocSum){
		// All of what we want is found in the first DocSum node
		if($firstDocSum){
			$firstDocSum = false;
			foreach($DocSum->children() as $child) {
				switch ($child->attributes()->Name) {
					case 'title':
						$results['title'] = (string)$child;
						break;
					case 'summary':
						$results['description'] = (string)$child;
						break;
					case 'PubMedIds':
						$results['PMIDs'] = array();
						foreach ($child->Item as $pmid){
							$results['PMIDs'][] = (string)$pmid[0];
						}
						break;
				}
			}
		
		}
	}
	
	$results['message'] = "";
	$results['status'] = 1;
	
	// Check to see if we already have this accession
	$sql = sprintf("SELECT `id`, `name` FROM `projects` WHERE `accession_geo` LIKE '%%%s%%'", mysql_real_escape_string($acc));
	$projects = mysql_query($sql);
	if(mysql_num_rows($projects) > 0){
		$project = mysql_fetch_array($projects);
		$results['message'] = '<strong>WARNING:</strong> There is already a project with this accession: <a href="project.php?id='.$project['id'].'">'.$project['name'].'</a>';
	}
	
	
	return $results;
}

// Script is being called directly (ajax)
if(__FILE__ == $_SERVER['SCRIPT_FILENAME']) {
	if(isset($_GET['acc'])){
		$results = get_geo_project ($_GET['acc']);
		echo json_encode($results, JSON_FORCE_OBJECT);
	} else {
		$results = array(
			'status' => 0,
			'message' => "No accession provided"
		);
		echo json_encode($results, JSON_FORCE_OBJECT);
	}
}


?>