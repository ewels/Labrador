<?php

include('includes/start.php');

include('includes/header.php');

function h ($string) {
	global $regex_terms;
	return preg_replace($regex_terms, '<span class="h">$0</span>', $string);
}

$search_raw = trim($_GET['s']);
preg_match_all('/"(?:\\\\.|[^\\\\"])*"|\S+/', $search_raw, $terms_raw);
$terms = $terms_raw[0];
if(count($terms) == 0){
	header("Location: index.php");
}
// strip " marks from search entries
foreach($terms as $key => $term){
	if(substr($terms[$key],0,1) == '"'){
		$terms[$key] = substr($terms[$key],1);
	}
	if(substr($terms[$key],-1) == '"'){
		$terms[$key]= substr($terms[$key],0,strlen($terms[$key])-1);
	}
}
foreach($terms as $term){
	$regex_terms[] = "/$term/i";
}

$project_fields = array('name', 'title', 'description', 'accession_geo', 'accession_sra', 'accession_ena', 'accession_ddjb', 'contact_name', 'contact_email', 'contact_group', 'assigned_to', 'notes');
$sql = "SELECT * FROM `projects` WHERE ";
foreach ($project_fields as $field){
	foreach ($terms as $term){
		$sql .= "LOWER(`$field`) LIKE '%".strtolower($term)."%' OR ";
	}
}
$sql = substr($sql, 0, strlen($sql) - 4);
$project_q = mysql_query($sql);
$num_project = mysql_num_rows($project_q);


$publication_fields = array('year', 'journal', 'title', 'authors', 'pmid', 'doi');
$sql = "SELECT * FROM `papers` WHERE ";
foreach ($publication_fields as $field){
	foreach ($terms as $term){
		$sql .= "LOWER(`$field`) LIKE '%".strtolower($term)."%' OR ";
	}
}
$sql = substr($sql, 0, strlen($sql) - 4);
$publications_q = mysql_query($sql);
$num_publications = mysql_num_rows($publications_q);

$dataset_fields = array('name', 'species', 'cell_type', 'data_type', 'accession_geo', 'accession_sra', 'notes');
$sql = "SELECT * FROM `datasets` WHERE ";
foreach ($dataset_fields as $field){
	foreach ($terms as $term){
		$sql .= "LOWER(`$field`) LIKE '%".strtolower($term)."%' OR ";
	}
}
$sql = substr($sql, 0, strlen($sql) - 4);
$dataset_q = mysql_query($sql);
$num_datasets = mysql_num_rows($dataset_q);




?>

<a class="labrador_help_toggle pull-right" href="#labrador_help" title="Help"><i class="icon-question-sign"></i></a>
<h1>Search results for <span class="h"><?php echo implode('</span> <span class="h">', $terms); ?></span></h1>
<div class="labrador_help" style="display:none;">
	<div class="well">
		<h3>Searching</h3>
		<p>The search looks for any project, publication or dataset containing any of your search keywords. You can search for a specific string using quotation marks, <em>eg.</em> <code>"example string"</code>.</p>
	</div>
</div>


<ul class="nav nav-tabs" id="myTab">
	<li class="active"><a data-toggle="tab" href="#projects" <?php if($num_project == 0){ echo 'class="disabled"'; } ?>>Projects &nbsp; <span class="badge <?php if($num_project > 0){ echo 'badge-success'; } ?>"><?php echo $num_project; ?></span></a></li>
	<li><a data-toggle="tab" href="#publications" <?php if($num_publications == 0){ echo 'class="disabled"'; } ?>>Publications &nbsp; <span class="badge <?php if($num_publications > 0){ echo 'badge-success'; } ?>"><?php echo $num_publications; ?></span></a></li>
	<li><a data-toggle="tab" href="#datasets" <?php if($num_datasets == 0){ echo 'class="disabled"'; } ?>>Datasets &nbsp; <span class="badge  <?php if($num_datasets > 0){ echo 'badge-success'; } ?>"><?php echo $num_datasets; ?></span></a></li>
</ul>


<div class="tab-content">
	<div class="tab-pane active" id="projects">
<?php if($num_project == 0){
	echo '<p>No projects found.</p>';
} else {
	while ($project = mysql_fetch_array($project_q)){
		echo '<h4><a href="project.php?id='.$project['id'].'">'.h($project['name']).'</a> &nbsp ';
		if($project['accession_geo']) echo accession_badges ($project['accession_geo'], 'geo');
		if($project['accession_sra']) echo accession_badges ($project['accession_sra'], 'sra');
		if($project['accession_ena']) echo accession_badges ($project['accession_ena'], 'ena');
		if($project['accession_ddjb']) echo accession_badges ($project['accession_ddjb'], 'ddjb');
		echo '</h4>';
		
		if($project['title']) echo '<p>'.h($project['title']).'</p>';
		if($project['description']) echo '<p><small class="muted">'.h($project['description']).'</small></p>';
		if($project['notes']) echo '<p><strong>Comments:</strong> <em>'.h($project['notes']).'</em></p>';
		
		echo '<hr>';
	}
} ?>
	</div>
	<div class="tab-pane" id="publications">
<?php if($num_publications == 0){
	echo '<p>No publications found.</p>';
} else {
	while ($pub = mysql_fetch_array($publications_q)){
		echo '<h4><a href="project.php?id='.$pub['project_id'].'">'.h($pub['title']).'</a></h4>';
		echo '<p><strong>'.h($pub['journal']).'</strong> ('.h($pub['year']).')</p>';
		echo '<p><small class="muted">'.h($pub['authors']).'</small></p>';
		if($pub['pmid'] || $pub['doi']) echo '<p><small>';
		if($pub['pmid']) echo 'PMID: <a href="http://www.ncbi.nlm.nih.gov/pubmed/'.$pub['pmid'].'" target="_blank">'.h($pub['pmid']).'</a> &nbsp; &nbsp; &nbsp; ';
		if($pub['doi']) echo 'DOI <a href="http://dx.doi.org/'.$pub['doi'].'" target="_blank">'.h($pub['doi']).'</a>';
		if($pub['pmid'] || $pub['doi']) echo '</small></p>';
		echo '<hr>';
	}
} ?>
	</div>
	<div class="tab-pane" id="datasets">
<?php if($num_datasets == 0){
	echo '<p>No datasets found.</p>';
} else {
	while ($ds = mysql_fetch_array($dataset_q)){
		echo '<h4><a href="datasets.php?id='.$ds['project_id'].'">'.h($ds['name']).'</a> &nbsp ';
		if($project['accession_geo']) echo accession_badges ($project['accession_geo'], 'geo');
		if($project['accession_sra']) echo accession_badges ($project['accession_sra'], 'sra');
		echo '</h4>';
		
		echo '<p>'.h($ds['species']).', '.h($ds['cell_type']).', '.h($ds['data_type']).'</p>';
		if($ds['notes']) echo '<p><strong>Comments:</strong> <em>'.h($ds['notes']).'</em></p>';
		
		echo '<hr>';
	}
} ?>
	</div>
</div>





	<?php include('includes/javascript.php'); ?>
	<script src="js/home.js" type="text/javascript"></script>
	</body>
</html>