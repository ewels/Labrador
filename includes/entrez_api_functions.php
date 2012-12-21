<?php
/*
	Entrez web API functions (entrez_api_functions.php)
	- Talks to the Entrez web API to find metadata from GEO code
	- Requires PHP 5 (uses SimpleXML)
*/


function get_GEO_GSE ($GEO_accession, $sra_codes = false) {
	// Get the first XML file with GEO ID accessions, using the supplied GEO accession
	// uses eSearch
	$url_1 = 'http://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?db=gds&term='.$GEO_accession.'&usehistory=y';
	$xml_1 = simplexml_load_file($url_1);
	// Check if we have any Ids - if not, accession probably wrong
	if(!isset($xml_1->IdList->Id)){
		$error = "No datasets found";
		return false;
	}
	$WebEnv = $xml_1->WebEnv;
	
	// Get the second XML file with GEO meta data and dataset information
	$url_2 = 'http://eutils.ncbi.nlm.nih.gov/entrez/eutils/esummary.fcgi?db=gds&query_key=1&WebEnv='.$WebEnv;
	$xml_2 = simplexml_load_file($url_2);
	// Check for an error
	if(isset($xml_2->ERROR)) {
		return false;
	}
	// Everything we want is found in the first DocSum node
	$results = array();
	$results['samples'] = array();
	
	foreach($xml_2->DocSum->children() as $child) {
		switch ($child->attributes()->Name) {
			case 'GSE':
				$results['GSE_acc'] = 'GSE'.$child;
				break;
			case 'PubMedIds':
				$results['PMID'] = (string)$child->Item;
				break;
			case 'Samples':
				foreach($child->children() as $sample) {
					$acc = (string)$sample->Item[0];
					$name = (string)$sample->Item[1];
					$results['samples'][$acc]['name'] = $name;
				}
				ksort($results['samples']);
				break;
		}
	}
	
	
	// Find SRA codes
	if ($sra_codes) {
		$results['msg'][] = 'position 2';
		foreach ($results['samples'] as $acc => $name) {
			$url_1 = 'http://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?db=sra&term='.$acc.'&usehistory=y';
			$xml_1 = simplexml_load_file($url_1);
			// Check if we have any Ids - if not, accession probably wrong
			if(!isset($xml_1->IdList->Id)){
				$error = "No datasets found";
				$results['msg'][] = 'FAIL - SRA not found';
				//return false;
			}
			$WebEnv = $xml_1->WebEnv;
			
			$url_2 = 'http://eutils.ncbi.nlm.nih.gov/entrez/eutils/esummary.fcgi?db=sra&query_key=1&WebEnv='.$WebEnv;
			$xml_2 = simplexml_load_file($url_2);
			// Check for an error
			if(isset($xml_2->ERROR)) {
				//return false;
				$results['msg'][] = 'FAIL - SRA error message found';
			}
			$sra_accessions = array();
			$total_bases = 0;
			foreach($xml_2->DocSum->children() as $child) {
				if($child->attributes()->Name == 'Runs') {
					$sra_raw = (string)$child;
					$sra_decoded = html_entity_decode($sra_raw);
					$sra_xml = simplexml_load_string('<document>'.$sra_decoded.'</document>');
					if($sra_xml){
					  foreach($sra_xml->children() as $child) {
					    foreach($child->attributes() as $att => $val) {
					      switch($att){
					      case 'acc':
						$sra_accessions[] = $val;
						break;
					      case 'total_bases':
						$total_bases += $val;
						break;
					      } //switch
					    } // attr foreach
					  } // child foreach
					} // xml success check
					$results['samples'][$acc]['sra'] = implode(' ', $sra_accessions);
					// $results['samples'][$acc]['bases'] = $total_bases; // HERE IF WANTED IN THE FUTURE - SRA BASE COUNT
				} // geo sra tag name check
			} // geo xml foreach
		}
	}
	

	return $results;
}

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
		
		
		// Paper Title
		$results['title'] = (string)$paper->Title;
		
		// Authors
		$results['authors'] = '';
		foreach($paper->Authors->children() as $author) {
			$results['authors'] .= $author->Name.', ';
		}
		$results['authors'] = substr($results['authors'], 0, -2);

		// DOI
		foreach ($paper->ArticleIds->children() as $articleID){
			if($articleID->IdType == 'doi') {
				$results['DOI'] = (string)$articleID->Value;
			}
		}
		
		
		return $results;
	}
		
	
}


?>