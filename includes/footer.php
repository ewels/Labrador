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
Template file - provides footer at the bottom of each page.
*/

?>
	<footer>
		<hr>
		<p><small>Labrador Data Management System. Written by <a href="http://phil.ewels.co.uk" target="_blank">Phil Ewels</a> at the <a href="http://www.babraham.ac.uk" target="_blank">Babraham Institute</a>, Cambridge, UK.</small></p>
		<p><small><a href="<?php echo $labrador_url; ?>documentation/">Read the Labrador Documenation here</a>.</small></p>
	</footer>
	<?php if(function_exists('labrador_login_modal')){ labrador_login_modal(); } ?>
	</body>
</html>