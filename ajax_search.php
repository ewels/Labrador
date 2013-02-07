<?php

session_start();

// Connect to database
include('includes/db_login.php');

// Cookie fields and filters
$search_fields = explode(',', $_COOKIE['search_fields']);
$search_filters = explode(',', $_COOKIE['search_filters']);
$search_fields_papers = array();
$search_fields_datasets = array();
$search_filters_species = array();
$search_filters_cellTypes = array();
$search_filters_dataTypes = array();

// remove empty array values and create separate arrays
foreach($search_fields as $var){
	if(trim($var) !== ''){
		$nvar = preg_replace('/[^\w]/', '_', substr($var, 16));
		if(substr($var, 0, 16) == 'search_fields_p_'){
			$search_fields_papers[] = $nvar;
		}
		if(substr($var, 0, 16) == 'search_fields_d_'){
			$search_fields_datasets[] = $nvar;
		}
	}
}
foreach($search_filters as $var){
	if(trim($var) !== ''){
		$nvar = preg_replace('/[^\w]/', '_', substr($var, 18));
		if(substr($var, 0, 18) == 'search_filters_sp_'){
			$search_filters_species[] = $nvar;
		}
		if(substr($var, 0, 18) == 'search_filters_ct_'){
			$search_filters_cellTypes[] = $nvar;
		}
		if(substr($var, 0, 18) == 'search_filters_dt_'){
			$search_filters_dataTypes[] = $nvar;
		}
	}
}

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

// Prepare query - split into words
$q = trim($_GET['q']);
if(empty($q)){
	$query = false;
} else {
	$qs = explode(' ', $q);
}

/*
if(!$q){
	exit;
	//$query = "SELECT * FROM `papers` ORDER BY `first_author`, `year`";
	//$browse = true;
} else {
*/
	$query = "SELECT * FROM `papers` INNER JOIN `datasets` ON `papers`.`id` = `datasets`.`paper_id` \n\n WHERE \n\n";
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

		if($i > 0) $query .= " \n\n AND \n\n";
		$query .= " (".implode(' OR ', $fields).') ';
		$i++;
	}
	if(count($search_filters_species) > 0 || count($search_filters_cellTypes) > 0 || count($search_filters_dataTypes) > 0){
		if($i > 0) {
			$query .= "\n\n AND \n\n";
		}
		$i = 0;
		if(count($search_filters_species) > 0){
			// Using LIKE instead of = allows _ to act as a one character wildcard, so _ can equal a space (eg. Mus_musculus = Mus musculus)
			$query .= "( `datasets`.`species` LIKE '". implode("' OR `datasets`.`species` LIKE '", $search_filters_species)."') ";
			$i++;
		}
		if(count($search_filters_cellTypes) > 0){
			if($i > 0) { $query .= " AND "; }
			$query .= "(`datasets`.`cell_type` LIKE '". implode("' OR `datasets`.`cell_type` LIKE '", $search_filters_cellTypes)."') ";
			$i++;
		}
		if(count($search_filters_dataTypes) > 0){
			if($i > 0) { $query .= " AND "; }
			$query .= "(`datasets`.`data_type` LIKE '". implode("' OR `datasets`.`data_type` LIKE '", $search_filters_dataTypes)."') ";
			$i++;
		}
	}
	$query .= "\n\n ORDER BY `first_author`, `year`";
	$browse = false;
// }
$datasets = mysql_query($query);
$num_results = mysql_num_rows($datasets);

//echo '<pre>'.print_r($search_filters_cellTypes, true).'</pre>';
//echo '<pre>'.print_r($_COOKIE, true).'</pre>';
// echo '<pre>'.$query.'</pre>';


if($num_results > 0) { ?>

<!-- Unsymantic (redundant) form to keep consistent page styling - sorry! -->
<form id="search_results">
	<fieldset>
		<legend>
			<div class="pull-right">
				<a href="javascript:void(0);" onclick="$('.search_result_hidden_div').slideDown();" class="btn btn-mini">Reveal All</a>
				<a href="javascript:void(0);" onclick="$('.search_result_hidden_div').slideUp();" class="btn btn-mini">Hide All</a>
			</div>
			Search Results: <span style="font-weight:bold; background-color: #FFFFAB; padding: 3px 5px;"><?php echo $_GET['q']; ?></span> <span class="label label-<?php echo ($num_results > 0 ? 'success' : 'important') ;?>"><?php echo $num_results; ?></span>
		</legend>
	</fieldset>
</form>


<div style="width:100%; ">
		<?php
		$last_fauthor = '';
		$last_year = '';
		$p = 0;
		$content = '';
		$counts = array();
		$pdatasets = array();
		while($result = mysql_fetch_array($datasets)) {
			$counts[$result['first_author'].'_'.$result['year']]++;
			$pdatasets[] = $result;
		}
		foreach ($pdatasets as $result){
			if($last_fauthor !== $result['first_author'] && $last_year !== $result['year']){
				$last_fauthor = $result['first_author'];
				$last_year = $result['year'];
				if($p > 0) echo '</table></div>';
				$p++;
				?>
					<h4>
						<span class="label label-success"><?= $counts[$result['first_author'].'_'.$result['year']] ?></span>
						<a href="javascript:void(0);" onclick="$('#paper-browser-table_<?= $result['first_author'].'_'.$result['year'] ?>').slideToggle();"><?= $result['first_author'] ?>, <?= $result['year'] ?></a>
					</h4>
					<div id="paper-browser-table_<?= $result['first_author'].'_'.$result['year'] ?>" style="display:none;" class="search_result_hidden_div">
					<table class="table table-striped table-hover table-condensed table-bordered small" style="cursor:pointer;">
					<tr>
						<th width="40%">Dataset Name</th>
						<th width="30%">Cell Type</th>
						<th width="10%">Species</th>
						<th width="20%">Data Type</th>
					</tr>
			<?php }
			// highlight search terms
			if($q){
				$result = preg_replace('~(' . implode('|', $qs) . ')~i', '<span style="font-weight:bold; background-color: #FFFFAB;">$0</span>', $result);
			}
			?>
				<tr id="dataset_<?= $result['id'] ?>" class="dataset">
					<td><?= $result['name']; ?></td>
					<td><?= $result['cell_type']; ?></td>
					<td><?= $result['species']; ?></td>
					<td><?= $result['data_type']; ?></td>
				</tr>
		<?php }
	if($p > 0) echo '</table></div>'; ?>
</div>



<?php } else {

	echo '<div class="alert alert-error">No results</div>';

}?>
