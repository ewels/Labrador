<?php
/*
	Create Dataset (create_dataset.php)
	- Handles form input for creating a new dataset in the database
	- Included by index.php if hidden form value is detected in POST
*/

$error = false;
$error_fields = array();
$msg = array();

$last_modified = time();
$paper = mysql_real_escape_string(trim($_POST['paper']));
if(!is_numeric($paper)) {
	echo 'Error - paper ID must be numeric.';
	exit;
}

$active_datasets = explode(' ', mysql_real_escape_string(trim($_POST['active_datasets'])));

foreach($active_datasets as $i){
	if(is_numeric($i) && isset($_POST[$i.'_name'])) {
	
		$name = mysql_real_escape_string(trim( $_POST[$i.'_name'] ));
		$species = mysql_real_escape_string(trim( $_POST[$i.'_species'] ));
		$cell_type = mysql_real_escape_string(trim( $_POST[$i.'_cell_type'] ));
		$data_type = mysql_real_escape_string(trim( $_POST[$i.'_data_type'] ));
		$geo_accession = mysql_real_escape_string(trim( $_POST[$i.'_geo_accession'] ));
		$sra_accession = mysql_real_escape_string(trim( $_POST[$i.'_sra_accession'] ));
		$srx_accession = mysql_real_escape_string(trim( $_POST[$i.'_srx_accession'] ));

		$name = empty($name) ? 'NULL' : "'".$name."'";
		$species = empty($species) ? 'NULL' : "'".$species."'";
		$cell_type = empty($cell_type) ? 'NULL' : "'".$cell_type."'";
		$data_type = empty($data_type) ? 'NULL' : "'".$data_type."'";
		$geo_accession = empty($geo_accession) ? 'NULL' : "'".$geo_accession."'";
		$sra_accession = empty($sra_accession) ? 'NULL' : "'".$sra_accession."'";
		$srx_accession = empty($srx_accession) ? 'NULL' : "'".$srx_accession."'";
		
		$query = "INSERT INTO `datasets` (
				`paper_id`,
				`name`,
				`species`,
				`cell_type`,
				`data_type`,
				`geo_accession`,
				`sra_accession`,
				`srx_accession`,
				`last_modified`
			) VALUES (
				$paper,
				$name,
				$species,
				$cell_type,
				$data_type,
				$geo_accession,
				$sra_accession,
				$srx_accession,
				$last_modified
			)";
			
		// echo $query; exit;
		
		if (mysql_query($query)) {
			$msg[] = '<strong>Success!</strong><br>Saved new dataset.';
		} else {
			$error = true;
			$msg[] = 'Could not save dataset: '.mysql_error().'<br>Query: '.$query.'<br><br>';
		}
	
	}
}

?>