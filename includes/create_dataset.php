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

// NOT USED
// Setting disabled attr with jquery stops values coming through with POST
// Have left code in place anyway, in case problems arise and it's better this way
/*
$active_datasets = explode(' ', mysql_real_escape_string(trim($_POST['active_datasets'])));
foreach ($active_datasets as $key => $var) {
	if(!is_numeric($var)) {
		unset ($active_datasets[$key]);
	}
}
if(count($active_datasets) < 1) {
	echo 'Error - no active datasets found.';
	exit;
}
*/

$i = 1;

while (isset($_POST[$i.'_name'])) {
	
	// if(in_array($i, $active_datasets)) {
	// Setting disabled attr with jquery stops values coming through with POST
	
		$name = mysql_real_escape_string(trim( $_POST[$i.'_name'] ));
		$species = mysql_real_escape_string(trim( $_POST[$i.'_species'] ));
		$cell_type = mysql_real_escape_string(trim( $_POST[$i.'_cell_type'] ));
		$data_type = mysql_real_escape_string(trim( $_POST[$i.'_data_type'] ));
		$geo_accession = mysql_real_escape_string(trim( $_POST[$i.'_geo_accession'] ));
		$sra_accession = mysql_real_escape_string(trim( $_POST[$i.'_sra_accession'] ));
		
		$query = "INSERT INTO `datasets` (
				`paper_id`,
				`name`,
				`species`,
				`cell_type`,
				`data_type`,
				`geo_accession`,
				`sra_accession`,
				`last_modified`
			) VALUES (
				'$paper',
				'$name',
				'$species',
				'$cell_type',
				'$data_type',
				'$geo_accession',
				'$sra_accession',
				'$last_modified'
			)";
			
		if (mysql_query($query)) {
			$msg[] = '<strong>Success!</strong><br>Saved new dataset.';
		} else {
			$error = true;
			$msg[] = 'Could not save dataset: '.mysql_error();
		}
		
	//}
	
	$i++;
}

?>