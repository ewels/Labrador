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

$config_file = __DIR__."/../conf/labrador_config.php";

if(!file_exists($config_file)){
	print "<html><head><title>Labrador: No config</title><style>body { padding:20px; font-family:sans-serif; } </style></head><body><h1>Welcome to Labrador</h1><p>Hi there! We weren't able to find a config file, which is needed to use Labrador.</p> <p>Please copy the example config file <code>conf/labrador_config.php.example</code> to <code>conf/labrador_config.php</code> and update it with your settings.</body></html>";
	exit;
}

require($config_file);

// Log in to the database
if($db_password){
	$dblink = mysqli_connect($db_host, $db_user, $db_password, $db_database) or die("Error " . mysqli_error($link));
} else {
	$dblink = mysqli_connect($db_host, $db_user, false, $db_database) or die("Error " . mysqli_error($link));
}

require('functions.php');

require('auth.php');


date_default_timezone_set('Europe/London');

/* Do update checks */
# TABLE `config`
# 	`option` = 'database_version'
# 	`option` = 'update_last_checked'
# 	`option` = 'update_avail_version'
# 	`option` = 'update_version_ignore'

?>
