<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Reik Lab - Dataset Browser</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="../css/bootstrap.css" rel="stylesheet">
	<link href="../css/styles.css" rel="stylesheet">
        <link href="../css/smoothness/jquery-ui-1.9.2.custom.min.css" rel="stylesheet">
	<link href="../css/responsive.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

  </head>

	<body>
	
	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</a>
				<a class="brand" href="../index.php">Lab<span style="color:#333;">radar</span><sub style="font-size:10px;">BETA</sub></a>
				<div class="nav-collapse collapse">
					<ul class="nav">
						<li><a href="../index.php#search">Search</a></li>
						<li><a href="../index.php#browse">Browse</a></li>
						<li><a href="../create_paper.php">Request Dataset</a></li>
					</ul>
				</div>
				<a href="index.php" class="btn pull-right">Admin</a>
    <p class="navbar-text pull-right" style="margin-right:30px;"><?php echo $_SERVER['PHP_AUTH_USER']; ?></p>
			</div>
		</div>
	</div>
	
	<?php if(!empty($msg)): ?>
	<div class="container alert alert-<?php echo $error ? 'error' : 'success'; ?>">
		<button type="button" class="close" data-dismiss="alert">×</button>
		<?php echo $error ? '<strong>Error!</strong><br>' : ''; ?> 
		<?php foreach($msg as $var)	echo $var.'<br>'; ?>
	</div>
	<?php endif; ?>

    <div class="container container-white">
		<h1>Reik Lab Dataset Browser</h1>