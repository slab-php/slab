<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
   		<meta name="viewport" content="width=device-width, initial-scale=1.0">
   		<title>Static file</title>
   		<link href="<?php e(bootstrap/css/bootstrap.css" rel="stylesheet">
		<style type="text/css">
			body {
				padding-top: 60px;
				padding-bottom: 40px;
			}
			.sidebar-nav {
				padding: 9px 0;
			}
		</style>
		<link href="bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
		<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
		<!--[if lt IE 9]>
			<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
	</head>

	<body>

		<div class="navbar navbar-fixed-top">
			<div class="navbar-inner">
				<div class="container-fluid">
					<a class="brand" href="#">Slab</a>
					<div class="nav-collapse">
						<ul class="nav">
							<li class="active"><a href="#">Home</a></li>
							<li><a href="#about">About</a></li>
						</ul>
						<p class="navbar-text pull-right">static.html</a></p>
					</div>
				</div>
			</div>
		</div>

		<div class="container-fluid">
			
			<div class="row-fluid">
				<div class="span4">
					<div class="hero-unit">
						<h1>Slab</h1>

						<p>This is just a normal, static HTML file (static.html).</p>
						<p>The links below generally perform server side actions on controllers.</p>
					</div>
				</div>
				<div class="span4">
					<h2>How did we get here?</h2>
					<p>The <code>.htaccess</code> file redirected to <code>slab.php</code>, which is the entry point to Slab. The <code>app/config.php</code> file configured the <em>Dispatcher</em> with a default route:</p>
					<pre>Dispatcher::setDefaultRoute('/action_tests/redirect_to/static.html');</pre>
					<p>The <em>Dispatcher</em> used the default route and loaded the <code>ActionTest</code> controller (<code>app/controllers/action_test.php</code>), then executed the <code>redirect_to($url)</code> method, with <code>'static.html'</code> as the <code>$url</code> parameter. That method generated a <a href="http://en.wikipedia.org/wiki/HTTP_302">302 Found</a> response, causing a redirection to this page.</p>
					<p>Aren't you glad you asked?</p>
				</div>
				<div class="span4">
					<blockquote>So come up to the lab and see what's on the slab. I see you shiver with antici-<br/><br/><br/><br/><br/>-pation. But maybe the rain is really to blame, so I'll remove the cause but not the symptom.</blockquote>.
				</div>
			</div>

			<div class="row-fluid">
				<div class="span4">
					<h2>ActionTests <code>app/controllers/action_tests.php</code></h2>
					<ul>
						<li><a href="slab.php?slab_url=/action_tests/text_action_hello_world">text_action_hello_world</a></li>
					</ul>
				</div>
				<div class="span4">
				</div>
				<div class="span4">
				</div>
			</div>

			<hr>
			<footer>
				<p><small>
					<a href="https://github.com/belfryimages/slab">Slab</a>
					(<a href="http://creativecommons.org/licenses/by-sa/3.0/">CC BY-SA 3.0</a>)
					<a href="http://swxben.com">Software by Ben Pty Ltd | SWXBEN</a>
			</footer>

		</div>

	</body>
</html>