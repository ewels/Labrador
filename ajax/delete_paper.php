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
Script to delete a paper from the database with ajax
 - First field is 1 or 0 (success or fail)
 - Second field is message
*/

require_once('../includes/start.php');

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