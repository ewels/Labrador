<?php

session_start();

// Connect to database
include('includes/db_login.php');

// Cookie fields and filters
$search_fields = explode(',', $_COOKIE['search_fields']);
$search_filters = explode(',', $_COOKIE['search_filters']);
$search_fields_papers = array();
$search_fields_datasets = array();

// remove empty array values and create separate arrays
foreach($search_fields as $key => $var){
	if(trim($var) !== ''){
		if(substr($key, 16) == 'search_fields_p_'){
			$search_fields_papers[] = trim($var);
		}
		if(substr($key, 16) == 'search_fields_d_'){
			$search_fields_datasets[] = trim($var);
		}
	}
}
/*
foreach($search_fields as $key => $var){
	if(trim($var) == ''){
		unset ($search_fields[$key]);
	}
	if(substr($key, 16) == 'search_fields_p_'){
		$search_fields_datasets[] = trim($var);
	}
}
*/

// check for empty array
if(count($search_fields_papers) == 0){
	$search_fields_papers = array('title', 'authors', 'PMID', 'DOI', 'geo', 'sra', 'notes', 'requested', 'processed');
}
if(count($search_fields_datasets) == 0){
	$search_fields_datasets = array('name', 'species', 'cellType', 'dataType', 'geo', 'sra', 'notes');
}

// special case values
if(in_array('noFields', $search_fields_papers)) {
	$search_fields_papers = array();
}
if(in_array('noFields', $search_fields_datasets)) {
	$search_fields_datasets = array();
}

$q = trim($_GET['q']);
if(empty($q)){
	$query = false;
} else {
	$qs = explode(' ', $q);
}

if(!$q){
	$query = "SELECT * FROM `papers` ORDER BY `first_author`, `year`";
	$browse = true;
} else {
	$query = "SELECT * FROM `papers` INNER JOIN `datasets` ON `papers`.`id` = `datasets`.`paper_id` WHERE \n";
	$i = 0;
	foreach($qs as $qw){
		$fields = array();
		if(in_array('title', $search_fields_papers)) 		$fields[] = "`papers`.`paper_title` LIKE '%".$qw."%' ";
		if(in_array('authors', $search_fields_papers)) 		$fields[] = "`papers`.`authors` LIKE '%".$qw."%' ";
		if(in_array('PMID', $search_fields_papers)) 		$fields[] = "`papers`.`PMID` LIKE '%".$qw."%' ";
		if(in_array('DOI', $search_fields_papers)) 			$fields[] = "`papers`.`DOI` LIKE '%".$qw."%' ";
		if(in_array('geo', $search_fields_papers)) 			$fields[] = "`papers`.`geo_accession` LIKE '%".$qw."%' ";
		if(in_array('sra', $search_fields_papers)) 			$fields[] = "`papers`.`sra_accession` LIKE '%".$qw."%' ";
		if(in_array('notes', $search_fields_papers)) 		$fields[] = "`papers`.`notes` LIKE '%".$qw."%' ";
		if(in_array('requested', $search_fields_papers)) 	$fields[] = "`papers`.`requested_by` LIKE '%".$qw."%' ";
		if(in_array('processed', $search_fields_papers)) 	$fields[] = "`papers`.`processed_by` LIKE '%".$qw."%' ";
		if(in_array('name', $search_fields_datasets)) 		$fields[] = "`datasets`.`name` LIKE '%".$qw."%' "; 
		if(in_array('species', $search_fields_datasets)) 	$fields[] = "`datasets`.`species` LIKE '%".$qw."%' ";
		if(in_array('cellType', $search_fields_datasets)) 	$fields[] = "`datasets`.`cell_type` LIKE '%".$qw."%' ";
		if(in_array('dataType', $search_fields_datasets)) 	$fields[] = "`datasets`.`data_type` LIKE '%".$qw."%' ";
		if(in_array('geo', $search_fields_datasets)) 		$fields[] = "`datasets`.`geo_accession` LIKE '%".$qw."%' ";
		if(in_array('sra', $search_fields_datasets)) 		$fields[] = "`datasets`.`sra_accession` LIKE '%".$qw."%' ";
		if(in_array('notes', $search_fields_datasets)) 		$fields[] = "`datasets`.`notes` LIKE '%".$qw."%' ";

		if($i > 0) $query .= " AND ";
		$query .= " (".implode(' OR ', $fields).') ';
		$i++;
	}
	$query .= " ORDER BY `first_author`, `year`";
	$browse = false;
}
$datasets = mysql_query($query);
$num_results = mysql_num_rows($datasets);

//echo '<pre>'.print_r($search_fields, true).'</pre>';
//echo '<pre>'.print_r($search_fields_datasets, true).'</pre>';
//echo '<pre>'.$query.'</pre>';



?>

<!-- Unsymantic (redundant) form to keep consistent page styling - sorry! -->
<form id="browse">
	<fieldset>
		<legend><?php echo $browse ? 'Browse Papers' : 'Search Results: <span style="font-weight:bold; background-color: #FFFFAB; padding: 3px 5px;">'.$_GET['q'].'</span> <span class="label label-'.($num_results > 0 ? 'success' : 'important').'">'.$num_results.'</span>'; ?></legend>
	</fieldset>
</form>
<?php /* * / ?>
<pre><?php echo $query; ?></pre>
<?php /* */ 

if($num_results > 0 && $browse){
?>
<div style="width:100%; overflow:auto;">
	<table id="paper-browser-table" class="table table-striped table-hover table-condensed table-bordered small" style="cursor:pointer;">
		<tr>
			<th width="10%">First Author</th>
			<th width="10%">Year of Publication</th>
			<th width="40%">Paper Title</th>
			<th width="40%">Authors</th>
		</tr>
		<?php
		while($result = mysql_fetch_array($datasets)): ?>
			<tr id="paper_<?= $result['id'] ?>" class="paper">
				<td><?= $result['first_author'] ?></td>
				<td><?= $result['year'] ?></td>
				<td><?= stripslashes($result['paper_title']) ?></td>
				<td><?php // limit the number of authors displayed and underline first and last.
				$authors_array = explode(',', $result['authors']);
				//echo $link;
				echo '<u>'.$authors_array[0].'</u>, ';
				if(count($authors_array) > 12){
					echo implode(', ', array_slice($authors_array, 1, 11)) . ' <span style="background-color:#CDCDCD;">...</span> ';
				} else {
					echo implode(', ', array_slice($authors_array, 1, -1));
				}
				echo ', <u>'.trim($authors_array[count($authors_array) - 1]).'</u>';
				;?>
				 </td>
			</tr>
		<?php endwhile; ?>
	</table>
</div>
<?php } else if($num_results > 0) { ?>
<div style="width:100%; overflow:auto;">
	<table id="paper-browser-table" class="table table-striped table-hover table-condensed table-bordered small" style="cursor:pointer;">
		<tr>
			<th width="10%">Paper</th>
			<th width="30%">Dataset Name</th>
			<th width="20%">Cell Type</th>
			<th width="20%">Species</th>
			<th width="20%">Data Type</th>
		</tr>
		<?php
		while($result = mysql_fetch_array($datasets)):
			// highlight search terms
			if(!$browse){
				$result = preg_replace('~(' . implode('|', $qs) . ')~i', '<span style="font-weight:bold; background-color: #FFFFAB; padding: 3px 5px;">$0</span>', $result);
			}
			?>
			<tr id="dataset_<?= $result['id'] ?>" class="dataset">
				<td><?= $result['first_author'].', '.$result['year'] ?></td>
				<td><?= $result['name']; ?></td>
				<td><?= $result['cell_type']; ?></td>
				<td><?= $result['species']; ?></td>
				<td><?= $result['data_type']; ?></td>
			</tr>
		<?php endwhile; ?>		
	</table>
</div>
<?php } else { // check for results ?>
<div class="alert alert-error">
	<strong>No results</strong> Sorry, no results were found for that search query...
</div>
<?php } ?>
