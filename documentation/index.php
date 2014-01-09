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


include('../includes/start.php');

require_once '../includes/PHP_Markdown_Lib_1.4.0/Michelf/Markdown.inc.php';
use \Michelf\Markdown;

$docs_md = file_get_contents('labrador_manual.md');
// strip heading numbers - keep markdown hashes if used
$docs_md = preg_replace('/^(#*)( )?\d?(\.\d)*/m',"$1", $docs_md);

// convert to HTML
$docs = Markdown::defaultTransform($docs_md);

$root = "../";
include('../includes/header.php');

?>

<div class="homepage sidebar-mainpage">
	<h1>Labrador Documentation</h1>
	<p class="lead">This documentation can also be found at <code><?php echo getcwd(); ?>/labrador_manual.md</code></p>
	<hr>
	<div id="docs">
		<?php echo $docs; ?>
	</div>
	<img class="pull-right visible-desktop" src="../img/puppy.jpg" style="margin: 0 -20px -40px 0;">
	<div class="clearfix"></div>
	<footer>
		<hr>
		<p><small>Labrador Data Management System. Written by <a href="http://phil.ewels.co.uk" target="_blank">Phil Ewels</a> at the <a href="http://www.babraham.ac.uk" target="_blank">Babraham Institute</a>, Cambridge, UK.</small></p>
		<p><small><a href="<?php echo $labrador_url; ?>/documentation/">Read the Labrador Documenation here</a>.</small></p>
	</footer>
</div>
<div class="homepage sidebar-nav">
	<h2>Table of Contents</h2>
	<ul class="nav nav-list filters" id="toc">
	</ul>
</div>

<?php include('../includes/javascript.php'); ?>

<script type="text/javascript">	
	var h1 = 1;
	var h2 = 1;
	var h3 = 1;
	var h4 = 1;
	$('#docs h1, #docs h2, #docs h3, #docs h4').each(function(){
		var id = '';
		var heading = $(this).text();
		if($(this).is("h1")){
			id = 'h1_'+h1;
			$('#toc').append('<li class="nav-header"><a href="#'+id+'">'+heading+'</a></li>');
			h1++;
		}
		if($(this).is("h2")){
			id = 'h2_'+h1+'.'+h2;
			$('#toc').append('<li><a href="#'+id+'">'+heading+'</a></li>');
			h2++;
		}
		if($(this).is("h3")){
			id = 'h3_'+h1+'.'+h2+'.'+h3;
			$('#toc').append('<li> &nbsp; <small><a href="#'+id+'">'+heading+'</a></small></li>');
			h3++;
		}
		if($(this).is("h4")){
			id = 'h4_'+h1+'.'+h2+'.'+h3+'.'+h4;
			$('#toc').append('<li> &nbsp;  &nbsp; <small><a href="#'+id+'">'+heading+'</a></small></li>');
			h4++;
		}
		if(id != ''){
			$(this).prepend('<a class="anchor" id="'+id+'" style="display: block; position: relative; top: -80px; visibility: hidden;"></a>')
		}
	});
</script>
</body>
</html>