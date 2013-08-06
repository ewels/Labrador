<?php

/*
Script to delete a paper from the database with ajax
 - First field is 1 or 0 (success or fail)
 - Second field is message
*/

require('../includes/db_login.php');

if(isset($_GET['pid']) && is_numeric($_GET['pid'])){
	$query = sprintf("DELETE FROM `papers` WHERE `id` = '%d'", $_GET['pid']);
	if(mysql_query($query)){
		$result = array(1, "Successfully deleted paper");
	} else {
		$result = array(0, "Error - Could not delete paper: ".mysql_error());
	}
	
} else {
	$result = array(0, "Error - no numeric delete ID supplied.");
}

echo json_encode($result, JSON_FORCE_OBJECT);


?>