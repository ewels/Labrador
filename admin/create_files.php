<?php

// Connect to database
include('../includes/db_login.php');


include('includes/admin_header.php');

$query = "SELECT * FROM `papers` WHERE `id` = '".$_GET['paper_id']."'";
$results = mysql_query($query);
$paper = mysql_fetch_array($results);
$directory = "/data/pipeline/public/TIDIED/".$paper['first_author']."_".$paper['year'].'/';

?>

                <img class="pull-right visible-desktop" src="../img/puppies/puppy_5.jpg" style="max-height:250px; margin-top:-50px;">
		<p class="lead">This page enables you to register files in the file system and associate them with datasets.</p>
		<p>You can also annotate files. For example, you can note down the alignment parameters used.</p>
		<p>The directory being searched for files is: <code><?= $directory ?></code></p>
		<p>This page can automate much of this work for you by looking at filenames in relevant directories. To do this, use the button below:</p>
		<p style="text-align:center;"><button class="btn btn-large btn-primary">Run boy, run!</button></p>
		<hr style="clear:both;">
		<div class="row">
<div class="span3">
<h2>Files</h2>

<h4>Directory 'raw'</h4>

<ul class="unknown_files nav nav-pills nav-stacked">
<?php
if($fh = opendir($directory.'raw/')) {
  while (false !== ($entry = readdir($fh))) {
    if($entry != "." && $entry != ".."){
      echo '<li class="file"><a>'.$entry.'</a></li>';
    }
  }
 }
closedir($fh)
?>


<h4>Directory 'aligned'</h4>

<ul class="unknown_files nav nav-pills nav-stacked">
<?php
if($fh = opendir($directory.'aligned/')) {
  while (false !== ($entry = readdir($fh))) {
    if($entry != "." && $entry != ".."){
      echo '<li class="file"><a>'.$entry.'</a></li>';
    }
  }
 }
closedir($fh)
?>


<h4>Directory 'derived'</h4>

<ul class="unknown_files nav nav-pills nav-stacked">
<?php
if($fh = opendir($directory.'derived/')) {
  while (false !== ($entry = readdir($fh))) {
    if($entry != "." && $entry != ".."){
      echo '<li class="file"><a>'.$entry.'</a></li>';
    }
  }
 }
closedir($fh)
?>



<h4>Root Directory</h4>

<ul class="unknown_files nav nav-pills nav-stacked">
<?php
if($fh = opendir($directory)) {
  while (false !== ($entry = readdir($fh))) {
    if($entry != "." && $entry != ".." && $entry != "raw" && $entry != "aligned" && $entry != "derived"){
      echo '<li class="file"><a>'.$entry.'</a></li>';
    }
  }
 }
closedir($fh)
?>



</ul>
</div>
<div class="span9">
<h2>Datasets</h2>
		<?php $query = "SELECT * FROM `datasets` WHERE `paper_id` = '".$_GET['paper_id']."' ORDER BY `name`";
$results = mysql_query($query);
while($result = mysql_fetch_array($results)): ?>

<div class="dataset_div" id="<?= $result['id'] ?>">
   <h4><?= $result['name']; ?></h4>

<div class="dataset_raw_div">
<h5>Raw</h5>
<ul class="nav nav-pills nav-stacked"></ul>
</div>

<div class="dataset_aligned_div">
<h5>Aligned</h5>
<ul class="nav nav-pills nav-stacked"></ul>
</div>

<div class="dataset_derived_div">
<h5>Derived</h5>
<ul class="nav nav-pills nav-stacked"></ul>
</div>
<div class="clearfix"></div>
</div>

<?php  endwhile; // end of dataset mysql while loop  ?>
		
</div>
  </div>
		




		

		
		</div> <!-- /container -->
		


		<!-- Le javascript
		================================================== -->
		<!-- Placed at the end of the document so the pages load faster -->
		<script src="../js/jquery-1.8.3.min.js"></script>
		<script src="../js/bootstrap-transition.js"></script>
		<script src="../js/bootstrap-alert.js"></script>
		<script src="../js/bootstrap-modal.js"></script>
		<script src="../js/bootstrap-dropdown.js"></script>
		<script src="../js/bootstrap-scrollspy.js"></script>
		<script src="../js/bootstrap-tab.js"></script>
		<script src="../js/bootstrap-tooltip.js"></script>
		<script src="../js/bootstrap-popover.js"></script>
		<script src="../js/bootstrap-button.js"></script>
		<script src="../js/bootstrap-collapse.js"></script>
		<script src="../js/bootstrap-carousel.js"></script>
		<script src="../js/bootstrap-typeahead.js"></script>
                <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>

<script type="text/javascript">
  $(function() {
      $('.dataset_raw_div ul, .dataset_aligned_div ul, .dataset_derived_div ul, .unknown_files').sortable({
	connectWith:  ".nav-pills"
	    }).disableSelection();

      /*
  $('.dataset_raw_div ul, .dataset_aligned_div ul, .dataset_derived_div ul, .unknown_files').sortable({ revert:true });

$('.file').draggable({
  connectToSortable: '.dataset_raw_div ul, .dataset_aligned_div ul, .dataset_derived_div ul, .unknown_files',
      helper: "clone",
      revert: "invalid"
      });
 $("ul, li").disableSelection();
      */
});

</script>
  </body>
</html>
