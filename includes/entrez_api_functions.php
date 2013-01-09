<?php
/*
	Entrez web API functions (entrez_api_functions.php)
	- Talks to the Entrez web API to find metadata from GEO code
	- Requires PHP 5 (uses SimpleXML)
*/


function get_GEO_GSE ($GEO_accession, $sra_codes = false) {
	// Get the first XML file with GEO ID accessions, using the supplied GEO accession
	// uses eSearch
	
	$results = array();
	$results['samples'] = array();
	
	$url_1 = 'http://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?db=gds&term='.$GEO_accession.'&usehistory=y';
	$results['msg'][] = "Looking for project GEO code - $url_1";
	$xml_1 = simplexml_load_file($url_1);
	// Check if we have any Ids - if not, accession probably wrong
	if(!isset($xml_1->IdList->Id)){
		$error = "No datasets found";
		return false;
	}
	$WebEnv = $xml_1->WebEnv;
	
	// Get the second XML file with GEO meta data and dataset information
	$url_2 = 'http://eutils.ncbi.nlm.nih.gov/entrez/eutils/esummary.fcgi?db=gds&query_key=1&WebEnv='.$WebEnv;
	$results['msg'][] = "Loading second URL for project GEO code - $url_2";
	$xml_2 = simplexml_load_file($url_2);
	// Check for an error
	if(isset($xml_2->ERROR)) {
		return false;
	}
	
	$firstDocSum = true;
	foreach($xml_2->children() as $DocSum){
		// Most of what we want is found in the first DocSum node
		if($firstDocSum){
			$firstDocSum = false;
			foreach($DocSum->children() as $child) {
				switch ($child->attributes()->Name) {
					case 'GSE':
						$results['GSE_acc'] = 'GSE'.$child;
						break;
					case 'taxon':
						$results['species'] = (string)$child;
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
		
		} /* GEO data isn't really good enough for this * /
		else {
			// Now we can loop through the other nodes to see if we can find any cell types
			$isthisGSM = false;
			$nodeTitle = '';
			$nodeSummary = '';
			foreach($DocSum->children() as $child) {
				if($child->attributes()->Name == 'entryType' && (string)$child == 'GSM'){
					$isthisGSM = true;
				}
				if($child->attributes()->Name == 'title'){
					$nodeTitle = (string)$child;
				}
				if($child->attributes()->Name == 'summary'){
					$nodeTitle = (string)$child;
				}
			}
			if($isthisGSM){
				foreach($results['samples'] as $acc => $sample){
					if($sample['name'] == $nodeTitle){
						$results['samples'][$acc]['cell_type'] = $nodeSummary;
					}
				}
			}
		}
		/* */
	}
	
	
	// Find SRA codes
	/* NEW CODE - this runs a single request for the experiment GSE accesion */
	if($sra_codes){
		$url_1 = 'http://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?db=sra&term='.$GEO_accession.'&usehistory=y';
		$results['msg'][] = "Looking for project SRA codes - $url_1";
		$xml_1 = simplexml_load_file($url_1);
		// Check if we have any Ids - if not, accession probably wrong
		if(!isset($xml_1->IdList->Id)){
			$error = "No datasets found";
			$results['msg'][] = "$GEO_accession  - not found in NCBI SRA database";
			//return false;
		} else {
			$WebEnv = $xml_1->WebEnv;
			$url_2 = 'http://eutils.ncbi.nlm.nih.gov/entrez/eutils/esummary.fcgi?db=sra&query_key=1&WebEnv='.$WebEnv;
			$results['msg'][] = "Loading second URL for project SRA codes - $url_2";
			$xml_2 = simplexml_load_file($url_2);
			// Check for an error
			if(isset($xml_2->ERROR)) {
				//return false;
				$results['msg'][] = "$GEO_accession - SRA error message found";
			} else {
				$total_bases = 0;
				foreach($xml_2->children() as $DocSum){
					$sra_accessions = array();
					$gsm_acc = false;
					$library_name = false;
					foreach($DocSum->children() as $child) {
						// Find GSM accession for this experiment
						if($child->attributes()->Name == 'ExpXml') {
							$sra_raw = (string)$child;
							if(preg_match('/GSM\d\d\d\d\d\d/', $sra_raw, $matches)){
								$gsm_acc = $matches[0];
								$results['msg'][] = "GSM match - $gsm_acc";
							}
							if(preg_match('#\<LIBRARY_NAME>(.+?)</LIBRARY_NAME>#', $sra_raw, $matches)){
								$library_name = $matches[1];
								$results['msg'][] = "library name match - $library_name";
							}
							if(preg_match('#\<Organism taxid="\d\d\d\d\d" CommonName="(.+?)"/>#', $sra_raw, $matches)){
								$organism = $matches[1];
								$results['msg'][] = "organism name match - $organism";
							}
							if(preg_match('#\<LIBRARY_STRATEGY>(.+?)</LIBRARY_STRATEGY>#', $sra_raw, $matches)){
								$methodology = $matches[1];
								$results['msg'][] = "methodology match - $methodology";
							}
						}
						// Find SRA accessions for this experiment
						if($child->attributes()->Name == 'Runs') {
							$sra_raw = (string)$child;
							if(preg_match_all('/SRR\d\d\d\d\d\d/', $sra_raw, $sra_accessions)){
								$results['msg'][] = "SRA match - ".implode(" ", $sra_accessions[0]);
							}
						} // geo sra tag name check
					} // geo xml foreach
					
					// No GSM found in SRA result, try to match by name
					$this_acc = false;
					if(!$gsm_acc && $library_name){
						$matches = array();
						$this_acc = '';
						foreach ($results['samples'] as $acc => $name) {
							if(preg_match('/'.$name['name'].'/i', $library_name)){
								$matches[$acc] = strlen($name['name']);
							}
						}
						// If we have more than one match, pick the one with the longest string
						arsort($matches);
						foreach($matches as $key => $var){
							$results['msg'][] = "Setting $key - ".implode(' ', $sra_accessions[0]);
							$this_acc = $key;
							break;
						}
					}
					
					// Associate SRA accessions with datasets by GSM acc
					if($gsm_acc){
						foreach ($results['samples'] as $acc => $name) {
							if($acc == $gsm_acc){
								$this_acc = $acc;
							}
						}
					}
					
					// Assign values
					if($this_acc){
						$results['samples'][$this_acc]['sra'] .= implode(' ', $sra_accessions[0]).' '; // That's right, sometimes there can be multiple SRA results. Because that's sensible. Gah!
						$results['samples'][$this_acc]['organism'] = $organism;
						$results['samples'][$this_acc]['methodology'] = $methodology;
					}
				}
			}
		}
	}
	/* OLD CODE - this runs a request for every dataset GSM accession * /
	if ($sra_codes) {
		$results['msg'][] = 'Looking for SRA codes';
		foreach ($results['samples'] as $acc => $name) {
			$url_1 = 'http://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?db=sra&term='.$acc.'&usehistory=y';
			$xml_1 = simplexml_load_file($url_1);
			// Check if we have any Ids - if not, accession probably wrong
			if(!isset($xml_1->IdList->Id)){
				$error = "No datasets found";
				$results['msg'][] = "$acc  - SRA not found";
				//return false;
			}
			$WebEnv = $xml_1->WebEnv;
			
			$url_2 = 'http://eutils.ncbi.nlm.nih.gov/entrez/eutils/esummary.fcgi?db=sra&query_key=1&WebEnv='.$WebEnv;
			$xml_2 = simplexml_load_file($url_2);
			// Check for an error
			if(isset($xml_2->ERROR)) {
				//return false;
				$results['msg'][] = "$acc - SRA error message found";
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
	//* */
	
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