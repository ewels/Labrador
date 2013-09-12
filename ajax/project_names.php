<?php
/*
Script to return project names for AJAX calls
*/

require_once('../includes/start.php');

header('Content-Type: application/json');

echo '{
	"projects": [';

$sql = "SELECT `name` FROM `projects` ORDER BY `name`";

if(isset($_GET['query'])) {
	$sql = sprintf("SELECT `name` FROM `projects` WHERE `name` LIKE '%s%%' ORDER BY `name`", mysql_real_escape_string($_GET['query']));
}
$results = mysql_query($sql);
if(mysql_num_rows($results) > 0){
$counter = 0;
while($result = mysql_fetch_array($results)){
	if($counter > 0) { echo ','; }
	$counter++;
	echo "\n\t\t".'"'.$result['name'].'"';
}
}

echo '
	]
}
';


?>