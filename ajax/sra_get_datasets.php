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
Script to handle NCBI GEO lookups using GSE accessions
Looks for dataset details rather than just the overall project
Provides a function if included, returns JSON if called directly
*/

/*
Script works by searching the SRA database using a GEO accession.
The results are ridiculous - an XML file with HTML encoded XML within fields.
Whhhhyyyyy?
*/

require_once('../includes/start.php');

function get_geo_datasets ($acc) {

	global $dblink;

	// Get the first XML file with GEO ID accessions, using the supplied GEO accession
	// Only get the info we want for the Project
	// uses eSearch

	$results = array();

	$url_1 = 'http://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?db=sra&term='.$acc.'&usehistory=y';
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
	$url_2 = 'http://eutils.ncbi.nlm.nih.gov/entrez/eutils/esummary.fcgi?db=sra&query_key=1&WebEnv='.$WebEnv;
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

	// Loop through each DocSum - each is a different dataset
	$i = 0;
	foreach($xml_2->children() as $DocSum){
		$sra_accessions = array();
		$gsm_acc = "";
		$library_name = "";
		// Loop through the nodes
		foreach($DocSum->children() as $child) {

			// Most stuff is within the ExpXml node. Remember - HTML encoded XML within here
			if($child->attributes()->Name == 'ExpXml') {

				// Dump this crap into a raw text string and use regexes
				// TODO: HTML unencode this and traverse it as XML?
				$sra_raw = (string)$child;

				if(preg_match('/GSM\d+/', $sra_raw, $matches)){
					$gsm_acc = $matches[0];
				}
				if(preg_match('#\<LIBRARY_NAME>(.+?)</LIBRARY_NAME>#', $sra_raw, $matches)){
					$library_name = $matches[1];
				} else {
					// Most records don't seem to have this field, grab it from <Title> with cleanup otherwise
					if(preg_match('#\<Title>(.+?)</Title>#', $sra_raw, $matches)){
						$title = explode(";", $matches[1]);
						if(substr(trim($title[0]), 0, 3) == 'GSM'){
							$library_name = substr($title[0], 12);
						} else {
							$library_name = $title[0];
						}
					}
				}
				if(preg_match('#\<Organism taxid="\d+" CommonName="(.+?)"/>#', $sra_raw, $matches)){
					$organism = $matches[1];
				}
				if(preg_match('#\<Organism taxid="\d+" ScientificName="(.+?)"/>#', $sra_raw, $matches)){
					$organism = $matches[1];
				}
				if(preg_match('#\<LIBRARY_STRATEGY>(.+?)</LIBRARY_STRATEGY>#', $sra_raw, $matches)){
					$methodology = $matches[1];
				}
			}
			// Find SRA accessions for this experiment
			if($child->attributes()->Name == 'Runs') {
				$sra_raw = (string)$child;
				preg_match_all('/[SDE]RR\d+/', $sra_raw, $sra_accessions);
			} // geo sra tag name check
		} // geo xml foreach

		// Trim off crap that we don't want from the library name
		$library_name = trim(preg_replace('/('.implode('|', $sra_accessions[0]).')[\:]?/', '', $library_name));
		$library_name = trim(preg_replace('/'.$gsm_acc.'[\:]?/', '', $library_name));
		$library_name = trim(preg_replace('/'.$acc.'[\:]?/', '', $library_name));

		// See if this dataset is a duplicate of one already in the database
		$duplicate = false;
		$gsm_acc_safe = preg_replace("/[^A-Za-z0-9]/", '', $gsm_acc);
		if(strlen($gsm_acc_safe) > 0){
			$sql = "SELECT `id` FROM `datasets` WHERE `accession_geo` = '".preg_replace("/[^A-Za-z0-9]/", '', $gsm_acc_safe)."'";
			$query = mysqli_query($dblink, $sql);
			if(mysqli_num_rows($query) > 0){
				$duplicate = true;
			}
		}
		foreach($sra_accessions[0] as $sra_accession){
			$sra_acc_safe = preg_replace("/[^A-Za-z0-9]/", '', $sra_accession);
				if(strlen($sra_acc_safe) > 0){
				$sql = "SELECT `id` FROM `datasets` WHERE `accession_sra` LIKE '%".$sra_acc_safe."%'";
				$query = mysqli_query($dblink, $sql);
				if(mysqli_num_rows($query) > 0){
					$duplicate = true;
				}
			}
		}

		// Assign values
		$results['samples'][$i]['name'] = $library_name;
		$results['samples'][$i]['accession_geo'] = $gsm_acc;
		$results['samples'][$i]['accession_sra'] = implode(' ', $sra_accessions[0]).' '; // Sometimes there can be multiple SRA results
		$results['samples'][$i]['species'] = $organism;
		$results['samples'][$i]['data_type'] = $methodology;
		$results['samples'][$i]['duplicate'] = $duplicate ? 'true' : 'false';

		// increment counter
		$i++;
	}

	// All done - worked!
	$results['status'] = 1;
	$results['message'] = "Success";
	return $results;
}

// Script is being called directly (ajax)
if(__FILE__ == $_SERVER['SCRIPT_FILENAME']) {
	if(isset($_GET['acc'])){
		$results = get_geo_datasets ($_GET['acc']);
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
