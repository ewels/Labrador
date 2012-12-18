<?php
/*
	Create Paper (create_paper.php)
	- Handles form input for creating a new paper in the database
	- Included by index.php if hidden form value is detected in POST
*/

$error = false;
$error_fields = array();
$msg = array();

$fields = array(
	'first_author',
	'year',
	'paper_title',
	'authors',
	'PMID',
	'DOI',
	'geo_accession',
	'sra_accession',
	'notes',
	'requested_by',
	'processed_by',
	'last_modified'
);

$form = array('last_modified' => time());
if(isset($_SESSION['email'])){
  $form['requested_by'] = $_SESSION['email'];
 }

// echo '<h4>POST Data</h4> <pre>'.print_r($_POST, true).'</pre>';

// Data sanitation
foreach ($_POST as $id => $var) {

	if(!in_array($id, $fields)) {
		continue;
	}

	$var = mysql_real_escape_string(trim($var));

	switch ($id) {

		// Required strings
		case 'title':
		case 'year':
			if(strlen($var) == 0){
				$error = true;
				$error_fields[] = 'name';
				$msg[] = $id.' is required.';
			}
			break;

		// Year - 4 digits
		case 'year':
			if(!empty($var) && (strlen($var) != 4 || !is_numeric($var))) {
				$error = true;
				$error_fields[] = 'year';
				$msg[] = 'Year must not be 4 numbers';
			}
			break;

		// Fields with a max length
		case 'first_author':
		case 'PMID':
		case 'DOI':
		case 'geo_accession':
			if(!empty($var) && strlen($var) > 250) {
				$error = true;
				$error_fields[] = $id;
				$msg[] = "$id is too long";
			}
			break;
	}

	$form[$id] = $var;
}

// Save dataset entry to database
if(!$error) {
	$query = "INSERT INTO `papers` (";
	foreach($form as $id => $var) {
		$query .= "`$id`, ";
	}
	$query = substr($query, 0, -2) . ") VALUES (";
	foreach($form as $id => $var) {
		$query .= "'$var', ";
	}
	$query = substr($query, 0, -2) . ")";

	if (mysql_query($query)) {
		$msg[] = '<strong>Success!</strong><br>Saved new paper.';
		$form = array();
	} else {
		$error = true;
		$msg[] = 'Could not save paper: '.mysql_error();
	}
}


?>