<?php
/*
	Fetch GEO Dataset (fetch_geo.php)
	- Talks to the Entrez web API to find metadata from GEO code
	- Requires PHP 5 (uses SimpleXML)
*/

include('includes/entrez_api_functions.php');

// FETCH JUST THE GSM DATASETS - JSON FORMAT
if($_GET['datasets'] == 'true') {
	$results = get_GEO_GSE($_GET['GEO']);
	if (!$results) {
		echo "false";
		exit;
	} else {
		$datasets = '';
		foreach($results['samples'] as $key => $var) {
			$datasets .= '  "'.$key.'": "'.$var['name'].'",'."\n";
		}
		$datasets = substr($datasets, 0, -2);
		echo "{\n";
		echo $datasets;
		echo "\n}";
	}
	exit;
}

// RETURN DETAILS OF GSE AND PAPER (NO DATASETS) - JSON FORMAT
$results = get_GEO_GSE($_GET['GEO']);
if (!$results) {
	echo "false";
	exit;
} else {
	echo "{\n";
	echo '  "GSE_acc": "'.$results['GSE_acc'].'",'."\n";
	echo '  "PMID": "'.$results['PMID'].'",'."\n";
}

$papers = get_pmid_details($results['PMID']);
if($papers) {
	echo '  "first_author": "'.$papers['first_author'].'",'."\n";
	echo '  "year": "'.$papers['year'].'",'."\n";
	echo '  "paper_title": "'.$papers['title'].'",'."\n";
	echo '  "authors": "'.$papers['authors'].'",'."\n";
	echo '  "DOI": "'.$papers['DOI'].'",'."\n";
}

// check if this paper is already in the database
if(strlen($results['PMID']) > 1 || strlen($results['GSE_acc']) > 1 || (strlen($results['first_author']) > 1 && strlen($results['year']) == 4)){
	// Connect to database
	include('includes/db_login.php');
	
	$wheres = array();
	$query = "SELECT `id` FROM `papers` WHERE ";
	if(strlen($results['PMID']) > 1) $wheres[] = "`PMID` = '".$results['PMID']." ' ";
	if(strlen($results['GSE_acc']) > 1) $wheres[] = "`geo_accession` = '".$results['GSE_acc']."' ";
	if(strlen($results['first_author']) > 1 && strlen($results['year']) == 4) $wheres[] = "(`first_author` = '".$papers['first_author']."' AND `year` = '".$papers['year']."')";
	$query .= implode(' OR ', $wheres);
	
	$existing_q = mysql_query($query);
	if (mysql_num_rows($existing_q) > 0) {
		$existing = mysql_fetch_array($existing_q);
		$existing_id = $existing['id'];
	} else {
		$existing_id = '-1';
	}
	echo '  "existing_paper": "'.$existing_id.'"'."\n";
} else {
	echo '  "existing_paper": "-1"'."\n";
}

echo "}";

?>