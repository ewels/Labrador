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
Script to handle PubMed ID Lookups
Provides a function if included, returns JSON if called directly
*/

function get_pmid_details ($PMID) {
	
	$results = array();

	$url = 'http://eutils.ncbi.nlm.nih.gov/entrez/eutils/esummary.fcgi?db=pubmed&id='.$PMID.'&version=2.0';
	$xml = simplexml_load_file($url);
	
	if($xml === FALSE){
		$results['status'] = 0;
		$results['message'] = "Could not load first NCBI API call: $url";
		return $results;
	}
	
	if(!isset($xml->DocumentSummarySet->DocumentSummary)){
		// no papers found
		$results['status'] = 0;
		$results['message'] = "Paper not found";
		return $results;
	} else {
		
		$paper = $xml->DocumentSummarySet->DocumentSummary;
		
		if(isset($paper->error)){
			$results['status'] = 0;
			$results['message'] = "Paper not found: ".$paper->error;
			return $results;
		}
		
		// First Author
		$first_author = (string)$paper->Authors->Author->Name;
		$first_author_words = explode(' ',trim($first_author));
		$results['first_author'] = $first_author_words[0];
		
		// Publication Year
		$pubyear = (string)$paper->PubDate;
		$pubyear_parts = explode(' ',trim($pubyear));
		$results['year'] = $pubyear_parts[0];
		
		// Journal
		$results['journal'] = (string)$paper->Source;
		
		// Paper Title
		$results['title'] = (string)$paper->Title;
		
		// Authors
		$results['authors'] = '';
		foreach($paper->Authors->children() as $author) {
			$results['authors'] .= $author->Name.', ';
		}
		$results['authors'] = substr($results['authors'], 0, -2);

		// DOI
		$results['DOI'] = (string)$paper->DOI;
		if(trim($results['DOI']) == ''){
			foreach ($paper->ArticleIds->children() as $articleID){
				if($articleID->IdType == 'doi') {
					$results['DOI'] = (string)$articleID->Value;
				}
			}
		}
		
		$results['status'] = 1;
		$results['message'] = "Success";
		return $results;
	}
	
}

// Script is being called directly (ajax)
if(__FILE__ == $_SERVER['SCRIPT_FILENAME']) {
	if(isset($_GET['PMID']) && is_numeric($_GET['PMID'])){
		$details = get_pmid_details ($_GET['PMID']);
		echo json_encode($details, JSON_FORCE_OBJECT);
	} else {
		$results = array(
			'status' => 0,
			'message' => "No PMID provided"
		);
		echo json_encode($results, JSON_FORCE_OBJECT);
	}
}

?>