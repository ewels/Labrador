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
Script to return project names for AJAX calls
*/

require_once('../includes/start.php');

header('Content-Type: application/json');

echo '{
	"results": [';

$sql = "SELECT `name` FROM `projects` ORDER BY `name`";

if(isset($_GET['query'])) {
	$sql = sprintf("SELECT `name` FROM `projects` WHERE `name` LIKE '%s%%' ORDER BY `name`", mysqli_real_escape_string($dblink, $_GET['query']));
}
$results = mysqli_query($dblink, $sql);
if(mysqli_num_rows($results) > 0){
$counter = 0;
while($result = mysqli_fetch_array($results)){
	if($counter > 0) { echo ','; }
	$counter++;
	echo "\n\t\t".'"'.$result['name'].'"';
}
}



$sql = "SELECT `email` FROM `users` ORDER BY `email`";

if(isset($_GET['query'])) {
	$sql = sprintf("SELECT `email` FROM `users` WHERE `email` LIKE '%s%%' ORDER BY `email`", mysqli_real_escape_string($dblink, $_GET['query']));
}
$results = mysqli_query($dblink, $sql);
if(mysqli_num_rows($results) > 0){
$counter = 0;
while($result = mysqli_fetch_array($results)){
	if($counter > 0) { echo ','; }
	$counter++;
	echo "\n\t\t".'"'.$result['email'].'"';
}
}

echo '
	]
}
';


?>
