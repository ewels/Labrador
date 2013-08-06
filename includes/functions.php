<?php

/*
* functions.php
* ---------
* Helper PHP functions for Labrador
*
*/

function accession_badges ($string, $type){
	$return = '';
	switch ($type){
		case 'geo':
			$class = '';
			$url = 'http://www.ncbi.nlm.nih.gov/geo/query/acc.cgi?acc=';
			break;
		case 'sra':
			$class = 'label-info';
			$url = 'http://www.ncbi.nlm.nih.gov/sra/';
			break;
		case 'ena':
			$class = 'label-success';
			$url = 'http://www.ebi.ac.uk/ena/data/view/';
			break;
		case 'ddjb':
			$class = 'label-warning';
			$url = 'http://trace.ddbj.nig.ac.jp/DRASearch/run?acc=';
			break;
		default:
			$class = 'label-inverse';
			$url = '#';
			break;
	}
	
	$accessions = preg_split('/\s+/', $string);
	if(count($accessions) > 0){
		foreach($accessions as $accession){
			if(strlen($accession) > 0){
				$return .= '<a class="label '.$class.'" href="'.$url.$accession.'" target="_blank">'.$accession.'</a> ';
			}
		}
	}
	
	return $return;
}

function project_header($project) {
	echo '<h1>';
	echo $project['name'].' ';
	echo accession_badges ($project['accession_geo'], 'geo').' ';
	echo accession_badges ($project['accession_sra'], 'sra').' ';
	echo accession_badges ($project['accession_ena'], 'ena').' ';
	echo accession_badges ($project['accession_ddjb'], 'ddjb');
	echo '</h1>';
	
	echo '<p class="project_header_tag"><span class="label ';
	if($project['status'] == 'Processing Complete'){ 
		echo 'label-success'; 
	} else if($project['status'] == 'Currently Processing'){
		echo 'label-warning'; 
	} else if($project['status'] == 'Not Started'){
		echo 'label-important';
	}
	echo '">'.$project['status'].'</span>';	
		
	if(!empty($project['contact_name']) && !empty($project['contact_email'])) {
		echo '<small>Requested by: <a href="mailto:'.$project['contact_email'].'">'.$project['contact_name'].'</a></small>'; 
	}
	if(empty($project['contact_name']) && !empty($project['contact_email'])) {
		echo '<small>Requested by: <a href="mailto:'.$project['contact_email'].'">'.$project['contact_email'].'</a></small>'; 
	}
	if($project['assigned_to'] != ''){
		echo '<small>Assigned to: <a href="mailto:'.$project['assigned_to'].'">'.$project['assigned_to'].'</a></small>';
	}
	echo '</p>';
}

?>