<?php

/*
* start.php
* ---------
* This is the first thing to be called in all pages. No output so as not to disrupt sending of headers.
*
*/

session_start();

require('config.php');

// Log in to the database
if($db_password){
	$dblink = mysql_connect($db_host, $db_user, $db_password);
} else {
	$dblink = mysql_connect($db_host, $db_user);
}
if (!$dblink) die('Could not connect: ' . mysql_error());
$db_selected = mysql_select_db($db_database, $dblink);
if (!$db_selected) die ('Can\'t use database : ' . mysql_error());

require('functions.php');

require('auth.php');


date_default_timezone_set('Europe/London');

?>