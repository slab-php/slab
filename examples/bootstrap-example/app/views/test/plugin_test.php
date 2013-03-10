<h3>Plugin test</h3>

<?php e($html->markdown(<<<EOT
The below [carousel](http://twitter.github.com/bootstrap/javascript.html#carousel) 
is loaded by a partial call to `/carousel` passing
in a list of gallery items which could have been loaded from a database. The
carousel controller and view is in the app's `plugins` folder.
EOT
)); ?>

<?php e($dispatcher->partial('/carousel', array('items' => $items))); ?>