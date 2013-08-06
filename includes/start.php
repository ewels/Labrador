<?php

/*
* start.php
* ---------
* This is the first thing to be called in all pages. No output so as not to disrupt sending of headers.
*
*/

session_start();
include('db_login.php');

include('config.php');

include('functions.php');

date_default_timezone_set('Europe/London');

?>