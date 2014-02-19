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
Script to handle EBI ENA lookups using ENA accessions
Provides a function if included, returns JSON if called directly
*/

require_once('../includes/start.php');

function ena_get_project ($acc, $editing = false) {
	// Get the first XML file with GEO ID accessions, using the supplied GEO accession
	// Only get the info we want for the Project
	// uses eSearch
	
	if(substr($acc, 0, 3) == 'ERR'){
		$results['status'] = 0;
		$results['message'] = "Accession is a ENA sample, not series. Needs to start ERP not ERR.";
		return $results;
	} else if(substr($acc, 0, 3) !== 'ERP'){
		$results['status'] = 0;
		$results['message'] = "Accession does not start with GSE. ";
		return $results;
	}
	
	$results = array();
	
	$url = 'http://www.ebi.ac.uk/ena/data/view/'.$acc.'&display=xml';
	$xml = simplexml_load_file($url);
	if($xml === FALSE){
		$results['status'] = 0;
		$results['message'] = "Could not load ENA information. This usually means that the EBI ENA API is down, try again later. API call URL: $url";
		return $results;
	}
	// Check if we have any Ids - if not, accession probably wrong
	if(!isset($xml->STUDY)){
		$results['status'] = 0;
		$results['message'] = "No projects found with accession $acc";
		return $results;
	}
	
	$results['title'] = (string)$xml->STUDY->DESCRIPTOR->STUDY_TITLE;
	$results['description'] = (string)$xml->STUDY->DESCRIPTOR->STUDY_ABSTRACT;
	$results['PMIDs'] = array();
	
	$results['message'] = "ENA project successfully found";
	$results['status'] = 1;
	
	// Check to see if we already have this accession
	$sql = sprintf("SELECT `id`, `name` FROM `projects` WHERE `accession_ena` LIKE '%%%s%%'", mysql_real_escape_string($acc));
	$projects = mysql_query($sql);
	if(mysql_num_rows($projects) > 0){
		$project = mysql_fetch_array($projects);
		if($project['id'] != $editing){
			$results['message'] = '<strong>WARNING:</strong> There is already a project with this accession: <a href="project.php?id='.$project['id'].'">'.$project['name'].'</a>';
		}
	}
	
	
	return $results;
}

// Script is being called directly (ajax)
if(__FILE__ == $_SERVER['SCRIPT_FILENAME']) {
	if(isset($_GET['acc'])){
		if(isset($_GET['editing'])){
			$results = ena_get_project ($_GET['acc'], $_GET['editing']);
		} else {
			$results = ena_get_project ($_GET['acc'], false);
		}
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