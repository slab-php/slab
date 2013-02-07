<html>
<head>
	<title><?php e($pageTitle); ?></title>
</head>
<body>
<h1>Default layout</h1>

<ul>
	<li><a href="<?php e($html->url('/test')); ?>">Index</a></li>
	<li><a href="<?php e($html->url('/test/hello_world')); ?>">Hello world</a></li>
	<li><a href="<?php e($html->url('/test/hello_world_via_get')); ?>">Hello world via GET</a></li>
	<li><a href="<?php e($html->url('/test/test_dispatch')); ?>">Test dispatch</a></li>
</ul>

<p>-- content starts --</p>
<?php e($pageContent); ?>
<p>-- content ends --</p>
</body>
</html>