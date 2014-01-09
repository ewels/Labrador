<?php

##########################################################################
# Copyright 2013, Philip Ewels (phil.ewels@babraham.ac.uk)               #
#                                                                        #
# This file is part of Labrador.                                         #
#                                                                        #
# Labrador is free software: you can redistribute it and/or modify       #
# it under the terms of the GNU General Public License as published by   #
# the Free Software Foundation, either version 3 of the License, or      #
# (at your option) any later version.                                    #
#                                                                        #
# Labrador is distributed in the hope that it will be useful,            #
# but WITHOUT ANY WARRANTY; without even the implied warranty of         #
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the          #
# GNU General Public License for more details.                           #
#                                                                        #
# You should have received a copy of the GNU General Public License      #
# along with Labrador.  If not, see <http://www.gnu.org/licenses/>.      #
##########################################################################

/*
* start.php
* ---------
* This is the first thing to be called in all pages. No output so as not to disrupt sending of headers.
*
*/

session_start();

require(__DIR__."/../conf/labrador_config.php");

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