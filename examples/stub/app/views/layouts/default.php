<html>
<head>
	<title><?php e($pageTitle); ?></title>
</head>
<body>

	<h1>stub/app/views/layouts/default.php</h1>

	<p>-- content starts --</p>
	<?php e($pageContent); ?>
	<p>-- content ends --</p>

	<?php e($dispatcher->pageLogger->to_table()); ?>

</body>
</html>