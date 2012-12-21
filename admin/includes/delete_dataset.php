<?php

include('../../includes/db_login.php');

if(!is_numeric($_GET['dataset_id']) || $_GET['dataset_id'] < 1){
  echo "Error: dataset ID doesn't look right.";
  exit;
}

$queries = array();
$queries[] = "DELETE FROM `files_raw` WHERE `dataset_id` = '".$_GET['dataset_id']."'";
$queries[] = "DELETE FROM `files_aligned` WHERE `dataset_id` = '".$_GET['dataset_id']."'";
$queries[] = "DELETE FROM `files_derived` WHERE `dataset_id` = '".$_GET['dataset_id']."'";
$queries[] = "DELETE FROM `datasets` WHERE `id` = '".$_GET['dataset_id']."'";

$error = false;
$errors = array();
foreach($queries as $query){
  if(!mysql_query($query)){
    $error = true;
    $errors[] = mysql_error();
  }
}

if($error){
  echo 'mySQL Error: '.implode("\n\n",$errors);
 } else {
  echo 'deleted';
 }

?>