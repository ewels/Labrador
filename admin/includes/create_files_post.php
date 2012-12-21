<?php

include('../../includes/db_login.php');

$error = false;
$error_msg = array();
$count = 0;
$modified = time();

foreach ($_POST['raw'] as $dataset_raw => $files){
  $dataset = substr($dataset_raw, 12);
  foreach ($files as $file){
    $query = "INSERT INTO `files_raw` (`dataset_id`, `filename`, `read_length`, `num_reads`, `modified`) VALUES ('$dataset', '".$file['path']."', '".$file['readLength']."', '".$file['numReads']."', '$modified')";
    if(!mysql_query($query)){
	 $error = true;
	 $error_msg[] = mysql_error()."<ul><li>$query</li></ul>";
    } else {
      $count++;
    }
  }
}


foreach ($_POST['aligned'] as $dataset_raw => $files){
  $dataset = substr($dataset_raw, 12);
  foreach ($files as $file){
    $query = "INSERT INTO `files_aligned` (`dataset_id`, `filename`, `genome`, `parameters`, `num_reads`, `modified`) VALUES ('$dataset', '".$file['path']."', '".$file['genome']."', '".$file['parameters']."', '".$file['numReads']."', '$modified')";
    if(!mysql_query($query)){
	 $error = true;
	 $error_msg[] = mysql_error()."<ul><li>$query</li></ul>";
    } else {
      $count++;
    }
  }
}

foreach ($_POST['derived'] as $dataset_raw => $files){
  $dataset = substr($dataset_raw, 12);
  foreach ($files as $file){
    $query = "INSERT INTO `files_derived` (`dataset_id`, `filename`, `type`, `notes`, `modified`) VALUES ('$dataset', '".$file['path']."', '".$file['type']."', '".$file['notes']."', '$modified')";
    if(!mysql_query($query)){
	 $error = true;
	 $error_msg[] = mysql_error()."<ul><li>$query</li></ul>";
    } else {
      $count++;
    }
  }
}

  if($error) {
    echo '<div class="modal-header"><h3>Error</h3></div><div class="modal-body"><p>Uh oh, there were some errors - this is what mySQL said:</p><ul>';
    foreach($error_msg as $msg){ echo "<li>$msg</li>"; }
    echo '</ul></div><div class="modal-footer"><button class="btn btn-primary" data-dismiss="modal">Close</button></div>';
  } else {
    echo '<div class="modal-header"><h3>File Organisation Saved</h3></div><div class="modal-body"><p>Great success! Everything worked and '.$count.' files were recorded...</p></div><div class="modal-footer"><button class="btn" data-dismiss="modal">Close</button><a class="btn btn-primary" href="index.php">Back to admin homepage</a></div>';
  }


?>