<ul>
	<li><a href="<?php e($html->url('/test/hello_world_via_get/red')); ?>">Red</a></li>
	<li><a href="<?php e($html->url('/test/hello_world_via_get/green')); ?>">Green</a></li>
	<li><a href="<?php e($html->url('/test/hello_world_via_get/blue')); ?>">Blue</a></li>
</ul>

<p><?php if (isset($colour)) { ?>
	You picked <span style="color:#<?php e($hex); ?>"><?php e($colour); ?></span>
<?php } else { ?>
	Pick a color above
<?php } ?>	
</p>