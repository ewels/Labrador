<?php

$dblink = mysql_connect('localhost', 'root','Inform6223');
if (!$dblink) die('Could not connect: ' . mysql_error());
$db_selected = mysql_select_db('labrador', $dblink);
if (!$db_selected) die ('Can\'t use database : ' . mysql_error());

?>