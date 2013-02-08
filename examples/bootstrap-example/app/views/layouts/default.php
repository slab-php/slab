<!DOCTYPE html>
<html>
	<head>
		<title><?php e($pageTitle); ?></title>
	    <meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link href="<?php e($html->url('/bootstrap/css/bootstrap.min.css')); ?>" rel="stylesheet" media="screen">
		<link href="<?php e($html->url('/css/site.css')); ?>" rel="stylesheet" media="screen">
		<link href="<?php e($html->url('/bootstrap/css/bootstrap-responsive.min.css')); ?>" rel="stylesheet" media="screen">
		<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
		<!--[if lt IE 9]>
			<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
	</head>
	<body>

		<div class="navbar navbar-inverse navbar-fixed-top">
			<div class="navbar-inner">
				<div class="container-fluid">
					<a class="brand" href="<?php e($html->url('/')); ?>">Slab</a>
					<div class="nav-collapse collapse">
						<p class="navbar-text pull-right">
							<a href="#">Log in</a>
						</p>
					</div>
				</div>
			</div>
		</div>
		
		<div class="container-fluid">
			<div class="row-fluid">
				<div class="span3">

					<div class="well sidebar-nav">
						<ul class="nav nav-list">
							<li class="nav-header">Application</li>
							<li><a href="<?php e($html->url('/test')); ?>">Index</a></li>
							<li><a href="<?php e($html->url('/test/hello_world')); ?>">Hello world</a></li>
							<li><a href="<?php e($html->url('/test/hello_world_via_get')); ?>">Hello world via GET</a></li>
							<li><a href="<?php e($html->url('/test/test_dispatch')); ?>">Test dispatch</a></li>
						</ul>
					</div>

				</div>
				<div class="span9">

					<div class="hero-unit">
						<h1>Bootstrap default layout</h1>
						<?php e($pageContent); ?>
					</div>

				</div>
			</div>

			<footer>
				<p><a href="http://swxben.com">Software by Ben Pty Ltd</a> | <a href="http://swxben.com">SWXBEN</a></p>
		</div>

		<script src="http://code.jquery.com/jquery-latest.js"></script>
		<script src="<?php e($html->url('/bootstrap/js/bootstrap.min.js')); ?>"></script>
	</body>
</html>