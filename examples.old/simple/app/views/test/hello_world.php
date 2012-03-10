<form action="<?php e($html->url('/test/hello_world')); ?>" method="post">
	<p>Name: <input type="text" name="data[name]"/></p>
	<button type="submit">Set name</button>
</form>

<p><?php if ($name == '') { ?>
	Enter your name above
<?php } else { ?>
	Hello, <em><?php eh($name); ?></em>!
<?php } ?></p>