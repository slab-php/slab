<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Slab - Error</title>
		<style type="text/css">
			body {
				background-image: url(<?php e($html->url('/slab_internals/rhps')); ?>);
				background-size: 100% auto;
				background-repeat: no-repeat;
				background-color: #000;
				padding: 2em 50% 0 2%;
				font-family: sans-serif;
			}
			.content-container {
				margin: 0 auto;
				background: rgba(255,255,255,0.7);
				padding: 1em;
			}
			pre {
				white-space: pre-wrap;
			}
		</style>
	</head>
	<body>
		<div class="content-container">
			<h1>Slab - error</h1>
			<p>Unhandled exception at line <?php e($ex->getLine()); ?> of <code><?php e($ex->getFile()); ?></code>:

			<?php pr($ex->getMessage()); ?>

			<p>Stack trace:</p>
			<?php pr($ex->getTraceAsString()); ?>
			<hr/>
			<p>
				<small><a href="https://github.com/swxben/slab">Slab</a> by 
					<a href="http://swxben.com">Software by Ben Pty Ltd</a> 
					and contributors | <a href="http://swxben.com">SWXBEN</a></small>
			</p>
		</div>
	</body>
</html>
