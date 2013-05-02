<?php

/*
Script to handle PubMed ID Lookups
Provides a function if included, returns JSON if called directly
*/

function get_pmid_details ($PMID) {
	$url = 'http://eutils.ncbi.nlm.nih.gov/entrez/eutils/esummary.fcgi?db=pubmed&id='.$PMID.'&version=2.0';
	$xml = simplexml_load_file($url);
	
	if(!isset($xml->DocumentSummarySet->DocumentSummary)){
		// no papers found
		return $url;
	} else {
		$results = array();
		$paper = $xml->DocumentSummarySet->DocumentSummary;
		
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
		
		return $results;
	}
	
}

// Script is being called directly (ajax)
if(__FILE__ == $_SERVER['SCRIPT_FILENAME']) {
	if(isset($_GET['PMID']) && is_numeric($_GET['PMID'])){
		$details = get_pmid_details ($_GET['PMID']);
		echo json_encode($details, JSON_FORCE_OBJECT);
	} else {
		echo '{}';
	}
}

?>